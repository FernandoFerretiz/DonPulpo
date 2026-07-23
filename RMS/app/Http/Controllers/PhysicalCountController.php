<?php

namespace App\Http\Controllers;

use App\Models\Adjustment;
use App\Models\InventoryProduct;
use App\Models\PhysicalCount;
use App\Models\Warehouse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PhysicalCountController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');

        $query = PhysicalCount::with('warehouse')->orderByDesc('created_at');
        if ($status) {
            $query->where('status', $status);
        }

        $counts     = $query->paginate(20)->withQueryString();
        $statusTabs = [
            ''          => 'Todos',
            'open'      => 'Abiertos',
            'confirmed' => 'Confirmados',
            'cancelled' => 'Cancelados',
        ];

        return view('inventory.physical-counts.index', compact('counts', 'status', 'statusTabs'));
    }

    public function create(): View
    {
        return view('inventory.physical-counts.create', [
            'warehouses' => Warehouse::where('status', 'active')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'count_date'   => 'required|date',
            'notes'        => 'nullable|string|max:1000',
        ]);

        $count = DB::transaction(function () use ($validated) {
            $count = PhysicalCount::create([
                'folio'        => PhysicalCount::generateFolio(),
                'warehouse_id' => $validated['warehouse_id'],
                'count_date'   => $validated['count_date'],
                'notes'        => $validated['notes'] ?? null,
                'status'       => PhysicalCount::STATUS_OPEN,
                'created_by'   => auth()->id(),
            ]);

            $products = InventoryProduct::where('is_active', true)
                ->where('tracks_inventory', true)
                ->orderBy('name')
                ->get();

            foreach ($products as $product) {
                $count->items()->create([
                    'inventory_product_id' => $product->id,
                    'system_quantity'      => $product->stockIn($validated['warehouse_id']),
                ]);
            }

            return $count;
        });

        return redirect()->route('inventory.physical-counts.show', $count)
            ->with('success', "Conteo {$count->folio} creado. Capturá las cantidades reales y confirmalo cuando termines.");
    }

    public function show(PhysicalCount $physicalCount): View
    {
        return view('inventory.physical-counts.show', [
            'count' => $physicalCount->load(['warehouse', 'createdBy', 'confirmedBy', 'adjustment', 'items.product']),
        ]);
    }

    public function capture(Request $request, PhysicalCount $physicalCount): RedirectResponse
    {
        if (!$physicalCount->isOpen()) {
            return back()->with('error', 'Solo se pueden capturar cantidades en conteos abiertos.');
        }

        $validated = $request->validate([
            'items'                     => 'required|array',
            'items.*.counted_quantity'  => 'nullable|numeric|min:0',
        ]);

        foreach ($validated['items'] as $itemId => $data) {
            if (!array_key_exists('counted_quantity', $data) || $data['counted_quantity'] === null || $data['counted_quantity'] === '') {
                continue;
            }

            $item = $physicalCount->items()->find($itemId);
            if (!$item) {
                continue;
            }

            $counted = (float) $data['counted_quantity'];
            $item->update([
                'counted_quantity' => $counted,
                'difference'       => $counted - (float) $item->system_quantity,
            ]);
        }

        return back()->with('success', 'Cantidades capturadas correctamente.');
    }

    public function confirm(PhysicalCount $physicalCount): RedirectResponse
    {
        if (!$physicalCount->isOpen()) {
            return back()->with('error', 'Este conteo ya fue confirmado o cancelado.');
        }

        $pendingItems = $physicalCount->items()->whereNull('counted_quantity')->count();
        if ($pendingItems > 0) {
            return back()->with('error', "Todavía hay {$pendingItems} producto(s) sin capturar.");
        }

        DB::transaction(function () use ($physicalCount) {
            $itemsWithDifference = $physicalCount->items()->where('difference', '!=', 0)->get();

            $adjustment = Adjustment::create([
                'folio'           => Adjustment::generateFolio(),
                'warehouse_id'    => $physicalCount->warehouse_id,
                'reason'          => "Conteo físico {$physicalCount->folio}",
                'adjustment_date' => now(),
                'status'          => Adjustment::STATUS_DRAFT,
                'created_by'      => auth()->id(),
            ]);

            foreach ($itemsWithDifference as $item) {
                $adjustment->items()->create([
                    'inventory_product_id' => $item->inventory_product_id,
                    'previous_quantity'    => $item->system_quantity,
                    'new_quantity'         => $item->counted_quantity,
                    'difference'           => $item->difference,
                ]);
            }

            if ($itemsWithDifference->isNotEmpty()) {
                app(AdjustmentController::class)->complete($adjustment);
            } else {
                $adjustment->update(['status' => Adjustment::STATUS_COMPLETED]);
            }

            $physicalCount->update([
                'status'       => PhysicalCount::STATUS_CONFIRMED,
                'adjustment_id' => $adjustment->id,
                'confirmed_by' => auth()->id(),
                'confirmed_at' => now(),
            ]);
        });

        return redirect()->route('inventory.physical-counts.show', $physicalCount)
            ->with('success', "Conteo {$physicalCount->folio} confirmado. El ajuste de inventario se generó y aplicó automáticamente.");
    }

    public function cancel(PhysicalCount $physicalCount): RedirectResponse
    {
        if (!$physicalCount->isOpen()) {
            return back()->with('error', 'Solo se pueden cancelar conteos abiertos.');
        }

        $physicalCount->update(['status' => PhysicalCount::STATUS_CANCELLED]);

        return back()->with('success', "Conteo {$physicalCount->folio} cancelado.");
    }

    public function destroy(PhysicalCount $physicalCount): RedirectResponse
    {
        if (!$physicalCount->isOpen()) {
            return back()->with('error', 'Solo se pueden eliminar conteos abiertos.');
        }

        $physicalCount->delete();

        return redirect()->route('inventory.physical-counts.index')->with('success', 'Conteo eliminado correctamente.');
    }
}

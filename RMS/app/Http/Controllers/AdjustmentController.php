<?php

namespace App\Http\Controllers;

use App\Models\Adjustment;
use App\Models\InventoryProduct;
use App\Models\StockMovement;
use App\Models\Warehouse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdjustmentController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');

        $query = Adjustment::with('warehouse')->orderByDesc('created_at');
        if ($status) {
            $query->where('status', $status);
        }

        $adjustments = $query->paginate(20)->withQueryString();
        $statusTabs  = [
            ''          => 'Todos',
            'draft'     => 'Borrador',
            'completed' => 'Completados',
            'cancelled' => 'Cancelados',
        ];

        return view('inventory.adjustments.index', compact('adjustments', 'status', 'statusTabs'));
    }

    public function create(): View
    {
        return view('inventory.adjustments.create', [
            'warehouses' => Warehouse::where('status', 'active')->orderBy('name')->get(),
            'products'   => InventoryProduct::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'warehouse_id'     => 'required|exists:warehouses,id',
            'reason'           => 'required|string|max:255',
            'adjustment_date'  => 'required|date',
            'notes'            => 'nullable|string|max:1000',
            'items'            => 'required|array|min:1',
            'items.*.inventory_product_id' => 'required|exists:inventory_products,id',
            'items.*.new_quantity'         => 'required|numeric|min:0',
        ]);

        $adjustment = DB::transaction(function () use ($validated) {
            $adjustment = Adjustment::create([
                'folio'           => Adjustment::generateFolio(),
                'warehouse_id'    => $validated['warehouse_id'],
                'reason'          => $validated['reason'],
                'adjustment_date' => $validated['adjustment_date'],
                'notes'           => $validated['notes'] ?? null,
                'status'          => Adjustment::STATUS_DRAFT,
                'created_by'      => auth()->id(),
            ]);

            foreach ($validated['items'] as $item) {
                $product  = InventoryProduct::findOrFail($item['inventory_product_id']);
                $previous = $product->stockIn($validated['warehouse_id']);
                $new      = (float) $item['new_quantity'];

                $adjustment->items()->create([
                    'inventory_product_id' => $product->id,
                    'previous_quantity'    => $previous,
                    'new_quantity'         => $new,
                    'difference'           => $new - $previous,
                ]);
            }

            return $adjustment;
        });

        return redirect()->route('inventory.adjustments.show', $adjustment)->with('success', "Ajuste {$adjustment->folio} creado como borrador.");
    }

    public function show(Adjustment $adjustment): View
    {
        return view('inventory.adjustments.show', [
            'adjustment' => $adjustment->load(['warehouse', 'createdBy', 'items.product']),
        ]);
    }

    public function complete(Adjustment $adjustment): RedirectResponse
    {
        if (!$adjustment->isDraft()) {
            return back()->with('error', 'Solo se pueden completar ajustes en borrador.');
        }

        DB::transaction(function () use ($adjustment) {
            foreach ($adjustment->items as $item) {
                if ((float) $item->difference === 0.0) {
                    continue;
                }

                StockMovement::record([
                    'inventory_product_id' => $item->inventory_product_id,
                    'warehouse_id'         => $adjustment->warehouse_id,
                    'type'                 => StockMovement::TYPE_ADJUSTMENT,
                    'quantity'             => (float) $item->difference,
                    'reference'            => $adjustment,
                    'notes'                => "Ajuste {$adjustment->folio}: {$adjustment->reason}",
                ]);
            }

            $adjustment->update(['status' => Adjustment::STATUS_COMPLETED]);
        });

        return back()->with('success', "Ajuste {$adjustment->folio} aplicado.");
    }

    public function cancel(Adjustment $adjustment): RedirectResponse
    {
        if (!$adjustment->isDraft()) {
            return back()->with('error', 'Solo se pueden cancelar ajustes en borrador.');
        }

        $adjustment->update(['status' => Adjustment::STATUS_CANCELLED]);

        return back()->with('success', "Ajuste {$adjustment->folio} cancelado.");
    }

    public function destroy(Adjustment $adjustment): RedirectResponse
    {
        if (!$adjustment->isDraft()) {
            return back()->with('error', 'Solo se pueden eliminar ajustes en borrador.');
        }

        $adjustment->delete();

        return redirect()->route('inventory.adjustments.index')->with('success', 'Ajuste eliminado correctamente.');
    }
}

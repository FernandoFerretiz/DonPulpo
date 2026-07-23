<?php

namespace App\Http\Controllers;

use App\Models\InventoryProduct;
use App\Models\Shrinkage;
use App\Models\StockMovement;
use App\Models\Warehouse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ShrinkageController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');

        $query = Shrinkage::with('warehouse')->orderByDesc('created_at');
        if ($status) {
            $query->where('status', $status);
        }

        $shrinkages = $query->paginate(20)->withQueryString();
        $statusTabs = [
            ''          => 'Todas',
            'draft'     => 'Borrador',
            'completed' => 'Completadas',
            'cancelled' => 'Canceladas',
        ];

        return view('inventory.shrinkages.index', compact('shrinkages', 'status', 'statusTabs'));
    }

    public function create(): View
    {
        return view('inventory.shrinkages.create', [
            'warehouses' => Warehouse::where('status', 'active')->orderBy('name')->get(),
            'products'   => InventoryProduct::where('is_active', true)->orderBy('name')->get(),
            'reasons'    => Shrinkage::REASONS,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'warehouse_id'    => 'required|exists:warehouses,id',
            'reason'          => ['required', Rule::in(Shrinkage::REASONS)],
            'shrinkage_date'  => 'required|date',
            'notes'           => 'nullable|string|max:1000',
            'items'           => 'required|array|min:1',
            'items.*.inventory_product_id' => 'required|exists:inventory_products,id',
            'items.*.quantity'              => 'required|numeric|min:0.001',
        ]);

        $shrinkage = DB::transaction(function () use ($validated) {
            $shrinkage = Shrinkage::create([
                'folio'          => Shrinkage::generateFolio(),
                'warehouse_id'   => $validated['warehouse_id'],
                'reason'         => $validated['reason'],
                'shrinkage_date' => $validated['shrinkage_date'],
                'notes'          => $validated['notes'] ?? null,
                'status'         => Shrinkage::STATUS_DRAFT,
                'created_by'     => auth()->id(),
            ]);

            foreach ($validated['items'] as $item) {
                $product = InventoryProduct::findOrFail($item['inventory_product_id']);

                $shrinkage->items()->create([
                    'inventory_product_id' => $product->id,
                    'quantity'             => $item['quantity'],
                    'unit_cost'            => $product->average_cost,
                ]);
            }

            return $shrinkage;
        });

        return redirect()->route('inventory.shrinkages.show', $shrinkage)->with('success', "Merma {$shrinkage->folio} creada como borrador.");
    }

    public function show(Shrinkage $shrinkage): View
    {
        return view('inventory.shrinkages.show', [
            'shrinkage' => $shrinkage->load(['warehouse', 'createdBy', 'items.product']),
        ]);
    }

    public function complete(Shrinkage $shrinkage): RedirectResponse
    {
        if (!$shrinkage->isDraft()) {
            return back()->with('error', 'Solo se pueden completar mermas en borrador.');
        }

        DB::transaction(function () use ($shrinkage) {
            foreach ($shrinkage->items as $item) {
                StockMovement::record([
                    'inventory_product_id' => $item->inventory_product_id,
                    'warehouse_id'         => $shrinkage->warehouse_id,
                    'type'                 => StockMovement::TYPE_SHRINKAGE,
                    'quantity'             => -1 * (float) $item->quantity,
                    'unit_cost'            => (float) $item->unit_cost,
                    'reference'            => $shrinkage,
                    'notes'                => "Merma {$shrinkage->folio}: {$shrinkage->getReasonLabel()}",
                ]);
            }

            $shrinkage->update(['status' => Shrinkage::STATUS_COMPLETED]);
        });

        return back()->with('success', "Merma {$shrinkage->folio} aplicada.");
    }

    public function cancel(Shrinkage $shrinkage): RedirectResponse
    {
        if (!$shrinkage->isDraft()) {
            return back()->with('error', 'Solo se pueden cancelar mermas en borrador.');
        }

        $shrinkage->update(['status' => Shrinkage::STATUS_CANCELLED]);

        return back()->with('success', "Merma {$shrinkage->folio} cancelada.");
    }

    public function destroy(Shrinkage $shrinkage): RedirectResponse
    {
        if (!$shrinkage->isDraft()) {
            return back()->with('error', 'Solo se pueden eliminar mermas en borrador.');
        }

        $shrinkage->delete();

        return redirect()->route('inventory.shrinkages.index')->with('success', 'Merma eliminada correctamente.');
    }
}

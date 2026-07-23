<?php

namespace App\Http\Controllers;

use App\Models\InventoryProduct;
use App\Models\Purchase;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\Warehouse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PurchaseController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');

        $query = Purchase::with(['supplier', 'warehouse'])->orderByDesc('created_at');
        if ($status) {
            $query->where('status', $status);
        }

        $purchases  = $query->paginate(20)->withQueryString();
        $statusTabs = [
            ''          => 'Todas',
            'draft'     => 'Borrador',
            'received'  => 'Recibidas',
            'cancelled' => 'Canceladas',
        ];

        return view('inventory.purchases.index', compact('purchases', 'status', 'statusTabs'));
    }

    public function create(): View
    {
        return view('inventory.purchases.create', [
            'suppliers' => Supplier::where('status', 'active')->orderBy('name')->get(),
            'warehouses' => Warehouse::where('status', 'active')->orderBy('name')->get(),
            'products'  => InventoryProduct::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'supplier_id'          => 'required|exists:suppliers,id',
            'warehouse_id'         => 'required|exists:warehouses,id',
            'invoice_number'       => 'nullable|string|max:255',
            'purchase_date'        => 'required|date',
            'notes'                => 'nullable|string|max:1000',
            'items'                => 'required|array|min:1',
            'items.*.inventory_product_id' => 'required|exists:inventory_products,id',
            'items.*.quantity'     => 'required|numeric|min:0.001',
            'items.*.unit_cost'    => 'required|numeric|min:0',
        ]);

        $purchase = DB::transaction(function () use ($validated) {
            $purchase = Purchase::create([
                'folio'          => Purchase::generateFolio(),
                'supplier_id'    => $validated['supplier_id'],
                'warehouse_id'   => $validated['warehouse_id'],
                'invoice_number' => $validated['invoice_number'] ?? null,
                'purchase_date'  => $validated['purchase_date'],
                'notes'          => $validated['notes'] ?? null,
                'status'         => Purchase::STATUS_DRAFT,
                'created_by'     => auth()->id(),
            ]);

            foreach ($validated['items'] as $item) {
                $purchase->items()->create([
                    'inventory_product_id' => $item['inventory_product_id'],
                    'quantity'             => $item['quantity'],
                    'unit_cost'            => $item['unit_cost'],
                    'subtotal'             => $item['quantity'] * $item['unit_cost'],
                ]);
            }

            return $purchase;
        });

        return redirect()->route('inventory.purchases.show', $purchase)->with('success', "Compra {$purchase->folio} creada como borrador.");
    }

    public function show(Purchase $purchase): View
    {
        return view('inventory.purchases.show', [
            'purchase' => $purchase->load(['supplier', 'warehouse', 'createdBy', 'items.product']),
        ]);
    }

    public function receive(Purchase $purchase): RedirectResponse
    {
        if (!$purchase->isDraft()) {
            return back()->with('error', 'Solo se pueden recibir compras en borrador.');
        }

        DB::transaction(function () use ($purchase) {
            foreach ($purchase->items()->with('product')->get() as $item) {
                $item->product->applyPurchaseCost((float) $item->quantity, (float) $item->unit_cost);

                StockMovement::record([
                    'inventory_product_id' => $item->inventory_product_id,
                    'warehouse_id'         => $purchase->warehouse_id,
                    'type'                 => StockMovement::TYPE_PURCHASE,
                    'quantity'             => (float) $item->quantity,
                    'unit_cost'            => (float) $item->unit_cost,
                    'reference'            => $purchase,
                    'notes'                => "Compra {$purchase->folio}",
                ]);
            }

            $purchase->update(['status' => Purchase::STATUS_RECEIVED]);
        });

        return back()->with('success', "Compra {$purchase->folio} recibida: existencias y costos actualizados.");
    }

    public function cancel(Purchase $purchase): RedirectResponse
    {
        if (!$purchase->isDraft()) {
            return back()->with('error', 'Solo se pueden cancelar compras en borrador (una compra recibida ya generó movimientos).');
        }

        $purchase->update(['status' => Purchase::STATUS_CANCELLED]);

        return back()->with('success', "Compra {$purchase->folio} cancelada.");
    }

    public function destroy(Purchase $purchase): RedirectResponse
    {
        if (!$purchase->isDraft()) {
            return back()->with('error', 'Solo se pueden eliminar compras en borrador.');
        }

        $purchase->delete();

        return redirect()->route('inventory.purchases.index')->with('success', 'Compra eliminada correctamente.');
    }
}

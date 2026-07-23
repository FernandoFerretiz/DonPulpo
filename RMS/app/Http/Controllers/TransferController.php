<?php

namespace App\Http\Controllers;

use App\Models\InventoryProduct;
use App\Models\StockMovement;
use App\Models\Transfer;
use App\Models\Warehouse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TransferController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');

        $query = Transfer::with(['originWarehouse', 'destinationWarehouse'])->orderByDesc('created_at');
        if ($status) {
            $query->where('status', $status);
        }

        $transfers  = $query->paginate(20)->withQueryString();
        $statusTabs = [
            ''          => 'Todas',
            'draft'     => 'Borrador',
            'completed' => 'Completadas',
            'cancelled' => 'Canceladas',
        ];

        return view('inventory.transfers.index', compact('transfers', 'status', 'statusTabs'));
    }

    public function create(): View
    {
        return view('inventory.transfers.create', [
            'warehouses' => Warehouse::where('status', 'active')->orderBy('name')->get(),
            'products'   => InventoryProduct::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'origin_warehouse_id'      => 'required|exists:warehouses,id|different:destination_warehouse_id',
            'destination_warehouse_id' => 'required|exists:warehouses,id',
            'transfer_date'            => 'required|date',
            'notes'                    => 'nullable|string|max:1000',
            'items'                    => 'required|array|min:1',
            'items.*.inventory_product_id' => 'required|exists:inventory_products,id',
            'items.*.quantity'         => 'required|numeric|min:0.001',
        ]);

        $transfer = DB::transaction(function () use ($validated) {
            $transfer = Transfer::create([
                'folio'                    => Transfer::generateFolio(),
                'origin_warehouse_id'      => $validated['origin_warehouse_id'],
                'destination_warehouse_id' => $validated['destination_warehouse_id'],
                'transfer_date'            => $validated['transfer_date'],
                'notes'                    => $validated['notes'] ?? null,
                'status'                   => Transfer::STATUS_DRAFT,
                'created_by'               => auth()->id(),
            ]);

            foreach ($validated['items'] as $item) {
                $transfer->items()->create([
                    'inventory_product_id' => $item['inventory_product_id'],
                    'quantity'             => $item['quantity'],
                ]);
            }

            return $transfer;
        });

        return redirect()->route('inventory.transfers.show', $transfer)->with('success', "Transferencia {$transfer->folio} creada como borrador.");
    }

    public function show(Transfer $transfer): View
    {
        return view('inventory.transfers.show', [
            'transfer' => $transfer->load(['originWarehouse', 'destinationWarehouse', 'createdBy', 'items.product']),
        ]);
    }

    public function complete(Transfer $transfer): RedirectResponse
    {
        if (!$transfer->isDraft()) {
            return back()->with('error', 'Solo se pueden completar transferencias en borrador.');
        }

        DB::transaction(function () use ($transfer) {
            foreach ($transfer->items as $item) {
                StockMovement::record([
                    'inventory_product_id' => $item->inventory_product_id,
                    'warehouse_id'         => $transfer->origin_warehouse_id,
                    'related_warehouse_id' => $transfer->destination_warehouse_id,
                    'type'                 => StockMovement::TYPE_TRANSFER,
                    'quantity'             => -1 * (float) $item->quantity,
                    'reference'            => $transfer,
                    'notes'                => "Transferencia {$transfer->folio} (salida)",
                ]);

                StockMovement::record([
                    'inventory_product_id' => $item->inventory_product_id,
                    'warehouse_id'         => $transfer->destination_warehouse_id,
                    'related_warehouse_id' => $transfer->origin_warehouse_id,
                    'type'                 => StockMovement::TYPE_TRANSFER,
                    'quantity'             => (float) $item->quantity,
                    'reference'            => $transfer,
                    'notes'                => "Transferencia {$transfer->folio} (entrada)",
                ]);
            }

            $transfer->update(['status' => Transfer::STATUS_COMPLETED]);
        });

        return back()->with('success', "Transferencia {$transfer->folio} completada.");
    }

    public function cancel(Transfer $transfer): RedirectResponse
    {
        if (!$transfer->isDraft()) {
            return back()->with('error', 'Solo se pueden cancelar transferencias en borrador.');
        }

        $transfer->update(['status' => Transfer::STATUS_CANCELLED]);

        return back()->with('success', "Transferencia {$transfer->folio} cancelada.");
    }

    public function destroy(Transfer $transfer): RedirectResponse
    {
        if (!$transfer->isDraft()) {
            return back()->with('error', 'Solo se pueden eliminar transferencias en borrador.');
        }

        $transfer->delete();

        return redirect()->route('inventory.transfers.index')->with('success', 'Transferencia eliminada correctamente.');
    }
}

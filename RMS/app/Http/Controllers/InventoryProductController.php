<?php

namespace App\Http\Controllers;

use App\Models\InventoryCategory;
use App\Models\InventoryProduct;
use App\Models\Supplier;
use App\Models\UnitOfMeasure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InventoryProductController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->get('search');
        $products = InventoryProduct::with(['category', 'unitOfMeasure'])
            ->when($search, fn($q) => $q->where('name', 'like', "%{$search}%")
                                          ->orWhere('internal_code', 'like', "%{$search}%"))
            ->orderBy('name')
            ->paginate(25)
            ->withQueryString();
        return view('inventory.products.index', compact('products', 'search'));
    }

    public function create(): View
    {
        return view('inventory.products.create', [
            'categories' => InventoryCategory::where('status', 'active')->orderBy('name')->get(),
            'units'      => UnitOfMeasure::orderBy('name')->get(),
            'suppliers'  => Supplier::where('status', 'active')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validated($request);
        $validated['internal_code'] = $validated['internal_code'] ?: InventoryProduct::generateInternalCode();
        $supplierId = $request->input('supplier_id');
        $supplierCost = $request->input('supplier_cost');

        $product = InventoryProduct::create($validated);

        if ($supplierId) {
            $product->suppliers()->attach($supplierId, ['cost' => $supplierCost ?? 0, 'is_primary' => true]);
        }

        return redirect()->route('inventory.products.index')->with('success', 'Producto de inventario creado correctamente.');
    }

    public function show(InventoryProduct $product): View
    {
        $movements = $product->movements()
            ->with(['warehouse', 'relatedWarehouse', 'user'])
            ->orderByDesc('movement_date')
            ->orderByDesc('id')
            ->paginate(30);

        $stocks = $product->stocks()->with('warehouse')->get();

        return view('inventory.products.show', compact('product', 'movements', 'stocks'));
    }

    public function edit(InventoryProduct $product): View
    {
        return view('inventory.products.edit', [
            'product'    => $product->load('suppliers'),
            'categories' => InventoryCategory::where('status', 'active')->orderBy('name')->get(),
            'units'      => UnitOfMeasure::orderBy('name')->get(),
            'suppliers'  => Supplier::where('status', 'active')->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, InventoryProduct $product): RedirectResponse
    {
        $validated = $this->validated($request, $product);
        $validated['internal_code'] = $validated['internal_code'] ?: $product->internal_code;
        $supplierId = $request->input('supplier_id');
        $supplierCost = $request->input('supplier_cost');

        $product->update($validated);

        if ($supplierId) {
            $product->suppliers()->sync([$supplierId => ['cost' => $supplierCost ?? 0, 'is_primary' => true]]);
        }

        return redirect()->route('inventory.products.index')->with('success', 'Producto de inventario actualizado correctamente.');
    }

    public function destroy(InventoryProduct $product): RedirectResponse
    {
        if ($product->movements()->exists()) {
            return back()->with('error', 'No se puede eliminar: el producto ya tiene movimientos de inventario.');
        }

        $product->delete();
        return redirect()->route('inventory.products.index')->with('success', 'Producto de inventario eliminado correctamente.');
    }

    private function validated(Request $request, ?InventoryProduct $product = null): array
    {
        $codeRule = 'nullable|string|max:100|unique:inventory_products,internal_code';
        if ($product) {
            $codeRule .= ',' . $product->id;
        }

        return $request->validate([
            'name'                   => 'required|string|max:255',
            'internal_code'          => $codeRule,
            'barcode'                => 'nullable|string|max:100',
            'inventory_category_id'  => 'nullable|exists:inventory_categories,id',
            'unit_of_measure_id'     => 'required|exists:units_of_measure,id',
            'min_stock'              => 'nullable|numeric|min:0',
            'max_stock'              => 'nullable|numeric|min:0',
            'is_active'              => 'nullable|boolean',
            'tracks_inventory'       => 'nullable|boolean',
            'tracks_lots'            => 'nullable|boolean',
            'tracks_expiration'      => 'nullable|boolean',
        ]) + [
            'is_active'         => $request->boolean('is_active', true),
            'tracks_inventory'  => $request->boolean('tracks_inventory', true),
            'tracks_lots'       => $request->boolean('tracks_lots'),
            'tracks_expiration' => $request->boolean('tracks_expiration'),
            'min_stock'         => $request->input('min_stock', 0),
            'max_stock'         => $request->input('max_stock', 0),
        ];
    }
}

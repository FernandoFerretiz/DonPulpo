<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class WarehouseController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->get('search');
        $warehouses = Warehouse::withCount('stocks')
            ->when($search, fn($q) => $q->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->paginate(25)
            ->withQueryString();
        return view('inventory.warehouses.index', compact('warehouses', 'search'));
    }

    public function create(): View
    {
        return view('inventory.warehouses.create', ['statuses' => Warehouse::STATUSES]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'   => 'required|string|max:255',
            'slug'   => 'nullable|string|max:255|unique:warehouses,slug',
            'status' => ['required', Rule::in(Warehouse::STATUSES)],
        ]);

        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['name']);

        Warehouse::create($validated);

        return redirect()->route('inventory.warehouses.index')->with('success', 'Almacén creado correctamente.');
    }

    public function edit(Warehouse $warehouse): View
    {
        return view('inventory.warehouses.edit', [
            'warehouse' => $warehouse,
            'statuses'  => Warehouse::STATUSES,
        ]);
    }

    public function update(Request $request, Warehouse $warehouse): RedirectResponse
    {
        $validated = $request->validate([
            'name'   => 'required|string|max:255',
            'slug'   => ['nullable', 'string', 'max:255', Rule::unique('warehouses', 'slug')->ignore($warehouse->id)],
            'status' => ['required', Rule::in(Warehouse::STATUSES)],
        ]);

        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['name']);

        $warehouse->update($validated);

        return redirect()->route('inventory.warehouses.index')->with('success', 'Almacén actualizado correctamente.');
    }

    public function destroy(Warehouse $warehouse): RedirectResponse
    {
        if ($warehouse->stocks()->where('quantity', '>', 0)->exists()) {
            return back()->with('error', 'No se puede eliminar: el almacén tiene existencias registradas.');
        }

        $warehouse->delete();
        return redirect()->route('inventory.warehouses.index')->with('success', 'Almacén eliminado correctamente.');
    }
}

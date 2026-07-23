<?php

namespace App\Http\Controllers;

use App\Models\InventoryCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class InventoryCategoryController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->get('search');
        $categories = InventoryCategory::withCount('products')
            ->when($search, fn($q) => $q->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->paginate(25)
            ->withQueryString();
        return view('inventory.categories.index', compact('categories', 'search'));
    }

    public function create(): View
    {
        return view('inventory.categories.create', ['statuses' => InventoryCategory::STATUSES]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'   => 'required|string|max:255',
            'slug'   => 'nullable|string|max:255|unique:inventory_categories,slug',
            'status' => ['required', Rule::in(InventoryCategory::STATUSES)],
        ]);

        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['name']);

        InventoryCategory::create($validated);

        return redirect()->route('inventory.categories.index')->with('success', 'Categoría de inventario creada correctamente.');
    }

    public function edit(InventoryCategory $category): View
    {
        return view('inventory.categories.edit', [
            'category' => $category,
            'statuses' => InventoryCategory::STATUSES,
        ]);
    }

    public function update(Request $request, InventoryCategory $category): RedirectResponse
    {
        $validated = $request->validate([
            'name'   => 'required|string|max:255',
            'slug'   => ['nullable', 'string', 'max:255', Rule::unique('inventory_categories', 'slug')->ignore($category->id)],
            'status' => ['required', Rule::in(InventoryCategory::STATUSES)],
        ]);

        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['name']);

        $category->update($validated);

        return redirect()->route('inventory.categories.index')->with('success', 'Categoría de inventario actualizada correctamente.');
    }

    public function destroy(InventoryCategory $category): RedirectResponse
    {
        if ($category->products()->exists()) {
            return back()->with('error', 'No se puede eliminar: hay productos asignados a esta categoría.');
        }

        $category->delete();
        return redirect()->route('inventory.categories.index')->with('success', 'Categoría de inventario eliminada correctamente.');
    }
}

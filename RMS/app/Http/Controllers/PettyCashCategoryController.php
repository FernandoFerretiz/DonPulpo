<?php

namespace App\Http\Controllers;

use App\Models\PettyCashCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PettyCashCategoryController extends Controller
{
    public function index(): View
    {
        $categories = PettyCashCategory::withCount('vouchers')->orderBy('name')->paginate(20);
        return view('petty-cash.categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('petty-cash.categories.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:petty_cash_categories,name',
        ]);
        PettyCashCategory::create(['name' => $request->name, 'is_active' => true]);
        return redirect()->route('petty-cash.categories.index')->with('success', 'Categoría creada.');
    }

    public function edit(PettyCashCategory $category): View
    {
        return view('petty-cash.categories.edit', compact('category'));
    }

    public function update(Request $request, PettyCashCategory $category): RedirectResponse
    {
        $request->validate([
            'name'      => 'required|string|max:255|unique:petty_cash_categories,name,' . $category->id,
            'is_active' => 'boolean',
        ]);
        $category->update([
            'name'      => $request->name,
            'is_active' => $request->boolean('is_active'),
        ]);
        return redirect()->route('petty-cash.categories.index')->with('success', 'Categoría actualizada.');
    }

    public function destroy(PettyCashCategory $category): RedirectResponse
    {
        if ($category->vouchers()->exists()) {
            return back()->with('error', 'No se puede eliminar una categoría con vales asociados.');
        }
        $category->delete();
        return redirect()->route('petty-cash.categories.index')->with('success', 'Categoría eliminada.');
    }
}

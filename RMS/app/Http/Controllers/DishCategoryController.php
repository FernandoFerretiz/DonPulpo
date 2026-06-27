<?php

namespace App\Http\Controllers;

use App\Models\DishCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DishCategoryController extends Controller
{
    public function index(): View
    {
        $categories = DishCategory::withCount('dishes')->orderBy('display_order')->paginate(20);
        return view('dish-categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('dish-categories.create', ['statuses' => DishCategory::STATUSES]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'slug'          => 'nullable|string|max:255|unique:dish_categories,slug',
            'display_order' => 'nullable|integer|min:0',
            'status'        => ['required', Rule::in(DishCategory::STATUSES)],
        ]);

        $validated['slug']          = $validated['slug'] ?? Str::slug($validated['name']);
        $validated['display_order'] = $validated['display_order'] ?? 0;

        DishCategory::create($validated);

        return redirect()->route('dish-categories.index')->with('success', 'Categoría creada correctamente.');
    }

    public function edit(DishCategory $dishCategory): View
    {
        return view('dish-categories.edit', [
            'category' => $dishCategory,
            'statuses' => DishCategory::STATUSES,
        ]);
    }

    public function update(Request $request, DishCategory $dishCategory): RedirectResponse
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'slug'          => ['nullable', 'string', 'max:255', Rule::unique('dish_categories', 'slug')->ignore($dishCategory->id)],
            'display_order' => 'nullable|integer|min:0',
            'status'        => ['required', Rule::in(DishCategory::STATUSES)],
        ]);

        $validated['slug']          = $validated['slug'] ?? Str::slug($validated['name']);
        $validated['display_order'] = $validated['display_order'] ?? 0;

        $dishCategory->update($validated);

        return redirect()->route('dish-categories.index')->with('success', 'Categoría actualizada correctamente.');
    }

    public function destroy(DishCategory $dishCategory): RedirectResponse
    {
        $dishCategory->delete();
        return redirect()->route('dish-categories.index')->with('success', 'Categoría eliminada correctamente.');
    }
}

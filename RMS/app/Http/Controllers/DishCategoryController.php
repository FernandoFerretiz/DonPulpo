<?php

namespace App\Http\Controllers;

use App\Models\DishCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DishCategoryController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->get('search');
        $categories = DishCategory::withCount('dishes')
            ->when($search, fn($q) => $q->where('name', 'like', "%{$search}%"))
            ->orderBy('display_order')
            ->paginate(20)
            ->withQueryString();

        if ($request->boolean('partial')) {
            return view('dish-categories._table', compact('categories', 'search'));
        }

        return view('dish-categories.index', compact('categories', 'search'));
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

    public function edit(Request $request, DishCategory $dishCategory): View
    {
        $data = [
            'category' => $dishCategory,
            'statuses' => DishCategory::STATUSES,
        ];

        if ($request->boolean('modal')) {
            return view('dish-categories._form', $data);
        }

        return view('dish-categories.edit', $data);
    }

    public function update(Request $request, DishCategory $dishCategory): RedirectResponse|JsonResponse
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

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Categoría actualizada correctamente.']);
        }

        return redirect()->route('dish-categories.index')->with('success', 'Categoría actualizada correctamente.');
    }

    public function destroy(DishCategory $dishCategory): RedirectResponse
    {
        $dishCategory->delete();
        return redirect()->route('dish-categories.index')->with('success', 'Categoría eliminada correctamente.');
    }
}

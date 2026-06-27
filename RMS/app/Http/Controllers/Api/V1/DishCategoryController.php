<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\DishCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class DishCategoryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = DishCategory::orderBy('display_order');

        if ($request->query('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->query('with_dishes')) {
            $query->where('status', 'active')
                  ->with(['activeDishes' => fn($q) => $q->orderBy('name')]);
            $categories = $query->get();

            $data = $categories->map(fn($cat) => [
                'id'            => $cat->id,
                'name'          => $cat->name,
                'slug'          => $cat->slug,
                'display_order' => $cat->display_order,
                'status'        => $cat->status,
                'dishes'        => $cat->activeDishes->map(fn($dish) => [
                    'id'               => $dish->id,
                    'dish_category_id' => $dish->dish_category_id,
                    'name'             => $dish->name,
                    'description'      => $dish->description,
                    'image_path'       => $dish->image_path,
                    'price'            => number_format($dish->price, 2, '.', ''),
                    'status'           => $dish->status,
                ]),
            ]);

            return response()->json(['success' => true, 'data' => $data, 'message' => 'Categorías obtenidas correctamente']);
        }

        $categories = $query->get();
        return response()->json(['success' => true, 'data' => $categories, 'message' => 'Categorías obtenidas correctamente']);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'slug'          => 'nullable|string|max:255|unique:dish_categories,slug',
            'display_order' => 'nullable|integer|min:0',
            'status'        => ['required', Rule::in(DishCategory::STATUSES)],
        ]);

        $validated['slug']          = $validated['slug'] ?? Str::slug($validated['name']);
        $validated['display_order'] = $validated['display_order'] ?? 0;

        $category = DishCategory::create($validated);

        return response()->json(['success' => true, 'data' => $category, 'message' => 'Categoría creada correctamente'], 201);
    }

    public function show(int $id): JsonResponse
    {
        $category = DishCategory::with('dishes')->findOrFail($id);
        return response()->json(['success' => true, 'data' => $category, 'message' => 'Categoría obtenida correctamente']);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $category = DishCategory::findOrFail($id);

        $validated = $request->validate([
            'name'          => 'sometimes|string|max:255',
            'slug'          => ['sometimes', 'string', 'max:255', Rule::unique('dish_categories', 'slug')->ignore($category->id)],
            'display_order' => 'sometimes|integer|min:0',
            'status'        => ['sometimes', Rule::in(DishCategory::STATUSES)],
        ]);

        if (isset($validated['name']) && !isset($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $category->update($validated);

        return response()->json(['success' => true, 'data' => $category->fresh(), 'message' => 'Categoría actualizada correctamente']);
    }

    public function destroy(int $id): JsonResponse
    {
        $category = DishCategory::findOrFail($id);
        $category->delete();

        return response()->json(['success' => true, 'data' => null, 'message' => 'Categoría eliminada correctamente']);
    }
}

<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Dish;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DishController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Dish::with('category')->orderBy('name');

        if ($request->query('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->query('dish_category_id') ?? $request->query('category_id')) {
            $categoryId = $request->query('dish_category_id') ?? $request->query('category_id');
            $query->where('dish_category_id', $categoryId);
        }

        $dishes = $query->get();
        return response()->json(['success' => true, 'data' => $dishes, 'message' => 'Platillos obtenidos correctamente']);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'dish_category_id' => 'nullable|exists:dish_categories,id',
            'name'             => 'required|string|max:255',
            'description'      => 'nullable|string',
            'image_path'       => 'nullable|string|max:500',
            'price'            => 'required|numeric|min:0',
            'status'           => ['required', Rule::in(Dish::STATUSES)],
        ]);

        $dish = Dish::create($validated);

        return response()->json(['success' => true, 'data' => $dish, 'message' => 'Platillo creado correctamente'], 201);
    }

    public function show(int $id): JsonResponse
    {
        $dish = Dish::with('category')->findOrFail($id);
        return response()->json(['success' => true, 'data' => $dish, 'message' => 'Platillo obtenido correctamente']);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $dish = Dish::findOrFail($id);

        $validated = $request->validate([
            'dish_category_id' => 'sometimes|nullable|exists:dish_categories,id',
            'name'             => 'sometimes|string|max:255',
            'description'      => 'sometimes|nullable|string',
            'image_path'       => 'sometimes|nullable|string|max:500',
            'price'            => 'sometimes|numeric|min:0',
            'status'           => ['sometimes', Rule::in(Dish::STATUSES)],
        ]);

        $dish->update($validated);

        return response()->json(['success' => true, 'data' => $dish->fresh(), 'message' => 'Platillo actualizado correctamente']);
    }

    public function destroy(int $id): JsonResponse
    {
        $dish = Dish::findOrFail($id);
        $dish->delete();

        return response()->json(['success' => true, 'data' => null, 'message' => 'Platillo eliminado correctamente']);
    }
}

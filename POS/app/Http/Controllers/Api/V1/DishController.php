<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Dish;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
}

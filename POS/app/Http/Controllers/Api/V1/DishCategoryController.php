<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\DishCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
                  ->with(['activeDishes' => fn($q) => $q->orderBy('name')->with('modifierOptions.group')]);

            $categories = $query->get();

            $data = $categories->map(fn($cat) => [
                'id'            => (int) $cat->id,
                'name'          => $cat->name,
                'slug'          => $cat->slug,
                'display_order' => $cat->display_order,
                'status'        => $cat->status,
                'dishes'        => $cat->activeDishes->map(fn($dish) => [
                    'id'               => (int) $dish->id,
                    'dish_category_id' => (int) $dish->dish_category_id,
                    'name'             => $dish->name,
                    'description'      => $dish->description,
                    'image_path'       => $dish->image_path,
                    'price'            => number_format($dish->price, 2, '.', ''),
                    'status'           => $dish->status,
                    'modifier_groups'  => $this->dishModifierGroups($dish),
                ]),
            ]);

            return response()->json(['success' => true, 'data' => $data, 'message' => 'Categorías obtenidas correctamente']);
        }

        $categories = $query->get();
        return response()->json(['success' => true, 'data' => $categories, 'message' => 'Categorías obtenidas correctamente']);
    }

    /**
     * Groups this dish's priced modifier options by their group. Only options with a
     * price set for this specific dish show up (price is per dish, see dish_modifier_options).
     */
    private function dishModifierGroups($dish): array
    {
        $groups = [];

        foreach ($dish->modifierOptions as $option) {
            $group = $option->group;
            if (! isset($groups[$group->id])) {
                $groups[$group->id] = [
                    'id'             => (int) $group->id,
                    'name'           => $group->name,
                    'selection_type' => $group->selection_type,
                    'pricing_mode'   => $group->pricing_mode,
                    'required'       => (bool) $group->required,
                    'options'        => [],
                ];
            }
            $groups[$group->id]['options'][] = [
                'id'          => (int) $option->id,
                'name'        => $option->name,
                'price_delta' => number_format($option->pivot->price_delta, 2, '.', ''),
            ];
        }

        return array_values($groups);
    }
}

<?php

namespace App\Http\Controllers\Api\V1\Sync;

use App\Http\Controllers\Controller;
use App\Models\Dish;
use App\Models\DishCategory;
use App\Models\PettyCashCategory;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BootstrapController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $branch = $request->user()->branch;
        $since  = $request->query('since');

        $scope = fn(string $model) => $model::query()
            ->where('company_id', $branch->company_id)
            ->when($since, fn($q) => $q->where('updated_at', '>', $since));

        $dishCategories = $scope(DishCategory::class)->get(['uuid', 'name', 'slug', 'display_order', 'status']);

        $dishes = $scope(Dish::class)
            ->with('category:id,uuid')
            ->get(['uuid', 'dish_category_id', 'name', 'description', 'image_path', 'price', 'status'])
            ->map(fn ($dish) => [
                'uuid'               => $dish->uuid,
                'dish_category_uuid' => $dish->category?->uuid,
                'name'               => $dish->name,
                'description'        => $dish->description,
                'image_path'         => $dish->image_path,
                'price'              => $dish->price,
                'status'             => $dish->status,
            ]);

        $users = $scope(User::class)
            ->get(['uuid', 'name', 'email', 'password', 'role', 'status'])
            ->map(fn ($user) => [
                'uuid'          => $user->uuid,
                'name'          => $user->name,
                'email'         => $user->email,
                'password_hash' => $user->password,
                'role'          => $user->role,
                'status'        => $user->status,
            ]);

        $pettyCashCategories = $scope(PettyCashCategory::class)->get(['uuid', 'name', 'is_active']);

        return response()->json([
            'success'     => true,
            'server_time' => now()->toIso8601String(),
            'data'        => [
                'branch' => [
                    'uuid'         => $branch->uuid,
                    'company_uuid' => $branch->company->uuid,
                    'code'         => $branch->code,
                    'name'         => $branch->name,
                    'address'      => $branch->address,
                    'city'         => $branch->city,
                    'timezone'     => $branch->timezone,
                    'status'       => $branch->status,
                ],
                'dish_categories'       => $dishCategories,
                'dishes'                => $dishes,
                'users'                 => $users,
                'petty_cash_categories' => $pettyCashCategories,
                // Placeholder until company_settings/branch_settings ship.
                'settings' => [
                    'tax_rate' => '0.16',
                ],
            ],
            'message' => 'Bootstrap obtenido correctamente',
        ]);
    }
}

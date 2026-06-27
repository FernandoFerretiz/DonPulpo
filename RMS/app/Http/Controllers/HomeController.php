<?php

namespace App\Http\Controllers;

use App\Models\DishCategory;
use App\Models\Dish;
use App\Models\User;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        return view('home', [
            'totalUsers'      => User::count(),
            'totalCategories' => DishCategory::count(),
            'totalDishes'     => Dish::count(),
        ]);
    }
}

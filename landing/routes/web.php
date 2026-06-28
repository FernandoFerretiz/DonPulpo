<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

Route::get('/', function () {
    $categories = DB::table('dish_categories')
        ->where('status', 'active')
        ->orderBy('display_order')
        ->orderBy('name')
        ->get();

    $dishes = DB::table('dishes')
        ->where('status', 'active')
        ->orderBy('dish_category_id')
        ->orderBy('name')
        ->get()
        ->groupBy('dish_category_id');

    return view('home', compact('categories', 'dishes'));
});

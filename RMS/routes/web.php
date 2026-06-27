<?php

use App\Http\Controllers\DishCategoryController;
use App\Http\Controllers\DishController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::resource('users', UserController::class)->except(['show']);
Route::resource('dish-categories', DishCategoryController::class)->except(['show']);
Route::resource('dishes', DishController::class)->except(['show']);

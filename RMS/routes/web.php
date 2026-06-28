<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DishCategoryController;
use App\Http\Controllers\DishController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Auth
Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rutas protegidas
Route::middleware('auth.rms')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');

    Route::resource('users',           UserController::class)->except(['show']);
    Route::resource('dish-categories', DishCategoryController::class)->except(['show']);
    Route::resource('dishes',          DishController::class)->except(['show']);
});

<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DishCategoryController;
use App\Http\Controllers\DishController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PettyCashCategoryController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Auth
Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rutas protegidas
Route::middleware('auth.rms')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::post('/keep-alive', fn() => response()->noContent())->name('keep-alive');

    Route::resource('users',           UserController::class)->except(['show']);
    Route::resource('dish-categories', DishCategoryController::class)->except(['show']);
    Route::resource('dishes',          DishController::class)->except(['show']);

    // Caja chica — categorías
    // (los vales en sí se solicitan/autorizan/pagan en POS, no en RMS —
    // ver App\Services\PettyCashVoucherService en POS)
    Route::prefix('petty-cash/categories')->name('petty-cash.categories.')->group(function () {
        Route::get('/',              [PettyCashCategoryController::class, 'index'])->name('index');
        Route::get('/create',        [PettyCashCategoryController::class, 'create'])->name('create');
        Route::post('/',             [PettyCashCategoryController::class, 'store'])->name('store');
        Route::get('/{category}/edit',   [PettyCashCategoryController::class, 'edit'])->name('edit');
        Route::put('/{category}',        [PettyCashCategoryController::class, 'update'])->name('update');
        Route::delete('/{category}',     [PettyCashCategoryController::class, 'destroy'])->name('destroy');
    });
});

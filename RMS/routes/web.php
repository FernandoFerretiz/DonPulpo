<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DishCategoryController;
use App\Http\Controllers\DishController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PettyCashCategoryController;
use App\Http\Controllers\PettyCashVoucherController;
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

    // Caja chica — vales
    Route::prefix('petty-cash/vouchers')->name('petty-cash.vouchers.')->group(function () {
        Route::get('/',                             [PettyCashVoucherController::class, 'index'])->name('index');
        Route::get('/create',                       [PettyCashVoucherController::class, 'create'])->name('create');
        Route::post('/',                            [PettyCashVoucherController::class, 'store'])->name('store');
        Route::patch('/{voucher}/authorize',        [PettyCashVoucherController::class, 'authorize'])->name('authorize');
        Route::patch('/{voucher}/reject',           [PettyCashVoucherController::class, 'reject'])->name('reject');
        Route::patch('/{voucher}/cancel',           [PettyCashVoucherController::class, 'cancel'])->name('cancel');
    });

    // Caja chica — categorías
    Route::prefix('petty-cash/categories')->name('petty-cash.categories.')->group(function () {
        Route::get('/',              [PettyCashCategoryController::class, 'index'])->name('index');
        Route::get('/create',        [PettyCashCategoryController::class, 'create'])->name('create');
        Route::post('/',             [PettyCashCategoryController::class, 'store'])->name('store');
        Route::get('/{category}/edit',   [PettyCashCategoryController::class, 'edit'])->name('edit');
        Route::put('/{category}',        [PettyCashCategoryController::class, 'update'])->name('update');
        Route::delete('/{category}',     [PettyCashCategoryController::class, 'destroy'])->name('destroy');
    });
});

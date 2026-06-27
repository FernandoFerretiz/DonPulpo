<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\DishCategoryController;
use App\Http\Controllers\Api\V1\DishController;
use App\Http\Controllers\Api\V1\OrderController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.v1.')->group(function () {
    // Auth (sin protección de sesión, para apps móviles/tablet)
    Route::post('auth/login', [AuthController::class, 'login']);
    Route::get('auth/me', [AuthController::class, 'me']);
    Route::post('auth/logout', [AuthController::class, 'logout']);

    // Menú (lectura pública dentro del contexto POS)
    Route::get('dish-categories', [DishCategoryController::class, 'index']);
    Route::get('dishes', [DishController::class, 'index']);

    // Órdenes
    Route::get('orders', [OrderController::class, 'index']);
    Route::post('orders', [OrderController::class, 'store']);
    Route::get('orders/{id}', [OrderController::class, 'show']);
    Route::put('orders/{id}', [OrderController::class, 'update']);
    Route::delete('orders/{id}', [OrderController::class, 'destroy']);

    // Ítems de una orden
    Route::post('orders/{id}/items', [OrderController::class, 'addItem']);
    Route::put('orders/{id}/items/{itemId}', [OrderController::class, 'updateItem']);
    Route::delete('orders/{id}/items/{itemId}', [OrderController::class, 'removeItem']);

    // Cobrar
    Route::post('orders/{id}/pay', [OrderController::class, 'pay']);

    // Dashboard
    Route::get('dashboard/summary', [DashboardController::class, 'summary']);
});

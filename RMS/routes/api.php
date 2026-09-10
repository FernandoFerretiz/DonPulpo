<?php

use App\Http\Controllers\Api\V1\Cobro\AuthController as CobroAuthController;
use App\Http\Controllers\Api\V1\Cobro\CustomerController as CobroCustomerController;
use App\Http\Controllers\Api\V1\Cobro\DashboardController as CobroDashboardController;
use App\Http\Controllers\Api\V1\Cobro\DiscountCodeController as CobroDiscountCodeController;
use App\Http\Controllers\Api\V1\Cobro\DishCategoryController as CobroDishCategoryController;
use App\Http\Controllers\Api\V1\Cobro\DishController as CobroDishController;
use App\Http\Controllers\Api\V1\Cobro\ExpenseController as CobroExpenseController;
use App\Http\Controllers\Api\V1\Cobro\OrderController as CobroOrderController;
use App\Http\Controllers\Api\V1\Cobro\PettyCashController as CobroPettyCashController;
use App\Http\Controllers\Api\V1\Cobro\ShiftController as CobroShiftController;
use App\Http\Controllers\Api\V1\Cobro\TableController as CobroTableController;
use App\Http\Controllers\Api\V1\DishCategoryController;
use App\Http\Controllers\Api\V1\DishController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.v1.')->group(function () {
    Route::apiResource('users', UserController::class);
    Route::apiResource('dish-categories', DishCategoryController::class);
    Route::apiResource('dishes', DishController::class);
});

// API que consume la app nativa de cobro (Flutter, DonPulpo Cobro). Vive bajo
// /api/v1/cobro para no chocar con las rutas admin de arriba (mismos nombres
// de recurso — dishes, dish-categories — pero controladores distintos).
// Antes vivía en el proyecto POS; se migró acá porque el POS web ya no se usa.
// El proyecto POS se deja intacto (no se borra) por si hace falta recuperar algo.
Route::prefix('v1/cobro')->name('api.v1.cobro.')->group(function () {
    Route::post('auth/pin-login', [CobroAuthController::class, 'pinLogin']);

    Route::get('dish-categories', [CobroDishCategoryController::class, 'index']);
    Route::get('dishes', [CobroDishController::class, 'index']);

    Route::post('discount-codes/validate', [CobroDiscountCodeController::class, 'validateCode']);

    Route::get('orders', [CobroOrderController::class, 'index']);
    Route::post('orders', [CobroOrderController::class, 'store']);
    Route::get('orders/active-count', [CobroOrderController::class, 'activeCount']);
    Route::get('orders/{id}', [CobroOrderController::class, 'show']);
    Route::put('orders/{id}', [CobroOrderController::class, 'update']);

    Route::post('orders/{id}/items', [CobroOrderController::class, 'addItem']);
    Route::put('orders/{id}/items/{itemId}', [CobroOrderController::class, 'updateItem']);
    Route::delete('orders/{id}/items/{itemId}', [CobroOrderController::class, 'removeItem']);

    Route::get('dashboard/summary', [CobroDashboardController::class, 'summary']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('auth/pin-verify', [CobroAuthController::class, 'pinVerify']);
        Route::post('auth/token-logout', [CobroAuthController::class, 'tokenLogout']);

        Route::post('orders/{id}/pay', [CobroOrderController::class, 'pay']);
        Route::delete('orders/{id}', [CobroOrderController::class, 'destroy']);
        Route::get('orders/{id}/ticket', [CobroOrderController::class, 'ticket']);
        Route::get('orders/{id}/comanda', [CobroOrderController::class, 'comanda']);

        Route::get('customers', [CobroCustomerController::class, 'index']);
        Route::post('customers', [CobroCustomerController::class, 'store']);

        Route::get('shifts/active', [CobroShiftController::class, 'active']);
        Route::post('shifts', [CobroShiftController::class, 'open']);
        Route::post('shifts/{id}/close', [CobroShiftController::class, 'close']);
        Route::get('shifts/{id}/summary', [CobroShiftController::class, 'summary']);
        Route::post('shifts/{id}/movements', [CobroShiftController::class, 'addMovement']);

        Route::get('petty-cash/vouchers', [CobroPettyCashController::class, 'authorizedVouchers']);
        Route::post('petty-cash/vouchers/{id}/pay', [CobroPettyCashController::class, 'pay']);

        Route::get('expenses', [CobroExpenseController::class, 'index']);
        Route::post('expenses', [CobroExpenseController::class, 'store']);

        Route::get('tables', [CobroTableController::class, 'index']);
    });
});

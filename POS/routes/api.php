<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CustomerController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\DiscountCodeController;
use App\Http\Controllers\Api\V1\DishCategoryController;
use App\Http\Controllers\Api\V1\DishController;
use App\Http\Controllers\Api\V1\ExpenseController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\PettyCashController;
use App\Http\Controllers\Api\V1\ShiftController;
use App\Http\Controllers\Api\V1\TableController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.v1.')->group(function () {
    // Auth (sin protección de sesión, para apps móviles/tablet)
    Route::post('auth/login', [AuthController::class, 'login']);
    Route::get('auth/me', [AuthController::class, 'me']);
    Route::post('auth/logout', [AuthController::class, 'logout']);

    // Auth por PIN + token Sanctum, para la app nativa (Flutter)
    Route::post('auth/pin-login', [AuthController::class, 'pinLogin']);

    // Menú (lectura pública dentro del contexto POS)
    Route::get('dish-categories', [DishCategoryController::class, 'index']);
    Route::get('dishes', [DishController::class, 'index']);

    // Códigos de descuento
    Route::post('discount-codes/validate', [DiscountCodeController::class, 'validateCode']);

    // Órdenes
    Route::get('orders', [OrderController::class, 'index']);
    Route::post('orders', [OrderController::class, 'store']);
    Route::get('orders/active-count', [OrderController::class, 'activeCount']);
    Route::get('orders/{id}', [OrderController::class, 'show']);
    Route::put('orders/{id}', [OrderController::class, 'update']);

    // Ítems de una orden
    Route::post('orders/{id}/items', [OrderController::class, 'addItem']);
    Route::put('orders/{id}/items/{itemId}', [OrderController::class, 'updateItem']);
    Route::delete('orders/{id}/items/{itemId}', [OrderController::class, 'removeItem']);

    // Dashboard
    Route::get('dashboard/summary', [DashboardController::class, 'summary']);

    // Rutas autenticadas por token Sanctum, para la app nativa (Flutter).
    // Duplican (bajo el mismo prefijo api/v1) rutas que hoy solo viven en web.php
    // protegidas por sesión — se agregan acá sin tocar web.php para no afectar
    // el flujo web actual, y de paso se registran los controllers que ya
    // existían pero no tenían ruta (Customer/Shift/PettyCash).
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('auth/pin-verify', [AuthController::class, 'pinVerify']);
        Route::post('auth/token-logout', [AuthController::class, 'tokenLogout']);

        // Cobrar una orden (registra movimiento de caja → requiere user_id real)
        Route::post('orders/{id}/pay', [OrderController::class, 'pay']);

        // Eliminar orden: solo admin (verificado en el controlador vía Auth::user())
        Route::delete('orders/{id}', [OrderController::class, 'destroy']);

        // Ticket de venta en PDF / comanda de cocina
        Route::get('orders/{id}/ticket', [OrderController::class, 'ticket']);
        Route::get('orders/{id}/comanda', [OrderController::class, 'comanda']);

        // Clientes (para pagos a crédito)
        Route::get('customers', [CustomerController::class, 'index']);
        Route::post('customers', [CustomerController::class, 'store']);

        // Turnos
        Route::get('shifts/active', [ShiftController::class, 'active']);
        Route::post('shifts', [ShiftController::class, 'open']);
        Route::post('shifts/{id}/close', [ShiftController::class, 'close']);
        Route::get('shifts/{id}/summary', [ShiftController::class, 'summary']);
        Route::post('shifts/{id}/movements', [ShiftController::class, 'addMovement']);

        // Vales de caja chica
        Route::get('petty-cash/vouchers', [PettyCashController::class, 'authorizedVouchers']);
        Route::post('petty-cash/vouchers/{id}/pay', [PettyCashController::class, 'pay']);

        // Gastos: independientes del turno de caja (no requieren turno abierto)
        Route::get('expenses', [ExpenseController::class, 'index']);
        Route::post('expenses', [ExpenseController::class, 'store']);

        // Catálogo de mesas (se administra desde RMS)
        Route::get('tables', [TableController::class, 'index']);
    });
});

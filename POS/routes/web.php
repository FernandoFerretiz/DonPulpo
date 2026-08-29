<?php

use App\Http\Controllers\Api\V1\PettyCashController;
use App\Http\Controllers\Api\V1\ShiftController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PosController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth.pos')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/home', [HomeController::class, 'index'])->name('home.alt');
    Route::get('/pos', [PosController::class, 'index'])->name('pos');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Rutas que necesitan sesión activa para Auth::id() — viven en web.php
    // Mantienen el mismo prefijo /api/v1 que usa el JS del frontend
    Route::prefix('api/v1')->group(function () {
        // Cobrar una orden (registra movimiento de caja → requiere user_id real)
        Route::post('orders/{id}/pay', [\App\Http\Controllers\Api\V1\OrderController::class, 'pay']);

        // Ticket de venta en PDF (mientras no hay impresora térmica integrada)
        Route::get('orders/{id}/ticket', [\App\Http\Controllers\Api\V1\OrderController::class, 'ticket']);

        // Comanda de cocina — se imprime aunque la orden no esté pagada
        Route::get('orders/{id}/comanda', [\App\Http\Controllers\Api\V1\OrderController::class, 'comanda']);

        // Clientes (para pagos a crédito)
        Route::get('customers',  [\App\Http\Controllers\Api\V1\CustomerController::class, 'index']);
        Route::post('customers', [\App\Http\Controllers\Api\V1\CustomerController::class, 'store']);

        // Turnos
        Route::get('shifts/active',           [ShiftController::class, 'active']);
        Route::post('shifts',                 [ShiftController::class, 'open']);
        Route::post('shifts/{id}/close',      [ShiftController::class, 'close']);
        Route::get('shifts/{id}/summary',     [ShiftController::class, 'summary']);
        Route::post('shifts/{id}/movements',  [ShiftController::class, 'addMovement']);

        // Vales de caja chica
        Route::get('petty-cash/vouchers',             [PettyCashController::class, 'authorizedVouchers']);
        Route::post('petty-cash/vouchers/{id}/pay',   [PettyCashController::class, 'pay']);
    });
});

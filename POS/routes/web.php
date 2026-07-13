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
    Route::post('/keep-alive', fn() => response()->noContent())->name('keep-alive');
    Route::get('/home', [HomeController::class, 'index'])->name('home.alt');
    Route::get('/pos', [PosController::class, 'index'])->name('pos');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Rutas que necesitan sesión activa para Auth::id() — viven en web.php
    // Mantienen el mismo prefijo /api/v1 que usa el JS del frontend
    Route::prefix('api/v1')->group(function () {
        // Cobrar una orden (registra movimiento de caja → requiere user_id real)
        Route::post('orders/{id}/pay', [\App\Http\Controllers\Api\V1\OrderController::class, 'pay']);

        // Turnos
        Route::get('shifts/active',           [ShiftController::class, 'active']);
        Route::post('shifts',                 [ShiftController::class, 'open']);
        Route::post('shifts/{id}/close',      [ShiftController::class, 'close']);
        Route::get('shifts/{id}/summary',     [ShiftController::class, 'summary']);
        Route::post('shifts/{id}/movements',  [ShiftController::class, 'addMovement']);

        // Vales de caja chica
        Route::get('petty-cash/vouchers',                    [PettyCashController::class, 'authorizedVouchers']);
        Route::get('petty-cash/vouchers/history',             [PettyCashController::class, 'index']);
        Route::post('petty-cash/vouchers',                    [PettyCashController::class, 'store']);
        Route::patch('petty-cash/vouchers/{id}/authorize',    [PettyCashController::class, 'authorize']);
        Route::patch('petty-cash/vouchers/{id}/reject',       [PettyCashController::class, 'reject']);
        Route::patch('petty-cash/vouchers/{id}/cancel',       [PettyCashController::class, 'cancel']);
        Route::post('petty-cash/vouchers/{id}/pay',           [PettyCashController::class, 'pay']);
    });
});

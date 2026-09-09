<?php

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
});

// Las rutas que necesitan sesión activa para Auth::id() (pagar orden, ticket,
// comanda, clientes, turnos, caja chica) viven en routes/api.php bajo
// auth:sanctum — gracias a $middleware->statefulApi() (ver bootstrap/app.php)
// esa misma ruta acepta tanto la sesión-cookie del navegador (POS web) como
// un token Bearer (app nativa Flutter), sin duplicar nada acá.

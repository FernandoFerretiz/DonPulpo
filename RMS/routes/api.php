<?php

use App\Http\Controllers\Api\V1\DishCategoryController;
use App\Http\Controllers\Api\V1\DishController;
use App\Http\Controllers\Api\V1\Sync\BootstrapController;
use App\Http\Controllers\Api\V1\Sync\PushController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.v1.')->group(function () {
    Route::apiResource('users', UserController::class);
    Route::apiResource('dish-categories', DishCategoryController::class);
    Route::apiResource('dishes', DishController::class);

    // Sync: branch-server-facing endpoints, authenticated with a Sanctum
    // token issued to a BranchInstallation (a device, not a person).
    Route::prefix('sync')->middleware('auth:sanctum')->name('sync.')->group(function () {
        Route::get('bootstrap', BootstrapController::class)->name('bootstrap');
        Route::post('push', PushController::class)->name('push');
    });
});

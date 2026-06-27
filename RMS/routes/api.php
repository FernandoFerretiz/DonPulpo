<?php

use App\Http\Controllers\Api\V1\DishCategoryController;
use App\Http\Controllers\Api\V1\DishController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::apiResource('users', UserController::class);
    Route::apiResource('dish-categories', DishCategoryController::class);
    Route::apiResource('dishes', DishController::class);
});

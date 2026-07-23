<?php

use App\Http\Controllers\AdjustmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DiscountCodeController;
use App\Http\Controllers\DishCategoryController;
use App\Http\Controllers\DishController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InventoryCategoryController;
use App\Http\Controllers\InventoryProductController;
use App\Http\Controllers\PettyCashCategoryController;
use App\Http\Controllers\PettyCashVoucherController;
use App\Http\Controllers\PhysicalCountController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\ShrinkageController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\UnitOfMeasureController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WarehouseController;
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
    Route::resource('discount-codes',  DiscountCodeController::class)->except(['show']);

    // Cortes de caja
    Route::resource('shifts', ShiftController::class)->only(['index', 'show']);

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

    // Inventario
    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::resource('categories', InventoryCategoryController::class)->except(['show']);
        Route::resource('units',      UnitOfMeasureController::class)->except(['show']);
        Route::resource('warehouses', WarehouseController::class)->except(['show']);
        Route::resource('suppliers',  SupplierController::class)->except(['show']);
        Route::resource('products',   InventoryProductController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);

        // Compras
        Route::prefix('purchases')->name('purchases.')->group(function () {
            Route::get('/',                     [PurchaseController::class, 'index'])->name('index');
            Route::get('/create',               [PurchaseController::class, 'create'])->name('create');
            Route::post('/',                    [PurchaseController::class, 'store'])->name('store');
            Route::get('/{purchase}',           [PurchaseController::class, 'show'])->name('show');
            Route::patch('/{purchase}/receive', [PurchaseController::class, 'receive'])->name('receive');
            Route::patch('/{purchase}/cancel',  [PurchaseController::class, 'cancel'])->name('cancel');
            Route::delete('/{purchase}',        [PurchaseController::class, 'destroy'])->name('destroy');
        });

        // Transferencias
        Route::prefix('transfers')->name('transfers.')->group(function () {
            Route::get('/',                      [TransferController::class, 'index'])->name('index');
            Route::get('/create',                [TransferController::class, 'create'])->name('create');
            Route::post('/',                     [TransferController::class, 'store'])->name('store');
            Route::get('/{transfer}',            [TransferController::class, 'show'])->name('show');
            Route::patch('/{transfer}/complete', [TransferController::class, 'complete'])->name('complete');
            Route::patch('/{transfer}/cancel',   [TransferController::class, 'cancel'])->name('cancel');
            Route::delete('/{transfer}',         [TransferController::class, 'destroy'])->name('destroy');
        });

        // Ajustes
        Route::prefix('adjustments')->name('adjustments.')->group(function () {
            Route::get('/',                        [AdjustmentController::class, 'index'])->name('index');
            Route::get('/create',                  [AdjustmentController::class, 'create'])->name('create');
            Route::post('/',                       [AdjustmentController::class, 'store'])->name('store');
            Route::get('/{adjustment}',            [AdjustmentController::class, 'show'])->name('show');
            Route::patch('/{adjustment}/complete', [AdjustmentController::class, 'complete'])->name('complete');
            Route::patch('/{adjustment}/cancel',   [AdjustmentController::class, 'cancel'])->name('cancel');
            Route::delete('/{adjustment}',         [AdjustmentController::class, 'destroy'])->name('destroy');
        });

        // Mermas
        Route::prefix('shrinkages')->name('shrinkages.')->group(function () {
            Route::get('/',                       [ShrinkageController::class, 'index'])->name('index');
            Route::get('/create',                 [ShrinkageController::class, 'create'])->name('create');
            Route::post('/',                      [ShrinkageController::class, 'store'])->name('store');
            Route::get('/{shrinkage}',            [ShrinkageController::class, 'show'])->name('show');
            Route::patch('/{shrinkage}/complete', [ShrinkageController::class, 'complete'])->name('complete');
            Route::patch('/{shrinkage}/cancel',   [ShrinkageController::class, 'cancel'])->name('cancel');
            Route::delete('/{shrinkage}',         [ShrinkageController::class, 'destroy'])->name('destroy');
        });

        // Conteos físicos
        Route::prefix('physical-counts')->name('physical-counts.')->group(function () {
            Route::get('/',                              [PhysicalCountController::class, 'index'])->name('index');
            Route::get('/create',                        [PhysicalCountController::class, 'create'])->name('create');
            Route::post('/',                             [PhysicalCountController::class, 'store'])->name('store');
            Route::get('/{physicalCount}',                [PhysicalCountController::class, 'show'])->name('show');
            Route::patch('/{physicalCount}/capture',      [PhysicalCountController::class, 'capture'])->name('capture');
            Route::patch('/{physicalCount}/confirm',      [PhysicalCountController::class, 'confirm'])->name('confirm');
            Route::patch('/{physicalCount}/cancel',       [PhysicalCountController::class, 'cancel'])->name('cancel');
            Route::delete('/{physicalCount}',             [PhysicalCountController::class, 'destroy'])->name('destroy');
        });
    });
});

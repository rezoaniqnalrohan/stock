<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\MenuItemController;
use App\Http\Controllers\MovementController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WasteController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'show'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.attempt');
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('ingredients', IngredientController::class)->except('show');
    Route::resource('menu-items', MenuItemController::class)->except('show');

    Route::get('purchases', [PurchaseController::class, 'index'])->name('purchases.index');
    Route::post('purchases', [PurchaseController::class, 'store'])->name('purchases.store');

    Route::get('sales', [SaleController::class, 'index'])->name('sales.index');
    Route::get('sales/create', [SaleController::class, 'create'])->name('sales.create');
    Route::post('sales', [SaleController::class, 'store'])->name('sales.store');

    Route::get('waste', [WasteController::class, 'index'])->name('waste.index');
    Route::post('waste', [WasteController::class, 'store'])->name('waste.store');

    Route::get('movements', [MovementController::class, 'index'])->name('movements.index');
    Route::post('adjustments', [MovementController::class, 'adjust'])->name('adjustments.store');

    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/export/{type}', [ReportController::class, 'export'])->name('reports.export');

    Route::middleware('can:admin')->group(function () {
        Route::resource('suppliers', SupplierController::class)->only('index', 'store', 'update', 'destroy');
        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::post('users', [UserController::class, 'store'])->name('users.store');
        Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });
});

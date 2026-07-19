<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SalesOrderController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;

// Auth
Route::get('/login', [AuthController::class, 'show'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('products', ProductController::class);
    Route::resource('suppliers', SupplierController::class);
    Route::resource('customers', CustomerController::class);

    // Inventory
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::post('/inventory/adjust', [InventoryController::class, 'adjust'])->name('inventory.adjust');
    Route::post('/inventory/transfer', [InventoryController::class, 'transfer'])->name('inventory.transfer');

    // Purchase Orders
    Route::resource('purchase-orders', PurchaseOrderController::class)->except(['edit', 'update']);
    Route::post('/purchase-orders/{purchase_order}/receive', [PurchaseOrderController::class, 'receive'])->name('purchase-orders.receive');

    // Sales Orders
    Route::resource('sales-orders', SalesOrderController::class)->except(['edit', 'update']);
    Route::post('/sales-orders/{sales_order}/advance', [SalesOrderController::class, 'advance'])->name('sales-orders.advance');

    // Reports
    Route::get('/reports/valuation', [ReportController::class, 'valuation'])->name('reports.valuation');
    Route::get('/reports/movement', [ReportController::class, 'movement'])->name('reports.movement');
    Route::get('/reports/expiring', [ReportController::class, 'expiring'])->name('reports.expiring');
    Route::get('/reports/sales', [ReportController::class, 'sales'])->name('reports.sales');

    // Settings (warehouses, categories, units, users)
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings/{type}', [SettingsController::class, 'store'])->name('settings.store');
    Route::delete('/settings/{type}/{id}', [SettingsController::class, 'destroy'])->name('settings.destroy');
});

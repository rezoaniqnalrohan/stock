<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SalesOrderController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect('/dashboard'));

// Auth
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Catalog
    Route::resource('products', ProductController::class);

    // Inventory
    Route::get('/inventory', [InventoryController::class, 'index']);
    Route::get('/inventory/expiring', [InventoryController::class, 'expiring']);
    Route::get('/inventory/adjust', [InventoryController::class, 'adjustForm']);
    Route::post('/inventory/adjust', [InventoryController::class, 'adjustStore']);
    Route::get('/inventory/transfer', [InventoryController::class, 'transferForm']);
    Route::post('/inventory/transfer', [InventoryController::class, 'transferStore']);

    // Partners
    Route::resource('suppliers', SupplierController::class)->except('show');
    Route::resource('customers', CustomerController::class)->except('show');

    // Procurement
    Route::get('/purchase-orders', [PurchaseOrderController::class, 'index']);
    Route::get('/purchase-orders/create', [PurchaseOrderController::class, 'create']);
    Route::post('/purchase-orders', [PurchaseOrderController::class, 'store']);
    Route::get('/purchase-orders/{purchaseOrder}', [PurchaseOrderController::class, 'show']);
    Route::post('/purchase-orders/{purchaseOrder}/receive', [PurchaseOrderController::class, 'receive']);

    // Sales orders
    Route::get('/orders', [SalesOrderController::class, 'index']);
    Route::get('/orders/create', [SalesOrderController::class, 'create']);
    Route::post('/orders', [SalesOrderController::class, 'store']);
    Route::get('/orders/{order}', [SalesOrderController::class, 'show']);
    Route::post('/orders/{order}/fulfill', [SalesOrderController::class, 'fulfill']);

    // Logistics
    Route::get('/shipments', [ShipmentController::class, 'index']);
    Route::get('/shipments/create', [ShipmentController::class, 'create']);
    Route::post('/shipments', [ShipmentController::class, 'store']);
    Route::post('/shipments/{shipment}/status', [ShipmentController::class, 'updateStatus']);

    // Reports
    Route::get('/reports/valuation', [ReportController::class, 'valuation']);
    Route::get('/reports/movement', [ReportController::class, 'movement']);
    Route::get('/reports/expiring', [ReportController::class, 'expiring']);
    Route::get('/reports/wastage', [ReportController::class, 'wastage']);

    // Settings (admin only)
    Route::middleware('admin')->group(function () {
        Route::get('/settings', [SettingsController::class, 'index']);
        Route::post('/settings/warehouses', [SettingsController::class, 'storeWarehouse']);
        Route::post('/settings/categories', [SettingsController::class, 'storeCategory']);
        Route::post('/settings/units', [SettingsController::class, 'storeUnit']);
        Route::post('/settings/users', [SettingsController::class, 'storeUser']);
        Route::delete('/settings/{type}/{id}', [SettingsController::class, 'destroy']);
    });
});

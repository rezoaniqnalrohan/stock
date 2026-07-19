<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SetupController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TransferController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'show'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Sell / POS
    Route::get('/sell', [PosController::class, 'index'])->name('pos');
    Route::post('/sell', [PosController::class, 'store'])->name('pos.store');

    // Catalog
    Route::resource('products', ProductController::class)->except('show');

    // Inventory
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory');
    Route::post('/inventory/adjust', [InventoryController::class, 'adjust'])->name('inventory.adjust');

    // Transfers
    Route::get('/transfers', [TransferController::class, 'index'])->name('transfers.index');
    Route::post('/transfers', [TransferController::class, 'store'])->name('transfers.store');
    Route::post('/transfers/{transfer}/receive', [TransferController::class, 'receive'])->name('transfers.receive');

    // Suppliers
    Route::resource('suppliers', SupplierController::class)->only(['index', 'store', 'destroy']);

    // Purchase orders
    Route::get('/purchase-orders', [PurchaseOrderController::class, 'index'])->name('purchase-orders.index');
    Route::get('/purchase-orders/create', [PurchaseOrderController::class, 'create'])->name('purchase-orders.create');
    Route::post('/purchase-orders', [PurchaseOrderController::class, 'store'])->name('purchase-orders.store');
    Route::post('/purchase-orders/{purchaseOrder}/dispatch', [PurchaseOrderController::class, 'dispatchOrder'])->name('purchase-orders.dispatch');
    Route::post('/purchase-orders/{purchaseOrder}/receive', [PurchaseOrderController::class, 'receive'])->name('purchase-orders.receive');

    // Customers
    Route::resource('customers', CustomerController::class)->only(['index', 'store', 'destroy']);

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports');

    // Setup (admin only)
    Route::middleware('admin')->group(function () {
        Route::get('/setup', [SetupController::class, 'index'])->name('setup.index');
        Route::post('/setup/outlets', [SetupController::class, 'storeOutlet'])->name('setup.outlets.store');
        Route::post('/setup/categories', [SetupController::class, 'storeCategory'])->name('setup.categories.store');
        Route::post('/setup/brands', [SetupController::class, 'storeBrand'])->name('setup.brands.store');
        Route::post('/setup/users', [SetupController::class, 'storeUser'])->name('setup.users.store');
    });
});

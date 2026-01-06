<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ERP\CustomerController;
use App\Http\Controllers\ERP\SupplierController;
use App\Http\Controllers\ERP\WarehouseController;
use App\Http\Controllers\ERP\ProductController;
use App\Http\Controllers\ERP\SalesOrderController;
use App\Http\Controllers\ERP\InvoiceController;
use App\Http\Controllers\ERP\StockMovementController;
use App\Http\Controllers\ERP\SettingController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Original modules (Legacy support)
    Route::resource('orders', OrderController::class);
    Route::resource('inventory', InventoryController::class);

    // ERP Master Data
    Route::prefix('master')->name('master.')->group(function () {
        Route::resource('products', ProductController::class);
        Route::resource('customers', CustomerController::class);
        Route::resource('suppliers', SupplierController::class);
        Route::resource('warehouses', WarehouseController::class);
    });

    // ERP Sales
    Route::prefix('sales')->name('sales.')->group(function () {
        Route::resource('orders', SalesOrderController::class);
        Route::resource('invoices', InvoiceController::class);
        Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'downloadPdf'])->name('invoices.pdf');
    });

    // ERP Stock
    Route::prefix('stock')->name('stock.')->group(function () {
        Route::resource('movements', StockMovementController::class);
    });

    // Settings
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

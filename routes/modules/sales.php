<?php

use App\Http\Controllers\ERP\CustomerController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'tenant:sales_crm'])->group(function () {
    Route::resource('customers', CustomerController::class)->names('crm.sales.customers');
    Route::patch('customers/{customer}/status', [CustomerController::class, 'updateStatus'])->name('crm.sales.customers.update-status');
    
    Route::resource('orders', OrderController::class)->names('crm.sales.orders');
});


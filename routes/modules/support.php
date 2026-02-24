<?php

use App\Http\Controllers\CRM\CrmSupportController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'tenant:customer_service'])->group(function () {
    Route::get('/dashboard', [CrmSupportController::class, 'dashboard'])->name('crm.support.dashboard');
    Route::get('/tickets', [CrmSupportController::class, 'tickets'])->name('crm.support.tickets.index');
    Route::get('/chat-history', [CrmSupportController::class, 'chatHistory'])->name('crm.support.chat-history.index');
    Route::get('/knowledge-base', [CrmSupportController::class, 'knowledgeBase'])->name('crm.support.knowledge-base.index');
});


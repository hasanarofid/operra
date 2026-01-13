<?php

use App\Http\Controllers\SupportController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'tenant:customer_service'])->group(function () {
    Route::get('/dashboard', [SupportController::class, 'dashboard'])->name('crm.support.dashboard');
    Route::get('/tickets', [SupportController::class, 'tickets'])->name('crm.support.tickets.index');
    Route::get('/chat-history', [SupportController::class, 'chatHistory'])->name('crm.support.chat-history.index');
    Route::get('/knowledge-base', [SupportController::class, 'knowledgeBase'])->name('crm.support.knowledge-base.index');
});


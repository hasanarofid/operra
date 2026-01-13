<?php

use App\Http\Controllers\WhatsAppConfigController;
use App\Http\Controllers\CRMChatController;
use App\Http\Controllers\ERP\CustomerController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'tenant:wa_blast'])->group(function () {
    // Shared Inbox
    Route::get('/inbox', [CRMChatController::class, 'index'])->name('crm.wa.inbox');
    Route::get('/inbox/{chatSession}', [CRMChatController::class, 'show'])->name('crm.wa.chat.show');
    Route::post('/inbox/{chatSession}/mark-as-read', [CRMChatController::class, 'markAsRead'])->name('crm.wa.chat.mark-as-read');
    Route::post('/inbox/{chatSession}/send', [CRMChatController::class, 'sendMessage'])->name('crm.wa.chat.send');

    // Leads (Consistent with crm-wa-blast slug)
    Route::get('/leads', [CustomerController::class, 'index'])->name('crm.wa.leads.index');
    Route::get('/leads/create', [CustomerController::class, 'create'])->name('crm.wa.leads.create');
    Route::post('/leads', [CustomerController::class, 'store'])->name('crm.wa.leads.store');
    Route::patch('/leads/{customer}/status', [CustomerController::class, 'updateStatus'])->name('crm.wa.leads.update-status');

    // WhatsApp Settings (Admin only within the module)
    Route::middleware(['role:super-admin'])->group(function () {
        Route::get('/settings', [WhatsAppConfigController::class, 'index'])->name('crm.wa.settings.index');
        Route::post('/settings', [WhatsAppConfigController::class, 'update'])->name('crm.wa.settings.update');
        Route::post('/accounts', [WhatsAppConfigController::class, 'storeAccount'])->name('crm.wa.accounts.store');
        Route::put('/accounts/{whatsappAccount}', [WhatsAppConfigController::class, 'updateAccount'])->name('crm.wa.accounts.update');
        Route::delete('/accounts/{whatsappAccount}', [WhatsAppConfigController::class, 'destroyAccount'])->name('crm.wa.accounts.destroy');
        Route::post('/accounts/{whatsappAccount}/sync', [WhatsAppConfigController::class, 'syncAccount'])->name('crm.wa.accounts.sync');
    });
});


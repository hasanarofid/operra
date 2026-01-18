<?php

use App\Http\Controllers\WhatsAppConfigController;
use App\Http\Controllers\CRMChatController;
use App\Http\Controllers\WhatsAppBlastController;
use App\Http\Controllers\WhatsAppMediaController;
use App\Http\Controllers\ExternalAppController;
use App\Http\Controllers\CustomerStatusController;
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

    // WhatsApp Marketing (Blast)
    Route::get('/blast', [WhatsAppBlastController::class, 'index'])->name('crm.wa.blast.index');
    Route::post('/blast', [WhatsAppBlastController::class, 'store'])->name('crm.wa.blast.store');
    Route::post('/blast/{campaign}/process', [WhatsAppBlastController::class, 'process'])->name('crm.wa.blast.process');
    Route::delete('/blast/{campaign}', [WhatsAppBlastController::class, 'destroy'])->name('crm.wa.blast.destroy');

    // WhatsApp Settings & Management (Admin only within the module)
    Route::middleware(['role:super-admin'])->group(function () {
        Route::get('/settings', [WhatsAppConfigController::class, 'index'])->name('crm.wa.settings.index');
        Route::post('/settings', [WhatsAppConfigController::class, 'update'])->name('crm.wa.settings.update');
        Route::post('/accounts', [WhatsAppConfigController::class, 'storeAccount'])->name('crm.wa.accounts.store');
        Route::put('/accounts/{whatsappAccount}', [WhatsAppConfigController::class, 'updateAccount'])->name('crm.wa.accounts.update');
        Route::delete('/accounts/{whatsappAccount}', [WhatsAppConfigController::class, 'destroyAccount'])->name('crm.wa.accounts.destroy');
        Route::post('/accounts/{whatsappAccount}/sync', [WhatsAppConfigController::class, 'syncAccount'])->name('crm.wa.accounts.sync');
        
        // Templates
        Route::post('/accounts/sync-meta', [WhatsAppConfigController::class, 'syncFromMeta'])->name('crm.wa.accounts.sync-meta');
        Route::post('/accounts/{whatsappAccount}/sync-templates', [WhatsAppConfigController::class, 'syncTemplates'])->name('crm.wa.accounts.sync-templates');

        // External Apps (Embedding)
        Route::get('/external-apps', [ExternalAppController::class, 'index'])->name('crm.wa.external-apps.index');
        Route::post('/external-apps', [ExternalAppController::class, 'store'])->name('crm.wa.external-apps.store');
        Route::put('/external-apps/{externalApp}', [ExternalAppController::class, 'update'])->name('crm.wa.external-apps.update');
        Route::delete('/external-apps/{externalApp}', [ExternalAppController::class, 'destroy'])->name('crm.wa.external-apps.destroy');
        Route::get('/external-apps/preview', [ExternalAppController::class, 'preview'])->name('crm.wa.external-apps.preview');

        // Media Management
        Route::get('/media', [WhatsAppMediaController::class, 'index'])->name('crm.wa.media.index');
        Route::delete('/media/{chatMessage}', [WhatsAppMediaController::class, 'destroy'])->name('crm.wa.media.destroy');

        // Customer Statuses
        Route::get('/customer-statuses', [CustomerStatusController::class, 'index'])->name('crm.wa.customer-statuses.index');
        Route::post('/customer-statuses', [CustomerStatusController::class, 'store'])->name('crm.wa.customer-statuses.store');
        Route::put('/customer-statuses/{customerStatus}', [CustomerStatusController::class, 'update'])->name('crm.wa.customer-statuses.update');
        Route::delete('/customer-statuses/{customerStatus}', [CustomerStatusController::class, 'destroy'])->name('crm.wa.customer-statuses.destroy');
    });
});

// Embedded Inbox (No tenant middleware here as it might be accessed from outside via App Key, 
// but it's protected by other means in the controller/middleware)
Route::get('/embed/inbox', [ExternalAppController::class, 'embeddedInbox'])
    ->name('external.inbox');

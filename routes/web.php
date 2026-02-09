<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ERP\SettingController;
use App\Http\Controllers\StaffManagementController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\LandingPageController;

Route::get('/', [LandingPageController::class, 'index'])->name('welcome');
Route::get('/contact', [LandingPageController::class, 'showContact'])->name('contact');
Route::post('/request-custom-crm', [LandingPageController::class, 'submitRequest'])->name('request.custom');
Route::get('/privacy-policy', [LandingPageController::class, 'privacyPolicy'])->name('privacy.policy');
Route::get('/terms-of-service', [LandingPageController::class, 'termsOfService'])->name('terms.service');

Route::get('/link-storage', function () {
    try {
        if (file_exists(public_path('storage'))) {
            @unlink(public_path('storage'));
        }
        Artisan::call('storage:link', ['--relative' => true]);
        return "Storage Link Recreated (Relative)";
    } catch (\Throwable $e) {
        $cmd = "ln -s ../storage/app/public public/storage";
        return "Gagal membuat link secara otomatis karena batasan server (exec/symlink disabled). <br><br> " .
               "Silahkan jalankan perintah ini di terminal server: <br><br> " .
               "<code style='background: #f4f4f4; padding: 10px; display: block;'>" . $cmd . "</code>";
    }
});

Route::get('/clear-system', function () {
    Artisan::call('optimize:clear');
    Artisan::call('view:clear');
    Artisan::call('cache:clear');
    return "System Optimized, View & App Cache Cleared!";
});


use App\Http\Controllers\Admin\LeadsRequestController;
use App\Http\Controllers\Admin\BotAntamAccountController;

Route::middleware(['auth', 'verified', 'role:super-admin', 'system_owner'])->group(function () {
    Route::get('/admin/leads-requests', [LeadsRequestController::class, 'index'])->name('admin.leads.index');
    Route::patch('/admin/leads-requests/{leadsRequest}/status', [LeadsRequestController::class, 'updateStatus'])->name('admin.leads.update-status');
    Route::delete('/admin/leads-requests/{leadsRequest}', [LeadsRequestController::class, 'destroy'])->name('admin.leads.destroy');

    // System Monitoring Routes (Super Admin Operra)
    Route::get('/admin/monitoring/companies', [\App\Http\Controllers\Admin\SystemAdminController::class, 'index'])->name('admin.system.companies.index');
    Route::patch('/admin/monitoring/companies/{company}', [\App\Http\Controllers\Admin\SystemAdminController::class, 'updateSubscription'])->name('admin.system.companies.update');

    // Bot Antam Accounts Management (Super Admin)
    Route::get('admin/bot-antam-accounts/{id}/logs', [BotAntamAccountController::class, 'logs'])->name('admin.bot-antam-accounts.logs');
    Route::resource('admin/bot-antam-accounts', BotAntamAccountController::class)->except(['show', 'create', 'edit']);

    // Admin Ticket System
    Route::get('/admin/tickets', [\App\Http\Controllers\Admin\TicketController::class, 'index'])->name('admin.tickets.index');
    Route::get('/admin/tickets/{id}', [\App\Http\Controllers\Admin\TicketController::class, 'show'])->name('admin.tickets.show');
    Route::post('/admin/tickets/{id}/reply', [\App\Http\Controllers\Admin\TicketController::class, 'reply'])->name('admin.tickets.reply');
});
Route::middleware(['auth', 'verified', 'tenant'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/onboarding/complete', function () {
        auth()->user()->update(['has_completed_onboarding' => true]);
        return back();
    })->name('onboarding.complete');

    // Legacy support or core features
    Route::resource('inventory', InventoryController::class);

    // CRM Modules
    Route::prefix('crm-wa-blast')->group(base_path('routes/modules/wa_blast.php'));
    Route::prefix('crm-sales')->group(function () {
        // Pipeline / Kanban
        Route::get('/pipeline', [\App\Http\Controllers\ERP\PipelineController::class, 'index'])->name('crm.sales.pipeline.index');
        Route::post('/pipeline/update-stage/{order}', [\App\Http\Controllers\ERP\PipelineController::class, 'updateOrderStage'])->name('crm.sales.pipeline.update');
        
        // Load legacy sales routes (if any) or keep them separate
        require base_path('routes/modules/sales.php'); 
    });
    Route::prefix('crm-marketing')->group(base_path('routes/modules/marketing.php'));
    Route::prefix('crm-support')->group(base_path('routes/modules/support.php'));
    
    // BOT ANTAM Portal
    Route::prefix('bot-antam')->group(base_path('routes/modules/bot_antam.php'));

    // Core Settings
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');

    // Billing & Payment
    Route::get('/billing', [\App\Http\Controllers\PaymentController::class, 'index'])->name('billing.index');
    Route::post('/billing', [\App\Http\Controllers\PaymentController::class, 'store'])->name('billing.store');

    // Admin Features
    Route::middleware(['role:super-admin'])->group(function () {
        // Staff Management
        Route::get('/staff-management', [StaffManagementController::class, 'index'])->name('staff.index');
        Route::post('/staff-management', [StaffManagementController::class, 'store'])->name('staff.store');
        Route::put('/staff-management/{user}', [StaffManagementController::class, 'update'])->name('staff.update');
        Route::delete('/staff-management/{user}', [StaffManagementController::class, 'destroy'])->name('staff.destroy');

        // System Owner Only Routes
        Route::middleware(['system_owner'])->group(function () {
            // Admin Payment Verification
            Route::get('/admin/payments', [\App\Http\Controllers\Admin\PaymentController::class, 'index'])->name('admin.payments.index');
            Route::patch('/admin/payments/{payment}', [\App\Http\Controllers\Admin\PaymentController::class, 'verify'])->name('admin.payments.verify');
        });
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';

Route::get('/debug-tenant', function () {
    $user = auth()->user();
    $company = $user->company;
    $hasModule = $company->isModuleEnabled('sales_crm');
    return [
        'user' => $user->id,
        'company' => $company->id,
        'has_sales_crm' => $hasModule,
        'modules' => $company->enabled_modules,
        'raw_modules' => $company->getAttributes()['enabled_modules']
    ];
})->middleware('auth');




<?php

use App\Http\Controllers\MarketingController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'tenant:marketing_crm'])->group(function () {
    Route::get('/dashboard', [MarketingController::class, 'dashboard'])->name('crm.marketing.dashboard');
    Route::get('/campaigns', [MarketingController::class, 'campaigns'])->name('crm.marketing.campaigns.index');
    Route::get('/blasts', [MarketingController::class, 'blasts'])->name('crm.marketing.blasts.index');
    Route::get('/automations', [MarketingController::class, 'automations'])->name('crm.marketing.automations.index');
    Route::get('/lead-scoring', [MarketingController::class, 'leadScoring'])->name('crm.marketing.lead-scoring.index');
});


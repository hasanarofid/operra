<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BotAntam\DashboardController;
use App\Http\Controllers\BotAntam\ConfigController;
use App\Http\Controllers\BotAntam\LogController;

// Dedicated Portal Routes
Route::middleware(['auth', 'verified', 'tenant:bot_antam'])->prefix('portal')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('bot_antam.dashboard');
    Route::post('/config', [ConfigController::class, 'update'])->name('bot_antam.config.update');
    Route::post('/test-run', [ConfigController::class, 'testRun'])->name('bot_antam.test.run');
    
    // Logs (Polled via API)
    Route::get('/logs', [LogController::class, 'index'])->name('bot_antam.logs.index');
    Route::post('/logs/clear', [LogController::class, 'clear'])->name('bot_antam.logs.clear');

    // Support Tickets
    Route::get('/support', [\App\Http\Controllers\BotAntam\SupportController::class, 'index'])->name('bot_antam.support.index');
    Route::post('/support', [\App\Http\Controllers\BotAntam\SupportController::class, 'store'])->name('bot_antam.support.store');
    Route::get('/support/{id}', [\App\Http\Controllers\BotAntam\SupportController::class, 'show'])->name('bot_antam.support.show');
    Route::post('/support/{id}/reply', [\App\Http\Controllers\BotAntam\SupportController::class, 'reply'])->name('bot_antam.support.reply');
});

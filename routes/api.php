<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WhatsAppWebhookController;

Route::post('/webhook/whatsapp', [WhatsAppWebhookController::class, 'handle']);

// External App Widget Config
Route::get('/external-app/config', [\App\Http\Controllers\ExternalAppController::class, 'getWidgetConfig']);


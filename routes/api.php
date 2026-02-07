<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WhatsAppWebhookController;

Route::match(['get', 'post'], '/webhook/whatsapp', [WhatsAppWebhookController::class, 'handle']);

// External App Widget Config
Route::get('/external-app/config', [\App\Http\Controllers\ExternalAppController::class, 'getWidgetConfig']);


Route::get('/debug-wa', function() {
    return [
        'config_token_prefix' => substr(config('services.whatsapp.meta_token'), 0, 10),
        'env_token_prefix' => substr(env('WA_TOKEN'), 0, 10),
        'env_id' => env('WA_ID'),
        'account_6_details' => \App\Models\WhatsappAccount::find(6),
        'wa_id_config' => config('services.whatsapp.meta_phone_number_id')
    ];
});
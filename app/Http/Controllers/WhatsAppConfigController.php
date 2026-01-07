<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WhatsAppConfigController extends Controller
{
    public function index(WhatsAppService $waService)
    {
        $settings = Setting::whereIn('key', [
            'wa_blast_number',
            'wa_blast_endpoint',
            'wa_blast_token',
            'wa_blast_key'
        ])->pluck('value', 'key');

        $waStatus = null;
        if (isset($settings['wa_blast_token']) && isset($settings['wa_blast_key'])) {
            $waStatus = $waService->checkStatus();
        }

        return Inertia::render('Settings/WhatsApp', [
            'settings' => $settings,
            'waStatus' => $waStatus
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'wa_blast_number' => 'nullable|string',
            'wa_blast_endpoint' => 'nullable|url',
            'wa_blast_token' => 'nullable|string',
            'wa_blast_key' => 'nullable|string',
        ]);

        foreach ($validated as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return redirect()->back()->with('message', 'WhatsApp settings updated successfully.');
    }
}

<?php

namespace App\Http\Controllers\BotAntam;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConfigController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'lm_username' => 'required|string',
            'lm_password' => 'required|string',
            'target_products' => 'nullable|array',
            'telegram_chat_id' => 'nullable|string'
        ]);

        $companyId = auth()->user()->company_id;

        DB::table('bot_antam_accounts')->updateOrInsert(
            ['company_id' => $companyId],
            [
                'lm_username' => $request->lm_username,
                'lm_password' => encrypt($request->lm_password),
                'target_products' => json_encode($request->target_products),
                'telegram_chat_id' => $request->telegram_chat_id,
                'updated_at' => now(),
                'created_at' => now() // Only if inserting
            ]
        );

        return back()->with('success', 'Konfigurasi berhasil disimpan.');
    }

    public function testRun(Request $request)
    {
        // Logic to trigger a single check (e.g. dispatch job or simple check)
        // For now, just log it
        $companyId = auth()->user()->company_id;
        $accountId = DB::table('bot_antam_accounts')->where('company_id', $companyId)->value('id');

        if ($accountId) {
             DB::table('bot_antam_logs')->insert([
                'bot_antam_account_id' => $accountId,
                'event_type' => 'TEST_RUN',
                'message' => 'Manual test run triggered by user.',
                'created_at' => now(),
                'updated_at' => now()
             ]);
        }

        return back()->with('success', 'Test run triggered.');
    }
}

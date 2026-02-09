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
            'lm_password' => 'nullable|string',
            'target_products' => 'nullable|array',
            'telegram_chat_id' => 'nullable|string'
        ]);

        $companyId = auth()->user()->company_id;

        $data = [
            'lm_username' => $request->lm_username,
            'target_products' => json_encode($request->target_products),
            'telegram_chat_id' => $request->telegram_chat_id,
            'updated_at' => now(),
        ];

        if ($request->filled('lm_password')) {
            $data['lm_password'] = encrypt($request->lm_password);
        }

        // Check if record exists to determine created_at
        $exists = DB::table('bot_antam_accounts')->where('company_id', $companyId)->exists();
        if (!$exists) {
            $data['company_id'] = $companyId;
            $data['created_at'] = now();
            DB::table('bot_antam_accounts')->insert($data);
        } else {
            DB::table('bot_antam_accounts')->where('company_id', $companyId)->update($data);
        }

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

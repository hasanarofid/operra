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
            'telegram_chat_id' => 'nullable|string',
            'target_butik' => 'nullable|string',
            'captcha_api_key' => 'nullable|string',
        ]);

        $companyId = auth()->user()->company_id;

        $data = [
            'lm_username' => $request->lm_username,
            'target_products' => json_encode($request->target_products),
            'telegram_chat_id' => $request->telegram_chat_id,
            'target_butik' => $request->target_butik,
            'captcha_api_key' => $request->captcha_api_key,
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
        $companyId = auth()->user()->company_id;
        $account = DB::table('bot_antam_accounts')->where('company_id', $companyId)->first();

        if (!$account) {
             return back()->with('error', 'Konfigurasi belum lengkap.');
        }

        try {
            // Set flag for background process (bypasses proc_open restriction)
            DB::table('bot_antam_accounts')->where('id', $account->id)->update([
                'test_requested_at' => now(),
                'updated_at' => now()
            ]);

            $this->logEvent($account->id, 'INFO', "🔔 Test request diterima. Real Engine akan berjalan otomatis dalam < 1 menit.");

            return back()->with('success', 'Percobaan Real Engine telah dijadwalkan. Silahkan pantau log dalam 1 menit ke depan.');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menjadwalkan test: ' . $e->getMessage());
        }
    }

    private function logEvent($accountId, $type, $message)
    {
        DB::table('bot_antam_logs')->insert([
            'bot_antam_account_id' => $accountId,
            'event_type' => $type,
            'message' => $message,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    private function sendTelegram($chatId, $message)
    {
        $token = env('TELEGRAM_BOT_TOKEN');
        if (!$token) return;

        try {
            \Illuminate\Support\Facades\Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'HTML'
            ]);
        } catch (\Exception $e) {
            // Silently fail or log to error log
        }
    }
}

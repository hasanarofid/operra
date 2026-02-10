<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class BotAntamCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bot:antam:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Simulate Bot Antam stock check for active accounts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Fetch accounts that are ACTIVE or have a PENDING test request
        $accounts = DB::table('bot_antam_accounts')
            ->where(function($query) {
                $query->where('is_active', true)
                      ->orWhereNotNull('test_requested_at');
            })
            ->get();

        if ($accounts->isEmpty()) {
            return;
        }

        foreach ($accounts as $account) {
            $isTest = !empty($account->test_requested_at);
            
            // Immediately clear test flag so it doesn't run twice
            if ($isTest) {
                DB::table('bot_antam_accounts')->where('id', $account->id)->update(['test_requested_at' => null]);
                $this->info("Handled test request for ID: {$account->id}");
            }

            $this->info("Starting Real Browser Engine for Account ID: {$account->id} ({$account->lm_username}) " . ($isTest ? "[MANUAL TEST]" : ""));

            try {
                // Decrypt password
                $password = decrypt($account->lm_password);
                $butik = $account->target_butik ?: 'Gedung Antam (Jakarta)';
                
                // Construct command
                $process = new \Symfony\Component\Process\Process([
                    'node', 
                    base_path('bot_engine.js'), 
                    $account->lm_username, 
                    $password, 
                    $butik, 
                    $account->captcha_api_key ?: '',
                    $account->id
                ]);
                
                $process->setTimeout(120); // 2 minutes max
                
                // Run process and capture output line by line for real-time logs
                $process->run(function ($type, $buffer) use ($account) {
                    $lines = explode("\n", trim($buffer));
                    foreach ($lines as $line) {
                        if (empty($line)) continue;
                        
                            // Parse [TYPE] Message
                        if (preg_match('/^\[(INFO|SUCCESS|ERROR|CHECK|FOUND)\] (.*)$/', $line, $matches)) {
                            $type = $matches[1];
                            $message = $matches[2];
                            
                            $this->logEvent($account->id, $type, $message);
                            
                            // Send Telegram on success
                            if ($type === 'SUCCESS') {
                                // Check if message contains image path
                                if (preg_match('/IMAGE:(.*)$/', $message, $imgMatches)) {
                                    $imagePath = trim($imgMatches[1]);
                                    $cleanMessage = str_replace("IMAGE:$imagePath", "", $message);
                                    
                                    if ($account->telegram_chat_id) {
                                        $this->sendTelegramPhoto($account->telegram_chat_id, $cleanMessage, $imagePath);
                                    }
                                } elseif (str_contains($message, 'Tiket')) {
                                    if ($account->telegram_chat_id) {
                                        $this->sendTelegram($account->telegram_chat_id, "🚀 <b>BOT NOTIFICATION</b>\n\n🎯 <b>{$message}</b>\n\nLokasi: <b>{$account->target_butik}</b>\nUsername: <b>{$account->lm_username}</b>");
                                    }
                                }
                            }
                        } else {
                            $this->info($line);
                        }
                    }
                });

            } catch (\Exception $e) {
                $this->logEvent($account->id, 'ERROR', "🚨 PHP Engine Error: " . $e->getMessage());
            }
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
        
        $this->info("[$type] $message");
    }

    private function sendTelegram($chatId, $message)
    {
        $token = env('TELEGRAM_BOT_TOKEN');
        
        if (!$token) {
            $this->error("TELEGRAM_BOT_TOKEN is not set in .env");
            return;
        }

        try {
            $response = Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'HTML'
            ]);

            if ($response->successful()) {
                $this->info("Telegram sent to $chatId");
            } else {
                $this->error("Failed to send Telegram: " . $response->body());
            }
        } catch (\Exception $e) {
            $this->error("Exception sending Telegram: " . $e->getMessage());
        }
    }

    private function sendTelegramPhoto($chatId, $caption, $imagePath)
    {
        $token = env('TELEGRAM_BOT_TOKEN');
        
        if (!$token || !file_exists($imagePath)) {
            $this->error("TELEGRAM_BOT_TOKEN missing or Image not found: $imagePath");
            return;
        }

        try {
            $response = Http::attach(
                'photo', file_get_contents($imagePath), 'ticket.png'
            )->post("https://api.telegram.org/bot{$token}/sendPhoto", [
                'chat_id' => $chatId,
                'caption' => $caption
            ]);

            if ($response->successful()) {
                $this->info("Telegram Photo sent to $chatId");
            } else {
                $this->error("Failed to send Telegram Photo: " . $response->body());
            }
        } catch (\Exception $e) {
            $this->error("Exception sending Telegram Photo: " . $e->getMessage());
        }
    }
}

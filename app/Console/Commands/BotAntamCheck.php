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
        $accounts = DB::table('bot_antam_accounts')->where('is_active', true)->get();

        if ($accounts->isEmpty()) {
            $this->info('No active accounts found.');
            return;
        }

        foreach ($accounts as $account) {
            $this->info("Checking for Account ID: {$account->id}");

            // 1. Log START
            DB::table('bot_antam_logs')->insert([
                'bot_antam_account_id' => $account->id,
                'event_type' => 'CHECK',
                'message' => 'Checking Logam Mulia stock...',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // 2. SIMULATION LOGIC
            // In a real scenario, this would curl to logammulia.com
            // For now, we simulate a 10% chance of finding stock
            $found = rand(1, 100) <= 10; 

            if ($found) {
                // Log FOUND
                DB::table('bot_antam_logs')->insert([
                    'bot_antam_account_id' => $account->id,
                    'event_type' => 'FOUND',
                    'message' => 'Stock FOUND! Simulating buy attempt...',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                // Simulate Buy
                sleep(1);
                $success = rand(1, 100) <= 50; // 50% success rate on buy

                if ($success) {
                     DB::table('bot_antam_logs')->insert([
                        'bot_antam_account_id' => $account->id,
                        'event_type' => 'SUCCESS',
                        'message' => 'Purchase Successful (Simulation)!',
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);

                    // Send Telegram (Mock)
                    if ($account->telegram_chat_id) {
                        $this->sendTelegram($account->telegram_chat_id, "🔥 STOCK FOUND AND BOUGHT! (Simulation)");
                    }

                } else {
                     DB::table('bot_antam_logs')->insert([
                        'bot_antam_account_id' => $account->id,
                        'event_type' => 'ERROR',
                        'message' => 'Purchase Failed: Session Timeout / Out of Stock',
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }

            } else {
                 // Optional: Don't log every "Not Found" to save DB space, or log sparingly
                 // $this->info("No stock found.");
            }
        }
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
}

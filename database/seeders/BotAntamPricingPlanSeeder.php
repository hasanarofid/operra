<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BotAntamPricingPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\PricingPlan::updateOrCreate(
            ['slug' => 'bot-antam'],
            [
                'name' => 'BOT ANTAM (War Mode)',
                'price' => 500000,
                'billing_cycle' => 'monthly',
                'features' => [
                    'bot_antam',
                    'telegram_notifications',
                    'auto_buy_simulation',
                    'priority_support'
                ],
                'is_popular' => false,
                'badge' => 'NEW',
                'cta_text' => 'Start War Now'
            ]
        );
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PricingPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        DB::table('pricing_plans')->truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();
        DB::table('pricing_plans')->insert([
            [
                'name' => 'Starter (UMKM)',
                'slug' => 'starter',
                'price' => 149000,
                'billing_cycle' => 'monthly',
                'features' => json_encode([
                    'Email Fallback (100/mo)',
                    'Notifikasi Stok Rendah',
                    'Shared Inbox (2 Agent)',
                    'Laporan Harian via Email'
                ]),
                'is_popular' => false,
                'badge' => 'Cocok untuk Pemula',
                'cta_text' => 'Pilih Starter',
                'created_at' => now(),
            ],
            [
                'name' => 'Business Pro',
                'slug' => 'business-pro',
                'price' => 399000,
                'billing_cycle' => 'monthly',
                'features' => json_encode([
                    'Portal Pelacakan Publik',
                    'Unlimited Email Fallback',
                    'Analitik Penjualan Dasar',
                    'Priority Support'
                ]),
                'is_popular' => true,
                'badge' => 'Paling Populer',
                'cta_text' => 'Mulai Uji Coba Gratis',
                'created_at' => now(),
            ],
            [
                'name' => 'Enterprise Custom',
                'slug' => 'enterprise',
                'price' => 0, // 0 means Custom Price
                'billing_cycle' => 'monthly',
                'features' => json_encode([
                    'Pengingat Bayar Otomatis',
                    'Custom Analytics Dashboard',
                    'On-Premise Deployment',
                    'Full White-Label Branding',
                    'Dedicated Server & Database',
                    'Account Manager Khusus'
                ]),
                'is_popular' => false,
                'badge' => 'Untuk Skala Besar',
                'cta_text' => 'Hubungi Kami',
                'created_at' => now(),
            ],
            [
                'name' => 'Bot Antam (War Mode)',
                'slug' => 'bot-antam',
                'price' => 500000,
                'billing_cycle' => 'monthly',
                'features' => json_encode([
                    'Bot Antam Full Access',
                    'Telegram Notifications',
                    'Auto-Buy Simulation',
                    'Priority Support',
                    'Historical Price Data'
                ]),
                'is_popular' => false,
                'badge' => 'Specialized Bot',
                'cta_text' => 'Start War Now',
                'created_at' => now(),
            ],
        ]);
    }
}

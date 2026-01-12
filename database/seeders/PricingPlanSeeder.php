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
        DB::table('pricing_plans')->insert([
            [
                'name' => 'Starter (UMKM)',
                'slug' => 'starter',
                'price' => 149000,
                'billing_cycle' => 'monthly',
                'features' => json_encode([
                    '1 Akun WhatsApp',
                    'Manajemen Lead Dasar',
                    'Shared Inbox (2 Agent)',
                    'Follow-up Otomatis',
                    'Laporan Harian via WA'
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
                    'Multi-Account WhatsApp (Up to 5)',
                    'Sales Pipeline & Deal Tracking',
                    'Unlimited Agents',
                    'WhatsApp Blast (Scheduler)',
                    'API Integration Ready',
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
                    'On-Premise Deployment',
                    'Full White-Label Branding',
                    'Custom Module Development',
                    'Dedicated Server & Database',
                    'SLA Guarantee 99.9%',
                    'Account Manager Khusus'
                ]),
                'is_popular' => false,
                'badge' => 'Untuk Skala Besar',
                'cta_text' => 'Hubungi Kami',
                'created_at' => now(),
            ],
        ]);
    }
}

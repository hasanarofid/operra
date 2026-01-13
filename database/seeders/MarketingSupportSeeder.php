<?php

namespace Database\Seeders;

use App\Models\MarketingCampaign;
use App\Models\MarketingBlast;
use App\Models\MarketingAutomation;
use App\Models\SupportTicket;
use App\Models\SupportKnowledgeBase;
use App\Models\Customer;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;

class MarketingSupportSeeder extends Seeder
{
    public function run(): void
    {
        $defaultCompany = Company::where('slug', 'operra-default')->first();
        if (!$defaultCompany) return;

        // 1. Update existing customers with lead scores
        $customers = Customer::all();
        foreach ($customers as $index => $customer) {
            $customer->update(['lead_score' => rand(20, 95)]);
        }

        // 2. Marketing Campaigns
        MarketingCampaign::create([
            'company_id' => $defaultCompany->id,
            'name' => 'Year End Sale 2025',
            'description' => 'Promo akhir tahun untuk produk IT',
            'type' => 'Email',
            'status' => 'active',
            'start_date' => now(),
            'end_date' => now()->addMonth(),
            'target_audience_count' => 1200
        ]);

        MarketingCampaign::create([
            'company_id' => $defaultCompany->id,
            'name' => 'WhatsApp Welcome Series',
            'description' => 'Otomasi sambutan pelanggan baru via WA',
            'type' => 'WhatsApp',
            'status' => 'active',
            'start_date' => now()->subDays(10),
            'target_audience_count' => 500
        ]);

        // 3. Marketing Blasts
        MarketingBlast::create([
            'company_id' => $defaultCompany->id,
            'subject' => 'Diskon 50% Menanti Anda!',
            'content' => 'Halo pelanggan setia, nikmati diskon khusus akhir tahun...',
            'channel' => 'email',
            'status' => 'sent',
            'sent_at' => now()->subDays(2),
            'total_recipients' => 1000,
            'success_count' => 980,
            'failed_count' => 20
        ]);

        // 4. Marketing Automations
        MarketingAutomation::create([
            'company_id' => $defaultCompany->id,
            'name' => 'New Lead Auto-Reply',
            'trigger_event' => 'customer_registered',
            'actions' => [
                ['type' => 'wait', 'duration' => '5m'],
                ['type' => 'send_whatsapp', 'template' => 'welcome_msg']
            ],
            'is_active' => true
        ]);

        // 5. Support Tickets
        $supportAgent = User::role('staff')->first();
        
        SupportTicket::create([
            'company_id' => $defaultCompany->id,
            'customer_id' => $customers->first()->id,
            'assigned_user_id' => $supportAgent ? $supportAgent->id : null,
            'ticket_number' => 'TKT-20260113-001',
            'subject' => 'Masalah Login Dashboard',
            'description' => 'Saya tidak bisa login meskipun password sudah benar.',
            'priority' => 'high',
            'status' => 'open',
            'sla_due_at' => now()->addHours(4)
        ]);

        SupportTicket::create([
            'company_id' => $defaultCompany->id,
            'customer_id' => $customers->last()->id,
            'ticket_number' => 'TKT-20260113-002',
            'subject' => 'Pertanyaan Refund Dana',
            'description' => 'Berapa lama proses refund dana jika transaksi dibatalkan?',
            'priority' => 'medium',
            'status' => 'in_progress',
            'sla_due_at' => now()->addDays(1)
        ]);

        // 6. Knowledge Base
        SupportKnowledgeBase::create([
            'company_id' => $defaultCompany->id,
            'title' => 'Cara Reset Password',
            'slug' => 'cara-reset-password',
            'content' => 'Langkah-langkah untuk melakukan reset password akun Anda...',
            'category' => 'Akun',
            'is_published' => true
        ]);

        SupportKnowledgeBase::create([
            'company_id' => $defaultCompany->id,
            'title' => 'Panduan Integrasi WhatsApp Official',
            'slug' => 'integrasi-wa-official',
            'content' => 'Dokumentasi lengkap mengenai cara menghubungkan nomor WhatsApp Official ke portal Operra...',
            'category' => 'Integrasi',
            'is_published' => true
        ]);
    }
}

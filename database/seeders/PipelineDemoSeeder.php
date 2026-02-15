<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\Customer;
use App\Models\SalesOrder;
use App\Models\PipelineStage;
use Carbon\Carbon;

class PipelineDemoSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Get the Sales Demo Company
        $company = Company::where('slug', 'sales-crm-demo')->first();
        
        if (!$company) {
            $this->command->error('Sales CRM Demo company not found. Please run PortalDemoSeeder first.');
            return;
        }

        // 2. Ensure default stages exist for this company
        $this->call(DefaultPipelineStagesSeeder::class);

        $stages = PipelineStage::where('company_id', $company->id)->orderBy('position')->get();

        if ($stages->isEmpty()) {
            $this->command->error('No pipeline stages found for this company.');
            return;
        }

        // 3. Create some demo customers
        $customers = [
            ['name' => 'PT Maju Bersama', 'email' => 'contact@majubersama.com', 'phone' => '081234567890'],
            ['name' => 'CV Sejahtera', 'email' => 'admin@sejahtera.co.id', 'phone' => '081987654321'],
            ['name' => 'Indra Wijaya', 'email' => 'indra.w@gmail.com', 'phone' => '087755443322'],
            ['name' => 'Sari Indah', 'email' => 'sari@indah.com', 'phone' => '082211223344'],
            ['name' => 'PT Cahaya Abadi', 'email' => 'info@cahayaabadi.com', 'phone' => '081122334455'],
            ['name' => 'Budi Santoso', 'email' => 'budi@santoso.me', 'phone' => '081344556677'],
            ['name' => 'Toko Berkah', 'email' => 'berkah@toko.id', 'phone' => '081566778899'],
            ['name' => 'PT Galaksi Baru', 'email' => 'ops@galaksi.com', 'phone' => '081900112233'],
        ];

        foreach ($customers as $c) {
            $customer = Customer::updateOrCreate(
                ['email' => $c['email'], 'company_id' => $company->id],
                array_merge($c, [
                    'company_id' => $company->id,
                    'status' => 'active',
                    'lead_score' => rand(20, 95)
                ])
            );

            // 4. Create Sales Orders for each stage
            $targetStage = $stages->random();
            
            SalesOrder::create([
                'company_id' => $company->id,
                'customer_id' => $customer->id,
                'stage_id' => $targetStage->id,
                'so_number' => 'SO-' . strtoupper(bin2hex(random_bytes(3))),
                'total_amount' => rand(1000000, 50000000),
                'status' => 'confirmed',
                'order_date' => Carbon::now()->subDays(rand(0, 30)),
                'expected_close_date' => Carbon::now()->addDays(rand(5, 45)),
                'probability' => $targetStage->probability,
            ]);
        }

        $this->command->info('Pipeline demo data seeded successfully!');
    }
}

<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Customer;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SalesCrmTest extends TestCase
{
    use RefreshDatabase;

    public function test_sales_crm_dashboard_stats_are_calculated_correctly()
    {
        $company = Company::create([
            'name' => 'Test Company',
            'slug' => 'test-company',
            'enabled_modules' => ['sales_crm'],
            'whatsapp_status' => 'connected',
        ]);
        $user = User::create([
            'name' => 'Test User',
            'email' => 'admin@operra.com',
            'password' => bcrypt('password'),
            'company_id' => $company->id,
            'email_verified_at' => now(),
        ]);
        
        // Create some customers
        for ($i = 0; $i < 10; $i++) {
            Customer::create([
                'company_id' => $company->id,
                'name' => "Customer $i",
                'phone' => "0812345678$i",
            ]);
        }
        
        // Create some orders
        for ($i = 0; $i < 5; $i++) {
            Order::create([
                'company_id' => $company->id,
                'order_number' => "ORD-$i",
                'customer_name' => 'John Doe',
                'status' => 'completed',
                'total_amount' => 100000,
                'created_at' => now(),
            ]);
        }

        $response = $this->actingAs($user)
            ->get(route('dashboard', ['portal' => 'sales_crm']));

        if ($response->status() === 302) {
            dump("Redirected to: " . $response->headers->get('Location'));
        }
        $response->assertStatus(200);
        
        $inertiaData = $response->getOriginalContent()->getData()['page']['props'];
        $stats = $inertiaData['stats'];

        $this->assertEquals(10, $stats['total_customers']);
        $this->assertEquals(5, $stats['total_orders']);
        $this->assertEquals(500000, $stats['revenue_this_month']);
        $this->assertCount(5, $stats['recent_orders']);
    }
}

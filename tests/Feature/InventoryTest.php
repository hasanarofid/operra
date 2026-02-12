<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_low_stock_calculation_on_dashboard()
    {
        $company = Company::create([
            'name' => 'Inventory Co',
            'slug' => 'inv-co',
            'enabled_modules' => ['sales_crm']
        ]);

        $user = User::create([
            'name' => 'Inv User',
            'email' => 'admin@operra.com',
            'password' => bcrypt('pass'),
            'company_id' => $company->id,
            'email_verified_at' => now(),
        ]);

        $warehouse = \App\Models\Warehouse::create([
            'company_id' => $company->id,
            'name' => 'Main Warehouse',
        ]);

        // Product 1: Low stock (Stock 5, Min 10)
        $p1 = Product::create([
            'company_id' => $company->id,
            'name' => 'Low Stock Item',
            'sku' => 'LS01',
            'min_stock' => 10,
            'selling_price' => 1000,
        ]);
        StockMovement::create([
            'company_id' => $company->id,
            'product_id' => $p1->id,
            'warehouse_id' => $warehouse->id,
            'quantity' => 5,
            'type' => 'in'
        ]);

        // Product 2: Normal stock (Stock 15, Min 10)
        $p2 = Product::create([
            'company_id' => $company->id,
            'name' => 'Safe Item',
            'sku' => 'SI01',
            'min_stock' => 10,
            'selling_price' => 2000,
        ]);
        StockMovement::create([
            'company_id' => $company->id,
            'product_id' => $p2->id,
            'warehouse_id' => $warehouse->id,
            'quantity' => 15,
            'type' => 'in'
        ]);

        $response = $this->actingAs($user)
            ->get(route('dashboard', ['portal' => 'sales_crm']));

        $response->assertStatus(200);
        
        $inertiaData = $response->getOriginalContent()->getData()['page']['props'];
        $stats = $inertiaData['stats'];

        $this->assertEquals(1, $stats['low_stock_count']);
    }

    public function test_sales_crm_can_access_products_index()
    {
        $company = Company::create([
            'name' => 'Inventory Co 2',
            'slug' => 'inv-co-2',
            'enabled_modules' => ['sales_crm']
        ]);

        $user = User::create([
            'name' => 'Inv User 2',
            'email' => 'admin@operra.com',
            'password' => bcrypt('pass'),
            'company_id' => $company->id,
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)
            ->get(route('crm.sales.products.index'));

        $response->assertStatus(200);
    }
}

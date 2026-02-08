<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Customer;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class LeadAssignmentTest extends TestCase
{
    // use RefreshDatabase;

    protected $company;
    protected $admin;
    protected $salesA;
    protected $salesB;

    protected function setUp(): void
    {
        parent::setUp();

        // Create Roles
        Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'sales', 'guard_name' => 'web']);

        // Setup Company and Users
        $this->company = Company::factory()->create();
        
        $this->admin = User::factory()->create(['company_id' => $this->company->id]);
        $this->admin->assignRole('super-admin');

        $this->salesA = User::factory()->create(['company_id' => $this->company->id, 'name' => 'Sales A']);
        $this->salesA->assignRole('sales');

        $this->salesB = User::factory()->create(['company_id' => $this->company->id, 'name' => 'Sales B']);
        $this->salesB->assignRole('sales');
    }

    public function test_admin_can_assign_lead_to_sales()
    {
        $response = $this->actingAs($this->admin)->post(route('crm.sales.customers.store'), [
            'name' => 'Test Lead',
            'email' => 'test@example.com',
            'assigned_to' => $this->salesA->id,
            'status' => 'lead'
        ]);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('customers', [
            'name' => 'Test Lead',
            'assigned_to' => $this->salesA->id,
            'company_id' => $this->company->id
        ]);
    }

    public function test_sales_user_cannot_create_leads()
    {
        $response = $this->actingAs($this->salesA)->get(route('crm.sales.customers.create'));
        $response->assertStatus(403);

        $response = $this->actingAs($this->salesA)->post(route('crm.sales.customers.store'), [
            'name' => 'Illegal Lead'
        ]);
        $response->assertStatus(403);
    }

    public function test_sales_user_only_sees_assigned_leads()
    {
        // Create 2 leads: one for Sales A, one for Sales B
        $leadForA = Customer::create([
            'name' => 'Lead for A',
            'email' => 'a@test.com',
            'company_id' => $this->company->id,
            'assigned_to' => $this->salesA->id,
            'status' => 'lead'
        ]);

        $leadForB = Customer::create([
            'name' => 'Lead for B',
            'email' => 'b@test.com',
            'company_id' => $this->company->id,
            'assigned_to' => $this->salesB->id,
            'status' => 'lead'
        ]);

        // Sales A should see Lead For A but NOT Lead For B
        $response = $this->actingAs($this->salesA)->get(route('crm.sales.customers.index'));
        
        $response->assertOk();
        $response->assertSee('Lead for A');
        $response->assertDontSee('Lead for B');
    }

    public function test_admin_sees_all_leads()
    {
        $leadForA = Customer::create([
            'name' => 'Lead for A',
            'company_id' => $this->company->id,
            'assigned_to' => $this->salesA->id
        ]);

        $leadUnassigned = Customer::create([
            'name' => 'Lead Unassigned',
            'company_id' => $this->company->id
        ]);

        $response = $this->actingAs($this->admin)->get(route('crm.sales.customers.index'));
        
        $response->assertOk();
        $response->assertSee('Lead for A');
        $response->assertSee('Lead Unassigned');
    }
}

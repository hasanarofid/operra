<?php

namespace Tests\Feature;

use App\Models\AdminTicket;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use App\Mail\AdminTicketCreated;
use Tests\TestCase;

class SupportTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_support_ticket()
    {
        Mail::fake();

        $company = Company::create([
            'name' => 'Test Co',
            'slug' => 'test-co',
            'enabled_modules' => ['sales_crm']
        ]);
        
        $user = User::create([
            'name' => 'Admin User',
            'email' => 'admin@operra.com',
            'password' => bcrypt('pass'),
            'company_id' => $company->id,
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)
            ->post(route('support.store'), [
                'subject' => 'Test Subject',
                'message' => 'Test Message',
                'priority' => 'high',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('admin_tickets', [
            'user_id' => $user->id,
            'subject' => 'Test Subject',
            'priority' => 'high',
        ]);

        Mail::assertSent(AdminTicketCreated::class);
    }

    public function test_user_can_reply_to_ticket()
    {
        $company = Company::create([
            'name' => 'Test Co 2',
            'slug' => 'test-co-2'
        ]);
        $user = User::create([
            'name' => 'Admin User',
            'email' => 'admin@operra.com',
            'password' => bcrypt('pass'),
            'company_id' => $company->id,
            'email_verified_at' => now(),
        ]);

        $ticket = AdminTicket::create([
            'user_id' => $user->id,
            'company_id' => $company->id,
            'subject' => 'Help',
            'status' => 'open',
            'priority' => 'normal',
        ]);

        $response = $this->actingAs($user)
            ->post(route('support.reply', $ticket->id), [
                'message' => 'I still need help',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('admin_ticket_messages', [
            'admin_ticket_id' => $ticket->id,
            'message' => 'I still need help',
        ]);
        
        $this->assertEquals('pending_admin', $ticket->fresh()->status);
    }
}

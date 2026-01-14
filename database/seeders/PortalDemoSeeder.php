<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class PortalDemoSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('password');
        $superAdminRole = Role::where('name', 'super-admin')->first();

        // 1. User Demo: Semua Portal
        $allPortalsCo = Company::updateOrCreate(['slug' => 'all-portals-demo'], [
            'name' => 'Demo Semua Portal',
            'status' => 'active',
            'enabled_modules' => ['wa_blast', 'sales_crm', 'marketing_crm', 'customer_service'],
        ]);

        $userAll = User::updateOrCreate(['email' => 'demo-all@operra.id'], [
            'name' => 'User Demo (Semua Portal)',
            'password' => $password,
            'company_id' => $allPortalsCo->id,
        ]);
        $userAll->assignRole($superAdminRole);

        // 2. User Demo: WhatsApp Blast Saja
        $waCo = Company::updateOrCreate(['slug' => 'wa-blast-demo'], [
            'name' => 'Demo WhatsApp Blast',
            'status' => 'active',
            'enabled_modules' => ['wa_blast'],
        ]);

        $userWa = User::updateOrCreate(['email' => 'demo-wa@operra.id'], [
            'name' => 'User Demo (WA Blast)',
            'password' => $password,
            'company_id' => $waCo->id,
        ]);
        $userWa->assignRole($superAdminRole);

        // 3. User Demo: Sales CRM Saja
        $salesCo = Company::updateOrCreate(['slug' => 'sales-crm-demo'], [
            'name' => 'Demo Sales CRM',
            'status' => 'active',
            'enabled_modules' => ['sales_crm'],
        ]);

        $userSales = User::updateOrCreate(['email' => 'demo-sales@operra.id'], [
            'name' => 'User Demo (Sales CRM)',
            'password' => $password,
            'company_id' => $salesCo->id,
        ]);
        $userSales->assignRole($superAdminRole);

        // 4. User Demo: Marketing CRM Saja
        $marketingCo = Company::updateOrCreate(['slug' => 'marketing-crm-demo'], [
            'name' => 'Demo Marketing CRM',
            'status' => 'active',
            'enabled_modules' => ['marketing_crm'],
        ]);

        $userMarketing = User::updateOrCreate(['email' => 'demo-marketing@operra.id'], [
            'name' => 'User Demo (Marketing)',
            'password' => $password,
            'company_id' => $marketingCo->id,
        ]);
        $userMarketing->assignRole($superAdminRole);

        // 5. User Demo: Customer Service Saja
        $supportCo = Company::updateOrCreate(['slug' => 'support-crm-demo'], [
            'name' => 'Demo Customer Support',
            'status' => 'active',
            'enabled_modules' => ['customer_service'],
        ]);

        $userSupport = User::updateOrCreate(['email' => 'demo-support@operra.id'], [
            'name' => 'User Demo (Customer Support)',
            'password' => $password,
            'company_id' => $supportCo->id,
        ]);
        $userSupport->assignRole($superAdminRole);
    }
}


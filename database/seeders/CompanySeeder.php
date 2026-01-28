<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $company = Company::updateOrCreate(['slug' => 'operra-default'], [
            'name' => 'Operra Default',
            'status' => 'active',
            'enabled_modules' => ['wa_blast', 'sales_crm', 'marketing_crm', 'customer_service', 'analytical_crm'],
            'is_system_owner' => true,
        ]);

        $admin = User::updateOrCreate(['email' => 'admin@operra.com'], [
            'name' => 'Admin Operra',
            'password' => Hash::make('password'),
            'company_id' => $company->id,
            'email_verified_at' => now(),
        ]);

        $admin->assignRole('super-admin');
    }
}

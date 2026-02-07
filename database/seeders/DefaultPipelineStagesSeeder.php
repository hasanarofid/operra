<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PipelineStage;
use App\Models\Company;

class DefaultPipelineStagesSeeder extends Seeder
{
    public function run()
    {
        $stages = [
            ['name' => 'Lead Baru', 'position' => 1, 'color' => '#3b82f6', 'probability' => 10], // Blue
            ['name' => 'Dihubungi', 'position' => 2, 'color' => '#f59e0b', 'probability' => 30], // Yellow
            ['name' => 'Proposal', 'position' => 3, 'color' => '#8b5cf6', 'probability' => 50], // Violet
            ['name' => 'Negosiasi', 'position' => 4, 'color' => '#f97316', 'probability' => 70], // Orange
            ['name' => 'Deal (Won)', 'position' => 5, 'color' => '#10b981', 'probability' => 100], // Green
            ['name' => 'Lost', 'position' => 6, 'color' => '#ef4444', 'probability' => 0], // Red
        ];

        // Apply to ALL existing companies
        $companies = Company::all();

        foreach ($companies as $company) {
            // Check if stages already exist to avoid duplicates
            if (PipelineStage::where('company_id', $company->id)->exists()) {
                continue;
            }

            foreach ($stages as $stage) {
                PipelineStage::create(array_merge($stage, ['company_id' => $company->id]));
            }
        }
    }
}

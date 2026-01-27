<?php

namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingController extends Controller
{
    public function index(Request $request)
    {
        $company = $request->user()->company;

        // Map company fields to what the view expects
        $settings = [
            'company_name' => $company->name,
            'company_address' => $company->address,
            'company_phone' => $company->phone,
            'company_email' => $company->email,
            'currency' => 'IDR', // Default or add to company migration
        ];

        return Inertia::render('ERP/Settings/Index', [
            'settings' => $settings
        ]);
    }

    public function update(Request $request)
    {
        $company = $request->user()->company;

        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'company_address' => 'nullable|string',
            'company_phone' => 'nullable|string',
            'company_email' => 'nullable|email',
        ]);

        $company->update([
            'name' => $validated['company_name'],
            'address' => $validated['company_address'],
            'phone' => $validated['company_phone'],
            'email' => $validated['company_email'],
        ]);

        return redirect()->back()->with('message', 'Company settings updated successfully.');
    }
}

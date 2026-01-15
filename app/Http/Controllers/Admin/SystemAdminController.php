<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\PricingPlan;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SystemAdminController extends Controller
{
    public function index()
    {
        // Pastikan hanya admin perusahan utama yang bisa akses
        if (!auth()->user()->company->is_system_owner) {
            abort(403);
        }

        $companies = Company::with('plan')
            ->where('is_system_owner', false)
            ->withCount('users')
            ->latest()
            ->get();

        $pricingPlans = PricingPlan::all();

        return Inertia::render('Admin/System/CompaniesIndex', [
            'companies' => $companies,
            'pricingPlans' => $pricingPlans,
        ]);
    }

    public function updateSubscription(Request $request, Company $company)
    {
        if (!auth()->user()->company->is_system_owner) {
            abort(403);
        }

        $request->validate([
            'pricing_plan_id' => 'required|exists:pricing_plans,id',
            'subscription_ends_at' => 'required|date',
            'status' => 'required|in:active,inactive,suspended',
        ]);

        $company->update($request->only(['pricing_plan_id', 'subscription_ends_at', 'status']));

        return back()->with('success', 'Data langganan berhasil diperbarui.');
    }
}

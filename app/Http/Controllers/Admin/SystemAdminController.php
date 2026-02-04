<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\PricingPlan;
use App\Models\WebhookLog;
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

        $companies = Company::with(['plan', 'payments' => function($query) {
                $query->where('status', 'pending');
            }])
            ->where('is_system_owner', false)
            ->withCount('users')
            ->latest()
            ->get();

        $pricingPlans = PricingPlan::all();

        return Inertia::render('Admin/System/CompaniesIndex', [
            'companies' => $companies,
            'pricingPlans' => $pricingPlans,
            'webhookLogs' => WebhookLog::latest()->limit(20)->get(),
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

        // If a payment was verified during this update
        if ($request->filled('verified_payment_id')) {
            $payment = \App\Models\Payment::find($request->verified_payment_id);
            if ($payment && $payment->company_id === $company->id) {
                $payment->update([
                    'status' => 'approved',
                    'verified_at' => now(),
                    'verified_by' => auth()->id(),
                    'notes' => 'Auto-approved via System Monitoring'
                ]);
            }
        }

        return back()->with('success', 'Data langganan berhasil diperbarui.');
    }
}

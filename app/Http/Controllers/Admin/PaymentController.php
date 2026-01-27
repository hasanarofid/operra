<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Company;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with(['company.plan'])
            ->latest()
            ->get();

        return Inertia::render('Admin/Payments/Index', [
            'payments' => $payments
        ]);
    }

    public function verify(Request $request, Payment $payment)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'notes' => 'nullable|string'
        ]);

        $payment->update([
            'status' => $request->status,
            'verified_at' => now(),
            'verified_by' => $request->user()->id,
            'notes' => $request->notes
        ]);

        if ($request->status === 'approved') {
            $company = $payment->company;
            $currentExp = $company->subscription_ends_at && $company->subscription_ends_at > now() 
                ? $company->subscription_ends_at 
                : now();
            
            $company->update([
                'subscription_ends_at' => $currentExp->addMonths($payment->months)
            ]);
        }

        return redirect()->back()->with('success', 'Status pembayaran diperbarui.');
    }
}

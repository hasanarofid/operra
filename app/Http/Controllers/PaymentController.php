<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $payments = $request->user()->company->payments()
            ->latest()
            ->get();
            
        return Inertia::render('Billing/Index', [
            'payments' => $payments,
            'plan' => $request->user()->company->plan,
            'subscription_ends_at' => $request->user()->company->subscription_ends_at,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'months' => 'required|integer|min:1',
            'amount' => 'required|numeric|min:1',
            'proof' => 'required|image|max:2048', // Max 2MB
        ]);

        $path = $request->file('proof')->store('payment-proofs', 'public');

        $request->user()->company->payments()->create([
            'months' => $request->months,
            'amount' => $request->amount,
            'proof_of_payment' => $path,
            'status' => 'pending',
            'payment_date' => now(),
        ]);

        return redirect()->back()->with('success', 'Bukti pembayaran berhasil diupload. Mohon tunggu verifikasi admin.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\PricingPlan;
use App\Models\LeadsRequest;
use App\Mail\CustomRequestMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class LandingPageController extends Controller
{
    public function index()
    {
        return Inertia::render('Welcome', [
            'pricingPlans' => PricingPlan::all(),
            'canLogin' => true,
            'canRegister' => true,
        ]);
    }

    public function showContact()
    {
        return Inertia::render('Contact');
    }

    public function submitRequest(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'business_type' => 'required|string',
            'message' => 'required|string',
        ]);

        LeadsRequest::create($validated);

        try {
            Mail::to('hasanarofid@gmail.com')->send(new CustomRequestMail($validated));
        } catch (\Exception $e) {
            \Log::error('Gagal mengirim email: ' . $e->getMessage());
        }

        return back()->with('success', 'Permintaan Anda telah kami terima. Tim kami akan segera menghubungi Anda!');
    }
}

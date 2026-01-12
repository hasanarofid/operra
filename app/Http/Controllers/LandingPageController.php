<?php

namespace App\Http\Controllers;

use App\Models\PricingPlan;
use App\Models\LeadsRequest;
use Illuminate\Http\Request;
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

        return back()->with('success', 'Permintaan Anda telah kami terima. Tim kami akan segera menghubungi Anda!');
    }
}

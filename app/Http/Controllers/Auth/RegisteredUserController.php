<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Company;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:' . User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'plan' => 'nullable|string|exists:pricing_plans,slug',
        ]);

        $pricingPlan = \App\Models\PricingPlan::where('slug', $request->plan)->first();

        // Default modules for trial/registration
        $enabledModules = ['wa_blast'];
        if ($pricingPlan && $pricingPlan->slug === 'business-pro') {
            $enabledModules = ['wa_blast', 'sales', 'marketing', 'support'];
        }

        // Create the company first
        $company = Company::create([
            'name' => $request->company_name,
            'slug' => \Illuminate\Support\Str::slug($request->company_name),
            'pricing_plan_id' => $pricingPlan?->id,
            'subscription_ends_at' => now()->addDays(7), // 7 days trial
            'status' => 'active',
            'enabled_modules' => $enabledModules,
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'company_id' => $company->id,
        ]);

        // Assign super-admin role to the first user of the company
        $user->assignRole('super-admin');

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }

}

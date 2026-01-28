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
    public function create(Request $request): Response
    {
        return Inertia::render('Auth/Register', [
            'plan' => $request->query('plan')
        ]);
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
            'email' => 'required|string|lowercase|email:rfc,dns|max:255|unique:' . User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'plan' => 'nullable|string|exists:pricing_plans,slug',
        ]);

        // Default to starter if not provided
        $planSlug = $request->plan ?: 'starter';
        $pricingPlan = \App\Models\PricingPlan::where('slug', $planSlug)->first();

        // Default modules (Starter gets WA Blast)
        $enabledModules = ['wa_blast'];

        // Modules for Business Pro or Enterprise
        if ($pricingPlan && in_array($pricingPlan->slug, ['business-pro', 'enterprise'])) {
            $enabledModules = ['wa_blast', 'sales_crm', 'marketing_crm', 'customer_service'];
        }

        // Create the company first
        $company = Company::create([
            'name' => $request->company_name,
            'slug' => \Illuminate\Support\Str::slug($request->company_name),
            'pricing_plan_id' => $pricingPlan?->id,
            'subscription_ends_at' => now()->addDays(3), // 3 days trial
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

        // Auth::login($user); // Disable auto-login

        return redirect(route('verification.notice'));
    }

}

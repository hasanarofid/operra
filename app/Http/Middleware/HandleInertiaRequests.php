<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use App\Models\ChatMessage;
use App\Models\LeadsRequest;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user() ? [
                    'id' => $request->user()->id,
                    'name' => $request->user()->name,
                    'email' => $request->user()->email,
                    'has_completed_onboarding' => $request->user()->has_completed_onboarding,
                    'roles' => $request->user()->roles->pluck('name'),
                    'permissions' => $request->user()->getAllPermissions()->pluck('name'),
                    'company' => $request->user()->company ? [
                        'id' => $request->user()->company->id,
                        'name' => $request->user()->company->name,
                        'enabled_modules' => $request->user()->company->enabled_modules ?? [],
                        'is_system_owner' => $request->user()->company->is_system_owner ?? false,
                        'pricing_plan' => $request->user()->company->plan ? [
                            'name' => $request->user()->company->plan->name,
                            'slug' => $request->user()->company->plan->slug,
                            'features' => $request->user()->company->plan->features,
                        ] : null,
                        'subscription_ends_at' => $request->user()->company->subscription_ends_at,
                    ] : null,
                ] : null,
            ],
            'unreadCount' => $request->user() ? ChatMessage::whereHas('chatSession', function ($query) use ($request) {
                $query->where('assigned_user_id', $request->user()->id);
            })->where('sender_type', 'customer')->whereNull('read_at')->count() : 0,
            'newLeadsCount' => $request->user() && $request->user()->hasRole('super-admin')
                ? LeadsRequest::where('status', 'new')->count()
                : 0,
        ];
    }
}

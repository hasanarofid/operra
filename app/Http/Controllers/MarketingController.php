<?php

namespace App\Http\Controllers;

use App\Models\MarketingCampaign;
use App\Models\MarketingBlast;
use App\Models\MarketingAutomation;
use App\Models\Customer;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MarketingController extends Controller
{
    public function dashboard()
    {
        return Inertia::render('Marketing/Dashboard', [
            'stats' => [
                'active_campaigns' => MarketingCampaign::where('status', 'active')->count(),
                'total_blasts' => MarketingBlast::count(),
                'avg_lead_score' => round(Customer::avg('lead_score') ?? 0, 1),
                'active_automations' => MarketingAutomation::where('is_active', true)->count(),
            ],
            'recent_campaigns' => MarketingCampaign::latest()->limit(5)->get(),
            'top_leads' => Customer::orderBy('lead_score', 'desc')->limit(5)->get(),
        ]);
    }

    public function campaigns()
    {
        return Inertia::render('Marketing/Campaigns/Index', [
            'campaigns' => MarketingCampaign::latest()->paginate(10)
        ]);
    }

    public function blasts()
    {
        return Inertia::render('Marketing/Blasts/Index', [
            'blasts' => MarketingBlast::with('campaign')->latest()->paginate(10)
        ]);
    }

    public function automations()
    {
        return Inertia::render('Marketing/Automations/Index', [
            'automations' => MarketingAutomation::latest()->paginate(10)
        ]);
    }

    public function leadScoring()
    {
        return Inertia::render('Marketing/LeadScoring/Index', [
            'leads' => Customer::orderBy('lead_score', 'desc')->paginate(15)
        ]);
    }
}

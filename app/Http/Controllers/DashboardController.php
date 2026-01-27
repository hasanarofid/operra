<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\ChatSession;
use App\Models\ChatMessage;
use App\Models\WhatsappAccount;
use App\Models\User;
use Carbon\Carbon;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $today = Carbon::today();
        $weekAgo = Carbon::now()->subDays(7);

        // Base queries based on roles
        $leadsQuery = Customer::query();
        $sessionsQuery = ChatSession::query();
        // ChatMessage doesn't have company_id directly, filter by session
        $messagesQuery = ChatMessage::whereHas('chatSession', function ($q) use ($user) {
            $q->where('company_id', $user->company_id);
        });

        $portal = $request->query('portal');

        // If no portal specified but company only has one module, auto-select it
        if (!$portal && $user->company && count($user->company->enabled_modules ?? []) === 1) {
            $portal = $user->company->enabled_modules[0];
        }

        if ($user->hasRole('sales')) {
            $leadsQuery->where('assigned_to', $user->id);
            $sessionsQuery->where('assigned_user_id', $user->id);
            // Only messages from their sessions
            $messagesQuery->whereHas('chatSession', function ($q) use ($user) {
                $q->where('assigned_user_id', $user->id);
            });
        }

        // 1. KPI Summary
        $stats = [
            'total_leads' => (clone $leadsQuery)->count(),
            'active_chats' => (clone $sessionsQuery)->where('status', 'open')->count(),
            'new_leads_today' => (clone $leadsQuery)->whereDate('created_at', $today)->count(),
            'messages_today' => (clone $messagesQuery)->whereDate('created_at', $today)->count(),
        ];

        if ($portal === 'marketing_crm') {
            $stats = [
                'total_leads' => (clone $leadsQuery)->count(),
                'active_campaigns' => \App\Models\MarketingCampaign::where('company_id', $user->company_id)->where('status', 'active')->count(),
                'avg_lead_score' => round((clone $leadsQuery)->avg('lead_score') ?? 0, 1),
                'active_automations' => \App\Models\MarketingAutomation::where('company_id', $user->company_id)->where('is_active', true)->count(),
                'recent_campaigns' => \App\Models\MarketingCampaign::where('company_id', $user->company_id)->latest()->limit(5)->get(),
                'top_leads' => (clone $leadsQuery)->orderBy('lead_score', 'desc')->limit(5)->get(),
            ];
        }

        if ($portal === 'customer_service') {
            $stats = [
                'open_tickets' => \App\Models\SupportTicket::where('company_id', $user->company_id)->where('status', 'open')->count(),
                'urgent_tickets' => \App\Models\SupportTicket::where('company_id', $user->company_id)->where('priority', 'urgent')->count(),
                'resolved_today' => \App\Models\SupportTicket::where('company_id', $user->company_id)->where('status', 'resolved')->whereDate('resolved_at', now())->count(),
                'messages_today' => (clone $messagesQuery)->whereDate('created_at', $today)->count(),
                'recent_tickets' => \App\Models\SupportTicket::where('company_id', $user->company_id)->with('customer')->latest()->limit(5)->get(),
            ];
        }

        // 2. Recent CRM Activity
        $recentLeads = (clone $leadsQuery)->latest()->limit(5)->get();
        $recentChats = (clone $sessionsQuery)
            ->with(['customer', 'assignedUser', 'whatsappAccount'])
            ->latest()
            ->limit(5)
            ->get();

        // 3. Lead Growth Trend (Last 7 Days)
        $chartData = Customer::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('count(*) as count')
        )
            ->where('created_at', '>=', $weekAgo);

        if ($user->hasRole('sales')) {
            $chartData->where('assigned_to', $user->id);
        }

        $chartData = $chartData->groupBy('date')
            ->orderBy('date')
            ->get();

        // Ensure we have data for all last 7 days (fill gaps with 0)
        $formattedChartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $existing = $chartData->firstWhere('date', $date);
            $formattedChartData[] = [
                'date' => $date,
                'count' => $existing ? $existing->count : 0
            ];
        }
        $chartData = $formattedChartData;

        // 4. Admin Only: Account & Agent Status
        $waAccounts = [];
        $adminStats = [];
        $systemLeads = [];
        $systemCompanies = [];

        if ($user->hasRole('super-admin')) {
            $waAccounts = WhatsappAccount::withCount('agents')->get();

            // System Admin Stats
            $adminStats = [
                'total_leads_request' => \App\Models\LeadsRequest::count(),
                'new_leads_request' => \App\Models\LeadsRequest::where('status', 'new')->count(),
                'total_companies' => \App\Models\Company::count(),
                'active_subscriptions' => \App\Models\Company::where('subscription_ends_at', '>', now())->count(),
            ];

            $systemLeads = \App\Models\LeadsRequest::latest()->limit(5)->get();
            $systemCompanies = \App\Models\Company::latest()->limit(5)->get();
        } elseif ($user->hasRole('manager')) {
            $waAccounts = WhatsappAccount::withCount('agents')->get();
        }

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'recentLeads' => $recentLeads,
            'recentChats' => $recentChats,
            'chartData' => $chartData,
            'waAccounts' => $waAccounts,
            'adminStats' => $adminStats,
            'systemLeads' => $systemLeads,
            'systemCompanies' => $systemCompanies,
            'userRole' => $user->getRoleNames()->first()
        ]);
    }
}

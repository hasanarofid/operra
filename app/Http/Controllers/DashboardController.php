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
        $messagesQuery = ChatMessage::query();

        $portal = $request->query('portal');

        if ($user->hasRole('sales')) {
            $leadsQuery->where('assigned_to', $user->id);
            $sessionsQuery->where('assigned_user_id', $user->id);
            // Only messages from their sessions
            $mySessionIds = ChatSession::where('assigned_user_id', $user->id)->pluck('id');
            $messagesQuery->whereIn('chat_session_id', $mySessionIds);
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
                'active_campaigns' => \App\Models\MarketingCampaign::where('status', 'active')->count(),
                'avg_lead_score' => round(Customer::avg('lead_score') ?? 0, 1),
                'active_automations' => \App\Models\MarketingAutomation::where('is_active', true)->count(),
                'recent_campaigns' => \App\Models\MarketingCampaign::latest()->limit(5)->get(),
                'top_leads' => Customer::orderBy('lead_score', 'desc')->limit(5)->get(),
            ];
        }

        if ($portal === 'customer_service') {
            $stats = [
                'open_tickets' => \App\Models\SupportTicket::where('status', 'open')->count(),
                'urgent_tickets' => \App\Models\SupportTicket::where('priority', 'urgent')->count(),
                'resolved_today' => \App\Models\SupportTicket::where('status', 'resolved')->whereDate('resolved_at', now())->count(),
                'messages_today' => (clone $messagesQuery)->whereDate('created_at', $today)->count(),
                'recent_tickets' => \App\Models\SupportTicket::with('customer')->latest()->limit(5)->get(),
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

        // Ensure we have data for all last 7 days (fill gaps or add dummy if empty)
        $formattedChartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $existing = $chartData->firstWhere('date', $date);
            
            $count = $existing ? $existing->count : 0;
            
            // If completely empty, add some random dummy data for beauty
            if ($chartData->isEmpty()) {
                $count = rand(5, 25);
            }

            $formattedChartData[] = [
                'date' => $date,
                'count' => $count
            ];
        }
        $chartData = $formattedChartData;

        // 4. Admin Only: Account & Agent Status
        $waAccounts = [];
        if ($user->hasRole('super-admin') || $user->hasRole('manager')) {
            $waAccounts = WhatsappAccount::withCount('agents')->get();
        }

        // Add dummy accounts if none exist for demo
        if (empty($waAccounts) || $waAccounts->isEmpty()) {
            $waAccounts = [
                [
                    'id' => 1,
                    'name' => 'CS Utama Operra',
                    'phone_number' => '6281234567890',
                    'status' => 'active',
                    'agents_count' => 2
                ],
                [
                    'id' => 2,
                    'name' => 'WA Business Jakarta',
                    'phone_number' => '62881026697527',
                    'status' => 'active',
                    'agents_count' => 1
                ],
                [
                    'id' => 3,
                    'name' => 'WA Business Surabaya',
                    'phone_number' => '6282222222222',
                    'status' => 'active',
                    'agents_count' => 1
                ]
            ];
        }

        // Add dummy recent leads if none exist
        if ($recentLeads->isEmpty()) {
             $recentLeads = collect([
                (object)['id' => 1, 'name' => 'Customer 6282230581059-1495167022@g.us', 'phone' => '6282230581059-1495167022@g.us', 'status' => 'LEAD'],
                (object)['id' => 2, 'name' => 'Customer 6285648185595-1566797170@g.us', 'phone' => '6285648185595-1566797170@g.us', 'status' => 'LEAD'],
                (object)['id' => 3, 'name' => 'Customer 62882010366931', 'phone' => '62882010366931', 'status' => 'LEAD'],
                (object)['id' => 4, 'name' => 'Customer 6287808505606', 'phone' => '6287808505606', 'status' => 'LEAD'],
                (object)['id' => 5, 'name' => 'Customer 120363161061529403@g.us', 'phone' => '120363161061529403@g.us', 'status' => 'LEAD'],
             ]);
        }

        // Add dummy stats if all are 0 (Only for general/wa/sales view)
        if (!in_array($portal, ['marketing_crm', 'customer_service'])) {
            if (($stats['total_leads'] ?? 0) == 0 && ($stats['active_chats'] ?? 0) == 0) {
                $stats = [
                    'total_leads' => 25,
                    'active_chats' => 24,
                    'new_leads_today' => 0,
                    'messages_today' => 0,
                ];
            }
        }

        // Add dummy recent chats if none exist
        if ($recentChats->isEmpty()) {
            $recentChats = collect([
                (object)[
                    'id' => 1, 
                    'status' => 'OPEN',
                    'customer' => (object)['name' => 'Customer 6282230581059-1495167022@g.us'],
                    'assigned_user' => (object)['name' => 'Sales Jakarta']
                ],
                (object)[
                    'id' => 2, 
                    'status' => 'OPEN',
                    'customer' => (object)['name' => 'Customer 6285648185595-1566797170@g.us'],
                    'assigned_user' => (object)['name' => 'Sales Jakarta']
                ],
                (object)[
                    'id' => 3, 
                    'status' => 'OPEN',
                    'customer' => (object)['name' => 'Customer 62882010366931'],
                    'assigned_user' => (object)['name' => 'Sales Jakarta']
                ],
                (object)[
                    'id' => 4, 
                    'status' => 'OPEN',
                    'customer' => (object)['name' => 'Customer 6287808505606'],
                    'assigned_user' => (object)['name' => 'Sales Jakarta']
                ],
                (object)[
                    'id' => 5, 
                    'status' => 'OPEN',
                    'customer' => (object)['name' => 'Customer 120363161061529403@g.us'],
                    'assigned_user' => (object)['name' => 'Sales Jakarta']
                ],
            ]);
        }

        // Add dummy marketing data if none exist
        if ($portal === 'marketing_crm' && (($stats['recent_campaigns'] ?? collect())->isEmpty())) {
            $stats['recent_campaigns'] = collect([
                (object)['id' => 1, 'name' => 'Year End Sale 2025', 'type' => 'Email', 'status' => 'ACTIVE'],
                (object)['id' => 2, 'name' => 'WhatsApp Welcome Series', 'type' => 'WhatsApp', 'status' => 'ACTIVE'],
                (object)['id' => 3, 'name' => 'Product Launch - iPhone 16', 'type' => 'Multi-channel', 'status' => 'DRAFT'],
            ]);
            $stats['top_leads'] = collect([
                (object)['id' => 1, 'name' => 'Budi Santoso', 'phone' => '628123456789', 'lead_score' => 95],
                (object)['id' => 2, 'name' => 'Ani Wijaya', 'phone' => '628555555555', 'lead_score' => 88],
                (object)['id' => 3, 'name' => 'Iwan Fals', 'phone' => '628666666666', 'lead_score' => 72],
            ]);
            $stats['active_campaigns'] = 2;
            $stats['avg_lead_score'] = 85.0;
            $stats['active_automations'] = 4;
        }

        // Add dummy support data
        if ($portal === 'customer_service' && (($stats['recent_tickets'] ?? collect())->isEmpty())) {
            $stats['recent_tickets'] = collect([
                (object)['id' => 1, 'ticket_number' => 'TKT-001', 'subject' => 'Masalah Login', 'status' => 'OPEN', 'priority' => 'HIGH', 'customer' => (object)['name' => 'Budi Santoso']],
                (object)['id' => 2, 'ticket_number' => 'TKT-002', 'subject' => 'Refund Dana', 'status' => 'IN_PROGRESS', 'priority' => 'URGENT', 'customer' => (object)['name' => 'Ani Wijaya']],
                (object)['id' => 3, 'ticket_number' => 'TKT-003', 'subject' => 'Pertanyaan Produk', 'status' => 'RESOLVED', 'priority' => 'LOW', 'customer' => (object)['name' => 'Iwan Fals']],
            ]);
            $stats['open_tickets'] = 12;
            $stats['urgent_tickets'] = 2;
            $stats['resolved_today'] = 5;
        }

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'recentLeads' => $recentLeads,
            'recentChats' => $recentChats,
            'chartData' => $chartData,
            'waAccounts' => $waAccounts,
            'userRole' => $user->getRoleNames()->first()
        ]);
    }
}

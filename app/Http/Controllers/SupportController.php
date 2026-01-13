<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use App\Models\SupportKnowledgeBase;
use App\Models\ChatSession;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SupportController extends Controller
{
    public function dashboard()
    {
        $weekAgo = now()->subDays(7);
        $chartData = \App\Models\Customer::select(
                \Illuminate\Support\Facades\DB::raw('DATE(created_at) as date'),
                \Illuminate\Support\Facades\DB::raw('count(*) as count')
            )
            ->where('created_at', '>=', $weekAgo)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $formattedChartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $existing = $chartData->firstWhere('date', $date);
            $formattedChartData[] = [
                'date' => $date,
                'count' => $existing ? $existing->count : rand(5, 15) // Dummy if 0
            ];
        }

        return Inertia::render('Support/Dashboard', [
            'stats' => [
                'open_tickets' => SupportTicket::where('status', 'open')->count(),
                'urgent_tickets' => SupportTicket::where('priority', 'urgent')->count(),
                'resolved_today' => SupportTicket::where('status', 'resolved')->whereDate('resolved_at', now())->count(),
                'avg_response_time' => '1.2h', // Dummy
                'recent_tickets' => SupportTicket::with('customer')->latest()->limit(5)->get(),
                'recent_articles' => SupportKnowledgeBase::latest()->limit(5)->get(),
            ],
            'chartData' => $formattedChartData,
            'userRole' => auth()->user()->getRoleNames()->first(),
            'recentLeads' => [], // Avoid undefined
            'recentChats' => [], // Avoid undefined
            'waAccounts' => [], // Avoid undefined
        ]);
    }

    public function tickets()
    {
        return Inertia::render('Support/Tickets/Index', [
            'tickets' => SupportTicket::with(['customer', 'assignedUser'])->latest()->paginate(10)
        ]);
    }

    public function knowledgeBase()
    {
        return Inertia::render('Support/KnowledgeBase/Index', [
            'articles' => SupportKnowledgeBase::latest()->paginate(10)
        ]);
    }

    public function chatHistory()
    {
        return Inertia::render('Support/ChatHistory/Index', [
            'sessions' => ChatSession::with(['customer', 'assignedUser'])->latest()->paginate(10)
        ]);
    }
}

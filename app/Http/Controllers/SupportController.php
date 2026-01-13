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
        return Inertia::render('Support/Dashboard', [
            'stats' => [
                'open_tickets' => SupportTicket::where('status', 'open')->count(),
                'urgent_tickets' => SupportTicket::where('priority', 'urgent')->count(),
                'resolved_today' => SupportTicket::where('status', 'resolved')->whereDate('resolved_at', now())->count(),
                'avg_response_time' => '1.2h', // Dummy
            ],
            'recent_tickets' => SupportTicket::with('customer')->latest()->limit(5)->get(),
            'recent_articles' => SupportKnowledgeBase::latest()->limit(5)->get(),
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

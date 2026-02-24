<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\ChatSession;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class CrmSupportController extends Controller
{
    /**
     * Display the Support Dashboard.
     */
    public function dashboard()
    {
        // Redirect to main dashboard with portal parameter for consistent stats view
        return redirect()->route('dashboard', ['portal' => 'customer_service']);
    }

    /**
     * Display the list of Customer Tickets.
     */
    public function tickets(Request $request)
    {
        $user = Auth::user();
        $view = $request->input('view', 'all');

        $query = SupportTicket::where('company_id', $user->company_id)
            ->with(['customer', 'assignedUser']);

        if ($view === 'mine') {
            $query->where('assigned_user_id', $user->id);
        } elseif ($view === 'unassigned') {
            $query->whereNull('assigned_user_id');
        }

        $tickets = $query->latest()->paginate(10)->withQueryString();

        return Inertia::render('CRM/Support/Tickets/Index', [
            'tickets' => $tickets,
            'filters' => [
                'view' => $view,
            ]
        ]);
    }

    /**
     * Display Chat History.
     */
    public function chatHistory()
    {
        $user = Auth::user();

        $chats = ChatSession::where('company_id', $user->company_id)
            ->with(['customer', 'assignedUser', 'whatsappAccount'])
            ->latest()
            ->paginate(20);

        return Inertia::render('CRM/Support/ChatHistory/Index', [
            'chats' => $chats
        ]);
    }

    /**
     * Display Knowledge Base.
     */
    public function knowledgeBase()
    {
        return Inertia::render('CRM/Support/KnowledgeBase/Index');
    }
}

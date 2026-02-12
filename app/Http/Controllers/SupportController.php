<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AdminTicket;
use App\Models\AdminTicketMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\AdminTicketCreated;
use Inertia\Inertia;

class SupportController extends Controller
{
    public function index()
    {
        $tickets = AdminTicket::where('user_id', Auth::id())
            ->with(['messages' => function($q) {
                $q->latest()->limit(1);
            }])
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Support/Index', [
            'tickets' => $tickets
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'priority' => 'required|in:low,normal,high,urgent',
        ]);

        $ticket = AdminTicket::create([
            'user_id' => Auth::id(),
            'company_id' => Auth::user()->company_id,
            'subject' => $request->subject,
            'status' => 'open',
            'priority' => $request->priority,
        ]);

        AdminTicketMessage::create([
            'admin_ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'message' => $request->message,
        ]);

        // Notify Admin
        $adminEmail = 'operra@gmail.com'; 
        try {
            Mail::to($adminEmail)->send(new AdminTicketCreated($ticket, $request->message));
        } catch (\Exception $e) {
            // Log error but don't stop the flow
            \Log::error("Failed to send support email: " . $e->getMessage());
        }

        return redirect()->back()->with('message', 'Laporan komplain Anda telah terkirim.');
    }

    public function show($id)
    {
        $ticket = AdminTicket::where('id', $id)
            ->where('company_id', Auth::user()->company_id)
            // If they are admin of their company, they can see all tickets? 
            // The prompt says "seperti di portal war bot", which usually means personal or company-wide.
            // Let's stick to their own or company's if they have role super-admin in their company.
            ->with(['messages.user', 'user'])
            ->firstOrFail();

        return Inertia::render('Support/Show', [
            'ticket' => $ticket
        ]);
    }

    public function reply(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $ticket = AdminTicket::where('id', $id)->firstOrFail();

        AdminTicketMessage::create([
            'admin_ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'message' => $request->message,
        ]);

        $ticket->update(['status' => 'pending_admin']);

        // Notify Admin
        $adminEmail = 'operra@gmail.com';
        try {
            Mail::to($adminEmail)->send(new AdminTicketCreated($ticket, $request->message));
        } catch (\Exception $e) {
            \Log::error("Failed to send support reply email: " . $e->getMessage());
        }

        return redirect()->back()->with('message', 'Pesan balasan terkirim.');
    }
}

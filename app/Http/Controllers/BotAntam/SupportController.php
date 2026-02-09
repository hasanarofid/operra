<?php

namespace App\Http\Controllers\BotAntam;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdminTicket;
use App\Models\AdminTicketMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\AdminTicketCreated;
use Inertia\Inertia;

class SupportController extends Controller
{
    public function index()
    {
        $tickets = AdminTicket::where('user_id', Auth::id())
            ->with('messages')
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('BotAntam/Support/Index', [
            'tickets' => $tickets
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $ticket = AdminTicket::create([
            'user_id' => Auth::id(),
            'company_id' => Auth::user()->company_id,
            'subject' => $request->subject,
            'status' => 'open',
        ]);

        $message = AdminTicketMessage::create([
            'admin_ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'message' => $request->message,
        ]);

        // Send Email to Admin (operra@gmail.com or configured admin email)
        // Hardcoding admin email as requested or better env config
        $adminEmail = 'operra@gmail.com'; 
        Mail::to($adminEmail)->send(new AdminTicketCreated($ticket, $request->message));

        return redirect()->back()->with('success', 'Ticket created successfully.');
    }

    public function show($id)
    {
        $ticket = AdminTicket::where('id', $id)
            ->where('user_id', Auth::id())
            ->with(['messages.user'])
            ->firstOrFail();

        return Inertia::render('BotAntam/Support/Show', [
            'ticket' => $ticket
        ]);
    }

    public function reply(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $ticket = AdminTicket::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        AdminTicketMessage::create([
            'admin_ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'message' => $request->message,
        ]);

        $ticket->update(['status' => 'pending_admin']);

        // Notify Admin
        $adminEmail = 'operra@gmail.com';
        Mail::to($adminEmail)->send(new AdminTicketCreated($ticket, $request->message)); // Reusing Mailable or create ReplyMailable

        return redirect()->back()->with('success', 'Reply sent.');
    }
}

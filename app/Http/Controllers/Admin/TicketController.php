<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdminTicket;
use App\Models\AdminTicketMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\AdminTicketReplied;
use Inertia\Inertia;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = AdminTicket::with(['user.company']) // Assuming User belongsTo Company
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Admin/Tickets/Index', [
            'tickets' => $tickets
        ]);
    }

    public function show($id)
    {
        $ticket = AdminTicket::with(['messages.user', 'user'])
            ->findOrFail($id);

        return Inertia::render('Admin/Tickets/Show', [
            'ticket' => $ticket
        ]);
    }

    public function reply(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $ticket = AdminTicket::findOrFail($id);

        AdminTicketMessage::create([
            'admin_ticket_id' => $ticket->id,
            'user_id' => Auth::id(), // Admin ID
            'message' => $request->message,
        ]);

        $ticket->update(['status' => 'pending_user']);

        // Notify User
        if ($ticket->user && $ticket->user->email) {
            Mail::to($ticket->user->email)->send(new AdminTicketReplied($ticket, $request->message));
        }

        return redirect()->back()->with('success', 'Reply sent to user.');
    }
}

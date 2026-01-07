<?php

namespace App\Http\Controllers;

use App\Models\ChatSession;
use App\Models\ChatMessage;
use App\Models\WhatsappAccount;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CRMChatController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        $sessionsQuery = ChatSession::with(['customer', 'assignedUser', 'whatsappAccount'])
            ->orderBy('last_message_at', 'desc');

        // RBAC: Sales only see their assigned chats
        if ($user->hasRole('sales')) {
            $sessionsQuery->where('assigned_user_id', $user->id);
        }

        return Inertia::render('CRM/Chat/Inbox', [
            'sessions' => $sessionsQuery->get(),
            'whatsappAccounts' => WhatsappAccount::where('status', 'active')->get(),
        ]);
    }

    public function show(ChatSession $chatSession, Request $request)
    {
        $user = $request->user();

        // Security check
        if ($user->hasRole('sales') && $chatSession->assigned_user_id !== $user->id) {
            abort(403, 'Unauthorized access to this chat.');
        }

        $chatSession->load(['customer', 'assignedUser', 'whatsappAccount']);
        $messages = ChatMessage::where('chat_session_id', $chatSession->id)
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'session' => $chatSession,
            'messages' => $messages
        ]);
    }

    public function sendMessage(Request $request, ChatSession $chatSession)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $message = ChatMessage::create([
            'chat_session_id' => $chatSession->id,
            'sender_id' => $request->user()->id,
            'sender_type' => 'user',
            'message_body' => $request->message,
            'message_type' => 'text',
        ]);

        $chatSession->update(['last_message_at' => now()]);

        return response()->json($message->load('sender'));
    }
}

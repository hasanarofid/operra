<?php

namespace App\Http\Controllers;

use App\Models\WhatsappAccount;
use App\Models\WhatsappAutoReply;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WhatsappAutoReplyController extends Controller
{
    public function index()
    {
        return Inertia::render('WhatsApp/AutoReply', [
            'autoReplies' => WhatsappAutoReply::with('whatsappAccount')->latest()->get(),
            'whatsappAccounts' => WhatsappAccount::where('status', 'active')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'whatsapp_account_id' => 'nullable|exists:whatsapp_accounts,id',
            'keyword' => 'required|string|max:255',
            'response' => 'required|string',
            'match_type' => 'required|in:exact,contains',
            'is_active' => 'boolean',
        ]);

        WhatsappAutoReply::create($validated);

        return redirect()->back()->with('message', 'Auto Reply created successfully.');
    }

    public function update(Request $request, WhatsappAutoReply $whatsappAutoReply)
    {
        $validated = $request->validate([
            'whatsapp_account_id' => 'nullable|exists:whatsapp_accounts,id',
            'keyword' => 'required|string|max:255',
            'response' => 'required|string',
            'match_type' => 'required|in:exact,contains',
            'is_active' => 'boolean',
        ]);

        $whatsappAutoReply->update($validated);

        return redirect()->back()->with('message', 'Auto Reply updated successfully.');
    }

    public function destroy(WhatsappAutoReply $whatsappAutoReply)
    {
        $whatsappAutoReply->delete();

        return redirect()->back()->with('message', 'Auto Reply deleted successfully.');
    }

    public function toggle(WhatsappAutoReply $whatsappAutoReply)
    {
        $whatsappAutoReply->update([
            'is_active' => !$whatsappAutoReply->is_active,
        ]);

        return redirect()->back()->with('message', 'Auto Reply status toggled.');
    }
}

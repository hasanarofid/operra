<?php

namespace App\Http\Controllers;

use App\Models\WhatsappCampaign;
use App\Models\WhatsappAccount;
use App\Models\Customer;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WhatsAppBlastController extends Controller
{
    protected $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    public function index()
    {
        return Inertia::render('WhatsApp/Blast/Index', [
            'campaigns' => WhatsappCampaign::with('account')->latest()->get(),
            'accounts' => WhatsappAccount::where('status', 'active')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'whatsapp_account_id' => 'required|exists:whatsapp_accounts,id',
            'customer_ids' => 'required|array',
            'message_template' => 'nullable|string',
            'template_name' => 'nullable|string', // For Official Meta
            'template_data' => 'nullable|array',
        ]);

        $campaign = WhatsappCampaign::create([
            'name' => $validated['name'],
            'whatsapp_account_id' => $validated['whatsapp_account_id'],
            'message_template' => $validated['message_template'] ?? '',
            'template_name' => $validated['template_name'],
            'template_data' => $validated['template_data'],
            'status' => 'draft',
            'total_recipients' => count($validated['customer_ids']),
        ]);

        foreach ($validated['customer_ids'] as $customerId) {
            $customer = Customer::find($customerId);
            if ($customer) {
                $campaign->logs()->create([
                    'customer_id' => $customer->id,
                    'phone_number' => $customer->phone,
                    'status' => 'pending',
                ]);
            }
        }

        return redirect()->back()->with('message', 'Campaign created successfully.');
    }

    public function process(WhatsappCampaign $campaign)
    {
        if ($campaign->status === 'completed') {
            return response()->json(['message' => 'Campaign already completed.'], 400);
        }

        // Set status to processing immediately
        $campaign->update(['status' => 'processing']);

        // Dispatch Job to Queue
        \App\Jobs\ProcessWhatsAppBlast::dispatch($campaign->id);

        return response()->json(['message' => 'Campaign processing started in background.']);
    }

    public function destroy(WhatsappCampaign $campaign)
    {
        $campaign->delete();
        return redirect()->back()->with('message', 'Campaign deleted successfully.');
    }
}


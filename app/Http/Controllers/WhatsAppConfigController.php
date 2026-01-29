<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\WhatsappAccount;
use App\Models\WhatsappTemplate;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WhatsAppConfigController extends Controller
{
    public function index(Request $request, WhatsAppService $waService)
    {
        $settings = Setting::whereIn('key', [
            'meta_access_token',
            'meta_webhook_verify_token',
            'meta_app_id',
            'meta_waba_id',
        ])->pluck('value', 'key');

        $waStatus = [
            'provider' => 'official',
            'connection' => 'ready'
        ];

        return Inertia::render('Settings/WhatsApp', [
            'settings' => $settings,
            'waStatus' => $waStatus,
            'accounts' => WhatsappAccount::where('company_id', $request->user()->company_id)->get()
        ]);
    }

    public function storeAccount(Request $request, WhatsAppService $waService)
    {
        $company = $request->user()->company;
        if (!$company->canAddWaAccount()) {
            return redirect()->back()->withErrors(['message' => 'Anda telah mencapai batas maksimum akun WhatsApp untuk paket ' . ($company->plan->name ?? 'ini') . '.']);
        }

        $validated = $request->validate([
            'name' => 'required|string',
            'phone_number' => 'required|string|unique:whatsapp_accounts,phone_number',
            'provider' => 'required|string',
            'token' => 'nullable|string',
            'key' => 'nullable|string',
            'endpoint' => 'nullable|url',
        ]);

        $account = WhatsappAccount::create([
            'company_id' => $request->user()->company_id,
            'name' => $validated['name'],
            'phone_number' => $validated['phone_number'],
            'provider' => $validated['provider'],
            'api_credentials' => [
                'token' => $validated['token'] ?? '',
                'key' => $validated['key'] ?? '',
                'endpoint' => $validated['endpoint'] ?? '',
            ],
            'status' => 'inactive',
        ]);

        $waService->syncAccountStatus($account);

        return redirect()->back()->with('message', 'WhatsApp Account added and synced successfully.');
    }

    public function updateAccount(Request $request, WhatsappAccount $whatsappAccount, WhatsAppService $waService)
    {
        if ($whatsappAccount->company_id !== $request->user()->company_id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required|string',
            'phone_number' => 'required|string|unique:whatsapp_accounts,phone_number,' . $whatsappAccount->id,
            'provider' => 'required|string',
            'token' => 'nullable|string',
            'key' => 'nullable|string',
            'endpoint' => 'nullable|url',
        ]);

        $whatsappAccount->update([
            'name' => $validated['name'],
            'phone_number' => $validated['phone_number'],
            'provider' => $validated['provider'],
            'api_credentials' => [
                'token' => $validated['token'] ?? '',
                'key' => $validated['key'] ?? '',
                'endpoint' => $validated['endpoint'] ?? '',
            ],
        ]);

        $waService->syncAccountStatus($whatsappAccount);

        return redirect()->back()->with('message', 'WhatsApp Account updated and synced successfully.');
    }

    public function destroyAccount(Request $request, WhatsappAccount $whatsappAccount)
    {
        if ($whatsappAccount->company_id !== $request->user()->company_id) {
            abort(403, 'Unauthorized action.');
        }

        $whatsappAccount->delete();
        return redirect()->back()->with('message', 'WhatsApp Account deleted successfully.');
    }

    public function syncAccount(WhatsappAccount $whatsappAccount, WhatsAppService $waService)
    {
        $waService->syncAccountStatus($whatsappAccount);
        return redirect()->back()->with('message', 'Account status synced.');
    }

    public function getQrCode(WhatsappAccount $whatsappAccount, WhatsAppService $waService)
    {
        if ($whatsappAccount->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        $result = $waService->getQrCode($whatsappAccount);
        return response()->json($result);
    }

    public function connectInstance(WhatsappAccount $whatsappAccount, WhatsAppService $waService)
    {
        if ($whatsappAccount->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        $result = $waService->getQrCode($whatsappAccount);
        return response()->json($result);
    }

    public function getInstanceStatus(WhatsappAccount $whatsappAccount, WhatsAppService $waService)
    {
        if ($whatsappAccount->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        $status = $waService->getInstanceStatus($whatsappAccount);
        return response()->json($status);
    }

    public function disconnectInstance(WhatsappAccount $whatsappAccount, WhatsAppService $waService)
    {
        if ($whatsappAccount->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        $result = $waService->disconnectInstance($whatsappAccount);
        return response()->json($result);
    }

    public function generateToken(Request $request)
    {
        $user = $request->user();
        // Simple logic: return a dummy token or integrate with a real token system
        $token = 'operra_live_' . bin2hex(random_bytes(16));
        
        return response()->json([
            'status' => true,
            'token' => $token,
            'message' => 'Token berhasil digenerate.'
        ]);
    }

    public function syncTemplates(WhatsappAccount $whatsappAccount, WhatsAppService $waService)
    {
        $result = $waService->fetchTemplates($whatsappAccount);

        if ($result['status']) {
            foreach ($result['data'] as $tpl) {
                if ($tpl['status'] !== 'APPROVED')
                    continue;

                WhatsappTemplate::updateOrCreate(
                    ['whatsapp_account_id' => $whatsappAccount->id, 'name' => $tpl['name']],
                    [
                        'company_id' => $whatsappAccount->company_id,
                        'language' => $tpl['language'],
                        'category' => $tpl['category'],
                        'components' => $tpl['components'],
                    ]
                );
            }
            return redirect()->back()->with('message', 'Templates synced successfully.');
        }

        return redirect()->back()->withErrors(['message' => 'Failed to sync templates: ' . $result['message']]);
    }

    public function syncFromMeta(WhatsAppService $waService)
    {
        $wabaId = Setting::get('meta_waba_id');
        $token = Setting::get('meta_access_token');

        if (!$wabaId || !$token) {
            return redirect()->back()->withErrors(['message' => 'WABA ID and Global Token are required for sync.']);
        }

        try {
            $response = \Illuminate\Support\Facades\Http::withToken($token)
                ->get("https://graph.facebook.com/v18.0/{$wabaId}/phone_numbers");

            if ($response->successful()) {
                $numbers = $response->json()['data'] ?? [];

                foreach ($numbers as $num) {
                    WhatsappAccount::updateOrCreate(
                        ['phone_number' => $num['id']], // Phone Number ID
                        [
                            'name' => $num['display_phone_number'] . ' (' . ($num['verified_name'] ?? 'Official') . ')',
                            'provider' => 'official',
                            'status' => 'active',
                            'is_verified' => ($num['code_verification_status'] === 'VERIFIED'),
                            'api_credentials' => ['token' => '', 'key' => '', 'endpoint' => ''],
                        ]
                    );
                }

                return redirect()->back()->with('message', count($numbers) . ' accounts synced from Meta.');
            }

            return redirect()->back()->withErrors(['message' => 'Meta API Error: ' . $response->body()]);

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'meta_access_token' => 'nullable|string',
            'meta_webhook_verify_token' => 'nullable|string',
            'meta_app_id' => 'nullable|string',
            'meta_waba_id' => 'nullable|string',
        ]);

        foreach ($validated as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value ?? '']);
        }

        return redirect()->back()->with('message', 'Global Meta configuration updated successfully.');
    }
}

<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected $baseUrl;
    protected $token;
    protected $key;
    protected $sender;
    protected $provider;

    public function __construct()
    {
        $settings = Setting::whereIn('key', [
            'meta_access_token',
            'meta_webhook_verify_token',
            'meta_app_id',
            'meta_waba_id'
        ])->pluck('value', 'key');

        $this->token = !empty($settings['meta_access_token']) ? $settings['meta_access_token'] : (config('services.whatsapp.meta_token') ?? env('WA_TOKEN'));
        $this->sender = config('services.whatsapp.meta_phone_number_id');
        $this->baseUrl = 'https://graph.facebook.com/v22.0';
        $this->provider = 'official';
    }

    /**
     * Mengirim pesan WhatsApp
     */
    public function sendMessage($to, $message, $account = null, $template = null, $templateData = [])
    {
        $token = ($account && !empty($account->api_credentials['token'])) ? $account->api_credentials['token'] : $this->token;
        $key = ($account && !empty($account->api_credentials['key'])) ? $account->api_credentials['key'] : $this->key;
        $sender = ($account && !empty($account->phone_number)) ? $account->phone_number : $this->sender;
        $baseUrl = ($account && !empty($account->api_credentials['endpoint'])) ? $account->api_credentials['endpoint'] : $this->baseUrl;
        $provider = $account ? $account->provider : $this->provider;

        if ($provider === 'third_party_api') $provider = 'generic';

        if (!$token) {
            return [
                'status' => false,
                'message' => 'WhatsApp API configuration (token) missing.'
            ];
        }

        try {
            if ($provider === 'fonnte' || $provider === 'internal') {
                return $this->sendInternal($to, $message, $account);
            } elseif ($provider === 'official') {
                return $this->sendOfficial($to, $message, $token, $baseUrl, $sender, $template, $templateData);
            }

            // Generic / Existing Logic
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'X-API-KEY' => $key,
            ])->post($baseUrl . '/messages', [
                'sender' => $sender,
                'to' => $to,
                'message' => $message,
            ]);

            if ($response->successful()) {
                return [
                    'status' => true,
                    'data' => $response->json()
                ];
            }

            return [
                'status' => false,
                'message' => 'Failed to send message: ' . $response->body()
            ];

        } catch (\Exception $e) {
            Log::error('WhatsApp Send Error: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    protected function sendFonnte($to, $message, $token, $baseUrl)
    {
        // For our internal gateway, we don't need 'token' from api_credentials
        // We rely on the internal mapping of accountId
        
        // However, this method signature is shared. We might need to pass the ACCOUNT ID here 
        // to know WHICH session to use.
        // The original architecture passed 'token' but maybe not the full account object to this protected method.
        // Check usage in sendMessage:
        // public function sendMessage($to, $message, $account = null, ...)
        
        // I will refactor sendMessage to call a new `sendInternal` method if provider is 'fonnte'
        return ['status' => false, 'message' => 'Redirected to Internal Gateway in Main Method']; 
    }

    protected function sendInternal($to, $message, $account)
    {
        if (!$account) return ['status' => false, 'message' => 'Account required for Internal Gateway'];

        try {
            $response = Http::post(config('services.whatsapp.gateway_url') . '/chat/send', [
                'accountId' => (string) $account->id,
                'to' => $to,
                'message' => $message
            ]);

            if ($response->successful()) {
                return ['status' => true, 'data' => $response->json()];
            }

            return ['status' => false, 'message' => 'Gateway Error: ' . $response->body()];

        } catch (\Exception $e) {
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    protected function sendOfficial($to, $message, $token, $baseUrl, $senderId, $template = null, $templateData = [])
    {
        // Meta Official menggunakan ID numerik sebagai senderId. 
        if (empty($senderId) || !is_numeric($senderId) || strlen($senderId) < 10) {
            $senderId = config('services.whatsapp.meta_phone_number_id');
        }

        // Pastikan baseUrl memiliki trailing slash jika ada, atau gunakan default Meta
        $base = rtrim($baseUrl ?: 'https://graph.facebook.com/v22.0', '/');
        
        // Endpoint Meta Official harus menyertakan Phone Number ID
        $endpoint = "{$base}/{$senderId}/messages";
        

        $payload = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $to,
        ];

        if ($template) {
            $payload['type'] = 'template';
            $payload['template'] = [
                'name' => $template,
                'language' => ['code' => 'id'],
                'components' => $templateData ?: [],
            ];
        } else {
            $payload['type'] = 'text';
            $payload['text'] = [
                'preview_url' => false,
                'body' => $message,
            ];
        }


        $response = Http::withToken($token)->post($endpoint, $payload);

        if ($response->successful()) {
            return [
                'status' => true,
                'data' => $response->json()
            ];
        }

        return [
            'status' => false,
            'message' => 'Official API Error: ' . $response->body()
        ];
    }

    public function fetchTemplates($account)
    {
        $token = !empty($account->api_credentials['token']) ? $account->api_credentials['token'] : $this->token;
        $wabaId = !empty($account->api_credentials['key']) ? $account->api_credentials['key'] : (Setting::get('meta_waba_id') ?? config('services.whatsapp.meta_waba_id'));
        
        if (!$wabaId) return ['status' => false, 'message' => 'WABA ID not configured.'];

        try {
            $response = Http::withToken($token)->get("https://graph.facebook.com/v22.0/{$wabaId}/message_templates");
            
            if ($response->successful()) {
                return [
                    'status' => true,
                    'data' => $response->json()['data'] ?? []
                ];
            }
            return ['status' => false, 'message' => $response->body()];
        } catch (\Exception $e) {
            Log::error('WhatsApp Fetch Templates Error: ' . $e->getMessage());
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    public function markAsRead($messageId, $account)
    {
        if ($account->provider !== 'official') {
            return ['status' => true];
        }

        $token = !empty($account->api_credentials['token']) ? $account->api_credentials['token'] : $this->token;
        $senderId = !empty($account->phone_number) ? $account->phone_number : $this->sender;
        $endpoint = "https://graph.facebook.com/v22.0/{$senderId}/messages";

        try {
            $response = Http::withToken($token)->post($endpoint, [
                'messaging_product' => 'whatsapp',
                'status' => 'read',
                'message_id' => $messageId,
            ]);

            return [
                'status' => $response->successful(),
                'data' => $response->json()
            ];
        } catch (\Exception $e) {
            Log::error('WhatsApp MarkAsRead Error: ' . $e->getMessage());
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    public function downloadMedia($mediaId, $account)
    {
        $token = !empty($account->api_credentials['token']) ? $account->api_credentials['token'] : $this->token;
        
        try {
            $response = Http::withToken($token)->get("https://graph.facebook.com/v22.0/{$mediaId}");
            if (!$response->successful()) return null;
            
            $mediaUrl = $response->json()['url'];
            $mediaResponse = Http::withToken($token)->get($mediaUrl);
            
            if (!$mediaResponse->successful()) return null;
            
            $content = $mediaResponse->body();
            $mimeType = $mediaResponse->header('Content-Type');
            $extension = explode('/', $mimeType)[1] ?? 'bin';
            
            $fileName = "whatsapp/media/{$mediaId}.{$extension}";
            \Illuminate\Support\Facades\Storage::disk('public')->put($fileName, $content);
            
            return $fileName;
        } catch (\Exception $e) {
            Log::error('WhatsApp Media Download Error: ' . $e->getMessage());
            return null;
        }
    }

    public function syncAccountStatus($account)
    {
        $token = !empty($account->api_credentials['token']) ? $account->api_credentials['token'] : $this->token;
        $key = $account->api_credentials['key'] ?? $this->key;
        $baseUrl = $account->api_credentials['endpoint'] ?? $this->baseUrl;
        $provider = $account->provider;

        if (empty($token) && $provider !== 'official') return false;

        try {
            if ($provider === 'fonnte') {
                $response = Http::withHeaders(['Authorization' => $token])
                    ->post('https://api.fonnte.com/device');
                
                if ($response->successful()) {
                    $data = $response->json();
                    $account->update([
                        'status' => ($data['status'] ?? '') === 'connect' ? 'active' : 'disconnected',
                    ]);
                    return true;
                }
            } elseif ($provider === 'internal') {
                $statusData = $this->getInstanceStatus($account);
                return $statusData['status'] === 'connected';
            } elseif ($provider === 'official') {
                $account->update([
                    'status' => 'active',
                    'is_verified' => true,
                ]);
                return true;
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'X-API-KEY' => $key,
            ])->get($baseUrl . '/account-info');

            if ($response->successful()) {
                $data = $response->json();
                $account->update([
                    'is_verified' => $data['is_official_account'] ?? $data['verified'] ?? false,
                    'status' => 'active'
                ]);
                return true;
            }

            $account->update(['status' => 'disconnected']);
            return false;

        } catch (\Exception $e) {
            Log::error('WhatsApp Sync Error: ' . $e->getMessage());
            return false;
        }
    }

    public function getQrCode($account)
    {
        // Call Local Node.js Gateway
        try {
            $response = Http::post(config('services.whatsapp.gateway_url') . '/session/start', [
                'accountId' => (string) $account->id // Ensure string format if needed
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'status' => true,
                    'qr' => $data['qr'],
                    'message' => 'Silakan scan QR Code yang muncul.'
                ];
            }

            return ['status' => false, 'message' => 'Gagal memulai sesi: ' . $response->body()];

        } catch (\Exception $e) {
            Log::error('WhatsApp Node QR Error: ' . $e->getMessage());
            return ['status' => false, 'message' => 'Gagal menghubungi Gateway Lokal. Pastikan service berjalan.'];
        }
    }

    public function getInstanceStatus($account)
    {
        try {
            $response = Http::timeout(3)->get(config('services.whatsapp.gateway_url') . '/session/status/' . $account->id);
            
            if ($response->successful()) {
                $data = $response->json();
                
                if ($data['status'] === 'connected') {
                    if ($account->status !== 'active') {
                        $account->update(['status' => 'active', 'name' => $data['name'] ?? $account->name]);
                    }
                    return $data;
                }
            }

            return ['status' => 'waiting_scan'];

        } catch (\Exception $e) {
            // If service is down or other error
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function disconnectInstance($account)
    {
        if ($account->provider === 'official') {
            $account->update(['status' => 'disconnected']);
            return ['status' => true, 'message' => 'Sesi Official diputuskan secara lokal.'];
        }

        try {
            $response = Http::post(config('services.whatsapp.gateway_url') . '/session/terminate/' . $account->id);
            
            if ($response->successful() || $response->status() === 404) {
                 $account->update(['status' => 'disconnected']);
                 return ['status' => true, 'message' => 'Berhasil memutuskan koneksi.'];
            }
            
            return ['status' => false, 'message' => 'Gagal memutuskan koneksi: ' . $response->body()];
        } catch (\Exception $e) {
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }
}

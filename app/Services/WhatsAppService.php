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
        $this->baseUrl = 'https://graph.facebook.com/v22.0';
        $this->provider = 'official';
    }

    public function sendMessage($to, $message, $account = null, $template = null, $templateData = [])
    {
        // Mengambil konfigurasi dari .env
        $token = env('WA_TOKEN');
        $sender = env('WA_ID');
        $baseUrl = 'https://graph.facebook.com/v22.0';

        // Fallback jika env kosong (Optional check)
        if (empty($token) || empty($sender)) {
            Log::error('WhatsAppService: Token atau Sender ID belum diset di .env');
            return ['status' => false, 'message' => 'Konfigurasi WA belum lengkap'];
        }
        
        Log::info('WS Debug - Sending Message', [
            'to' => $to,
            'sender_id' => $sender,
            'token_prefix' => substr($token, 0, 10) . '...'
        ]);

        return $this->sendOfficial($to, $message, $token, $baseUrl, $sender, $template, $templateData);
    }

    protected function sendOfficial($to, $message, $token, $baseUrl, $senderId, $template = null, $templateData = [])
    {
        $endpoint = "{$baseUrl}/{$senderId}/messages";

        $payload = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $to,
            'type' => 'text',
            'text' => ['body' => $message]
        ];

        $response = Http::withToken($token)->post($endpoint, $payload);

        if ($response->successful()) {
            return ['status' => true, 'data' => $response->json()];
        }

        Log::error('WS Debug - Meta API Error', ['body' => $response->json()]);
        return ['status' => false, 'message' => $response->body()];
    }

    // Placeholder untuk kompatibilitas
    protected function sendFonnte($to, $message, $token) { return ['status' => false]; }
    protected function sendInternal($to, $message, $token, $baseUrl, $key) { return ['status' => false]; }
}

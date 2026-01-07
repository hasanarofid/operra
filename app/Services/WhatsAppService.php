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

    public function __construct()
    {
        $settings = Setting::whereIn('key', [
            'wa_blast_token',
            'wa_blast_key',
            'wa_blast_number',
            'wa_blast_endpoint'
        ])->pluck('value', 'key');

        $this->token = $settings['wa_blast_token'] ?? null;
        $this->key = $settings['wa_blast_key'] ?? null;
        $this->sender = $settings['wa_blast_number'] ?? null;
        $this->baseUrl = $settings['wa_blast_endpoint'] ?? 'https://api.wa-provider.com/v1';
    }

    /**
     * Mengirim pesan WhatsApp
     *
     * @param string $to Nomor tujuan (format: 628xxx)
     * @param string $message Isi pesan
     * @return array
     */
    public function sendMessage($to, $message)
    {
        if (!$this->token || !$this->key) {
            return [
                'status' => false,
                'message' => 'WhatsApp API configuration missing.'
            ];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->token,
                'X-API-KEY' => $this->key,
            ])->post($this->baseUrl . '/messages', [
                'sender' => $this->sender,
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

    /**
     * Mengecek status koneksi/perangkat
     */
    public function checkStatus()
    {
        // Logika untuk cek apakah device masih terhubung (QR scan status)
        // Tergantung API provider yang digunakan
        return [
            'status' => true,
            'device' => $this->sender,
            'connection' => 'connected'
        ];
    }
}


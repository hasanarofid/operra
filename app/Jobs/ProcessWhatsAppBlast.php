<?php

namespace App\Jobs;

use App\Models\WhatsappCampaign;
use App\Models\WhatsappAccount;
use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessWhatsAppBlast implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $campaignId;

    /**
     * Create a new job instance.
     */
    public function __construct($campaignId)
    {
        $this->campaignId = $campaignId;
    }

    /**
     * Execute the job.
     */
    public function handle(WhatsAppService $whatsappService): void
    {
        Log::info("Job Blast Started for Campaign ID: {$this->campaignId}");

        $campaign = WhatsappCampaign::with('account')->find($this->campaignId);

        if (!$campaign) {
            Log::error("Campaign {$this->campaignId} not found.");
            return;
        }

        if ($campaign->status === 'completed') {
            Log::info("Campaign {$this->campaignId} already completed.");
            return;
        }

        // Status is already set to 'processing' by controller, but safe to ensure
        $campaign->update(['status' => 'processing']);

        $logs = $campaign->logs()->where('status', 'pending')->get();
        $account = $campaign->account;

        if (!$account || $account->status !== 'active') {
             Log::error("Account for Campaign {$this->campaignId} is invalid or inactive.");
             $campaign->update(['status' => 'failed']); // Or partial
             return;
        }

        foreach ($logs as $log) {
            // Rate Limiting Prevention (Optional: Sleep 1-2 seconds)
            // sleep(1); 

            try {
                $result = $whatsappService->sendMessage(
                    $log->phone_number,
                    $campaign->message_template,
                    $account,
                    $campaign->template_name,
                    $campaign->template_data
                );

                if ($result['status']) {
                    $log->update(['status' => 'sent', 'sent_at' => now()]);
                    $campaign->increment('sent_count');
                } else {
                    $log->update(['status' => 'failed', 'error_message' => $result['message']]);
                    $campaign->increment('failed_count');
                }
            } catch (\Exception $e) {
                Log::error("Error sending to {$log->phone_number}: " . $e->getMessage());
                $log->update(['status' => 'failed', 'error_message' => 'System Error: ' . $e->getMessage()]);
                $campaign->increment('failed_count');
            }
        }

        $campaign->update(['status' => 'completed']);
        Log::info("Job Blast Completed for Campaign ID: {$this->campaignId}");
    }
}

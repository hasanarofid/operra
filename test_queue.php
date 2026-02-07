<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

echo "Bootstrapped!\n";

try {
    // Fetch valid account
    $account = App\Models\WhatsappAccount::first();
    if (!$account) die("No WhatsApp Account found!\n");

    // Create Dummy Campaign
    $campaign = App\Models\WhatsappCampaign::create([
        'name' => 'Queue Test ' . time(),
        'whatsapp_account_id' => $account->id,
        'company_id' => $account->company_id, // Add this
        'status' => 'draft',
        'message_template' => 'Queue Test Message',
        'total_recipients' => 1
    ]);
    
    // Create Dummy Log
    $campaign->logs()->create([
        'customer_id' => 1,
        'phone_number' => '62881026697527', // Use valid number
        'status' => 'pending'
    ]);

    echo "Campaign Created: ID {$campaign->id}\n";
    
    // Dispatch
    App\Jobs\ProcessWhatsAppBlast::dispatch($campaign->id);
    
    echo "Job Dispatched!\n";
    
} catch (\Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}

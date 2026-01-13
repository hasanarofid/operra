<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketingBlast extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'marketing_campaign_id',
        'subject',
        'content',
        'channel',
        'status',
        'scheduled_at',
        'sent_at',
        'total_recipients',
        'success_count',
        'failed_count',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(MarketingCampaign::class, 'marketing_campaign_id');
    }
}

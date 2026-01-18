<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WhatsappCampaign extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'whatsapp_account_id',
        'name',
        'message_template',
        'template_name',
        'template_data',
        'status',
        'scheduled_at',
        'total_recipients',
        'sent_count',
        'failed_count',
    ];

    protected $casts = [
        'template_data' => 'array',
        'scheduled_at' => 'datetime',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(WhatsappAccount::class, 'whatsapp_account_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(WhatsappCampaignLog::class);
    }
}


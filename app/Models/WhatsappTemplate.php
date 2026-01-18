<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsappTemplate extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'whatsapp_account_id',
        'name',
        'language',
        'category',
        'components',
        'is_active',
    ];

    protected $casts = [
        'components' => 'array',
        'is_active' => 'boolean',
    ];

    public function whatsappAccount(): BelongsTo
    {
        return $this->belongsTo(WhatsappAccount::class);
    }
}


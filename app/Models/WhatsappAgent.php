<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsappAgent extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'user_id',
        'whatsapp_account_id',
        'is_available',
        'last_assigned_at',
        'active_chats_count',
        'company_id',
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'last_assigned_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function whatsappAccount(): BelongsTo
    {
        return $this->belongsTo(WhatsappAccount::class);
    }
}

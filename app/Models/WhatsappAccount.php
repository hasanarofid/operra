<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WhatsappAccount extends Model
{
    protected $fillable = [
        'name',
        'phone_number',
        'provider',
        'api_credentials',
        'is_verified',
        'status',
    ];

    protected $casts = [
        'api_credentials' => 'array',
        'is_verified' => 'boolean',
    ];

    public function agents(): HasMany
    {
        return $this->hasMany(WhatsappAgent::class);
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(ChatSession::class);
    }
}

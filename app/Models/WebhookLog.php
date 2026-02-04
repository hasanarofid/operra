<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebhookLog extends Model
{
    protected $fillable = [
        'provider',
        'payload',
        'headers',
        'sender_ip',
        'status_code',
    ];

    protected $casts = [
        'payload' => 'array',
        'headers' => 'array',
    ];
}

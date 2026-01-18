<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class ExternalApp extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'name',
        'app_key',
        'app_secret',
        'webhook_url',
        'widget_settings',
        'is_active',
    ];

    protected $casts = [
        'widget_settings' => 'array',
        'is_active' => 'boolean',
    ];
}


<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class MarketingAutomation extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'name',
        'trigger_event',
        'actions',
        'is_active',
    ];

    protected $casts = [
        'actions' => 'array',
        'is_active' => 'boolean',
    ];
}

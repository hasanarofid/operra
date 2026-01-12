<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PricingPlan extends Model
{
    protected $fillable = [
        'name', 'slug', 'price', 'billing_cycle', 'features', 'is_popular', 'badge', 'cta_text'
    ];

    protected $casts = [
        'features' => 'array',
        'is_popular' => 'boolean'
    ];
}

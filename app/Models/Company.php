<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'logo',
        'address',
        'phone',
        'email',
        'status',
        'enabled_modules',
        'pricing_plan_id',
        'subscription_ends_at',
        'is_system_owner',
    ];

    protected $casts = [
        'enabled_modules' => 'array',
        'subscription_ends_at' => 'datetime',
        'is_system_owner' => 'boolean',
    ];

    public function plan(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(PricingPlan::class, 'pricing_plan_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function isModuleEnabled(string $module): bool
    {
        if (is_null($this->enabled_modules)) {
            return false;
        }
        return in_array($module, $this->enabled_modules);
    }
}

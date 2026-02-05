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

    public function getLimit(string $key)
    {
        $plan = $this->plan;
        if (!$plan) return 0;

        switch ($key) {
            case 'wa_accounts':
                if ($plan->slug === 'starter') return 1;
                if ($plan->slug === 'business-pro') return 5;
                return 999;
            case 'agents':
                if ($plan->slug === 'starter') return 2;
                if ($plan->slug === 'business-pro') return 999;
                return 999;
            default:
                return 0;
        }
    }

    public function canAddWaAccount(): bool
    {
        $limit = $this->getLimit('wa_accounts');
        // Count only accounts that are NOT disconnected (active or inactive/pending)
        $current = WhatsappAccount::where('company_id', $this->id)
            ->where('status', '!=', 'disconnected')
            ->count();
        return $current < $limit;
    }

    public function canAddAgent(): bool
    {
        $limit = $this->getLimit('agents');
        // Agents are users with a role other than super-admin/manager usually, 
        // but here we check WhatsappAgent records.
        $current = WhatsappAgent::whereHas('user', function($q) {
            $q->where('company_id', $this->id);
        })->count();
        return $current < $limit;
    }
}

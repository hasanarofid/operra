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
    ];

    protected $casts = [
        'enabled_modules' => 'array',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
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

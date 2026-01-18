<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomerStatus extends Model
{
    use BelongsToCompany;

    protected $fillable = ['company_id', 'name', 'color', 'order'];

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }
}


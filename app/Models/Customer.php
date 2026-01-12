<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'status',
        'lead_source',
        'assigned_to',
        'company_id',
    ];

    public function assignedSales()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function chatSessions()
    {
        return $this->hasMany(ChatSession::class);
    }
}

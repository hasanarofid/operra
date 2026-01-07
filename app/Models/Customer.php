<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'status',
        'lead_source',
        'assigned_to',
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

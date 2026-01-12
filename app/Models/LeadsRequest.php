<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadsRequest extends Model
{
    protected $fillable = [
        'name', 'company_name', 'email', 'phone', 'business_type', 'message', 'status'
    ];
}

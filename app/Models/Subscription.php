<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = ['plan_name', 'monthly_price', 'active_customers', 'mrr'];
}

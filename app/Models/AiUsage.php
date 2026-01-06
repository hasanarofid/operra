<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiUsage extends Model
{
    protected $fillable = ['model_name', 'tokens_used', 'estimated_cost', 'usage_time'];
}

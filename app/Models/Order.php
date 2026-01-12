<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use BelongsToCompany;

    protected $fillable = ['order_number', 'customer_name', 'total_amount', 'status', 'company_id'];
}

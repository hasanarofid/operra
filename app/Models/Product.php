<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use BelongsToCompany;

    protected $fillable = ['name', 'sku', 'description', 'purchase_price', 'selling_price', 'min_stock', 'company_id'];

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function getCurrentStockAttribute()
    {
        return $this->stockMovements()->sum('quantity');
    }
}

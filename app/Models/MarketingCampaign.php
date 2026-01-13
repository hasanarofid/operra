<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MarketingCampaign extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'name',
        'description',
        'type',
        'status',
        'start_date',
        'end_date',
        'target_audience_count',
    ];

    public function blasts(): HasMany
    {
        return $this->hasMany(MarketingBlast::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PipelineStage extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'position',
        'color',
        'probability',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(SalesOrder::class, 'stage_id');
    }
}

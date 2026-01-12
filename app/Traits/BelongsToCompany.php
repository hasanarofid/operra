<?php

namespace App\Traits;

use App\Models\Company;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait BelongsToCompany
{
    protected static function bootBelongsToCompany()
    {
        static::creating(function ($model) {
            if (Auth::check() && !isset($model->company_id)) {
                $model->company_id = Auth::user()->company_id;
            }
        });

        static::addGlobalScope('company', function (Builder $builder) {
            if (Auth::check() && Auth::user()->company_id) {
                $builder->where($builder->getQuery()->from . '.company_id', Auth::user()->company_id);
            }
        });
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}


<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class SupportKnowledgeBase extends Model
{
    use BelongsToCompany;

    protected $table = 'support_knowledge_base';

    protected $fillable = [
        'company_id',
        'title',
        'slug',
        'content',
        'category',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];
}

<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsappAutoReply extends Model
{
    use HasFactory, BelongsToCompany;

    protected $fillable = [
        'company_id',
        'whatsapp_account_id',
        'keyword',
        'response',
        'match_type',
        'is_active',
    ];

    public function whatsappAccount()
    {
        return $this->belongsTo(WhatsappAccount::class);
    }
}

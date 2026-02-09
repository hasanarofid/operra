<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminTicket extends Model
{
    protected $fillable = ['user_id', 'company_id', 'subject', 'status', 'priority'];

    public function messages()
    {
        return $this->hasMany(AdminTicketMessage::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminTicketMessage extends Model
{
    protected $fillable = ['admin_ticket_id', 'user_id', 'message', 'attachment_path'];

    public function ticket()
    {
        return $this->belongsTo(AdminTicket::class, 'admin_ticket_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

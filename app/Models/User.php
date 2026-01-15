<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'company_id',
        'has_completed_onboarding',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function whatsappAgents()
    {
        return $this->hasMany(WhatsappAgent::class);
    }

    public function assignedCustomers()
    {
        return $this->hasMany(Customer::class, 'assigned_to');
    }

    public function assignedChats()
    {
        return $this->hasMany(ChatSession::class, 'assigned_user_id');
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'has_completed_onboarding' => 'boolean',
        ];
    }
}

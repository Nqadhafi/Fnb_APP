<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone','role','is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    
    public const ROLE_ADMIN = 'admin';
    public const ROLE_USER  = 'user';

    public function scopeAdmins($q){ return $q->where('role', self::ROLE_ADMIN); }
    public function scopeActive($q){ return $q->where('is_active', true); }

    // Relasi yang relevan
    public function openedTableSessions() { return $this->hasMany(TableSession::class, 'opened_by'); }
    public function openedPosSessions()   { return $this->hasMany(PosSession::class, 'opened_by'); }
    public function closedPosSessions()   { return $this->hasMany(PosSession::class, 'closed_by'); }
    public function verifiedPayments()    { return $this->hasMany(OrderPayment::class, 'verified_by'); }
    public function orders()              { return $this->hasMany(Order::class); }
    public function carts()               { return $this->hasMany(Cart::class); }

    public function isAdmin(): bool { return $this->role === self::ROLE_ADMIN; }
}

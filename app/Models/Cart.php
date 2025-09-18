<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cart extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id','session_id','table_session_id','status',
        'subtotal','discount_total','grand_total'
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_total' => 'decimal:2',
        'grand_total' => 'decimal:2',
    ];

    public const ST_ACTIVE    = 'active';
    public const ST_CONVERTED = 'converted';
    public const ST_ABANDONED = 'abandoned';

    public function user(){ return $this->belongsTo(User::class); }
    public function tableSession(){ return $this->belongsTo(TableSession::class); }
    public function items(){ return $this->hasMany(CartItem::class); }

    public function scopeActive($q){ return $q->where('status', self::ST_ACTIVE); }
}

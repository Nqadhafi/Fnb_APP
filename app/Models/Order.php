<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code','receipt_no','order_type','status',
        'user_id','table_session_id','cart_id','pos_session_id',
        'subtotal','discount_total','service_charge','tax_total','grand_total',
        'payment_method','paid_at','notes'
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_total' => 'decimal:2',
        'service_charge' => 'decimal:2',
        'tax_total' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public const TYPE_DINEIN  = 'dine_in';
    public const TYPE_TAKEAWAY= 'takeaway';

    public const ST_OPEN      = 'open';
    public const ST_PENDING   = 'pending';
    public const ST_PAID      = 'paid';
    public const ST_PREP      = 'preparing';
    public const ST_READY     = 'ready';
    public const ST_SERVED    = 'served';
    public const ST_DONE      = 'completed';
    public const ST_CANCEL    = 'cancelled';

    // Hooks: generate code & receipt_no kalau kosong
    protected static function booted()
    {
        static::creating(function (self $order) {
            if (!$order->code) {
                $order->code = 'ORD-'.now()->format('Ymd').'-'.Str::padLeft((string) random_int(1, 9999), 4, '0');
            }
            if (!$order->receipt_no) {
                $order->receipt_no = 'RC'.now()->format('ymd').Str::padLeft((string) random_int(1, 99999), 5, '0');
            }
        });
    }

    // Relasi
    public function user(){ return $this->belongsTo(User::class); }
    public function tableSession(){ return $this->belongsTo(TableSession::class); }
    public function cart(){ return $this->belongsTo(Cart::class); }
    public function posSession(){ return $this->belongsTo(PosSession::class); }

    public function items(){ return $this->hasMany(OrderItem::class); }
    public function payments(){ return $this->hasMany(OrderPayment::class); }

    public function scopeStatus($q, string $st){ return $q->where('status', $st); }
    public function scopeToday($q){ return $q->whereDate('created_at', today()); }
}

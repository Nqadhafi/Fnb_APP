<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id','status','method','amount','paid_at',
        'proof_path','proof_disk',
        'cash_received','change_given',
        'verified_by','verified_at','notes'
    ];

    protected $casts = [
        'amount'        => 'decimal:2',
        'cash_received' => 'decimal:2',
        'change_given'  => 'decimal:2',
        'paid_at'       => 'datetime',
        'verified_at'   => 'datetime',
    ];

    public const ST_PENDING  = 'pending';
    public const ST_VERIFIED = 'verified';
    public const ST_REJECTED = 'rejected';

    public function order()    { return $this->belongsTo(Order::class); }
    public function verifier() { return $this->belongsTo(User::class, 'verified_by'); }
}

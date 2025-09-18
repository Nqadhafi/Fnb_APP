<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PosSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'counter_name','opened_by','opened_at','opening_float',
        'closed_by','closed_at',
        'total_transactions','cash_total','noncash_total','expected_cash','actual_cash','cash_variance',
        'notes'
    ];

    protected $casts = [
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
        'opening_float' => 'decimal:2',
        'cash_total' => 'decimal:2',
        'noncash_total' => 'decimal:2',
        'expected_cash' => 'decimal:2',
        'actual_cash' => 'decimal:2',
        'cash_variance' => 'decimal:2',
    ];

    public function opener(){ return $this->belongsTo(User::class, 'opened_by'); }
    public function closer(){ return $this->belongsTo(User::class, 'closed_by'); }
    public function orders(){ return $this->hasMany(Order::class); }

    public function scopeOpen($q){ return $q->whereNull('closed_at'); }
}

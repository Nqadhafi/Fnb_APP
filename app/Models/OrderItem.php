<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id','product_id','product_name','unit_price','qty','selected_options','notes',
        'prep_status','line_total'
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'qty' => 'integer',
        'selected_options' => 'array',
        'line_total' => 'decimal:2',
    ];

    public const PS_QUEUED   = 'queued';
    public const PS_PREP     = 'preparing';
    public const PS_READY    = 'ready';
    public const PS_SERVED   = 'served';
    public const PS_VOID     = 'void';

    public function order(){ return $this->belongsTo(Order::class); }
    public function product(){ return $this->belongsTo(Product::class); }
}

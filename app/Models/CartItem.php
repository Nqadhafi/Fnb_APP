<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id','product_id','product_name','unit_price','qty','selected_options','notes'
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'qty' => 'integer',
        'selected_options' => 'array',
    ];

    public function cart(){ return $this->belongsTo(Cart::class); }
    public function product(){ return $this->belongsTo(Product::class); }
}

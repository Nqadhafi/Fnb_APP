<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TableSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'dining_table_id','opened_by','guest_count','opened_at','closed_at','notes'
    ];

    protected $casts = [
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function table(){ return $this->belongsTo(DiningTable::class, 'dining_table_id'); }
    public function opener(){ return $this->belongsTo(User::class, 'opened_by'); }
    public function carts(){ return $this->hasMany(Cart::class); }
    public function orders(){ return $this->hasMany(Order::class); }

    public function scopeActive($q){ return $q->whereNull('closed_at'); }
}

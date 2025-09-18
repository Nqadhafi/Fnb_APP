<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id','name','slug','sku','description',
        'price','discount_price','stock','is_active',
        'options_schema',
        'main_image_path','main_image_disk',
    ];

    protected $casts = [
        'price'          => 'decimal:2',
        'discount_price' => 'decimal:2',
        'is_active'      => 'boolean',
        'options_schema' => 'array',
    ];

    // Relasi
    public function category()   { return $this->belongsTo(Category::class); }
    public function images()     { return $this->hasMany(ProductImage::class); }
    public function orderItems() { return $this->hasMany(OrderItem::class); }

    // Helper harga final (tanpa accessor URL)
    public function getFinalPriceAttribute(): string
    {
        return (string) ($this->discount_price ?? $this->price);
    }
        public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

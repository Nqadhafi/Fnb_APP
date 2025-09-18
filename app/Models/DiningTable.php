<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DiningTable extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['code','name','capacity','status'];

    public const ST_AVAILABLE = 'available';
    public const ST_OCCUPIED  = 'occupied';
    public const ST_RESERVED  = 'reserved';
    public const ST_DISABLED  = 'disabled';

    public function sessions(){ return $this->hasMany(TableSession::class); }

    public function scopeAvailable($q){ return $q->where('status', self::ST_AVAILABLE); }
}

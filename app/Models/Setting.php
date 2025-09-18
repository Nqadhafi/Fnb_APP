<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = ['key','value','type'];

    protected $casts = [
        'value' => 'string', // jika JSON, decode manual saat ambil
    ];

    public static function get(string $key, $default = null)
    {
        $row = static::query()->where('key', $key)->first();
        if (!$row) return $default;
        if ($row->type === 'json') {
            return json_decode($row->value ?? 'null', true) ?? $default;
        }
        if ($row->type === 'number') {
            return is_numeric($row->value) ? +$row->value : $default;
        }
        if ($row->type === 'bool') {
            return filter_var($row->value, FILTER_VALIDATE_BOOLEAN);
        }
        return $row->value ?? $default;
    }
}

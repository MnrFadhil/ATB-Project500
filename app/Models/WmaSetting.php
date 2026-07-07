<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WmaSetting extends Model
{
    protected $fillable = ['key', 'w1', 'w2', 'w3'];

    public static function getWeights(string $key, array $default): array
    {
        $row = static::where('key', $key)->first();
        return $row ? [(int) $row->w1, (int) $row->w2, (int) $row->w3] : $default;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitOperation extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'shift_id',
        'barscreen',
        'finescreen_a',
        'finescreen_b',
        'compressor_a',
        'compressor_b',
        'air_drayer',
    ];
}

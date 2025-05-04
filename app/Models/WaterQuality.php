<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WaterQuality extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'shift_id',
        'type',
        'ph',
        'turbidity',
        'color',
        'tds',
        'free_chlor',
        'orp',
    ];
}

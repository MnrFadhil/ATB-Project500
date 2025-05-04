<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FlowMeter extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'shift_id',
        'type',
        'location',
        'flow',
        'totalizer',
    ];
}

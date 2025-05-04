<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PumpProccess extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'shift_id',
        'type',
        'status',
        'name',
        'ampere',
        'frequency',
        'pressure',
    ];
}

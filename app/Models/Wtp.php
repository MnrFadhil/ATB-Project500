<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wtp extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'shift_id',
        'flokulator_a',
        'flokulator_b',
        'clarifier_a',
        'clarifier_b',
        'gravity_filter_a',
        'gravity_filter_b',
        'gravity_filter_c',
        'gravity_filter_d',
        'gravity_filter_e',
        'gravity_filter_f',
    ];
}

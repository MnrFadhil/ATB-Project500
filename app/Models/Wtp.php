<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Wtp extends Model
{
    use HasUuids, SoftDeletes;

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
        'filtration',
        'gravity_filter_a',
        'gravity_filter_b',
        'gravity_filter_c',
        'gravity_filter_d',
        'gravity_filter_e',
        'gravity_filter_f',
        'gravity_filter_a_status',
        'gravity_filter_b_status',
        'gravity_filter_c_status',
        'gravity_filter_d_status',
        'gravity_filter_e_status',
        'gravity_filter_f_status',
    ];
}

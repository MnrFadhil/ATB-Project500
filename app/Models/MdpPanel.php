<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MdpPanel extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'shift_id',
        'kwh_total',
        'wdp',
        'lwbp',
        'kvar',
    ];
}

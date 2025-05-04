<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PumpChemical extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'shift_id',
        'type',
        'frequency',
        'dosage',
        'concentration',
        'stirring',
        'tank_level',
        'flow_rate',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shift extends Model
{
    use HasUuids, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'date',
        'shift',
        'notes',
        'start_date',
        'start_time',
        'end_time',
        'collection_tank'
    ];

    /* -------------------------------------------------------------------------- */
    /*                                Relationship                                */
    /* -------------------------------------------------------------------------- */

    // Operators (One to Many)
    public function shiftOperatorsCreate(): HasMany
    {
        return $this->hasMany(User::class, 'shift_operators', 'shift_id', 'user_id');
    }

    // Operators (Many to Many)
    public function shiftOperators()
    {
        return $this->belongsToMany(User::class, 'shift_operators', 'shift_id', 'user_id');
    }

    // Water Quality (One to Many)
    public function waterQualities(): HasMany
    {
        return $this->hasMany(WaterQuality::class, 'shift_id', 'id');
    }

    // Flow Meter (One to Many)
    public function flowMeters(): HasMany
    {
        return $this->hasMany(FlowMeter::class, 'shift_id', 'id');
    }

    // Reservoir Level (One to One)
    public function reservoirLevels(): HasOne
    {
        return $this->hasOne(ReservoirLevel::class, 'shift_id', 'id');
    }

    // MDP Panels Level (One to One)
    public function mdpPanels(): HasOne
    {
        return $this->hasOne(MdpPanel::class, 'shift_id', 'id');
    }

    // Pump Proccess (One to Many)
    public function pumpProccess(): HasMany
    {
        return $this->hasMany(PumpProccess::class, 'shift_id', 'id');
    }

    // Pump Chemicals (One to Many)
    public function pumpChemicals(): HasMany
    {
        return $this->hasMany(PumpChemical::class, 'shift_id', 'id');
    }

    // Unit Operation (One to Many)
    public function unitOperation(): HasOne
    {
        return $this->hasOne(UnitOperation::class, 'shift_id', 'id');
    }

    // WTPS (One to Many)
    public function wtps(): HasOne
    {
        return $this->hasOne(Wtp::class, 'shift_id', 'id');
    }

    // Pressure Static Mixer (One to One)
    public function pressureStaticMixer(): HasOne
    {
        return $this->hasOne(PressureStaticMixer::class, 'shift_id', 'id');
    }
}

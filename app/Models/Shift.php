<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
    public function shiftOperators(): HasMany
    {
        return $this->hasMany(ShiftOperator::class, 'shift_id', 'id');
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

    // Reservoir Level (One to Many)
    public function reservoirLevels(): HasMany
    {
        return $this->hasMany(ReservoirLevel::class, 'shift_id', 'id');
    }

    // MDP Panels Level (One to Many)
    public function mdpPanels(): HasMany
    {
        return $this->hasMany(MdpPanel::class, 'shift_id', 'id');
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
    public function unitOperation(): HasMany
    {
        return $this->hasMany(UnitOperation::class, 'shift_id', 'id');
    }

    // WTPS (One to Many)
    public function wtps(): HasMany
    {
        return $this->hasMany(Wtp::class, 'shift_id', 'id');
    }

    // Pressure Static Mixer (One to Many)
    public function pressureStaticMixer(): HasMany
    {
        return $this->hasMany(PressureStaticMixer::class, 'shift_id', 'id');
    }
}

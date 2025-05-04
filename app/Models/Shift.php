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
    ];

    /* -------------------------------------------------------------------------- */
    /*                                Relationship                                */
    /* -------------------------------------------------------------------------- */

    // Operators (One to Many)
    public function shiftOperators(): HasMany
    {
        return $this->hasMany(ShiftOperator::class);
    }

    // Water Quality (One to Many)
    public function waterQualities(): HasMany
    {
        return $this->hasMany(WaterQuality::class);
    }

    // Flow Meter (One to Many)
    public function flowMeters(): HasMany
    {
        return $this->hasMany(FlowMeter::class);
    }

    // Reservoir Level (One to Many)
    public function reservoirLevels(): HasMany
    {
        return $this->hasMany(ReservoirLevel::class);
    }

    // MDP Panels Level (One to Many)
    public function mdpPanels(): HasMany
    {
        return $this->hasMany(MdpPanel::class);
    }

    // Pump Proccess (One to Many)
    public function pumpProccess(): HasMany
    {
        return $this->hasMany(PumpProccess::class);
    }

    // Pump Chemicals (One to Many)
    public function pumpChemicals(): HasMany
    {
        return $this->hasMany(PumpChemical::class);
    }

    // Unit Operation (One to Many)
    public function unitOperation(): HasMany
    {
        return $this->hasMany(UnitOperation::class);
    }

    // WTPS (One to Many)
    public function wtps(): HasMany
    {
        return $this->hasMany(Wtp::class);
    }

    // Pressure Static Mixer (One to Many)
    public function pressureStaticMixer(): HasMany
    {
        return $this->hasMany(PressureStaticMixer::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SensorLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'timestamp',
        'pressure_intake',
        'flow_intake',
        'turbidity_baku',
        'ph_baku',
        'level_reservoir_a',
        'level_reservoir_b',
        'pressure_distribusi',
        'turbidity_reservoir',
        'ph_reservoir',
        'free_chlorine',
        'flow_yos_sudarso',
        'flow_veteran',
        'flow_bypass_yoss',
        'flow_bypass_vet',
        'pressure_backwash',
        'flow_backwash',
        'pressure_service2',
        'turbidity_filter',
        'flow_sludgepond',
        'pressure_kompressor',
        'scm',
        'freq_distribusi_a',
        'freq_distribusi_c',
    ];

    protected $casts = [
        'timestamp' => 'datetime',
    ];
}

<?php

use App\Models\SensorLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('sensor.key')->post('/sensor', function (Request $request) {
    $data = $request->validate([
        'timestamp'            => 'required|date',
        'pressure_intake'      => 'nullable|numeric',
        'flow_intake'          => 'nullable|numeric',
        'turbidity_baku'       => 'nullable|numeric',
        'ph_baku'              => 'nullable|numeric',
        'pressure_reservoir_a' => 'nullable|numeric',
        'pressure_reservoir_b' => 'nullable|numeric',
        'pressure_distribusi'  => 'nullable|numeric',
        'turbidity_reservoir'  => 'nullable|numeric',
        'ph_reservoir'         => 'nullable|numeric',
        'free_chlorine'        => 'nullable|numeric',
        'flow_yos_sudarso'     => 'nullable|numeric',
        'flow_veteran'         => 'nullable|numeric',
        'pressure_service'     => 'nullable|numeric',
        'turbidity_sedimen'    => 'nullable|numeric',
        'pressure_backwash'    => 'nullable|numeric',
        'flow_backwash'        => 'nullable|numeric',
        'pressure_service2'    => 'nullable|numeric',
        'turbidity_filter'     => 'nullable|numeric',
        'flow_sludgepond'      => 'nullable|numeric',
        'pressure_kompressor'  => 'nullable|numeric',
        'scm'                  => 'nullable|numeric',
        'freq_distribusi_a'    => 'nullable|numeric',
        'freq_distribusi_c'    => 'nullable|numeric',
    ]);

    SensorLog::create($data);

    return response()->json(['status' => 'ok']);
});

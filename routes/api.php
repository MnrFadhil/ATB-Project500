<?php

use App\Events\SensorDataReceived;
use App\Models\SensorLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('sensor.key')->post('/sensor', function (Request $request) {
    $data = $request->validate([
        'timestamp'           => 'required|date',
        'pressure_intake'     => 'nullable|numeric',
        'flow_intake'         => 'nullable|numeric',
        'turbidity_baku'      => 'nullable|numeric',
        'ph_baku'             => 'nullable|numeric',
        'level_reservoir_a'   => 'nullable|numeric',
        'level_reservoir_b'   => 'nullable|numeric',
        'pressure_distribusi' => 'nullable|numeric',
        'turbidity_reservoir' => 'nullable|numeric',
        'ph_reservoir'        => 'nullable|numeric',
        'free_chlorine'       => 'nullable|numeric',
        'flow_yos_sudarso'    => 'nullable|numeric',
        'flow_veteran'        => 'nullable|numeric',
        'flow_bypass_yoss'    => 'nullable|numeric',
        'flow_bypass_vet'     => 'nullable|numeric',
        'pressure_backwash'   => 'nullable|numeric',
        'flow_backwash'       => 'nullable|numeric',
        'pressure_service2'   => 'nullable|numeric',
        'turbidity_filter'    => 'nullable|numeric',
        'flow_sludgepond'     => 'nullable|numeric',
        'pressure_kompressor' => 'nullable|numeric',
        'scm'                 => 'nullable|numeric',
    ]);

    SensorLog::create($data);

    // Broadcast ke semua browser yang terhubung via WebSocket (Reverb)
    // Data langsung dikirim tanpa query DB tambahan
    broadcast(new SensorDataReceived($data));

    return response()->json(['status' => 'ok']);
});

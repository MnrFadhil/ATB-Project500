<?php

use App\Events\SensorDataReceived;
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
        'level_reservoir_a'    => 'nullable|numeric',
        'level_reservoir_b'    => 'nullable|numeric',
        // backward compat — sensor lama masih kirim nama lama
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

    // map nama lama ke nama baru jika sensor belum diupdate
    if (isset($data['pressure_reservoir_a'])) {
        $data['level_reservoir_a'] = $data['level_reservoir_a'] ?? $data['pressure_reservoir_a'];
        unset($data['pressure_reservoir_a']);
    }
    if (isset($data['pressure_reservoir_b'])) {
        $data['level_reservoir_b'] = $data['level_reservoir_b'] ?? $data['pressure_reservoir_b'];
        unset($data['pressure_reservoir_b']);
    }

    SensorLog::create($data);

    // Broadcast ke semua browser yang terhubung via WebSocket (Reverb)
    // Data langsung dikirim tanpa query DB tambahan
    broadcast(new SensorDataReceived($data));

    return response()->json(['status' => 'ok']);
});

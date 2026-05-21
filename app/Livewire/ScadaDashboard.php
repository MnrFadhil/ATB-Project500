<?php

namespace App\Livewire;

use App\Models\SensorLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class ScadaDashboard extends Component
{
    private function getLatestReading(): ?SensorLog
    {
        return SensorLog::latest('timestamp')->first();
    }

    public function render()
    {
        $latest = $this->getLatestReading();

        // Cached logs for data table
        $logs = Cache::remember('scada_logs_100', 25, fn() =>
            SensorLog::orderByDesc('timestamp')->limit(100)->get()
        );

        // Build chart data from already-cached $logs (no extra query, always in sync)
        $chronological = $logs->reverse()->values();
        $chartData = $chronological->isNotEmpty() ? [
            'labels' => $chronological->map(fn($l) => Carbon::parse($l->timestamp)->format('d/m H:i'))->toArray(),
            'flow' => [
                'flow_intake' => $chronological->pluck('flow_intake')->toArray(),
                'flow_total'  => $chronological->map(fn($l) => round(($l->flow_yos_sudarso ?? 0) + ($l->flow_veteran ?? 0), 2))->toArray(),
            ],
            'turbidity' => [
                'turbidity_reservoir' => $chronological->pluck('turbidity_reservoir')->toArray(),
                'turbidity_sedimen'   => $chronological->pluck('turbidity_sedimen')->toArray(),
                'turbidity_filter'    => $chronological->pluck('turbidity_filter')->toArray(),
            ],
            'quality' => [
                'ph_baku'      => $chronological->pluck('ph_baku')->toArray(),
                'ph_reservoir' => $chronological->pluck('ph_reservoir')->toArray(),
            ],
        ] : [];

        $latestPressure = $logs->first(fn($l) => $l->pressure_intake !== null);
        $pressure = $latestPressure ? [
            'intake'     => $latestPressure->pressure_intake,
            'distribusi' => $latestPressure->pressure_distribusi,
            'service'    => $latestPressure->pressure_service,
            'backwash'   => $latestPressure->pressure_backwash,
            'kompressor' => $latestPressure->pressure_kompressor,
        ] : null;

        $staleMinutes = 0;
        $isStale      = false;
        if ($latest) {
            $staleMinutes = (int) Carbon::parse($latest->timestamp)->diffInMinutes(now());
            $isStale      = $staleMinutes >= 5;
        }

        // 4 latest complete records (newest first) — dipakai JS untuk fake-live cycling
        $last4Fields = [
            'timestamp',
            'flow_intake', 'flow_yos_sudarso', 'flow_veteran', 'flow_backwash',
            'turbidity_baku', 'turbidity_reservoir', 'turbidity_sedimen', 'turbidity_filter',
            'ph_baku', 'ph_reservoir', 'free_chlorine',
            'pressure_intake', 'pressure_distribusi', 'pressure_service', 'pressure_backwash', 'pressure_kompressor',
            'scm',
        ];
        $last4 = $logs->take(4)->map(fn($l) =>
            collect($last4Fields)->mapWithKeys(fn($f) => [$f => $l->$f])->toArray()
        )->values()->toArray();

        // Sparklines — 30 titik per field untuk tampilan awal
        $sparkFields = [
            'flow_intake', 'flow_yos_sudarso', 'flow_veteran', 'flow_backwash',
            'turbidity_baku', 'turbidity_reservoir', 'turbidity_sedimen', 'turbidity_filter',
            'ph_baku', 'ph_reservoir', 'free_chlorine',
        ];
        $sparkData = [];
        foreach ($sparkFields as $f) {
            $sparkData[$f] = $logs->pluck($f)->take(30)->reverse()->values()->toArray();
        }

        // Reservoir warning flag
        $resA = $latest ? ($latest->level_reservoir_a ?? 0) : 0;
        $resB = $latest ? ($latest->level_reservoir_b ?? 0) : 0;
        $reservoirWarn = ($resA > 5.7 || $resB > 5.7 || ($resA > 0 && $resA < 1.8) || ($resB > 0 && $resB < 1.8));

        return view('livewire.scada-dashboard', compact(
            'latest', 'logs', 'chartData', 'pressure',
            'isStale', 'staleMinutes', 'last4', 'sparkData', 'reservoirWarn'
        ))->layout('layouts.app');
    }
}

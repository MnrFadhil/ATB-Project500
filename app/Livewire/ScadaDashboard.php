<?php

namespace App\Livewire;

use App\Models\SensorLog;
use Carbon\Carbon;
use Livewire\Component;

class ScadaDashboard extends Component
{
    private function getLatestReading(): ?SensorLog
    {
        return SensorLog::latest('timestamp')->first();
    }

    private function getChartData(): array
    {
        $logs = SensorLog::orderByDesc('timestamp')
            ->limit(100)
            ->get()
            ->reverse()
            ->values();

        if ($logs->isEmpty()) return [];

        $labels = $logs->map(fn($l) => Carbon::parse($l->timestamp)->format('d/m H:i'))->toArray();

        return [
            'labels' => $labels,
            'flow' => [
                'flow_intake'      => $logs->pluck('flow_intake')->toArray(),
                'flow_yos_sudarso' => $logs->pluck('flow_yos_sudarso')->toArray(),
                'flow_veteran'     => $logs->pluck('flow_veteran')->toArray(),
                'flow_backwash'    => $logs->pluck('flow_backwash')->toArray(),
            ],
            'pressure' => [
                'pressure_intake'      => $logs->pluck('pressure_intake')->toArray(),
                'pressure_reservoir_a' => $logs->pluck('pressure_reservoir_a')->toArray(),
                'pressure_reservoir_b' => $logs->pluck('pressure_reservoir_b')->toArray(),
                'pressure_distribusi'  => $logs->pluck('pressure_distribusi')->toArray(),
                'pressure_service'     => $logs->pluck('pressure_service')->toArray(),
                'pressure_backwash'    => $logs->pluck('pressure_backwash')->toArray(),
                'pressure_kompressor'  => $logs->pluck('pressure_kompressor')->toArray(),
            ],
            'turbidity' => [
                'turbidity_baku'      => $logs->pluck('turbidity_baku')->toArray(),
                'turbidity_reservoir' => $logs->pluck('turbidity_reservoir')->toArray(),
                'turbidity_sedimen'   => $logs->pluck('turbidity_sedimen')->toArray(),
                'turbidity_filter'    => $logs->pluck('turbidity_filter')->toArray(),
            ],
            'quality' => [
                'ph_baku'       => $logs->pluck('ph_baku')->toArray(),
                'ph_reservoir'  => $logs->pluck('ph_reservoir')->toArray(),
                'free_chlorine' => $logs->pluck('free_chlorine')->toArray(),
            ],
        ];
    }

    public function render()
    {
        $latest    = $this->getLatestReading();
        $chartData = $this->getChartData();
        $logs      = SensorLog::orderByDesc('timestamp')->limit(100)->get();

        $this->dispatch('scada-charts-ready', chartData: $chartData);

        return view('livewire.scada-dashboard', compact('latest', 'logs'))
            ->layout('layouts.app');
    }
}

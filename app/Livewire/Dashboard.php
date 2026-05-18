<?php

namespace App\Livewire;

use App\Models\Shift;
use Livewire\Component;
use Carbon\Carbon;

class Dashboard extends Component
{
    public string $date = '';
    public string $selectedMonth = '';

    public function mount()
    {
        $this->date = Carbon::now()->format('Y-m-d');
        $this->selectedMonth = Carbon::now()->format('Y-m');
    }

    public function updatedDate()
    {
        $shiftChart = Shift::whereDate('date', $this->date)->orderBy('end_time', 'asc')
            ->with(['flowMeters', 'reservoirLevels', 'waterQualities', 'pumpChemicals'])->get()->toArray();
        $this->dispatch('post-created', shiftChartData: $shiftChart);
    }

    public function updatedSelectedMonth()
    {
        $monthlyData = $this->getMonthlyChartData();
        $this->dispatch('monthly-data-ready', monthlyData: $monthlyData);
    }

    private function getMonthlyChartData(): array
    {
        if (!$this->selectedMonth) return [];

        $start = Carbon::parse($this->selectedMonth . '-01')->startOfMonth()->format('Y-m-d');
        $end   = Carbon::parse($this->selectedMonth . '-01')->endOfMonth()->format('Y-m-d');

        $shifts = Shift::whereBetween('date', [$start, $end])
            ->whereNull('deleted_at')
            ->with([
                'flowMeters'     => fn($q) => $q->whereNull('deleted_at'),
                'waterQualities' => fn($q) => $q->whereNull('deleted_at'),
            ])
            ->orderBy('date')
            ->get();

        $result = [];
        foreach ($shifts->groupBy('date') as $date => $dayShifts) {
            $airBakuFlows = [];
            $totalFlows   = [];
            $ntuAirBaku   = [];
            $ntuReservoir = [];

            foreach ($dayShifts as $shift) {
                foreach ($shift->flowMeters->filter(fn($fm) => $fm->location === null) as $fm) {
                    if ($fm->flow > 0) $airBakuFlows[] = $fm->flow;
                }

                $yos = $shift->flowMeters->where('location', 'yos sudarso')->avg('flow');
                $vet = $shift->flowMeters->where('location', 'veteran')->avg('flow');
                if ($yos !== null && $vet !== null) {
                    $totalFlows[] = $yos + $vet;
                }

                foreach ($shift->waterQualities->where('type', 'air baku') as $wq) {
                    $ntuAirBaku[] = $wq->turbidity;
                }
                foreach ($shift->waterQualities->where('type', 'reservoir') as $wq) {
                    $ntuReservoir[] = $wq->turbidity;
                }
            }

            $result[] = [
                'date'          => $date,
                'label'         => Carbon::parse($date)->format('d/m'),
                'debit_air_baku'=> count($airBakuFlows) ? round(array_sum($airBakuFlows) / count($airBakuFlows), 1) : null,
                'total_flow'    => count($totalFlows)   ? round(array_sum($totalFlows)   / count($totalFlows),   1) : null,
                'ntu_air_baku'  => count($ntuAirBaku)   ? round(array_sum($ntuAirBaku)   / count($ntuAirBaku),   2) : null,
                'ntu_reservoir' => count($ntuReservoir) ? round(array_sum($ntuReservoir) / count($ntuReservoir), 2) : null,
            ];
        }

        return $result;
    }

    public function render()
    {
        $shiftChart       = Shift::whereDate('date', $this->date)->orderBy('end_time', 'asc')
            ->with(['flowMeters', 'reservoirLevels', 'waterQualities', 'pumpChemicals'])->get()->toArray();
        $monthlyChartData = $this->getMonthlyChartData();

        $this->dispatch('post-created');
        $this->dispatch('monthly-data-ready', monthlyData: $monthlyChartData);

        return view('livewire.dashboard', compact('shiftChart', 'monthlyChartData'))->layout('layouts.app');
    }
}

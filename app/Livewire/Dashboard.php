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
        $shiftChart = $this->computeShiftChartFlows($shiftChart);
        $this->dispatch('post-created', shiftChartData: $shiftChart);
        $this->dispatch('pie-charts-ready', pieData: $this->getPieChartData());
    }

    public function updatedSelectedMonth()
    {
        $monthlyData = $this->getMonthlyChartData();
        $this->dispatch('monthly-data-ready', monthlyData: $monthlyData);
    }

    private function getPrevDayTotalizers(string $date): array
    {
        $prevDate  = Carbon::parse($date)->subDay()->format('Y-m-d');
        $prevShift = Shift::whereDate('date', $prevDate)
            ->whereNull('deleted_at')
            ->where('end_time', '23:00:00')
            ->with(['flowMeters' => fn($q) => $q->whereNull('deleted_at')])
            ->first();

        if (! $prevShift) return [];

        $airBaku = $prevShift->flowMeters->filter(fn($fm) => $fm->location === null)->first();
        $yos     = $prevShift->flowMeters->firstWhere('location', 'yos sudarso');
        $vet     = $prevShift->flowMeters->firstWhere('location', 'veteran');

        return [
            'air_baku' => $airBaku?->totalizer,
            'yos'      => $yos?->totalizer,
            'vet'      => $vet?->totalizer,
        ];
    }

    private function computeShiftChartFlows(array $shiftChart): array
    {
        $prev        = $this->getPrevDayTotalizers($this->date);
        $prevAirBaku = $prev['air_baku'] ?? null;
        $prevYos     = $prev['yos']      ?? null;
        $prevVet     = $prev['vet']      ?? null;

        foreach ($shiftChart as &$row) {
            foreach ($row['flow_meters'] as &$fm) {
                if ($fm['location'] === null) {
                    $selisih = ($prevAirBaku !== null && $fm['totalizer'] !== null)
                        ? $fm['totalizer'] - $prevAirBaku
                        : $fm['flow'] * 7.2;
                    $fm['flow'] = $selisih > 0 ? round($selisih / 7.2, 1) : 0;
                    if ($fm['totalizer'] !== null) $prevAirBaku = $fm['totalizer'];
                } elseif ($fm['location'] === 'yos sudarso') {
                    $selisih = ($prevYos !== null && $fm['totalizer'] !== null)
                        ? $fm['totalizer'] - $prevYos
                        : $fm['flow'] * 7.2;
                    $fm['flow'] = round($selisih / 7.2, 1);
                    if ($fm['totalizer'] !== null) $prevYos = $fm['totalizer'];
                } elseif ($fm['location'] === 'veteran') {
                    $selisih = ($prevVet !== null && $fm['totalizer'] !== null)
                        ? $fm['totalizer'] - $prevVet
                        : $fm['flow'] * 7.2;
                    $fm['flow'] = round($selisih / 7.2, 1);
                    if ($fm['totalizer'] !== null) $prevVet = $fm['totalizer'];
                }
            }
            unset($fm);
        }
        unset($row);

        return $shiftChart;
    }

    private function getMonthlyChartData(): array
    {
        if (!$this->selectedMonth) return [];

        $start = Carbon::parse($this->selectedMonth . '-01')->startOfMonth()->format('Y-m-d');
        $end   = Carbon::parse($this->selectedMonth . '-01')->endOfMonth()->format('Y-m-d');

        $seedShift = Shift::where('date', '<', $start)
            ->whereNull('deleted_at')
            ->where('end_time', '23:00:00')
            ->orderByDesc('date')
            ->with(['flowMeters' => fn($q) => $q->whereNull('deleted_at')])
            ->first();

        $prevAirBakuTotalizer = null;
        $prevYosTotalizer     = null;
        $prevVetTotalizer     = null;

        if ($seedShift) {
            $ab  = $seedShift->flowMeters->filter(fn($fm) => $fm->location === null)->first();
            $yos = $seedShift->flowMeters->firstWhere('location', 'yos sudarso');
            $vet = $seedShift->flowMeters->firstWhere('location', 'veteran');
            $prevAirBakuTotalizer = $ab?->totalizer;
            $prevYosTotalizer     = $yos?->totalizer;
            $prevVetTotalizer     = $vet?->totalizer;
        }

        $shifts = Shift::whereBetween('date', [$start, $end])
            ->whereNull('deleted_at')
            ->with([
                'flowMeters'     => fn($q) => $q->whereNull('deleted_at'),
                'waterQualities' => fn($q) => $q->whereNull('deleted_at'),
            ])
            ->orderBy('date')
            ->orderBy('end_time')
            ->get();

        $shiftFlows = [];

        foreach ($shifts as $shift) {
            $abFm  = $shift->flowMeters->filter(fn($fm) => $fm->location === null)->first();
            $yosFm = $shift->flowMeters->firstWhere('location', 'yos sudarso');
            $vetFm = $shift->flowMeters->firstWhere('location', 'veteran');

            $abTotalizer  = $abFm?->totalizer;
            $yosTotalizer = $yosFm?->totalizer;
            $vetTotalizer = $vetFm?->totalizer;

            $abSelisih = ($prevAirBakuTotalizer !== null && $abTotalizer !== null)
                ? $abTotalizer - $prevAirBakuTotalizer
                : ($abFm?->flow ?? 0) * 7.2;

            $yosSelisih = ($prevYosTotalizer !== null && $yosTotalizer !== null)
                ? $yosTotalizer - $prevYosTotalizer
                : ($yosFm?->flow ?? 0) * 7.2;

            $vetSelisih = ($prevVetTotalizer !== null && $vetTotalizer !== null)
                ? $vetTotalizer - $prevVetTotalizer
                : ($vetFm?->flow ?? 0) * 7.2;

            $abFlow   = $abSelisih > 0 ? round($abSelisih / 7.2, 1) : 0;
            $distFlow = round(($yosSelisih + $vetSelisih) / 7.2, 1);

            if ($abFlow > 0) $shiftFlows[$shift->date]['air_baku'][] = $abFlow;
            $shiftFlows[$shift->date]['total'][] = $distFlow;

            if ($abTotalizer !== null)  $prevAirBakuTotalizer = $abTotalizer;
            if ($yosTotalizer !== null) $prevYosTotalizer = $yosTotalizer;
            if ($vetTotalizer !== null) $prevVetTotalizer = $vetTotalizer;
        }

        $result = [];
        foreach ($shifts->groupBy('date') as $date => $dayShifts) {
            $ntuAirBaku   = [];
            $ntuReservoir = [];

            foreach ($dayShifts as $shift) {
                foreach ($shift->waterQualities->where('type', 'air baku') as $wq) {
                    $ntuAirBaku[] = $wq->turbidity;
                }
                foreach ($shift->waterQualities->where('type', 'reservoir') as $wq) {
                    $ntuReservoir[] = $wq->turbidity;
                }
            }

            $abFlows    = $shiftFlows[$date]['air_baku'] ?? [];
            $totalFlows = $shiftFlows[$date]['total']    ?? [];

            $result[] = [
                'date'          => $date,
                'label'         => Carbon::parse($date)->format('d/m'),
                'debit_air_baku'=> count($abFlows)      ? round(array_sum($abFlows)      / count($abFlows),      1) : null,
                'total_flow'    => count($totalFlows)   ? round(array_sum($totalFlows)   / count($totalFlows),   1) : null,
                'ntu_air_baku'  => count($ntuAirBaku)   ? round(array_sum($ntuAirBaku)   / count($ntuAirBaku),   2) : null,
                'ntu_reservoir' => count($ntuReservoir) ? round(array_sum($ntuReservoir) / count($ntuReservoir), 2) : null,
            ];
        }

        return $result;
    }

    private function getPieChartData(): array
    {
        $prev        = $this->getPrevDayTotalizers($this->date);
        $prevAirBaku = $prev['air_baku'] ?? null;
        $prevYos     = $prev['yos']      ?? null;
        $prevVet     = $prev['vet']      ?? null;

        $shifts = Shift::whereDate('date', $this->date)
            ->whereNull('deleted_at')
            ->with([
                'flowMeters'    => fn($q) => $q->whereNull('deleted_at'),
                'pumpChemicals' => fn($q) => $q->whereNull('deleted_at'),
            ])
            ->orderBy('end_time')
            ->get();

        $totalAbSelisih  = 0;
        $totalYosSelisih = 0;
        $totalVetSelisih = 0;
        $chemicals = ['PAC' => 0, 'Chlorine' => 0, 'Soda Ash' => 0, 'Polymer' => 0];
        $chemMap   = ['pac' => 'PAC', 'chlorine/kaporit' => 'Chlorine', 'soda ash' => 'Soda Ash', 'polymer' => 'Polymer'];

        foreach ($shifts as $shift) {
            $abFm  = $shift->flowMeters->filter(fn($fm) => $fm->location === null)->first();
            $yosFm = $shift->flowMeters->firstWhere('location', 'yos sudarso');
            $vetFm = $shift->flowMeters->firstWhere('location', 'veteran');

            $abTotalizer  = $abFm?->totalizer;
            $yosTotalizer = $yosFm?->totalizer;
            $vetTotalizer = $vetFm?->totalizer;

            $abSelisih = ($prevAirBaku !== null && $abTotalizer !== null)
                ? $abTotalizer - $prevAirBaku
                : ($abFm?->flow ?? 0) * 7.2;

            $yosSelisih = ($prevYos !== null && $yosTotalizer !== null)
                ? $yosTotalizer - $prevYos
                : ($yosFm?->flow ?? 0) * 7.2;

            $vetSelisih = ($prevVet !== null && $vetTotalizer !== null)
                ? $vetTotalizer - $prevVet
                : ($vetFm?->flow ?? 0) * 7.2;

            if ($abSelisih > 0) $totalAbSelisih += $abSelisih;
            $totalYosSelisih += $yosSelisih;
            $totalVetSelisih += $vetSelisih;

            if ($abTotalizer !== null)  $prevAirBaku = $abTotalizer;
            if ($yosTotalizer !== null) $prevYos = $yosTotalizer;
            if ($vetTotalizer !== null) $prevVet = $vetTotalizer;

            foreach ($shift->pumpChemicals as $pc) {
                $key = $chemMap[$pc->type] ?? null;
                if ($key && $pc->dosage > 0) $chemicals[$key] += $pc->dosage;
            }
        }

        $yosFlow = round($totalYosSelisih / 7.2, 1);
        $vetFlow = round($totalVetSelisih / 7.2, 1);
        $airBaku = round($totalAbSelisih  / 7.2, 1);
        $dist    = $yosFlow + $vetFlow;
        $losses  = max(0, $airBaku - $dist);

        $chemLabels = [];
        $chemValues = [];
        foreach ($chemicals as $label => $val) {
            if ($val > 0) { $chemLabels[] = $label; $chemValues[] = round($val, 1); }
        }

        return [
            'distribution' => ['labels' => ['Yos Sudarso', 'Veteran'], 'values' => [$yosFlow, $vetFlow]],
            'water_loss'   => ['labels' => ['Distribusi', 'Losses'],   'values' => [$dist, $losses]],
            'chemicals'    => ['labels' => $chemLabels,                 'values' => $chemValues],
        ];
    }

    public function render()
    {
        $shiftChart = Shift::whereDate('date', $this->date)->orderBy('end_time', 'asc')
            ->with(['flowMeters', 'reservoirLevels', 'waterQualities', 'pumpChemicals'])->get()->toArray();
        $shiftChart       = $this->computeShiftChartFlows($shiftChart);
        $monthlyChartData = $this->getMonthlyChartData();

        $this->dispatch('post-created');
        $this->dispatch('monthly-data-ready', monthlyData: $monthlyChartData);
        $this->dispatch('pie-charts-ready', pieData: $this->getPieChartData());

        return view('livewire.dashboard', compact('shiftChart', 'monthlyChartData'))->layout('layouts.app');
    }
}

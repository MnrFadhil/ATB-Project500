<?php

namespace App\Livewire;

use App\Models\Shift;
use Carbon\Carbon;
use Livewire\Component;

class WaterLoss extends Component
{
    public string $date = '';

    public function mount(): void
    {
        $this->date = Carbon::now()->format('Y-m-d');
    }

    public function prevDay(): void
    {
        $this->date = Carbon::parse($this->date)->subDay()->format('Y-m-d');
    }

    public function nextDay(): void
    {
        $this->date = Carbon::parse($this->date)->addDay()->format('Y-m-d');
    }

    private function getPrevTotalizers(): array
    {
        $prevDate  = Carbon::parse($this->date)->subDay()->format('Y-m-d');
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
            'dist'     => ($yos?->totalizer ?? 0) + ($vet?->totalizer ?? 0),
        ];
    }

    public function getTableData(): array
    {
        $shifts = Shift::whereDate('date', $this->date)
            ->whereNull('deleted_at')
            ->with([
                'flowMeters'     => fn($q) => $q->whereNull('deleted_at'),
                'reservoirLevels',
            ])
            ->orderBy('end_time')
            ->get();

        if ($shifts->isEmpty()) return [];

        $prev                 = $this->getPrevTotalizers();
        $prevAirBakuTotalizer = $prev['air_baku'] ?? null;
        $prevDistTotalizer    = $prev['dist']     ?? null;

        $rows = [];

        foreach ($shifts as $shift) {
            $airBaku = $shift->flowMeters->filter(fn($fm) => $fm->location === null)->first();
            $yos     = $shift->flowMeters->firstWhere('location', 'yos sudarso');
            $vet     = $shift->flowMeters->firstWhere('location', 'veteran');
            $resv    = $shift->reservoirLevels;

            $airBakuFlow = $airBaku?->flow ?? 0;
            $yosFlow     = $yos?->flow ?? 0;
            $vetFlow     = $vet?->flow ?? 0;
            $totalFlow   = $yosFlow + $vetFlow;

            $airBakuTotalizer = $airBaku?->totalizer;
            $distTotalizer    = ($yos?->totalizer ?? 0) + ($vet?->totalizer ?? 0);

            if ($prevAirBakuTotalizer !== null && $airBakuTotalizer !== null) {
                $airBakuSelisih = $airBakuTotalizer - $prevAirBakuTotalizer;
            } else {
                $airBakuSelisih = $airBakuFlow * 7.2;
            }

            if ($prevDistTotalizer !== null && $distTotalizer > 0) {
                $distSelisih = $distTotalizer - $prevDistTotalizer;
            } else {
                $distSelisih = $totalFlow * 7.2;
            }

            $waterLossPct = $airBakuSelisih > 0
                ? (($airBakuSelisih - $distSelisih) / $airBakuSelisih) * 100
                : null;

            $rows[] = [
                'time'                => substr($shift->end_time, 0, 5),
                'shift'               => $shift->shift,
                'air_baku_flow'       => $airBakuFlow,
                'air_baku_totalizer'  => $airBakuTotalizer,
                'air_baku_selisih'    => round($airBakuSelisih),
                'yos_flow'            => $yosFlow,
                'yos_totalizer'       => $yos?->totalizer,
                'vet_flow'            => $vetFlow,
                'vet_totalizer'       => $vet?->totalizer,
                'total_flow'          => $totalFlow,
                'total_totalizer'     => $distTotalizer ?: null,
                'total_selisih'       => round($distSelisih),
                'water_loss_pct'      => $waterLossPct !== null ? round($waterLossPct, 2) : null,
                'level_a'             => $resv?->level_a,
                'level_b'             => $resv?->level_b,
            ];

            $prevAirBakuTotalizer = $airBakuTotalizer;
            $prevDistTotalizer    = $distTotalizer;
        }

        return $rows;
    }

    public function getShiftSummary(array $rows): array
    {
        $groups = ['shift i' => [], 'shift ii' => [], 'shift iii' => []];

        foreach ($rows as $row) {
            if (isset($groups[$row['shift']]) && $row['water_loss_pct'] !== null) {
                $groups[$row['shift']][] = $row;
            }
        }

        $map = [
            'shift i'   => 'Shift 1',
            'shift ii'  => 'Shift 2',
            'shift iii' => 'Shift 3',
        ];

        $result = [];
        foreach ($map as $key => $label) {
            $shiftRows = $groups[$key];
            $sumWl     = array_sum(array_column($shiftRows, 'water_loss_pct'));
            $wl        = round($sumWl / 4, 2);

            $result[] = [
                'label'          => $label,
                'shift_key'      => $key,
                'water_loss_pct' => $wl,
                'count'          => count($shiftRows),
            ];
        }

        return $result;
    }

    private function getGrandTotal(array $rows): array
    {
        if (empty($rows)) return [];

        $validRows = array_values(array_filter($rows, fn($r) => $r['air_baku_flow'] > 0));
        if (empty($validRows)) return [];

        $totalAirBakuSelisih = array_sum(array_column($rows, 'air_baku_selisih'));
        $totalDistSelisih    = array_sum(array_column($rows, 'total_selisih'));

        $levelsA = array_filter(array_column($validRows, 'level_a'));
        $levelsB = array_filter(array_column($validRows, 'level_b'));

        return [
            'avg_air_baku_flow'      => round(array_sum(array_column($validRows, 'air_baku_flow')) / count($validRows), 1),
            'total_air_baku_selisih' => $totalAirBakuSelisih,
            'avg_yos_flow'           => round(array_sum(array_column($validRows, 'yos_flow')) / count($validRows), 1),
            'avg_vet_flow'           => round(array_sum(array_column($validRows, 'vet_flow')) / count($validRows), 1),
            'avg_total_flow'         => round(array_sum(array_column($validRows, 'total_flow')) / count($validRows), 1),
            'total_dist_selisih'     => $totalDistSelisih,
            'water_loss_pct'         => $totalAirBakuSelisih > 0
                ? round(($totalAirBakuSelisih - $totalDistSelisih) / $totalAirBakuSelisih * 100, 2)
                : null,
            'avg_level_a'            => count($levelsA) ? round(array_sum($levelsA) / count($levelsA), 2) : null,
            'avg_level_b'            => count($levelsB) ? round(array_sum($levelsB) / count($levelsB), 2) : null,
        ];
    }

    public function render()
    {
        $rows         = $this->getTableData();
        $grandTotal   = $this->getGrandTotal($rows);
        $shiftSummary = $this->getShiftSummary($rows);

        $chartData = [
            'labels'     => array_column($rows, 'time'),
            'water_loss' => array_column($rows, 'water_loss_pct'),
            'level_a'    => array_column($rows, 'level_a'),
            'level_b'    => array_column($rows, 'level_b'),
        ];

        $this->dispatch('waterloss-chart-ready', chartData: $chartData);

        return view('livewire.water-loss', compact('rows', 'grandTotal', 'shiftSummary'))
            ->layout('layouts.app');
    }
}

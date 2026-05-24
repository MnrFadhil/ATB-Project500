<?php

namespace App\Livewire;

use App\Models\Shift;
use Carbon\Carbon;
use Livewire\Component;

class WaterLoss extends Component
{
    public string $date      = '';
    public string $period    = 'daily';   // 'daily' | 'weekly' | 'monthly'
    public string $weekStart = '';        // Y-m-d (Senin awal minggu)
    public string $monthYear = '';        // Y-m  (bulan aktif)

    public function mount(): void
    {
        $this->date      = Carbon::now()->format('Y-m-d');
        $this->weekStart = Carbon::now()->startOfWeek(Carbon::MONDAY)->format('Y-m-d');
        $this->monthYear = Carbon::now()->format('Y-m');
    }

    // -------------------------------------------------------------------------
    // Navigasi
    // -------------------------------------------------------------------------

    public function prevDay(): void
    {
        $this->date = Carbon::parse($this->date)->subDay()->format('Y-m-d');
    }

    public function nextDay(): void
    {
        $this->date = Carbon::parse($this->date)->addDay()->format('Y-m-d');
    }

    public function setPeriod(string $p): void
    {
        $this->period = $p;
    }

    public function prevPeriod(): void
    {
        if ($this->period === 'daily') {
            $this->prevDay();
        } elseif ($this->period === 'weekly') {
            $this->weekStart = Carbon::parse($this->weekStart)->subWeek()->format('Y-m-d');
        } elseif ($this->period === 'monthly') {
            $this->monthYear = Carbon::parse($this->monthYear . '-01')->subMonth()->format('Y-m');
        }
    }

    public function nextPeriod(): void
    {
        if ($this->period === 'daily') {
            $this->nextDay();
        } elseif ($this->period === 'weekly') {
            $this->weekStart = Carbon::parse($this->weekStart)->addWeek()->format('Y-m-d');
        } elseif ($this->period === 'monthly') {
            $this->monthYear = Carbon::parse($this->monthYear . '-01')->addMonth()->format('Y-m');
        }
    }

    // -------------------------------------------------------------------------
    // Data Harian (tidak diubah dari implementasi asli)
    // -------------------------------------------------------------------------

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
            'yos'      => $yos?->totalizer,
            'vet'      => $vet?->totalizer,
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
        $prevYosTotalizer     = $prev['yos']      ?? null;
        $prevVetTotalizer     = $prev['vet']      ?? null;

        $rows = [];

        foreach ($shifts as $shift) {
            $airBaku = $shift->flowMeters->filter(fn($fm) => $fm->location === null)->first();
            $yos     = $shift->flowMeters->firstWhere('location', 'yos sudarso');
            $vet     = $shift->flowMeters->firstWhere('location', 'veteran');
            $resv    = $shift->reservoirLevels;

            $airBakuTotalizer = $airBaku?->totalizer;
            $yosTotalizer     = $yos?->totalizer;
            $vetTotalizer     = $vet?->totalizer;
            $distTotalizer    = ($yosTotalizer ?? 0) + ($vetTotalizer ?? 0);

            $airBakuSelisih = ($prevAirBakuTotalizer !== null && $airBakuTotalizer !== null)
                ? $airBakuTotalizer - $prevAirBakuTotalizer
                : ($airBaku?->flow ?? 0) * 7.2;

            $yosSelisih = ($prevYosTotalizer !== null && $yosTotalizer !== null)
                ? $yosTotalizer - $prevYosTotalizer
                : ($yos?->flow ?? 0) * 7.2;

            $vetSelisih = ($prevVetTotalizer !== null && $vetTotalizer !== null)
                ? $vetTotalizer - $prevVetTotalizer
                : ($vet?->flow ?? 0) * 7.2;

            $distSelisih  = $yosSelisih + $vetSelisih;
            $airBakuFlow  = $airBakuSelisih > 0 ? round($airBakuSelisih / 7.2, 1) : 0;
            $yosFlow      = round($yosSelisih / 7.2, 1);
            $vetFlow      = round($vetSelisih / 7.2, 1);
            $totalFlow    = round($yosFlow + $vetFlow, 1);

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
                'yos_totalizer'       => $yosTotalizer,
                'vet_flow'            => $vetFlow,
                'vet_totalizer'       => $vetTotalizer,
                'total_flow'          => $totalFlow,
                'total_totalizer'     => $distTotalizer ?: null,
                'total_selisih'       => round($distSelisih),
                'water_loss_pct'      => $waterLossPct !== null ? round($waterLossPct, 2) : null,
                'level_a'             => $resv?->level_a,
                'level_b'             => $resv?->level_b,
            ];

            $prevAirBakuTotalizer = $airBakuTotalizer;
            $prevYosTotalizer     = $yosTotalizer;
            $prevVetTotalizer     = $vetTotalizer;
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

    // -------------------------------------------------------------------------
    // Data Periodik (Mingguan / Bulanan)
    // -------------------------------------------------------------------------

    private function extractTotalizers(Shift $shift): array
    {
        $fms = $shift->flowMeters;
        return [
            'air_baku' => (float) ($fms->filter(fn($f) => $f->location === null)->first()?->totalizer ?? 0),
            'yos'      => (float) ($fms->firstWhere('location', 'yos sudarso')?->totalizer ?? 0),
            'vet'      => (float) ($fms->firstWhere('location', 'veteran')?->totalizer ?? 0),
        ];
    }

    private function getPeriodicData(): array
    {
        if ($this->period === 'weekly') {
            $startDate = Carbon::parse($this->weekStart)->startOfDay();
            $endDate   = Carbon::parse($this->weekStart)->addDays(6)->endOfDay();
        } else {
            $startDate = Carbon::parse($this->monthYear . '-01')->startOfMonth();
            $endDate   = Carbon::parse($this->monthYear . '-01')->endOfMonth();
        }

        // Muat semua shift dari 1 hari sebelum periode hingga akhir periode (satu query)
        $allShifts = Shift::whereBetween('date', [
                $startDate->copy()->subDay()->format('Y-m-d'),
                $endDate->format('Y-m-d'),
            ])
            ->whereNull('deleted_at')
            ->with(['flowMeters' => fn($q) => $q->whereNull('deleted_at')])
            ->orderBy('date')
            ->orderBy('end_time')
            ->get();

        $today = Carbon::now()->format('Y-m-d');

        $grouped = $allShifts->groupBy('date');

        // Index per tanggal: ambil shift 23:00 sebagai "totalizer akhir hari"
        // Untuk hari ini (belum ada 23:00), pakai shift terakhir yang tersedia
        $byDate = $grouped->map(fn($dayShifts, $date) => $date === $today
            ? $dayShifts->last()
            : $dayShifts->firstWhere('end_time', '23:00:00')
        );

        // Hitung flow per shift dari selisih totalizer antar shift berurutan
        // (sama persis dengan formula Excel: selisih_m3 × 1000 / 7200 = lps)
        // AVERAGEIF(>0): hanya shift aktif yang dirata-rata
        $perShiftFlows = [];
        $prevAb = $prevYos = $prevVet = null;

        foreach ($allShifts as $shift) {
            $fms = $shift->flowMeters;
            $ab  = (float) ($fms->filter(fn($f) => $f->location === null)->first()?->totalizer ?? 0);
            $yos = (float) ($fms->firstWhere('location', 'yos sudarso')?->totalizer ?? 0);
            $vet = (float) ($fms->firstWhere('location', 'veteran')?->totalizer ?? 0);

            if ($prevAb !== null && $ab > 0) {
                $abSel  = max(0.0, $ab  - $prevAb);
                $yosSel = max(0.0, $yos - $prevYos);
                $vetSel = max(0.0, $vet - $prevVet);
                // Threshold: max selisih per 2h shift ~6000 m³ — anomalous spikes excluded
                $maxSel = 6000.0;
                $perShiftFlows[$shift->date][] = [
                    'ab'  => ($abSel  > 0 && $abSel  <= $maxSel) ? round($abSel  * 1000 / 7200, 1) : 0.0,
                    'yos' => ($yosSel > 0 && $yosSel <= $maxSel) ? round($yosSel * 1000 / 7200, 1) : 0.0,
                    'vet' => ($vetSel > 0 && $vetSel <= $maxSel) ? round($vetSel * 1000 / 7200, 1) : 0.0,
                ];
            }

            if ($ab > 0) { $prevAb = $ab; $prevYos = $yos; $prevVet = $vet; }
        }

        // Daily AVERAGEIF(>0) per tanggal (untuk kolom Flow di tabel harian)
        $flowByDate = collect($perShiftFlows)->map(function ($shifts) {
            $abF  = array_values(array_filter(array_column($shifts, 'ab'),  fn($v) => $v > 0));
            $yosF = array_values(array_filter(array_column($shifts, 'yos'), fn($v) => $v > 0));
            $vetF = array_values(array_filter(array_column($shifts, 'vet'), fn($v) => $v > 0));
            return [
                'ab'  => count($abF)  ? round(array_sum($abF)  / count($abF),  1) : null,
                'yos' => count($yosF) ? round(array_sum($yosF) / count($yosF), 1) : null,
                'vet' => count($vetF) ? round(array_sum($vetF) / count($vetF), 1) : null,
            ];
        });

        // Flat per-shift flows hanya untuk tanggal dalam periode (untuk summary bulanan/mingguan)
        // Sesuai Excel C782 = AVERAGEIF(C7:C781, ">0") — rata-rata SEMUA shift non-zero langsung
        $flatShiftFlows = ['ab' => [], 'yos' => [], 'vet' => [], 'dist' => []];
        $periodStart = $startDate->format('Y-m-d');
        $periodEnd   = $endDate->format('Y-m-d');
        foreach ($perShiftFlows as $date => $shifts) {
            if ($date < $periodStart || $date > $periodEnd) continue;
            foreach ($shifts as $s) {
                if ($s['ab']  > 0) $flatShiftFlows['ab'][]   = $s['ab'];
                if ($s['yos'] > 0) $flatShiftFlows['yos'][]  = $s['yos'];
                if ($s['vet'] > 0) $flatShiftFlows['vet'][]  = $s['vet'];
                if ($s['yos'] + $s['vet'] > 0) $flatShiftFlows['dist'][] = round($s['yos'] + $s['vet'], 1);
            }
        }

        // Seed prevTotalizers dari 23:00 hari sebelum periode dimulai
        // Sesuai Excel: E8 = D8 - April!D755 (referensi 23:00 hari terakhir bulan lalu)
        $prevDateStr    = $startDate->copy()->subDay()->format('Y-m-d');
        $seedShift      = $byDate->get($prevDateStr);
        $prevTotalizers = $seedShift ? $this->extractTotalizers($seedShift) : null;

        $rows    = [];
        $current = $startDate->copy()->startOfDay();
        $end     = $endDate->copy()->startOfDay();

        while ($current->lte($end)) {
            $dateStr    = $current->format('Y-m-d');
            $todayShift = $byDate->get($dateStr);

            $dayFlow = $flowByDate->get($dateStr, ['ab' => null, 'yos' => null, 'vet' => null]);

            if ($todayShift !== null && $prevTotalizers !== null) {
                $cur = $this->extractTotalizers($todayShift);

                $airBakuSelisih = max(0.0, $cur['air_baku'] - $prevTotalizers['air_baku']);
                $yosSelisih     = max(0.0, $cur['yos']      - $prevTotalizers['yos']);
                $vetSelisih     = max(0.0, $cur['vet']      - $prevTotalizers['vet']);
                $distSelisih    = $yosSelisih + $vetSelisih;

                $wlPct = $airBakuSelisih > 0
                    ? round(($airBakuSelisih - $distSelisih) / $airBakuSelisih * 100, 2)
                    : null;

                $distFlow = ($dayFlow['yos'] !== null && $dayFlow['vet'] !== null)
                    ? round($dayFlow['yos'] + $dayFlow['vet'], 1)
                    : null;

                $rows[] = [
                    'date'           => $dateStr,
                    'date_label'     => $current->translatedFormat('D, d M'),
                    'flow_ab'        => $dayFlow['ab'],
                    'flow_yos'       => $dayFlow['yos'],
                    'flow_vet'       => $dayFlow['vet'],
                    'flow_dist'      => $distFlow,
                    'air_baku_m3'    => (int) round($airBakuSelisih),
                    'yos_m3'         => (int) round($yosSelisih),
                    'vet_m3'         => (int) round($vetSelisih),
                    'dist_m3'        => (int) round($distSelisih),
                    'loss_m3'        => (int) round($airBakuSelisih - $distSelisih),
                    'water_loss_pct' => $wlPct,
                    'data_available' => true,
                ];

                $prevTotalizers = $cur;
            } else {
                if ($todayShift !== null) {
                    $prevTotalizers = $this->extractTotalizers($todayShift);
                } else {
                    $prevTotalizers = null;
                }

                $rows[] = [
                    'date'           => $dateStr,
                    'date_label'     => $current->translatedFormat('D, d M'),
                    'flow_ab'        => null,
                    'flow_yos'       => null,
                    'flow_vet'       => null,
                    'flow_dist'      => null,
                    'air_baku_m3'    => null,
                    'yos_m3'         => null,
                    'vet_m3'         => null,
                    'dist_m3'        => null,
                    'loss_m3'        => null,
                    'water_loss_pct' => null,
                    'data_available' => false,
                ];
            }

            $current->addDay();
        }

        return ['rows' => $rows, 'shiftFlows' => $flatShiftFlows];
    }

    private function getPeriodicSummary(array $rows, array $flatShiftFlows): array
    {
        $available = array_values(array_filter($rows, fn($r) => $r['data_available']));

        if (empty($available)) return [];

        $totalAirBaku = array_sum(array_column($available, 'air_baku_m3'));
        $totalYos     = array_sum(array_column($available, 'yos_m3'));
        $totalVet     = array_sum(array_column($available, 'vet_m3'));
        $totalDist    = array_sum(array_column($available, 'dist_m3'));
        $totalLoss    = $totalAirBaku - $totalDist;

        // Rata-rata WL% harian — sesuai Excel AVERAGEIFS:
        // Excel menghitung hari tanpa data sebagai 0 (IF(T>0,...,) returns 0 bukan blank)
        // sehingga dibagi total hari dalam periode, bukan hanya hari yang ada data
        $wlValues = array_filter(array_column($available, 'water_loss_pct'), fn($v) => $v !== null);
        $totalDays = count($rows);  // total hari dalam periode (termasuk yang tanpa data)
        $avgWl = $totalDays > 0 ? round(array_sum($wlValues) / $totalDays, 2) : null;

        // Rata-rata flow bulanan: AVERAGEIF semua shift non-zero langsung (sesuai Excel C782)
        $avgFlowAb   = count($flatShiftFlows['ab'])   ? round(array_sum($flatShiftFlows['ab'])   / count($flatShiftFlows['ab']),   1) : null;
        $avgFlowYos  = count($flatShiftFlows['yos'])  ? round(array_sum($flatShiftFlows['yos'])  / count($flatShiftFlows['yos']),  1) : null;
        $avgFlowVet  = count($flatShiftFlows['vet'])  ? round(array_sum($flatShiftFlows['vet'])  / count($flatShiftFlows['vet']),  1) : null;
        $avgFlowDist = count($flatShiftFlows['dist']) ? round(array_sum($flatShiftFlows['dist']) / count($flatShiftFlows['dist']), 1) : null;

        return [
            'total_air_baku'  => $totalAirBaku,
            'total_yos'       => $totalYos,
            'total_vet'       => $totalVet,
            'total_dist'      => $totalDist,
            'total_loss'      => max(0, $totalLoss),
            'overall_wl'      => $avgWl,
            'avg_flow_ab'     => $avgFlowAb,
            'avg_flow_yos'    => $avgFlowYos,
            'avg_flow_vet'    => $avgFlowVet,
            'avg_flow_dist'   => $avgFlowDist,
            'days_with_data'  => count($available),
            'total_days'      => count($rows),
        ];
    }

    // -------------------------------------------------------------------------
    // Render
    // -------------------------------------------------------------------------

    public function render()
    {
        if ($this->period === 'daily') {
            $rows            = $this->getTableData();
            $grandTotal      = $this->getGrandTotal($rows);
            $shiftSummary    = $this->getShiftSummary($rows);
            $periodicRows    = [];
            $periodicSummary = [];

            $chartData = [
                'mode'       => 'daily',
                'labels'     => array_column($rows, 'time'),
                'water_loss' => array_column($rows, 'water_loss_pct'),
                'level_a'    => array_column($rows, 'level_a'),
                'level_b'    => array_column($rows, 'level_b'),
            ];
        } else {
            $rows            = [];
            $grandTotal      = [];
            $shiftSummary    = [];
            $periodicResult  = $this->getPeriodicData();
            $periodicRows    = $periodicResult['rows'];
            $periodicSummary = $this->getPeriodicSummary($periodicRows, $periodicResult['shiftFlows']);

            $chartData = [
                'mode'       => $this->period,
                'labels'     => array_column($periodicRows, 'date_label'),
                'water_loss' => array_column($periodicRows, 'water_loss_pct'),
                'level_a'    => [],
                'level_b'    => [],
            ];
        }

        $this->dispatch('waterloss-chart-ready', chartData: $chartData);

        return view('livewire.water-loss', compact(
            'rows', 'grandTotal', 'shiftSummary',
            'periodicRows', 'periodicSummary'
        ))->layout('layouts.app');
    }
}

<?php

namespace App\Services;

use App\Models\Shift;
use Carbon\Carbon;

class FuzzyEvaluationService
{
    // =========================================================================
    // PAC EVALUATION MODE — toggle mudah, ubah nilai konstanta di bawah ini
    //
    //   'rekomen' → Aktual (T+1)  vs  Rekomendasi Fuzzy          ← MODE AKTIF
    //   'prev'    → Aktual (T+1)  vs  Dosis aktual 2 jam sebelumnya (shift T)
    //
    // Cara toggle: ganti nilai PAC_EVAL_MODE, atau swap comment dua baris ini:
    // =========================================================================
    const PAC_EVAL_MODE = 'rekomen';
    // const PAC_EVAL_MODE = 'prev';

    // -------------------------------------------------------------------------
    // Membership Functions
    // -------------------------------------------------------------------------

    private function leftShoulderMF(float $x, float $a, float $b): float
    {
        if ($x <= $a) return 1.0;
        if ($x >= $b) return 0.0;
        return ($b - $x) / ($b - $a);
    }

    private function triangularMF(float $x, float $a, float $b, float $c): float
    {
        if ($x <= $a || $x >= $c) return 0.0;
        if ($x <= $b) return ($x - $a) / ($b - $a);
        return ($c - $x) / ($c - $b);
    }

    private function rightShoulderMF(float $x, float $a, float $b): float
    {
        if ($x <= $a) return 0.0;
        if ($x >= $b) return 1.0;
        return ($x - $a) / ($b - $a);
    }

    private function defuzzify(array $rules): float
    {
        $sumMuCenter = 0.0;
        $sumMu       = 0.0;
        foreach ($rules as [$mu, $center]) {
            $sumMuCenter += $mu * $center;
            $sumMu       += $mu;
        }
        return $sumMu > 0 ? round($sumMuCenter / $sumMu, 2) : 0.0;
    }

    // -------------------------------------------------------------------------
    // Fuzzy Mamdani — PAC (output: dosis ppm, batas 5–20)
    // -------------------------------------------------------------------------

    private function fuzzyPAC(float $turbidity, float $previousDosis = 10.0): float
    {
        $t  = $turbidity;
        $mu = [
            'sangat_rendah' => $this->leftShoulderMF($t,  0.0, 2.0),
            'rendah'        => $this->triangularMF($t,    1.0, 2.5, 3.2),
            'optimal'       => $this->triangularMF($t,    2.8, 3.3, 3.8),
            'tinggi'        => $this->triangularMF($t,    3.4, 4.1, 5.0),
            'sangat_tinggi' => $this->rightShoulderMF($t, 4.5, 6.0),
        ];
        $rules = [
            [$mu['sangat_rendah'], -3.0],
            [$mu['rendah'],        -1.0],
            [$mu['optimal'],        0.0],
            [$mu['tinggi'],        +1],
            [$mu['sangat_tinggi'], +3],
        ];
        $delta = $this->defuzzify($rules);
        return round(max(8.0, min(20.0, $previousDosis + $delta)), 2);
    }

    // -------------------------------------------------------------------------
    // Fuzzy Mamdani — Klorin (output: dosis ppm, batas 0–3)
    // -------------------------------------------------------------------------

    private function fuzzyKlorin(float $freeChlorine, float $previousDosis = 1.5): float
    {
        $f = $freeChlorine;

        $mu = [
            'sangat_rendah' => $this->leftShoulderMF($f,  0.0,  0.20),
            'rendah'        => $this->triangularMF($f,    0.15, 0.26, 0.30),
            'optimal'       => $this->triangularMF($f,    0.31, 0.37, 0.46),
            'tinggi'        => $this->triangularMF($f,    0.43, 0.48, 0.51),
            'sangat_tinggi' => $this->rightShoulderMF($f, 0.50, 0.60),
        ];

        $rules = [
            [$mu['sangat_rendah'], +1.0],
            [$mu['rendah'],        +0.4],
            [$mu['optimal'],        0.0],
            [$mu['tinggi'],        -0.7],
            [$mu['sangat_tinggi'], -2.0],
        ];
        $delta = $this->defuzzify($rules);

        if ($f >= 0.60 && $mu['sangat_tinggi'] >= 1.0) return 0.0;
        return round(max(0.0, min(3.0, $previousDosis + $delta)), 2);
    }

    // -------------------------------------------------------------------------
    // Fuzzy Mamdani — Soda Ash (output: dosis ppm, batas 0–10)
    // -------------------------------------------------------------------------

    private function fuzzySodaAsh(float $ph, float $previousDosis = 2.0): float
    {
        $p = $ph;

        $mu = [
            'sangat_rendah'  => $this->triangularMF($p, 3.0, 4.5, 5.2),
            'rendah'         => $this->triangularMF($p, 4.8, 5.5, 6.1),
            'sedikit_rendah' => $this->triangularMF($p, 5.8, 6.2, 6.5),
            'normal'         => $this->triangularMF($p, 6.5, 7.0, 7.8),
        ];
        $rules = [
            [$mu['sangat_rendah'],  +3.0],
            [$mu['rendah'],         +2.0],
            [$mu['sedikit_rendah'], +1.0],
            [$mu['normal'],          0.0],
        ];
        $delta = $this->defuzzify($rules);

        if ($p >= 6.5 || $delta == 0) return 0.0;
        return round(max(0.0, min(10.0, $previousDosis + $delta)), 2);
    }

    // -------------------------------------------------------------------------
    // Membership Function Definitions (untuk ditampilkan di UI)
    // -------------------------------------------------------------------------

    public function getMembershipDefinitions(): array
    {
        return [
            'pac' => [
                'label' => 'PAC',
                'unit'  => 'NTU',
                'sets'  => [
                    ['kategori' => 'Sangat Rendah', 'type' => 'left',     'params' => [0.0, 2.0],            'range' => '≤ 2.0',        'delta' => -3.0],
                    ['kategori' => 'Rendah',        'type' => 'triangle', 'params' => [1.0, 2.5, 3.2],       'range' => '1.0 – 3.2',    'delta' => -1.0],
                    ['kategori' => 'Optimal',        'type' => 'triangle', 'params' => [2.8, 3.3, 3.8],       'range' => '2.8 – 3.8',    'delta' =>  0.0],
                    ['kategori' => 'Tinggi',         'type' => 'triangle', 'params' => [3.4, 4.1, 5.0],       'range' => '3.4 – 5.0',    'delta' => +1.0],
                    ['kategori' => 'Sangat Tinggi',  'type' => 'right',    'params' => [4.5, 6.0],            'range' => '≥ 4.5',        'delta' => +3.0],
                ],
            ],
            'chlorine' => [
                'label' => 'Klorin',
                'unit'  => 'mg/L',
                'sets'  => [
                    ['kategori' => 'Sangat Rendah', 'type' => 'left',     'params' => [0.0, 0.20],            'range' => '≤ 0.20',       'delta' => +1.0],
                    ['kategori' => 'Rendah',         'type' => 'triangle', 'params' => [0.15, 0.26, 0.30],    'range' => '0.15 – 0.30',  'delta' => +0.4],
                    ['kategori' => 'Optimal',        'type' => 'triangle', 'params' => [0.31, 0.37, 0.46],    'range' => '0.31 – 0.46',  'delta' =>  0.0],
                    ['kategori' => 'Tinggi',         'type' => 'triangle', 'params' => [0.43, 0.48, 0.51],    'range' => '0.43 – 0.51',  'delta' => -0.7],
                    ['kategori' => 'Sangat Tinggi',  'type' => 'right',    'params' => [0.50, 0.60],           'range' => '≥ 0.50',       'delta' => -2.0],
                ],
            ],
            'soda_ash' => [
                'label' => 'Soda Ash',
                'unit'  => 'pH',
                'sets'  => [
                    ['kategori' => 'Sangat Rendah',  'type' => 'triangle', 'params' => [3.0, 4.5, 5.2],       'range' => '3.0 – 5.2',    'delta' => +3.0],
                    ['kategori' => 'Rendah',          'type' => 'triangle', 'params' => [4.8, 5.5, 6.1],       'range' => '4.8 – 6.1',    'delta' => +2.0],
                    ['kategori' => 'Sedikit Rendah',  'type' => 'triangle', 'params' => [5.8, 6.2, 6.5],       'range' => '5.8 – 6.5',    'delta' => +1.0],
                    ['kategori' => 'Normal (≥ 6.5)',  'type' => 'triangle', 'params' => [6.5, 7.0, 7.8],       'range' => '≥ 6.5',        'delta' =>  0.0],
                ],
            ],
        ];
    }

    // -------------------------------------------------------------------------
    // Format tanggal ke Bahasa Indonesia
    // -------------------------------------------------------------------------

    private function formatDateIndonesian(string $date): string
    {
        $dayNames = [
            'Monday'    => 'Senin',
            'Tuesday'   => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday'  => 'Kamis',
            'Friday'    => 'Jumat',
            'Saturday'  => 'Sabtu',
            'Sunday'    => 'Minggu',
        ];
        $carbon  = Carbon::parse($date);
        $dayName = $dayNames[$carbon->format('l')] ?? $carbon->format('l');
        return $dayName . '/' . $carbon->format('d-m-Y');
    }

    // -------------------------------------------------------------------------
    // Cari "next shift" berikutnya — O(1) lookup via index
    // -------------------------------------------------------------------------

    private function findNextShiftByIndex(array $index, array $current): ?array
    {
        $endTime = substr($current['end_time'], 0, 5);
        $date    = $current['date'];

        if ($endTime === '23:00') {
            $nextDate    = date('Y-m-d', strtotime('+1 day', strtotime($date)));
            $nextEndTime = '01:00';
        } else {
            $nextDate    = $date;
            $nextEndTime = date('H:i', strtotime('+2 hours', strtotime($endTime)));
        }

        return $index[$nextDate . '|' . $nextEndTime] ?? null;
    }

    // -------------------------------------------------------------------------
    // Build evaluation data
    // -------------------------------------------------------------------------

    public function buildEvaluationData(string $startDate, string $endDate): array
    {
        // Safety net — range panjang butuh lebih dari default 128M
        ini_set('memory_limit', '512M');

        // Extend end by 1 day untuk mengambil "next shift" dari shift terakhir
        $extendedEnd = date('Y-m-d', strtotime('+1 day', strtotime($endDate)));

        // Hanya select kolom yang dibutuhkan — hemat memory
        $shifts = Shift::select(['id', 'date', 'shift', 'end_time'])
            ->with([
                'waterQualities:id,shift_id,type,turbidity,free_chlor,ph',
                'pumpChemicals:id,shift_id,type,status,dosage',
            ])
            ->whereBetween('date', [$startDate, $extendedEnd])
            ->orderBy('date', 'asc')
            ->orderBy('end_time', 'asc')
            ->get();

        // Bangun series + index untuk O(1) findNextShift lookup
        $series      = [];
        $seriesIndex = []; // key: "date|HH:MM"

        foreach ($shifts as $shift) {
            $sed = $shift->waterQualities->firstWhere('type', 'sedimentation');
            $res = $shift->waterQualities->firstWhere('type', 'reservoir');

            $pacRunning      = $shift->pumpChemicals->where('type', 'pac')->where('status', 'running')->first()
                            ?? $shift->pumpChemicals->firstWhere('type', 'pac');
            $klorinRunning   = $shift->pumpChemicals->where('type', 'chlorine/kaporit')->where('status', 'running')->first()
                            ?? $shift->pumpChemicals->firstWhere('type', 'chlorine/kaporit');
            $sodaashRunning  = $shift->pumpChemicals->where('type', 'soda ash')->where('status', 'running')->first()
                            ?? $shift->pumpChemicals->firstWhere('type', 'soda ash');

            $entry = [
                'shift_id'          => $shift->id,
                'date'              => $shift->date,
                'shift'             => $shift->shift,
                'end_time'          => $shift->end_time,
                'turb_sed'          => $sed ? (float) $sed->turbidity : null,
                'free_chlor'        => $res ? (float) $res->free_chlor : null,
                'ph_res'            => $res ? (float) $res->ph : null,
                'prev_pac'          => ($pacRunning && $pacRunning->status === 'running') ? (float) $pacRunning->dosage : 10.0,
                'prev_klorin'       => ($klorinRunning && $klorinRunning->status === 'running') ? (float) $klorinRunning->dosage : 1.5,
                'prev_sodaash'      => ($sodaashRunning && $sodaashRunning->status === 'running') ? (float) $sodaashRunning->dosage : 2.0,
                'aktual_pac'        => ($pacRunning && $pacRunning->status === 'running') ? (float) $pacRunning->dosage : 0.0,
                'aktual_klorin'     => ($klorinRunning && $klorinRunning->status === 'running') ? (float) $klorinRunning->dosage : 0.0,
                'aktual_sodaash'    => ($sodaashRunning && $sodaashRunning->status === 'running') ? (float) $sodaashRunning->dosage : 0.0,
            ];

            $series[] = $entry;
            $seriesIndex[$shift->date . '|' . substr($shift->end_time, 0, 5)] = $entry;
        }

        // Bebaskan Eloquent collection — data sudah di $series
        unset($shifts);

        $rows = [];
        foreach ($series as $current) {
            // Hanya tampilkan shift dalam range filter
            if ($current['date'] < $startDate || $current['date'] > $endDate) continue;

            // Skip jika data kualitas air tidak lengkap
            if ($current['turb_sed'] === null || $current['free_chlor'] === null || $current['ph_res'] === null) continue;

            // Cari next shift via O(1) index lookup
            $next = $this->findNextShiftByIndex($seriesIndex, $current);
            if ($next === null) continue;

            // Hitung rekomendasi fuzzy
            $rekomPac     = $this->fuzzyPAC($current['turb_sed'], (float) $current['prev_pac']);
            $rekomKlorin  = $this->fuzzyKlorin($current['free_chlor'], (float) $current['prev_klorin']);
            $rekomSodaAsh = $this->fuzzySodaAsh($current['ph_res'], (float) $current['prev_sodaash']);

            // Ambil dosis aktual next shift (0 jika pompa standby)
            $aktualPac     = (float) $next['aktual_pac'];
            $aktualKlorin  = (float) $next['aktual_klorin'];
            $aktualSodaAsh = (float) $next['aktual_sodaash'];

            $rows[] = [
                'no'              => count($rows) + 1,
                'date'            => $this->formatDateIndonesian($current['date']),
                'shift'           => $current['shift'],
                'end_time'        => substr($current['end_time'], 0, 5),
                'next_end_time'   => substr($next['end_time'], 0, 5),
                'next_date'       => $next['date'],
                // Input fuzzy
                'turb_sed'        => $current['turb_sed'],
                'free_chlor'      => $current['free_chlor'],
                'ph_res'          => $current['ph_res'],
                // PAC
                'prev_pac'        => (float) $current['prev_pac'],
                'rekomen_pac'     => $rekomPac,
                'aktual_pac'      => $aktualPac,
                'baseline_pac'    => (float) $current['aktual_pac'], // dosis aktual 2 jam sebelumnya (shift T)
                'error_pac'       => round($rekomPac - $aktualPac, 2),
                // Klorin
                'prev_klorin'     => (float) $current['prev_klorin'],
                'rekomen_klorin'  => $rekomKlorin,
                'aktual_klorin'   => $aktualKlorin,
                'error_klorin'    => round($rekomKlorin - $aktualKlorin, 2),
                // Soda Ash
                'prev_sodaash'    => (float) $current['prev_sodaash'],
                'rekomen_sodaash' => $rekomSodaAsh,
                'aktual_sodaash'  => $aktualSodaAsh,
                'error_sodaash'   => round($rekomSodaAsh - $aktualSodaAsh, 2),
            ];
        }

        return $rows;
    }

    // -------------------------------------------------------------------------
    // Hitung metrik: RMSE, MAE, MAPE
    // -------------------------------------------------------------------------

    public function calculateMetrics(array $rows, string $param): array
    {
        // PAC: gunakan mode yang dikonfigurasi via PAC_EVAL_MODE
        $rekomKey  = ($param === 'pac' && static::PAC_EVAL_MODE === 'prev')
            ? 'baseline_pac'       // Aktual (T+1) vs Dosis 2 jam sebelumnya (shift T)
            : "rekomen_$param";    // Aktual (T+1) vs Rekomendasi Fuzzy
        $aktualKey = "aktual_$param";

        $n = count($rows);
        if ($n === 0) return ['rmse' => 0, 'mae' => 0, 'mape' => 0, 'n' => 0];

        $sumSquare = 0;
        $sumAbs    = 0;
        $sumPct    = 0;
        $countPct  = 0;

        foreach ($rows as $r) {
            $err        = $r[$rekomKey] - $r[$aktualKey];
            $sumSquare += $err * $err;
            $sumAbs    += abs($err);

            if ($r[$aktualKey] != 0) {
                $sumPct += abs($err / $r[$aktualKey]);
                $countPct++;
            }
        }

        return [
            'rmse' => round(sqrt($sumSquare / $n), 4),
            'mae'  => round($sumAbs / $n, 4),
            'mape' => $countPct > 0 ? round(($sumPct / $countPct) * 100, 2) : 0,
            'n'    => $n,
        ];
    }

    public function interpretMape(float $mape): string
    {
        if ($mape < 10)  return 'Sangat Akurat';
        if ($mape < 20)  return 'Akurat / Baik';
        if ($mape < 50)  return 'Cukup / Wajar';
        return 'Tidak Akurat';
    }
}

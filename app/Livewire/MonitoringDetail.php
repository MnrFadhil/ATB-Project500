<?php

namespace App\Livewire;

use App\Models\Shift;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class MonitoringDetail extends Component
{
    public $id;
    public $isAdmin;

    public function mount($id)
    {
        $this->id      = $id;
        $this->isAdmin = Auth::user()->role == 'admin';
    }

    // -------------------------------------------------------------------------
    // WMA — Weighted Moving Average
    // Bobot [1, 3, 30]: data terbaru paling berpengaruh
    // -------------------------------------------------------------------------

    private function calculateWMA(array $dataArray): float
    {
        $lastThree   = array_slice($dataArray, -3);
        $weights     = [1, 3, 30];
        $weightedSum = 0;
        $weightTotal = 0;

        foreach ($lastThree as $i => $value) {
            $weightedSum += $value * $weights[$i];
            $weightTotal += $weights[$i];
        }

        return round($weightedSum / $weightTotal, 2);
    }

    // -------------------------------------------------------------------------
    // Fuzzy Membership Functions
    // -------------------------------------------------------------------------

    // Sisi kiri: μ=1 saat x <= a, turun ke 0 di x = b
    private function leftShoulderMF(float $x, float $a, float $b): float
    {
        if ($x <= $a) return 1.0;
        if ($x >= $b) return 0.0;
        return ($b - $x) / ($b - $a);
    }

    // Segitiga: naik dari a ke b (puncak), turun dari b ke c
    private function triangularMF(float $x, float $a, float $b, float $c): float
    {
        if ($x <= $a || $x >= $c) return 0.0;
        if ($x <= $b) return ($x - $a) / ($b - $a);
        return ($c - $x) / ($c - $b);
    }

    // Sisi kanan: μ=0 saat x <= a, naik ke 1 di x = b, tetap 1 sampai ∞
    private function rightShoulderMF(float $x, float $a, float $b): float
    {
        if ($x <= $a) return 0.0;
        if ($x >= $b) return 1.0;
        return ($x - $a) / ($b - $a);
    }

    // Defuzzifikasi centroid: Σ(μ × center) / Σ(μ)
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
    // Fuzzy Mamdani — PAC
    // Input: turbidity sedimentasi (NTU), domain 0 → ∞
    // Output: rekomendasi dosis PAC (ppm), batas 5–20 ppm
    // -------------------------------------------------------------------------

    private function fuzzyPAC($sedimentationTurbidity, float $previousDosis = 0): array
    {
        $t = (float) $sedimentationTurbidity;

        // Fuzzifikasi
        $mu = [
            'sangat_rendah' => $this->leftShoulderMF($t,  0.0, 2.0),
            'rendah'        => $this->triangularMF($t,    1.0, 2.5, 3.2),
            'optimal'       => $this->triangularMF($t,    2.8, 3.3, 3.8),
            'tinggi'        => $this->triangularMF($t,    3.4, 4.1, 5.0),
            'sangat_tinggi' => $this->rightShoulderMF($t, 4.5, 6.0),
        ];

        // Rules [μ, delta dosis]
        $rules = [
            [$mu['sangat_rendah'], -3.0],
            [$mu['rendah'],        -1.0],
            [$mu['optimal'],        0.0],
            [$mu['tinggi'],        +1.5],
            [$mu['sangat_tinggi'], +3.5],
        ];

        $delta          = $this->defuzzify($rules);
        $recommendation = round(max(5.0, min(20.0, $previousDosis + $delta)), 2);

        $dominant  = array_search(max($mu), $mu);
        $statusMap = [
            'sangat_rendah' => ['Dosis Terlalu Tinggi',            'danger'],
            'rendah'        => ['Dosis Sedikit Tinggi',            'warning'],
            'optimal'       => ['Dosis Optimal',                   'success'],
            'tinggi'        => ['Dosis Sedikit Rendah',            'warning'],
            'sangat_tinggi' => ['Emergency - Dosis Sangat Rendah', 'danger'],
        ];
        [$status, $color] = $statusMap[$dominant];

        $hexColor = match ($color) {
            'danger'  => 'e74a3b',
            'success' => '1cc88a',
            default   => 'f6c23e',
        };

        return [
            'status'         => $status,
            'recommendation' => $recommendation,
            'message'        => "Turbidity sedimentasi <strong style='font-size:13px;color:#{$hexColor};'>{$t} NTU</strong>, Delta dosis: <strong>{$delta} ppm</strong> → Rekomendasi: <strong style='font-size:13px;color:#{$hexColor};'>{$recommendation} ppm</strong>",
            'color'          => $color,
        ];
    }

    // -------------------------------------------------------------------------
    // Fuzzy Mamdani — Klorin
    // Input: free chlorine reservoir (mg/L), domain 0 → ∞
    // Output: rekomendasi dosis Klorin (ppm), batas 0–3 ppm
    // -------------------------------------------------------------------------

    private function fuzzyKlorin($freeChlorine, float $previousDosis = 0): array
    {
        $f = (float) $freeChlorine;

        // Fuzzifikasi
        $mu = [
            'sangat_rendah' => $this->leftShoulderMF($f,  0.0,  0.20),
            'rendah'        => $this->triangularMF($f,    0.15, 0.26, 0.30),
            'optimal'       => $this->triangularMF($f,    0.28, 0.37, 0.46),
            'tinggi'        => $this->triangularMF($f,    0.43, 0.48, 0.51),
            'sangat_tinggi' => $this->rightShoulderMF($f, 0.50, 0.60),
        ];

        // Rules [μ, delta dosis]
        $rules = [
            [$mu['sangat_rendah'], +1.0],
            [$mu['rendah'],        +0.4],
            [$mu['optimal'],        0.0],
            [$mu['tinggi'],        -0.7],
            [$mu['sangat_tinggi'], -2.0],
        ];

        $delta = $this->defuzzify($rules);

        // Kondisi khusus: free chlor >= 0.60 → matikan pompa
        if ($f >= 0.60 && $mu['sangat_tinggi'] >= 1.0) {
            return [
                'status'         => 'Emergency - Matikan Pompa',
                'recommendation' => 0,
                'message'        => "Free Chlor Reservoir <strong style='font-size:13px;color:#e74a3b;'>{$f} mg/L</strong> (Sangat Tinggi). <strong style='font-size:13px;color:#e74a3b;'>Matikan Pompa Dosing Chlorine!!!</strong><br><small class='font-weight-bold' style='color:#000;font-size:13px;'>Silahkan Cek Free Chlorine Air Reservoir Secara Berkala!!!</small>",
                'color'          => 'danger',
            ];
        }

        $recommendation = round(max(0.0, min(3.0, $previousDosis + $delta)), 2);

        $dominant  = array_search(max($mu), $mu);
        $statusMap = [
            'sangat_rendah' => ['Emergency - Free Chlor Sangat Rendah', 'danger'],
            'rendah'        => ['Dosis Terlalu Rendah',                 'warning'],
            'optimal'       => ['OPTIMAL',                              'success'],
            'tinggi'        => ['Dosis Terlalu Tinggi',                 'warning'],
            'sangat_tinggi' => ['Dosis Sangat Tinggi',                  'danger'],
        ];
        [$status, $color] = $statusMap[$dominant];

        $hexColor = match ($color) {
            'danger'  => 'e74a3b',
            'success' => '1cc88a',
            default   => 'f6c23e',
        };

        return [
            'status'         => $status,
            'recommendation' => $recommendation,
            'message'        => "Free Chlor Reservoir <strong style='font-size:13px;color:#{$hexColor};'>{$f} mg/L</strong>, Delta dosis: <strong>{$delta} ppm</strong> → Rekomendasi: <strong style='font-size:13px;color:#{$hexColor};'>{$recommendation} ppm</strong>",
            'color'          => $color,
        ];
    }

    // -------------------------------------------------------------------------
    // Fuzzy Mamdani — Soda Ash
    // Input: pH reservoir, domain 0 → ∞
    // Output: rekomendasi dosis Soda Ash (ppm), batas 0–10 ppm
    // Catatan: Soda Ash hanya untuk MENAIKKAN pH, jika normal → standby
    // -------------------------------------------------------------------------

    private function fuzzySodaAsh($ph, float $previousDosis = 2.0): array
    {
        $p = (float) $ph;

        // Fuzzifikasi
        $mu = [
            'sangat_rendah'  => $this->triangularMF($p, 3.0, 4.5, 5.2),
            'rendah'         => $this->triangularMF($p, 4.8, 5.5, 6.1),
            'sedikit_rendah' => $this->triangularMF($p, 5.8, 6.2, 6.5),
            'normal'         => $this->triangularMF($p, 6.3, 7.0, 7.8),
        ];

        // Rules [μ, delta dosis]
        $rules = [
            [$mu['sangat_rendah'],  +3.0],
            [$mu['rendah'],         +2.0],
            [$mu['sedikit_rendah'], +1.0],
            [$mu['normal'],          0.0],
        ];

        $delta = $this->defuzzify($rules);

        // pH >= 6.5 atau delta = 0 → tidak perlu dosing
        if ($p >= 6.5 || $delta == 0) {
            return [
                'status'         => 'Pompa Standby',
                'recommendation' => 0,
                'message'        => "pH normal <strong>({$p})</strong>, Pompa Soda Ash Bisa Standby",
                'color'          => 'secondary',
            ];
        }

        $recommendation = round(max(0.0, min(10.0, $previousDosis + $delta)), 2);

        $dominant  = array_search(max($mu), $mu);
        $statusMap = [
            'sangat_rendah'  => ['Pompa Running - EMERGENCY',      'danger'],
            'rendah'         => ['Pompa Running - Terlalu Rendah', 'warning'],
            'sedikit_rendah' => ['Pompa Running - Sedikit Rendah', 'warning'],
            'normal'         => ['Pompa Standby',                  'secondary'],
        ];
        [$status, $color] = $statusMap[$dominant];

        $hexColor = $color === 'danger' ? 'e74a3b' : 'f6c23e';

        return [
            'status'         => $status,
            'recommendation' => $recommendation,
            'message'        => "pH Reservoir <strong style='font-size:13px;color:#{$hexColor};'>{$p}</strong>, Delta dosis: <strong>{$delta} ppm</strong> → Rekomendasi: <strong style='font-size:13px;'>{$recommendation} ppm</strong>",
            'color'          => $color,
        ];
    }

    // -------------------------------------------------------------------------
    // Ambil data historis untuk WMA
    // Mengambil 2 shift sebelumnya + shift sekarang = 3 titik data
    // -------------------------------------------------------------------------

    private function getHistoricalWaterQualities($currentShiftId): ?array
    {
        $currentShift   = Shift::find($currentShiftId);
        $currentDate    = $currentShift->date;
        $currentEndTime = $currentShift->end_time;
        $currentHour    = (int) substr($currentEndTime, 0, 2);
        $baseTime       = strtotime($currentEndTime);

        if ($currentHour == 1) {
            // end_time 01:00 → ambil 23:00 dan 21:00 hari kemarin
            $yesterdayDate = date('Y-m-d', strtotime('-1 day', strtotime($currentDate)));
            $shifts = Shift::where('id', '!=', $currentShiftId)
                ->where('date', $yesterdayDate)
                ->whereIn('end_time', ['23:00', '21:00'])
                ->orderBy('end_time', 'asc')
                ->with('waterQualities', 'pumpChemicals')
                ->get();

        } elseif ($currentHour == 3) {
            // end_time 03:00 → ambil 01:00 hari ini + 23:00 hari kemarin
            $yesterdayDate = date('Y-m-d', strtotime('-1 day', strtotime($currentDate)));

            $shift01Today = Shift::where('id', '!=', $currentShiftId)
                ->where('date', $currentDate)->where('end_time', '01:00')
                ->with('waterQualities', 'pumpChemicals')->first();

            $shift23Yest = Shift::where('id', '!=', $currentShiftId)
                ->where('date', $yesterdayDate)->where('end_time', '23:00')
                ->with('waterQualities', 'pumpChemicals')->first();

            $shifts = collect();
            if ($shift23Yest)  $shifts->push($shift23Yest);
            if ($shift01Today) $shifts->push($shift01Today);

        } else {
            // end_time lain → ambil 2 jam dan 4 jam sebelumnya
            $time2HoursAgo = date('H:i', strtotime('-2 hours', $baseTime));
            $time4HoursAgo = date('H:i', strtotime('-4 hours', $baseTime));

            $shifts = Shift::where('id', '!=', $currentShiftId)
                ->where('date', $currentDate)
                ->whereIn('end_time', [$time2HoursAgo, $time4HoursAgo])
                ->orderBy('end_time', 'asc')
                ->with('waterQualities', 'pumpChemicals')
                ->get();
        }

        if ($shifts->count() < 2) return null;

        $data = [
            'turbidityAB'   => [],
            'turbiditySeq'  => [],
            'phAB'          => [],
            'phRes'         => [],
            'tdsAB'         => [],
            'freeChlorine'  => [],
            'pACDosis'      => [],
            'chlorineDosis' => [],
            'sodaAshDosis'  => [],
        ];

        // Data dari shift-shift sebelumnya
        foreach ($shifts as $shift) {
            foreach ($shift->waterQualities as $wq) {
                if ($wq->type == 'air baku') {
                    $data['turbidityAB'][] = $wq->turbidity;
                    $data['phAB'][]        = $wq->ph;
                    $data['tdsAB'][]       = $wq->tds;
                } elseif ($wq->type == 'sedimentation') {
                    $data['turbiditySeq'][] = $wq->turbidity;
                } elseif ($wq->type == 'reservoir') {
                    $data['phRes'][]        = $wq->ph;
                    $data['freeChlorine'][] = $wq->free_chlor ?? 0;
                }
            }

            $data['pACDosis'][]      = $shift->pumpChemicals()->where('type', 'pac')->first()?->dosage ?? 0;
            $data['chlorineDosis'][] = $shift->pumpChemicals()->where('type', 'chlorine/kaporit')->first()?->dosage ?? 0;
            $data['sodaAshDosis'][]  = $shift->pumpChemicals()->where('type', 'soda ash')->first()?->dosage ?? 0;
        }

        // Data dari shift sekarang (titik ke-3)
        foreach ($currentShift->waterQualities as $wq) {
            if ($wq->type == 'air baku') {
                $data['turbidityAB'][] = $wq->turbidity;
                $data['phAB'][]        = $wq->ph;
                $data['tdsAB'][]       = $wq->tds;
            } elseif ($wq->type == 'sedimentation') {
                $data['turbiditySeq'][] = $wq->turbidity;
            } elseif ($wq->type == 'reservoir') {
                $data['phRes'][]        = $wq->ph;
                $data['freeChlorine'][] = $wq->free_chlor ?? 0;
            }
        }

        $data['pACDosis'][]      = $currentShift->pumpChemicals()->where('type', 'pac')->first()?->dosage ?? 0;
        $data['chlorineDosis'][] = $currentShift->pumpChemicals()->where('type', 'chlorine/kaporit')->first()?->dosage ?? 0;
        $data['sodaAshDosis'][]  = $currentShift->pumpChemicals()->where('type', 'soda ash')->first()?->dosage ?? 0;

        if (count($data['turbidityAB']) < 3) return null;

        return $data;
    }

    // -------------------------------------------------------------------------
    // Render
    // -------------------------------------------------------------------------

    public function render()
    {
        $shift = Shift::with([
            'shiftOperators',
            'waterQualities',
            'flowMeters',
            'reservoirLevels',
            'mdpPanels',
            'pumpProccess',
            'pumpChemicals',
            'unitOperation',
            'wtps',
            'pressureStaticMixer',
        ])->find($this->id);

        $historicalData = $this->getHistoricalWaterQualities($this->id);

        // Pisahkan water quality berdasarkan tipe
        $currentAB  = null;
        $currentSed = null;
        $currentRes = null;

        foreach ($shift->waterQualities as $wq) {
            if ($wq->type == 'air baku')          $currentAB  = $wq;
            elseif ($wq->type == 'sedimentation') $currentSed = $wq;
            elseif ($wq->type == 'reservoir')     $currentRes = $wq;
        }

        $dataIncomplete = $historicalData === null;

        // Default jika data historis belum tersedia
        $wmaData = [
            'turbidityAB'   => null,
            'phAB'          => null,
            'tdsAB'         => null,
            'freeChlorine'  => null,
            'pacDosis'      => null,
            'chlorineDosis' => null,
            'sodaAshDosis'  => null,
        ];
        $chartData  = [
            'historicalTurbidity' => [],
            'historicalPH'        => [],
            'historicalTDS'       => [],
            'wmaPredictions'      => $wmaData,
        ];
        $timeLabels = [];

        // Hitung WMA jika data historis tersedia
        if (!$dataIncomplete) {
            $wmaData = [
                'turbidityAB'   => $this->calculateWMA($historicalData['turbidityAB']),
                'phAB'          => $this->calculateWMA($historicalData['phAB']),
                'tdsAB'         => $this->calculateWMA($historicalData['tdsAB']),
                'freeChlorine'  => $this->calculateWMA($historicalData['freeChlorine']),
                'pacDosis'      => $this->calculateWMA($historicalData['pACDosis']),
                'chlorineDosis' => $this->calculateWMA($historicalData['chlorineDosis']),
                'sodaAshDosis'  => $this->calculateWMA($historicalData['sodaAshDosis']),
            ];

            $chartData = [
                'historicalTurbidity' => $historicalData['turbidityAB'],
                'historicalPH'        => $historicalData['phAB'],
                'historicalTDS'       => $historicalData['tdsAB'],
                'wmaPredictions'      => $wmaData,
            ];

            $baseTime     = strtotime($shift->end_time);
            $timeLabels[] = date('H:i', strtotime('-4 hours', $baseTime));
            $timeLabels[] = date('H:i', strtotime('-2 hours', $baseTime));
            $timeLabels[] = date('H:i', $baseTime);
            $timeLabels[] = 'Prediksi';
        }

        // Hitung Fuzzy Mamdani — selalu berjalan, independen dari WMA
        // Prioritaskan pompa yang statusnya 'running', fallback ke first()
        $pacChemical      = $shift->pumpChemicals()->where('type', 'pac')->where('status', 'running')->first()
                         ?? $shift->pumpChemicals()->where('type', 'pac')->first();
        $chlorineChemical = $shift->pumpChemicals()->where('type', 'chlorine/kaporit')->where('status', 'running')->first()
                         ?? $shift->pumpChemicals()->where('type', 'chlorine/kaporit')->first();
        $sodaAshChemical  = $shift->pumpChemicals()->where('type', 'soda ash')->where('status', 'running')->first()
                         ?? $shift->pumpChemicals()->where('type', 'soda ash')->first();

        $sdsRecommendations = [
            'pac'     => $currentSed ? $this->fuzzyPAC($currentSed->turbidity, $pacChemical?->dosage ?? 10.0)              : null,
            'klorin'  => $currentRes ? $this->fuzzyKlorin($currentRes->free_chlor ?? 0, $chlorineChemical?->dosage ?? 1.5) : null,
            'sodaAsh' => $currentRes ? $this->fuzzySodaAsh($currentRes->ph, $sodaAshChemical?->dosage ?? 2.0)              : null,
        ];

        return view('livewire.monitoring-detail', [
            'shifts'             => $shift,
            'wmaData'            => $wmaData,
            'sdsRecommendations' => $sdsRecommendations,
            'chartData'          => $chartData,
            'currentAB'          => $currentAB,
            'currentSed'         => $currentSed,
            'currentRes'         => $currentRes,
            'dataIncomplete'     => $dataIncomplete,
            'timeLabels'         => $timeLabels,
        ])->layout('layouts.app');
    }
}

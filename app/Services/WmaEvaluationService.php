<?php

namespace App\Services;

use App\Models\Shift;
use Carbon\Carbon;

class WmaEvaluationService
{
    /**
     * Bobot WMA: [data terlama, tengah, terbaru]
     * W1=8 (terbaru), W2=2, W3=1 (terlama)
     */
    protected array $weights = [1, 3, 30];

    /**
     * Hitung WMA dari array data (3 elemen, urut lama ke baru)
     */
    public function calculateWMA(array $lastThree): float
    {
        $weightedSum = 0;
        $weightTotal = 0;
        foreach ($lastThree as $i => $value) {
            $weightedSum += $value * $this->weights[$i];
            $weightTotal += $this->weights[$i];
        }
        return round($weightedSum / $weightTotal, 2);
    }


    /**
     * Format tanggal ke Bahasa Indonesia lengkap (Hari/dd-mm-yyyy)
     */
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

        $carbon = \Carbon\Carbon::parse($date);
        $dayName = $dayNames[$carbon->format('l')] ?? $carbon->format('l');
        
        return $dayName . '/' . $carbon->format('d-m-Y');
    }

    /**
     * Ambil semua shift dalam range tanggal, urutkan kronologis,
     * lalu untuk setiap shift hitung prediksi WMA berdasarkan 3 shift sebelumnya.
     * HANYA hitung jika 3 shift sebelumnya BERURUTAN (tidak ada gap tanggal besar).
     */
    public function buildEvaluationData(string $startDate, string $endDate): array
    {
        // Ambil data 3 hari sebelum startDate untuk keperluan prior WMA
        $extendedStart = date('Y-m-d', strtotime('-3 days', strtotime($startDate)));

        // Ambil semua shift dari extendedStart sampai endDate
        $shifts = Shift::with('waterQualities')
            ->whereBetween('date', [$extendedStart, $endDate])
            ->orderBy('date', 'asc')
            ->orderBy('end_time', 'asc')
            ->get();

        // Ekstrak nilai air baku per shift
        $series = [];
        foreach ($shifts as $shift) {
            $ab = $shift->waterQualities->firstWhere('type', 'air baku');
            if (!$ab) continue;

            $series[] = [
                'shift_id'   => $shift->id,
                'date'       => $shift->date,
                'shift'      => $shift->shift,
                'end_time'   => $shift->end_time,
                'turbidity'  => (float) $ab->turbidity,
                'ph'         => (float) $ab->ph,
                'tds'        => (float) $ab->tds,
            ];
        }

        // Untuk setiap shift mulai indeks ke-3,
        // hitung WMA dari 3 shift sebelumnya
        // TAPI hanya tampilkan data yang tanggalnya dalam range filter
        $rows = [];
        for ($i = 3; $i < count($series); $i++) {

            // Skip jika tanggal diluar range filter
            if ($series[$i]['date'] < $startDate || $series[$i]['date'] > $endDate) continue;

            $prev3 = array_slice($series, $i - 3, 3);

            $predTurb = $this->calculateWMA(array_column($prev3, 'turbidity'));
            $predPh   = $this->calculateWMA(array_column($prev3, 'ph'));
            $predTds  = $this->calculateWMA(array_column($prev3, 'tds'));

            $rows[] = [
                'no'              => count($rows) + 1,
                'date'            => $this->formatDateIndonesian($series[$i]['date']),
                'shift'           => $series[$i]['shift'],
                'end_time'        => substr($series[$i]['end_time'], 0, 5),  // Ambil HH:MM saja
                'aktual_turb'     => $series[$i]['turbidity'],
                'prediksi_turb'   => $predTurb,
                'error_turb'      => round($series[$i]['turbidity'] - $predTurb, 2),
                'aktual_ph'       => $series[$i]['ph'],
                'prediksi_ph'     => $predPh,
                'error_ph'        => round($series[$i]['ph'] - $predPh, 2),
                'aktual_tds'      => $series[$i]['tds'],
                'prediksi_tds'    => $predTds,
                'error_tds'       => round($series[$i]['tds'] - $predTds, 2),
            ];
        }

        return $rows;
    }
    /**
     * Hitung metrik akurasi: RMSE, MAE, MAPE
     */
    public function calculateMetrics(array $rows, string $param): array
    {
        $aktualKey   = "aktual_$param";
        $prediksiKey = "prediksi_$param";

        $n = count($rows);
        if ($n === 0) {
            return ['rmse' => 0, 'mae' => 0, 'mape' => 0, 'n' => 0];
        }

        $sumSquare = 0;
        $sumAbs    = 0;
        $sumPct    = 0;
        $countPct  = 0;

        foreach ($rows as $r) {
            $err = $r[$aktualKey] - $r[$prediksiKey];
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

    /**
     * Interpretasi MAPE sesuai standar Lewis (1982)
     */
    public function interpretMape(float $mape): string
    {
        if ($mape < 10)  return 'Sangat Akurat';
        if ($mape < 20)  return 'Akurat / Baik';
        if ($mape < 50)  return 'Cukup / Wajar';
        return 'Tidak Akurat';
    }

    /**
     * Get bobot WMA untuk ditampilkan di view
     */
    public function getWeights(): array
    {
        return [
            'w1' => $this->weights[2],  // terbaru
            'w2' => $this->weights[1],  // tengah
            'w3' => $this->weights[0],  // terlama
            'total' => array_sum($this->weights),
        ];
    }
}
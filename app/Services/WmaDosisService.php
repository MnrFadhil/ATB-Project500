<?php

namespace App\Services;

use App\Models\Shift;
use App\Models\WmaSetting;
use Carbon\Carbon;

class WmaDosisService
{
    protected array $weights;

    public function __construct()
    {
        $this->weights = WmaSetting::getWeights('dosis_kimia', [1, 1, 10]);
    }

    private const CHEM_TYPES = [
        'pac'      => 'pac',
        'chlorine' => 'chlorine/kaporit',
        'soda_ash' => 'soda ash',
    ];

    private const CHEM_LABELS = [
        'pac'      => 'PAC (Koagulan)',
        'chlorine' => 'Klorin (Desinfektan)',
        'soda_ash' => 'Soda Ash (pH)',
    ];

    public function getWeights(): array
    {
        return [
            'w1' => $this->weights[2],
            'w2' => $this->weights[1],
            'w3' => $this->weights[0],
            'total' => array_sum($this->weights),
        ];
    }

    public function getChemLabels(): array { return self::CHEM_LABELS; }

    private function wma(array $three): float
    {
        $sum = 0;
        foreach ($three as $i => $v) $sum += $v * $this->weights[$i];
        return round($sum / array_sum($this->weights), 2);
    }

    /**
     * Ambil rata-rata dosis per BULAN kalender, grouped per chem type.
     * Menggantikan pendekatan lama yang mengelompokkan per minggu ISO —
     * dengan basis bulan kalender, prediksi jadi apple-to-apple dengan
     * "1 bulan" tanpa sisa hari yang kepotong minggu ISO.
     *
     * @param string $start Y-m-d
     * @param string $end   Y-m-d
     */
    public function getMonthlyAverages(string $start, string $end): array
    {
        $shifts = Shift::with(['pumpChemicals' => fn($q) => $q->whereNull('deleted_at')])
            ->whereBetween('date', [$start, $end])
            ->whereNull('deleted_at')
            ->orderBy('date')->get();

        $result = [];
        foreach (self::CHEM_TYPES as $key => $dbType) {
            $months = [];
            foreach ($shifts as $s) {
                foreach ($s->pumpChemicals->where('type', $dbType) as $c) {
                    if ($c->dosage <= 0) continue;

                    $mKey = Carbon::parse($s->date)->format('Y-m');
                    if (!isset($months[$mKey])) {
                        $months[$mKey] = [
                            'month' => $mKey,
                            'total' => 0,
                            'count' => 0,
                            'start' => $s->date,
                            'end'   => $s->date,
                        ];
                    }
                    $months[$mKey]['total'] += $c->dosage;
                    $months[$mKey]['count']++;
                    $months[$mKey]['records'][] = [
                        'date'   => $s->date,
                        'shift'  => $s->shift,
                        'dosage' => $c->dosage,
                    ];
                    if ($s->date > $months[$mKey]['end'])   $months[$mKey]['end']   = $s->date;
                    if ($s->date < $months[$mKey]['start']) $months[$mKey]['start'] = $s->date;
                }
            }

            $result[$key] = collect($months)->sortKeys()->map(fn($m) => [
                'month'      => $m['month'],
                'label'      => Carbon::parse($m['month'] . '-01')->translatedFormat('M Y'),
                'start'      => $m['start'],
                'end'        => $m['end'],
                'avg_dosage' => round($m['total'] / $m['count'], 2),
                'count'      => $m['count'],
                'records'    => $m['records'] ?? [],
            ])->values()->toArray();
        }
        return $result;
    }

    public function interpretMape(float $mape): string
    {
        if ($mape < 10)  return 'Sangat Akurat';
        if ($mape < 20)  return 'Akurat / Baik';
        if ($mape < 50)  return 'Cukup / Wajar';
        return 'Tidak Akurat';
    }

    private function calculateDosisMetrics(array $rows): array
    {
        $n = count($rows);
        if ($n === 0) return ['mape' => 0, 'n' => 0, 'interpretasi' => '-'];

        $sumPct    = 0;
        $countPct  = 0;

        foreach ($rows as $r) {
            $err = $r['prediksi'] - $r['aktual'];
            if ($r['aktual'] != 0) {
                $sumPct += abs($err / $r['aktual']);
                $countPct++;
            }
        }

        $mape = $countPct > 0 ? round(($sumPct / $countPct) * 100, 2) : 0;
        return [
            'mape'        => $mape,
            'n'           => $n,
            'interpretasi'=> $countPct > 0 ? $this->interpretMape($mape) : '-',
        ];
    }

    /**
     * Evaluasi prediksi 1-bulan terhadap data aktual bulan target.
     * Hanya dievaluasi jika bulan target sudah selesai penuh (bukan bulan berjalan),
     * supaya rata-rata aktual tidak dihitung dari data yang belum lengkap sebulan.
     */
    public function evaluatePredictions(array $predictions, string $targetMonth): array
    {
        $target     = Carbon::parse($targetMonth . '-01');
        $start      = $target->copy()->startOfMonth()->format('Y-m-d');
        $end        = $target->copy()->endOfMonth()->format('Y-m-d');
        $isComplete = Carbon::today()->greaterThan($target->copy()->endOfMonth());

        $actualMonthly = $isComplete ? $this->getMonthlyAverages($start, $end) : [];

        $evaluation = [];
        foreach ($predictions as $chemKey => $pred) {
            $actualEntry = $actualMonthly[$chemKey][0] ?? null;
            $rows = [];

            if ($pred !== null && $actualEntry !== null) {
                $aktual  = $actualEntry['avg_dosage'];
                $predVal = $pred['avg_dosage'];

                $rows[] = [
                    'label'        => $pred['label'],
                    'aktual'       => $aktual,
                    'prediksi'     => $predVal,
                    'error'        => round($predVal - $aktual, 2),
                    'aktual_count' => $actualEntry['count'],
                    'prior_data'   => $pred['prior_data'] ?? [],
                ];
            }

            $evaluation[$chemKey] = [
                'rows'        => $rows,
                'is_complete' => $isComplete,
                'metrics'     => $this->calculateDosisMetrics($rows),
            ];
        }

        return $evaluation;
    }

    /**
     * Prediksi 1 BULAN ke depan (bulan target) dari rata-rata 3 bulan sebelumnya.
     * Non-rekursif: hanya 1x hitung WMA dari 3 titik data bulanan ASLI
     * (tidak pernah pakai hasil prediksi sistem sendiri sebagai input).
     */
    public function predictNextMonth(array $monthlyData, string $targetMonth): array
    {
        $targetLabel = Carbon::parse($targetMonth . '-01')->translatedFormat('F Y');
        $predictions = [];

        foreach ($monthlyData as $chemKey => $months) {
            $n = count($months);

            if ($n >= 3) {
                $three = array_column(array_slice($months, -3), 'avg_dosage');
            } elseif ($n === 2) {
                $three = [$months[0]['avg_dosage'], $months[0]['avg_dosage'], $months[1]['avg_dosage']];
            } elseif ($n === 1) {
                $three = [$months[0]['avg_dosage'], $months[0]['avg_dosage'], $months[0]['avg_dosage']];
            } else {
                $predictions[$chemKey] = null;
                continue;
            }

            $predictions[$chemKey] = [
                'month'         => $targetMonth,
                'label'         => $targetLabel,
                'avg_dosage'    => $this->wma($three),
                'is_prediction' => true,
                'prior_data'    => $three,
            ];
        }

        return $predictions;
    }
}

<?php

namespace App\Services;

use App\Models\Shift;
use Carbon\Carbon;

class WmaDosisService
{
    protected array $weights = [1, 1, 10]; // [terlama, tengah, terbaru]

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
     * Ambil rata-rata dosis per minggu ISO, grouped per chem type
     * @param string $start Y-m-d
     * @param string $end Y-m-d
     */
    public function getWeeklyAverages(string $start, string $end): array
    {
        $shifts = Shift::with(['pumpChemicals' => fn($q) => $q->whereNull('deleted_at')])
            ->whereBetween('date', [$start, $end])
            ->whereNull('deleted_at')
            ->orderBy('date')->get();

        $result = [];
        foreach (self::CHEM_TYPES as $key => $dbType) {
            $weeks = [];
            foreach ($shifts as $s) {
                foreach ($s->pumpChemicals->where('type', $dbType) as $c) {
                    if ($c->dosage <= 0) continue;
                    $w = Carbon::parse($s->date)->isoWeek();
                    $wKey = Carbon::parse($s->date)->format('o-\WW');
                    if (!isset($weeks[$wKey])) {
                        $weeks[$wKey] = ['week' => $wKey, 'week_num' => $w, 'total' => 0, 'count' => 0,
                            'start' => $s->date, 'end' => $s->date];
                    }
                    $weeks[$wKey]['total'] += $c->dosage;
                    $weeks[$wKey]['count']++;
                    if ($s->date > $weeks[$wKey]['end']) $weeks[$wKey]['end'] = $s->date;
                    if ($s->date < $weeks[$wKey]['start']) $weeks[$wKey]['start'] = $s->date;
                }
            }
            $result[$key] = collect($weeks)->sortKeys()->map(fn($w) => [
                'week' => $w['week'], 'week_num' => $w['week_num'],
                'start' => $w['start'], 'end' => $w['end'],
                'avg_dosage' => round($w['total'] / $w['count'], 2),
                'count' => $w['count'],
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
        if ($n === 0) return ['rmse' => 0, 'mae' => 0, 'mape' => 0, 'n' => 0, 'interpretasi' => '-'];

        $sumSquare = 0;
        $sumAbs    = 0;
        $sumPct    = 0;
        $countPct  = 0;

        foreach ($rows as $r) {
            $err        = $r['prediksi'] - $r['aktual'];
            $sumSquare += $err * $err;
            $sumAbs    += abs($err);
            if ($r['aktual'] != 0) {
                $sumPct += abs($err / $r['aktual']);
                $countPct++;
            }
        }

        $mape = $countPct > 0 ? round(($sumPct / $countPct) * 100, 2) : 0;
        return [
            'rmse'        => round(sqrt($sumSquare / $n), 4),
            'mae'         => round($sumAbs / $n, 4),
            'mape'        => $mape,
            'n'           => $n,
            'interpretasi'=> $this->interpretMape($mape),
        ];
    }

    /**
     * Evaluasi prediksi terhadap data aktual bulan target
     */
    public function evaluatePredictions(array $predictions, string $targetMonth): array
    {
        $target = Carbon::parse($targetMonth . '-01');
        $start  = $target->copy()->startOfMonth()->format('Y-m-d');
        $end    = $target->copy()->endOfMonth()->format('Y-m-d');

        $actualWeekly = $this->getWeeklyAverages($start, $end);

        // Keluarkan minggu yang sedang berjalan (belum selesai)
        $todayIsoWeek = Carbon::today()->isoWeek();
        $todayIsoYear = Carbon::today()->isoWeekYear();
        foreach ($actualWeekly as $key => $weeks) {
            $actualWeekly[$key] = array_values(array_filter($weeks, function ($w) use ($todayIsoWeek, $todayIsoYear) {
                $wDate = Carbon::parse($w['start']);
                return !($wDate->isoWeek() === $todayIsoWeek && $wDate->isoWeekYear() === $todayIsoYear);
            }));
        }

        $evaluation = [];
        foreach ($predictions as $chemKey => $preds) {
            $actuals = array_values($actualWeekly[$chemKey] ?? []);
            $rows    = [];

            foreach ($preds as $i => $pred) {
                if (!isset($actuals[$i])) continue;

                $aktual  = $actuals[$i]['avg_dosage'];
                $predVal = $pred['avg_dosage'];
                $error   = round($predVal - $aktual, 2);

                $rows[] = [
                    'week_num'    => $i + 1,
                    'aktual'      => $aktual,
                    'prediksi'    => $predVal,
                    'error'       => $error,
                    'aktual_count'=> $actuals[$i]['count'],
                    'prior_data'  => $pred['prior_data'] ?? [],
                ];
            }

            $evaluation[$chemKey] = [
                'rows'    => $rows,
                'metrics' => $this->calculateDosisMetrics($rows),
            ];
        }

        return $evaluation;
    }

    /**
     * Prediksi 4 minggu kedepan dari data mingguan
     */
    public function predictNextMonth(array $weeklyData): array
    {
        $predictions = [];
        foreach ($weeklyData as $chemKey => $weeks) {
            $all = $weeks;
            $preds = [];
            for ($i = 1; $i <= 4; $i++) {
                $n = count($all);
                if ($n >= 3) {
                    $three = array_column(array_slice($all, -3), 'avg_dosage');
                } elseif ($n == 2) {
                    $three = [$all[0]['avg_dosage'], $all[0]['avg_dosage'], $all[1]['avg_dosage']];
                } elseif ($n == 1) {
                    $three = [$all[0]['avg_dosage'], $all[0]['avg_dosage'], $all[0]['avg_dosage']];
                } else continue;

                $pred = $this->wma($three);
                $last = end($all);
                $entry = [
                    'week' => "Prediksi $i", 'week_num' => ($last['week_num'] ?? 0) + $i,
                    'avg_dosage' => $pred, 'count' => 0, 'is_prediction' => true,
                    'prior_data' => $three,
                    'start' => '', 'end' => '',
                ];
                $preds[] = $entry;
                $all[] = $entry;
            }
            $predictions[$chemKey] = $preds;
        }
        return $predictions;
    }
}
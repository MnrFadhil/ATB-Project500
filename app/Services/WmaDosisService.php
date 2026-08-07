<?php

namespace App\Services;

use App\Models\Shift;
use App\Models\WmaSetting;
use Carbon\Carbon;

/**
 * Service ini menangani PREDIKSI KEBUTUHAN DOSIS KIMIA BULANAN (PAC, Klorin,
 * Soda Ash) pakai metode WMA, serta evaluasi akurasinya.
 *
 * Beda dengan WmaEvaluationService.php:
 *   - WmaEvaluationService → prediksi per SHIFT (tiap 2 jam), untuk parameter
 *     kualitas air baku (turbidity/ph/tds).
 *   - Service ini            → prediksi per BULAN, untuk jumlah pemakaian
 *     bahan kimia (ppm), dipakai analis kimia buat rencana pengadaan barang.
 *
 * Sumber data yang dipakai:
 *   - Tabel `shifts`          → daftar shift per tanggal (kolom `date`)
 *   - Tabel `pump_chemicals`  → catatan dosis pompa kimia tiap shift, difilter
 *                               kolom `type` ('pac' / 'chlorine/kaporit' / 'soda ash')
 *                               dan kolom `dosage` (angka ppm-nya), relasi
 *                               `pumpChemicals` di model Shift
 *   - Tabel `wma_settings`    → bobot [w1,w2,w3] untuk key 'dosis_kimia'
 */
class WmaDosisService
{
    // Bobot [w1, w2, w3] dari tabel wma_settings (key='dosis_kimia'), dibaca
    // sekali waktu class ini dibuat. Default [1,1,10] cuma dipakai kalau
    // row 'dosis_kimia' belum ada di DB — di sistem produksi, row ini sudah
    // ada dan sudah diubah admin jadi [1,1,20].
    protected array $weights;

    public function __construct()
    {
        $this->weights = WmaSetting::getWeights('dosis_kimia', [1, 1, 10]);
    }

    // Pemetaan nama singkat (dipakai sebagai key array di kode) ke nilai
    // asli kolom `pump_chemicals.type` di database. Kalau mau nambah jenis
    // kimia baru, tambahkan pasangan di sini SEKALIGUS di CHEM_LABELS di bawah.
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

    /**
     * Versi bobot untuk DITAMPILKAN di layar (bukan untuk hitung-hitung).
     * PERHATIAN: index-nya sengaja DIBALIK dari $this->weights aslinya —
     * di sini 'w1' malah berisi weights[2] (bobot untuk data terbaru).
     * Ini beda dari WmaSetting::getWeights() yang urutannya APA ADANYA
     * dari kolom database. Jangan bingung kalau nemu angka 'w1' di sini
     * beda dengan kolom `w1` di tabel wma_settings — bukan bug hitungan,
     * cuma penamaan tampilan yang kebalik dari label form edit admin.
     */
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

    /**
     * Hitung 1 nilai WMA dari 3 angka rata-rata bulanan.
     * @param array $three tepat 3 angka, urut dari bulan PALING LAMA ke PALING BARU
     *                      contoh: [11.76, 11.67, 10.43] = rata-rata Jul, Ags, Sep
     * Rumus sama persis dengan WmaEvaluationService::calculateWMA() — cuma versi
     * privat di sini karena datanya beda konteks (bulanan, bukan per-shift).
     */
    private function wma(array $three): float
    {
        $sum = 0;
        foreach ($three as $i => $v) $sum += $v * $this->weights[$i];
        return round($sum / array_sum($this->weights), 2);
    }

    /**
     * Ambil rata-rata dosis per BULAN kalender, grouped per jenis bahan kimia.
     * Menggantikan pendekatan lama yang mengelompokkan per minggu ISO —
     * dengan basis bulan kalender, prediksi jadi apple-to-apple dengan
     * "1 bulan" tanpa sisa hari yang kepotong minggu ISO.
     *
     * DARI MANA DATANYA & CARA OLAHNYA:
     *   1. Ambil semua baris `shifts` dalam rentang $start–$end, sekalian tarik
     *      relasi `pumpChemicals` miliknya (satu shift bisa punya beberapa baris
     *      pump_chemicals — satu per jenis kimia per unit pompa A/B).
     *   2. Untuk tiap jenis kimia (pac/chlorine/soda_ash — lihat CHEM_TYPES),
     *      filter baris pump_chemicals yang `type`-nya cocok.
     *   3. Baris dengan `dosage` <= 0 DILEWATI (`if ($c->dosage <= 0) continue`) —
     *      ini kondisi pompa mati/maintenance, bukan pemakaian riil, jadi tidak
     *      ikut dihitung ke rata-rata bulan tersebut.
     *   4. Sisa baris yang dosage > 0 dikelompokkan per bulan kalender (kolom
     *      `date` shift-nya, format 'Y-m'), dijumlah semua dosage-nya (`total`)
     *      dan dihitung berapa banyak baris (`count`).
     *   5. Rata-rata per bulan = total ÷ count → inilah `avg_dosage` yang jadi
     *      titik data historis buat prediksi WMA di predictNextMonth().
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
                // Ambil baris pump_chemicals shift ini yang type-nya cocok
                // dengan jenis kimia yang lagi diproses (bisa 2 baris: pompa A & B)
                foreach ($s->pumpChemicals->where('type', $dbType) as $c) {
                    if ($c->dosage <= 0) continue; // pompa mati/maintenance, skip

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

    /**
     * Hitung MAPE dari daftar baris prediksi-vs-aktual dosis bulanan.
     * Rumus dan cara kerja sama persis dengan WmaEvaluationService::calculateMetrics(),
     * cuma nama key array-nya beda ('prediksi'/'aktual' langsung, tanpa suffix
     * parameter, karena fungsi ini dipanggil 1x per jenis kimia — bukan 1x untuk
     * 3 parameter sekaligus seperti di WmaEvaluationService).
     *
     * Sama seperti di WmaEvaluationService: baris dengan aktual = 0 dilewati
     * dari perhitungan rata-rata (hindari pembagian dengan nol), tapi 'n' yang
     * di-return tetap jumlah baris ASLI sebelum penyaringan itu.
     */
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
     * Bandingkan hasil prediksi (dari predictNextMonth()) dengan dosis AKTUAL
     * bulan target, untuk tahu seberapa akurat prediksinya (dipakai halaman
     * "Prediksi Dosis WMA" untuk tampilkan MAPE di bawah tiap grafik).
     *
     * Hanya dievaluasi jika bulan target sudah selesai penuh (bukan bulan berjalan),
     * supaya rata-rata aktual tidak dihitung dari data yang belum lengkap sebulan.
     * Kalau bulan target belum selesai (`$isComplete` false), $actualMonthly
     * dikosongkan → hasil evaluasi jadi kosong juga (belum bisa dibandingkan).
     *
     * DARI MANA "aktual"-nya: dipanggil ulang getMonthlyAverages() tapi kali
     * ini rentang tanggalnya dipersempit hanya 1 bulan (bulan target itu sendiri),
     * jadi hasilnya cuma 1 baris per jenis kimia = rata-rata dosis riil bulan itu.
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
                $aktual  = $actualEntry['avg_dosage']; // rata-rata dosis riil bulan target
                $predVal = $pred['avg_dosage'];         // hasil prediksi WMA dari 3 bulan sebelumnya

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
     *
     * INPUT $monthlyData: hasil dari getMonthlyAverages() — array per jenis
     * kimia, isinya daftar bulan berurutan dengan `avg_dosage` masing-masing
     * (data ini yang berasal dari tabel pump_chemicals, sudah dijelaskan
     * detailnya di komentar getMonthlyAverages() di atas).
     *
     * PENANGANAN KALAU DATA HISTORIS KURANG DARI 3 BULAN:
     *   Idealnya WMA butuh 3 titik data [bulan lama, tengah, baru]. Tapi kalau
     *   histori yang tersedia belum sampai 3 bulan (sistem baru jalan, atau
     *   bahan kimia jarang dipakai seperti Soda Ash), array $three "dipalsukan"
     *   dengan cara duplikasi titik data yang ada:
     *     - $n >= 3 → pakai 3 bulan TERAKHIR apa adanya (array_slice ambil dari belakang)
     *     - $n == 2 → titik tertua diduplikasi jadi 2x: [bulan1, bulan1, bulan2]
     *     - $n == 1 → satu-satunya titik diduplikasi 3x: [bulan1, bulan1, bulan1]
     *       (WMA dari 3 angka yang sama = angka itu sendiri, jadi prediksinya
     *       otomatis sama dengan rata-rata bulan itu)
     *     - $n == 0 → data historis nihil sama sekali → prediksi di-set null
     *       (nanti di UI ditampilkan pesan "prediksi tidak dapat dihitung")
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

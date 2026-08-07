<?php

namespace App\Services;

use App\Models\Shift;
use Carbon\Carbon;

/**
 * Service ini menghitung EVALUASI AKURASI untuk rekomendasi dosis Fuzzy Mamdani
 * (PAC, Klorin, Soda Ash), dengan cara menghitung ulang rekomendasi fuzzy untuk
 * SEMUA shift dalam rentang tanggal, lalu bandingkan ke dosis yang beneran
 * dipakai operator di shift berikutnya.
 *
 * Bedanya dengan app/Livewire/MonitoringDetail.php:
 *   - MonitoringDetail.php → hitung rekomendasi utk 1 shift yang lagi dibuka user,
 *                            real-time, dipakai operator sebagai acuan.
 *   - Service ini           → hitung ulang rumus fuzzy yang SAMA PERSIS (membership
 *                            function, rules, defuzzifikasi, clamp) tapi untuk
 *                            ribuan shift sekaligus (data lampau), demi menghasilkan
 *                            metrik MAPE (seberapa dekat rekomendasi sistem ke
 *                            keputusan operator yang sebenarnya).
 *
 * Sumber data yang dipakai (lihat detail di buildEvaluationData() di bawah):
 *   - Tabel `shifts`          → daftar shift (kolom `date`, `end_time`, `shift`)
 *   - Tabel `water_qualities` → input fuzzy: turbidity (type='sedimentation'),
 *                               free_chlor & ph (type='reservoir')
 *   - Tabel `pump_chemicals`  → dosis AKTUAL yang dipakai operator, difilter
 *                               `type` (pac/chlorine-kaporit/soda ash) dan
 *                               `status`='running'
 */
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
    // Fuzzy Mamdani — PAC (output: dosis ppm, batas 8–20)
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
    //
    // Kenapa perlu fungsi ini: skema evaluasinya adalah "rekomendasi dihitung
    // dari data shift T, dibandingkan ke dosis aktual shift T+1" (2 jam
    // setelahnya). Fungsi ini nyari shift T+1 itu tanpa harus query database
    // lagi — cukup lookup di array $index (dibangun sebelumnya di
    // buildEvaluationData, key-nya "tanggal|jam").
    //
    // Kasus khusus shift yang berakhir jam 23:00: shift berikutnya BUKAN di
    // hari yang sama (karena sudah lewat tengah malam), jadi tanggalnya harus
    // ditambah 1 hari dan jamnya balik ke 01:00.
    // -------------------------------------------------------------------------

    private function findNextShiftByIndex(array $index, array $current): ?array
    {
        $endTime = substr($current['end_time'], 0, 5);
        $date    = $current['date'];

        if ($endTime === '23:00') {
            // Shift 23:00 → shift berikutnya adalah 01:00 di HARI BERIKUTNYA
            $nextDate    = date('Y-m-d', strtotime('+1 day', strtotime($date)));
            $nextEndTime = '01:00';
        } else {
            // Shift lainnya → shift berikutnya 2 jam kemudian, HARI SAMA
            $nextDate    = $date;
            $nextEndTime = date('H:i', strtotime('+2 hours', strtotime($endTime)));
        }

        return $index[$nextDate . '|' . $nextEndTime] ?? null;
    }

    // -------------------------------------------------------------------------
    // Build evaluation data
    //
    // Tabel hasil fungsi ini yang jadi dasar halaman "Evaluasi Fuzzy Mamdani"
    // (tabel per baris + hitung MAPE) dan file export PDF/Excel-nya.
    // -------------------------------------------------------------------------

    public function buildEvaluationData(string $startDate, string $endDate): array
    {
        // Safety net — range panjang butuh lebih dari default 128M
        ini_set('memory_limit', '512M');

        // Extend end by 1 day untuk mengambil "next shift" dari shift terakhir
        // (shift terakhir dalam rentang filter tetap butuh tahu shift SESUDAHNYA,
        // walau shift sesudahnya itu sendiri di luar rentang filter yang dipilih user)
        $extendedEnd = date('Y-m-d', strtotime('+1 day', strtotime($endDate)));

        // Ambil semua shift dari tabel `shifts`, plus 2 relasi:
        //   - waterQualities → nilai turbidity (type sedimentation), free_chlor
        //     & ph (type reservoir) — ini yang jadi INPUT rumus fuzzy
        //   - pumpChemicals   → dosis PAC/Klorin/Soda Ash yang beneran dipakai
        //     operator — ini yang jadi pembanding "aktual" dalam MAPE
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
            // Dari semua baris water_qualities milik shift ini, ambil yang
            // type='sedimentation' (dipakai fuzzy PAC) dan type='reservoir'
            // (dipakai fuzzy Klorin & Soda Ash sekaligus, karena keduanya
            // sama-sama diukur di titik reservoir)
            $sed = $shift->waterQualities->firstWhere('type', 'sedimentation');
            $res = $shift->waterQualities->firstWhere('type', 'reservoir');

            // Cari baris pump_chemicals shift ini yang statusnya 'running'
            // (pompa lagi aktif jalan). Kalau tidak ada yang running, fallback
            // ambil baris pertama apa adanya (dipakai buat nilai 'prev_*' saja,
            // sedangkan 'aktual_*' tetap 0 kalau memang tidak ada yang running —
            // lihat pengecekan status di bawah).
            $pacRunning      = $shift->pumpChemicals->where('type', 'pac')->where('status', 'running')->first()
                            ?? $shift->pumpChemicals->firstWhere('type', 'pac');
            $klorinRunning   = $shift->pumpChemicals->where('type', 'chlorine/kaporit')->where('status', 'running')->first()
                            ?? $shift->pumpChemicals->firstWhere('type', 'chlorine/kaporit');
            $sodaashRunning  = $shift->pumpChemicals->where('type', 'soda ash')->where('status', 'running')->first()
                            ?? $shift->pumpChemicals->firstWhere('type', 'soda ash');

            // 'prev_*'   = dosis SEBELUM shift ini, dipakai sebagai basis
            //              clamp(dosis_sebelumnya + delta, ...) waktu ngitung
            //              rekomendasi fuzzy. Kalau pompa tidak running, pakai
            //              nilai default (10.0/1.5/2.0 — sama dengan default
            //              parameter di calculatePAC/Klorin/SodaAsh).
            // 'aktual_*' = dosis AKTUAL shift ini, dipakai sebagai pembanding
            //              "kebenaran lapangan" waktu hitung MAPE. Kalau pompa
            //              tidak running, dianggap 0 (pompa memang mati).
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

        // Loop utama: untuk TIAP shift ($current), hitung rekomendasi fuzzy-nya
        // pakai data shift itu sendiri, lalu bandingkan ke dosis aktual yang
        // dipakai operator di shift SESUDAHNYA ($next). 1 shift = 1 baris tabel.
        $rows = [];
        foreach ($series as $current) {
            // Hanya tampilkan shift dalam range filter yang dipilih user
            if ($current['date'] < $startDate || $current['date'] > $endDate) continue;

            // Skip jika data kualitas air belum lengkap diisi operator
            // (turbidity sedimentasi / free chlorine / pH reservoir kosong)
            if ($current['turb_sed'] === null || $current['free_chlor'] === null || $current['ph_res'] === null) continue;

            // Cari shift 2 jam sesudahnya via O(1) index lookup (lihat findNextShiftByIndex())
            $next = $this->findNextShiftByIndex($seriesIndex, $current);
            if ($next === null) continue; // shift terakhir dalam data, tidak ada shift sesudahnya

            // Hitung rekomendasi fuzzy PAKAI DATA SHIFT SEKARANG ($current) —
            // rumusnya persis calculatePAC/Klorin/SodaAsh di MonitoringDetail.php,
            // cuma versi private di file ini (fuzzyPAC/fuzzyKlorin/fuzzySodaAsh
            // di atas), supaya hasilnya konsisten dengan yang tampil real-time
            // di halaman Monitoring Detail.
            $rekomPac     = $this->fuzzyPAC($current['turb_sed'], (float) $current['prev_pac']);
            $rekomKlorin  = $this->fuzzyKlorin($current['free_chlor'], (float) $current['prev_klorin']);
            $rekomSodaAsh = $this->fuzzySodaAsh($current['ph_res'], (float) $current['prev_sodaash']);

            // Ambil dosis AKTUAL dari shift BERIKUTNYA ($next, bukan $current) —
            // inilah "kebenaran lapangan" yang jadi pembanding rekomendasi di atas.
            // 0 kalau pompa standby/mati di shift itu.
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
    // Hitung metrik: MAPE
    //
    // Bukan query baru ke database — murni olah array $rows yang sudah
    // dibentuk oleh buildEvaluationData(). Dipanggil 1x per bahan kimia
    // ($param = 'pac' / 'klorin' / 'sodaash').
    // -------------------------------------------------------------------------

    public function calculateMetrics(array $rows, string $param): array
    {
        // PAC punya 2 mode perbandingan (lihat konstanta PAC_EVAL_MODE di atas
        // file ini): default-nya 'rekomen' → bandingkan ke rekomendasi fuzzy.
        // Klorin & Soda Ash selalu pakai "rekomen_$param" (tidak ada mode 'prev').
        $rekomKey  = ($param === 'pac' && static::PAC_EVAL_MODE === 'prev')
            ? 'baseline_pac'       // Aktual (T+1) vs Dosis 2 jam sebelumnya (shift T)
            : "rekomen_$param";    // Aktual (T+1) vs Rekomendasi Fuzzy
        $aktualKey = "aktual_$param";

        $n = count($rows);
        if ($n === 0) return ['mape' => 0, 'n' => 0];

        $sumPct    = 0; // akumulasi persentase error dari baris yang valid
        $countPct  = 0; // jumlah baris valid (aktual != 0)

        foreach ($rows as $r) {
            $err = $r[$rekomKey] - $r[$aktualKey];

            // Baris dengan aktual = 0 (pompa standby di shift T+1) DILEWATI dari
            // rata-rata — sama seperti di WmaEvaluationService/WmaDosisService,
            // supaya tidak ada pembagian dengan nol. 'n' yang di-return tetap
            // jumlah baris ASLI (count($rows)), bukan $countPct.
            if ($r[$aktualKey] != 0) {
                $sumPct += abs($err / $r[$aktualKey]);
                $countPct++;
            }
        }

        return [
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

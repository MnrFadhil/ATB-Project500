<?php

namespace App\Services;

use App\Models\Shift;
use App\Models\WmaSetting;
use Carbon\Carbon;

/**
 * Service ini menghitung EVALUASI AKURASI untuk WMA Air Baku, yaitu prediksi
 * turbidity (kekeruhan), pH, dan TDS air baku 2 jam ke depan.
 *
 * Bedanya dengan prediksi WMA yang tampil di halaman Monitoring Detail
 * (app/Livewire/MonitoringDetail.php):
 *   - MonitoringDetail.php  → hitung PREDIKSI untuk 1 shift yang lagi dibuka,
 *                             dipakai operator sebagai acuan sebelum shift berikutnya.
 *   - Service ini            → hitung prediksi untuk RIBUAN shift SEKALIGUS (data yang
 *                             sudah lewat), lalu bandingkan tiap prediksi ke nilai
 *                             aktual yang sudah tercatat, buat tahu seberapa akurat
 *                             metode WMA ini secara keseluruhan (pakai metrik MAPE).
 *
 * Sumber data yang dipakai:
 *   - Tabel `shifts`          → daftar shift per tanggal + jam (kolom `date`, `end_time`, `shift`)
 *   - Tabel `water_qualities` → nilai turbidity/ph/tds, difilter type = 'air baku'
 *                               (relasi `waterQualities` di model Shift)
 *   - Tabel `wma_settings`    → bobot [w1,w2,w3] untuk key 'air_baku' (lihat WmaSetting.php)
 */
class WmaEvaluationService
{
    // Bobot [w1, w2, w3] hasil baca dari tabel wma_settings (key='air_baku'),
    // diambil SEKALI saja waktu class ini dibuat (constructor), lalu dipakai
    // berulang-ulang di semua method di bawah tanpa query ulang ke DB.
    protected array $weights;

    public function __construct()
    {
        // Default [1, 3, 30] cuma dipakai kalau row 'air_baku' belum ada di DB.
        $this->weights = WmaSetting::getWeights('air_baku', [1, 3, 30]);
    }

    /**
     * Hitung 1 nilai prediksi WMA dari 3 data historis.
     *
     * @param array $lastThree tepat 3 angka, URUTAN WAJIB dari yang PALING LAMA
     *                         ke yang PALING BARU, contoh: [17.62, 17.74, 17.42]
     *                         artinya data 4 jam lalu, 2 jam lalu, dan barusan.
     * @return float           hasil prediksi (dibulatkan 2 desimal)
     *
     * Rumus: WMA = (d1×w1 + d2×w2 + d3×w3) / (w1+w2+w3)
     * Data paling baru (index terakhir array) selalu dikalikan bobot paling besar (w3),
     * karena kondisi air baku yang paling relevan untuk prediksi adalah kondisi terkini.
     */
    public function calculateWMA(array $lastThree): float
    {
        $weightedSum = 0;
        $weightTotal = 0;
        foreach ($lastThree as $i => $value) {
            // $i=0 → data terlama × weights[0] (w1, bobot terkecil)
            // $i=2 → data terbaru × weights[2] (w3, bobot terbesar)
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
     * Bangun tabel perbandingan "prediksi WMA vs nilai aktual" untuk SEMUA shift
     * dalam rentang tanggal $startDate s/d $endDate. Ini data mentah yang dipakai
     * halaman "Evaluasi WMA Air Baku" (tabel + hitung MAPE) dan export PDF/Excel.
     *
     * DARI MANA DATANYA:
     *   1. Tabel `shifts` — ambil semua baris shift antara $extendedStart s/d $endDate,
     *      diurutkan kronologis (tanggal lalu jam).
     *   2. Untuk tiap shift, ambil relasi `waterQualities` miliknya, lalu FILTER
     *      hanya baris yang type = 'air baku' (bukan 'sedimentation'/'reservoir' —
     *      itu dipakai fitur Fuzzy, bukan di sini). Dari baris itu diambil
     *      kolom `turbidity`, `ph`, `tds`.
     *   3. Shift yang belum punya data water_qualities type 'air baku' (operator
     *      belum input) otomatis di-skip (lihat `if (!$ab) continue;`).
     *
     * KENAPA $extendedStart mundur 3 hari dari $startDate:
     *   Prediksi untuk shift PERTAMA dalam rentang filter tetap butuh 3 shift
     *   SEBELUM tanggal filter mulai (untuk jadi bahan hitung WMA-nya). Makanya
     *   query database narik data 3 hari ekstra ke belakang, tapi baris hasil
     *   prediksi yang ditampilkan/dihitung MAPE-nya tetap disaring hanya yang
     *   tanggalnya masuk $startDate–$endDate (lihat pengecekan di dalam loop).
     *
     * CARA MEMBENTUK PREDIKSI per baris:
     *   - $series = daftar semua shift (lengkap dgn turbidity/ph/tds), urut waktu.
     *   - Untuk shift ke-i (mulai index 3, karena butuh 3 shift sebelumnya):
     *     ambil 3 shift SEBELUM index i ($prev3), hitung WMA dari situ,
     *     lalu bandingkan hasil WMA itu ke nilai aktual shift ke-i sendiri.
     *   - Jadi tiap baris tabel = 1 shift, isinya: nilai aktual shift itu,
     *     nilai yang DIPREDIKSI dari 3 shift sebelumnya, dan selisihnya (error).
     */
    public function buildEvaluationData(string $startDate, string $endDate): array
    {
        // Ambil data 3 hari sebelum startDate untuk keperluan prior WMA
        $extendedStart = date('Y-m-d', strtotime('-3 days', strtotime($startDate)));

        // Ambil semua shift dari extendedStart sampai endDate, beserta relasi
        // waterQualities-nya (1 shift bisa punya beberapa baris water_qualities,
        // satu untuk tiap titik pengukuran: air baku / sedimentation / reservoir)
        $shifts = Shift::with('waterQualities')
            ->whereBetween('date', [$extendedStart, $endDate])
            ->orderBy('date', 'asc')
            ->orderBy('end_time', 'asc')
            ->get();

        // Ekstrak nilai air baku per shift — cuma ambil baris water_qualities
        // yang type-nya 'air baku' (titik pengukuran paling hulu, sebelum diolah)
        $series = [];
        foreach ($shifts as $shift) {
            $ab = $shift->waterQualities->firstWhere('type', 'air baku');
            if (!$ab) continue; // shift ini belum ada data air baku → lewati

            $series[] = [
                'shift_id'   => $shift->id,
                'date'       => $shift->date,
                'shift'      => $shift->shift,
                'end_time'   => $shift->end_time,
                'turbidity'  => (float) $ab->turbidity, // kolom water_qualities.turbidity
                'ph'         => (float) $ab->ph,         // kolom water_qualities.ph
                'tds'        => (float) $ab->tds,        // kolom water_qualities.tds
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
     * Hitung MAPE (Mean Absolute Percentage Error) dari hasil buildEvaluationData().
     * Bukan query baru ke database — ini murni olah array $rows yang sudah
     * dibentuk sebelumnya (jadi 1x query DB bisa dipakai hitung MAPE turbidity,
     * ph, dan tds sekaligus, tinggal panggil fungsi ini 3x dengan $param beda).
     *
     * @param array  $rows  hasil dari buildEvaluationData()
     * @param string $param 'turb', 'ph', atau 'tds' — dipakai buat susun nama key
     *                      array secara dinamis: "aktual_$param" & "prediksi_$param"
     *                      (misal $param='turb' → baca $r['aktual_turb'] & $r['prediksi_turb'])
     *
     * PENTING — soal 'n' yang di-return:
     *   'n' di sini adalah JUMLAH BARIS TOTAL yang masuk (count($rows)), BUKAN
     *   jumlah baris yang benar-benar dipakai menghitung rata-rata MAPE.
     *   Baris yang nilai aktualnya persis 0 sengaja DILEWATI dari penjumlahan
     *   (lihat `if ($r[$aktualKey] != 0)`), soalnya rumus MAPE ada pembagian
     *   dengan nilai aktual — kalau aktualnya 0, hasil baginya jadi tak terhingga.
     *   Jadi rata-rata MAPE yang dihasilkan itu rata-rata dari $countPct baris
     *   (baris ber-aktual ≠ 0), sedangkan 'n' yang ditampilkan ke user tetap
     *   angka $n (jumlah baris asli sebelum penyaringan itu).
     */
    public function calculateMetrics(array $rows, string $param): array
    {
        $aktualKey   = "aktual_$param";
        $prediksiKey = "prediksi_$param";

        $n = count($rows);
        if ($n === 0) {
            return ['mape' => 0, 'n' => 0];
        }

        $sumPct    = 0; // akumulasi total persentase error dari semua baris valid
        $countPct  = 0; // jumlah baris valid (aktual != 0) yang ikut dihitung

        foreach ($rows as $r) {
            $err = $r[$aktualKey] - $r[$prediksiKey];

            if ($r[$aktualKey] != 0) {
                // |error| / |aktual| → persentase kesalahan baris ini (selalu positif)
                $sumPct += abs($err / $r[$aktualKey]);
                $countPct++;
            }
        }

        return [
            // MAPE = rata-rata seluruh persentase error, dikali 100 jadi bentuk %
            'mape' => $countPct > 0 ? round(($sumPct / $countPct) * 100, 2) : 0,
            'n'    => $n,
        ];
    }

    /**
     * Interpretasi MAPE sesuai standar Khusmiawati et al. (2025)
     */
    public function interpretMape(float $mape): string
    {
        if ($mape < 10)  return 'Sangat Akurat';
        if ($mape < 20)  return 'Akurat / Baik';
        if ($mape < 50)  return 'Cukup / Wajar';
        return 'Tidak Akurat';
    }

    /**
     * Get bobot WMA untuk ditampilkan di view.
     * PERHATIAN: index-nya sengaja DIBALIK dari $this->weights aslinya —
     * di sini 'w1' malah berisi weights[2] (bobot untuk data terbaru), bukan
     * kolom `w1` yang sebenarnya di tabel wma_settings. Ini cuma soal
     * penamaan tampilan, bukan bug hitungan — hitungan asli (calculateWMA())
     * tetap baca $this->weights langsung tanpa dibalik.
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
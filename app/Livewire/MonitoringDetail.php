<?php

namespace App\Livewire;

use App\Models\Shift;
use App\Models\WmaSetting;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

/**
 * Halaman "Monitoring Detail" — komponen INTI dari sistem ini. Di sinilah
 * rekomendasi dosis Fuzzy Mamdani dan prediksi WMA air baku ditampilkan
 * REAL-TIME untuk 1 shift yang lagi dibuka user.
 *
 * DARI MANA SEMUA DATANYA (garis besar, detail per bagian ada di komentar
 * masing-masing method di bawah):
 *   - Tabel `shifts`          → 1 baris = 1 shift (kolom `date`, `end_time`, `shift`)
 *   - Tabel `water_qualities` → hasil ukur kualitas air, DIBEDAKAN oleh kolom
 *                               `type`: 'air baku' (sebelum diolah, input WMA),
 *                               'sedimentation' (setelah sedimentasi, input Fuzzy PAC),
 *                               'reservoir' (siap distribusi, input Fuzzy Klorin & Soda Ash)
 *   - Tabel `pump_chemicals`  → dosis pompa kimia (PAC/Klorin/Soda Ash) yang lagi
 *                               berjalan (`status`='running'), dipakai sebagai
 *                               "dosis sebelumnya" basis rekomendasi fuzzy berikutnya
 *   - Tabel `wma_settings`    → bobot WMA (lihat WmaSetting.php)
 *
 * ALUR SINGKAT tiap kali halaman ini dibuka (lihat method render() paling bawah):
 *   1. Ambil 1 shift + semua relasinya berdasarkan $id
 *   2. getHistoricalWaterQualities() cari 2 shift SEBELUMNYA → gabung dengan
 *      data shift sekarang jadi 3 titik [d1=lama, d2=tengah, d3=baru]
 *   3. calculateWMA() dipakai untuk prediksi turbidity/pH/TDS air baku
 *   4. calculatePAC()/calculateKlorin()/calculateSodaAsh() dipakai untuk
 *      rekomendasi dosis fuzzy — jalan independen, tidak butuh data historis
 *   5. Semua hasil dikirim ke Blade view untuk ditampilkan + dipakai chart
 */
class MonitoringDetail extends Component
{
    public $id;
    public $isAdmin;

    public function mount($id)
    {
        $this->id      = $id;
        $this->isAdmin = Auth::user()->role == 'admin';
    }

    // =========================================================================
    // FUZZY MEMBERSHIP FUNCTIONS
    // =========================================================================

    private function leftShoulderMF(float $x, float $a, float $b): float
    // Fungsi keanggotaan sisi kiri: μ=1 dari -∞ sampai a, turun dari a ke b, μ=0 dari b ke ∞
    // Dipakai untuk himpunan "sangat rendah" (nilai kecil = anggota penuh)
    {
        if ($x <= $a) return 1.0;
        // x masih di sisi kiri batas bawah → derajat penuh
        // contoh: leftShoulder(0.5, 0.0, 2.0) → 0.5 ≤ 0.0? tidak, lanjut bawah
        if ($x >= $b) return 0.0;
        // x sudah melewati batas kanan → tidak termasuk himpunan ini
        // contoh: leftShoulder(3.0, 0.0, 2.0) → 3.0 ≥ 2.0? ya → return 0.0
        return ($b - $x) / ($b - $a);
        // interpolasi linier turun dari a ke b
        // contoh: leftShoulder(1.47, 0.0, 2.0) = (2.0-1.47)/(2.0-0.0) = 0.53/2.0 = 0.265
    }

    private function triangularMF(float $x, float $a, float $b, float $c): float
    // Fungsi keanggotaan segitiga: naik dari a ke b (puncak μ=1), turun dari b ke c
    // Dipakai untuk himpunan "rendah", "optimal", "tinggi" (nilai tengah)
    {
        if ($x <= $a || $x >= $c) return 0.0;
        // x di luar rentang [a, c] → tidak termasuk himpunan ini sama sekali
        // contoh: triangular(0.5, 1.0, 2.5, 3.2) → 0.5 ≤ 1.0? ya → return 0.0
        if ($x <= $b) return ($x - $a) / ($b - $a);
        // x berada di sisi naik (a → b) → interpolasi naik
        // contoh: triangular(1.47, 1.0, 2.5, 3.2) → (1.47-1.0)/(2.5-1.0) = 0.47/1.5 = 0.313
        return ($c - $x) / ($c - $b);
        // x berada di sisi turun (b → c) → interpolasi turun
        // contoh: triangular(2.85, 1.0, 2.5, 3.2) → (3.2-2.85)/(3.2-2.5) = 0.35/0.7 = 0.500
    }

    private function rightShoulderMF(float $x, float $a, float $b): float
    // Fungsi keanggotaan sisi kanan: μ=0 dari -∞ sampai a, naik dari a ke b, μ=1 dari b ke ∞
    // Dipakai untuk himpunan "sangat tinggi" (nilai besar = anggota penuh)
    {
        if ($x <= $a) return 0.0;
        // x belum mencapai batas kiri → belum masuk himpunan ini
        // contoh: rightShoulder(4.0, 4.5, 6.0) → 4.0 ≤ 4.5? ya → return 0.0
        if ($x >= $b) return 1.0;
        // x sudah melewati batas kanan → derajat keanggotaan penuh
        // contoh: rightShoulder(8.0, 4.5, 6.0) → 8.0 ≥ 6.0? ya → return 1.0
        return ($x - $a) / ($b - $a);
        // interpolasi linier naik dari a ke b
        // contoh: rightShoulder(5.25, 4.5, 6.0) = (5.25-4.5)/(6.0-4.5) = 0.75/1.5 = 0.500
    }

    // =========================================================================
    // DEFUZZIFIKASI — Metode Centroid
    // =========================================================================

    private function defuzzify(array $rules): float
    // Mengubah kumpulan nilai fuzzy (μ) menjadi satu angka tegas (crisp output)
    // Rumus Centroid: Σ(μᵢ × centerᵢ) / Σ(μᵢ)
    {
        $sumMuCenter = 0.0;
        $sumMu       = 0.0;
        // Akumulator: sumMuCenter = Σ(μ × center), sumMu = Σ(μ)

        foreach ($rules as [$mu, $center]) {
            $sumMuCenter += $mu * $center;
            // Kontribusi tiap rule: derajat keanggotaan × titik tengah output
            // contoh rule sangat_rendah: μ=0.265 × center=(-3.0) = -0.795
            $sumMu += $mu;
            // Akumulasi total bobot fuzzy
            // contoh setelah 2 rule: sumMu = 0.265 + 0.313 = 0.578
        }

        return $sumMu > 0 ? round($sumMuCenter / $sumMu, 2) : 0.0;
        // Bagi Σ(μ×center) dengan Σ(μ) → hasil delta crisp
        // contoh PAC turbidity=1.47: (-0.795 + -0.313) / 0.578 = -1.108/0.578 = -1.92 ppm
        // Jika sumMu=0 (tidak ada rule aktif) → return 0, tidak ada perubahan dosis
    }

    // =========================================================================
    // FUZZY MAMDANI — PAC (Koagulan)
    // Sumber: water_qualities.turbidity WHERE type='sedimentation'
    // Prev  : pump_chemicals.dosage WHERE type='pac' AND status='running'
    // Output: rekomendasi dosis PAC shift berikutnya, di-clamp 8–20 ppm
    // =========================================================================

    /**
     * Fungsi murni hitung-hitungan PAC: μ → rules → delta → rekomendasi.
     * Tidak ada logika tampilan (status/warna/pesan) di sini sama sekali.
     */
    private function calculatePAC(float $turbidity, float $previousDosis = 10.0): array
    {
        $mu = [
            'sangat_rendah' => $this->leftShoulderMF($turbidity,  0.0, 2.0),
            'rendah'        => $this->triangularMF($turbidity,    1.0, 2.5, 3.2),
            'optimal'       => $this->triangularMF($turbidity,    2.8, 3.3, 3.8),
            'tinggi'        => $this->triangularMF($turbidity,    3.4, 4.1, 5.0),
            'sangat_tinggi' => $this->rightShoulderMF($turbidity, 4.5, 6.0),
        ];

        $rules = [
            [$mu['sangat_rendah'], -3.0],
            [$mu['rendah'],        -1.0],
            [$mu['optimal'],        0.0],
            [$mu['tinggi'],        +1],
            [$mu['sangat_tinggi'], +3],
        ];

        $delta          = $this->defuzzify($rules);
        $recommendation = round(max(8.0, min(20.0, $previousDosis + $delta)), 2);

        return [
            'mu'             => $mu,
            'delta'          => $delta,
            'recommendation' => $recommendation,
            'previous_dosis' => $previousDosis,
        ];
    }

    /**
     * Bungkus hasil calculatePAC() jadi data siap tampil ke Blade
     * (status, warna, pesan, dan parameter kurva untuk detail perhitungan).
     */
    private function fuzzyPAC($sedimentationTurbidity, float $previousDosis = 0): array
    {
        $t    = (float) $sedimentationTurbidity;
        $calc = $this->calculatePAC($t, $previousDosis);
        $mu   = $calc['mu'];

        $dominant  = array_search(max($mu), $mu);
        // Cari himpunan dengan μ terbesar → menentukan status teks
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
        // Mapping warna badge: merah=bahaya, hijau=aman, kuning=peringatan

        return [
            'status'         => $status,
            'recommendation' => $calc['recommendation'],
            'message'        => "Turbidity sedimentasi <strong style='font-size:13px;color:#{$hexColor};'>{$t} NTU</strong>, Delta dosis: <strong>{$calc['delta']} ppm</strong> → Rekomendasi: <strong style='font-size:13px;color:#{$hexColor};'>{$calc['recommendation']} ppm</strong>",
            'color'          => $color,
            'input_value'    => $t,
            'input_label'    => 'Turbidity Sedimentasi',
            'unit'           => 'NTU',
            'mu'             => $mu,
            'delta'          => $calc['delta'],
            'previous_dosis' => $previousDosis,
            'clamp_min'      => 8.0,
            'clamp_max'      => 20.0,
            'categories'     => ['sangat_rendah' => 'Sangat Rendah', 'rendah' => 'Rendah', 'optimal' => 'Optimal', 'tinggi' => 'Tinggi', 'sangat_tinggi' => 'Sangat Tinggi'],
            'rule_centers'   => ['sangat_rendah' => -3.0, 'rendah' => -1.0, 'optimal' => 0.0, 'tinggi' => 1.0, 'sangat_tinggi' => 3.0],
            'mf_params'      => [
                'sangat_rendah' => ['type' => 'left',     'a' => 0.0, 'b' => 2.0],
                'rendah'        => ['type' => 'triangle', 'a' => 1.0, 'b' => 2.5, 'c' => 3.2],
                'optimal'       => ['type' => 'triangle', 'a' => 2.8, 'b' => 3.3, 'c' => 3.8],
                'tinggi'        => ['type' => 'triangle', 'a' => 3.4, 'b' => 4.1, 'c' => 5.0],
                'sangat_tinggi' => ['type' => 'right',    'a' => 4.5, 'b' => 6.0],
            ],
        ];
    }

    // =========================================================================
    // FUZZY MAMDANI — Klorin (Desinfektan)
    // Sumber: water_qualities.free_chlor WHERE type='reservoir'
    // Prev  : pump_chemicals.dosage WHERE type='chlorine/kaporit' AND status='running'
    // Output: rekomendasi dosis Klorin shift berikutnya, di-clamp 0–3 ppm
    // =========================================================================

    /**
     * Fungsi murni hitung-hitungan Klorin: μ → rules → delta → rekomendasi.
     * Termasuk deteksi kondisi darurat (free_chlor ≥ 0.60 & sudah puncak sangat_tinggi).
     * Tidak ada logika tampilan (status/warna/pesan) di sini sama sekali.
     */
    private function calculateKlorin(float $freeChlorine, float $previousDosis = 0): array
    {
        $mu = [
            'sangat_rendah' => $this->leftShoulderMF($freeChlorine,  0.0,  0.20),
            'rendah'        => $this->triangularMF($freeChlorine,    0.15, 0.26, 0.30),
            'optimal'       => $this->triangularMF($freeChlorine,    0.31, 0.37, 0.46),
            'tinggi'        => $this->triangularMF($freeChlorine,    0.43, 0.48, 0.51),
            'sangat_tinggi' => $this->rightShoulderMF($freeChlorine, 0.50, 0.60),
        ];

        $rules = [
            [$mu['sangat_rendah'], +1.0],
            [$mu['rendah'],        +0.4],
            [$mu['optimal'],        0.0],
            [$mu['tinggi'],        -0.7],
            [$mu['sangat_tinggi'], -2.0],
        ];

        $delta = $this->defuzzify($rules);

        // KONDISI DARURAT: free_chlor ≥ 0.60 dan sudah di puncak sangat_tinggi
        // → matikan pompa langsung, jangan tunggu defuzzifikasi
        $isEmergency = ($freeChlorine >= 0.60 && $mu['sangat_tinggi'] >= 1.0);

        $recommendation = $isEmergency
            ? 0.0
            : round(max(0.0, min(3.0, $previousDosis + $delta)), 2);

        return [
            'mu'             => $mu,
            'delta'          => $delta,
            'recommendation' => $recommendation,
            'previous_dosis' => $previousDosis,
            'is_emergency'   => $isEmergency,
        ];
    }

    /**
     * Bungkus hasil calculateKlorin() jadi data siap tampil ke Blade
     * (status, warna, pesan, dan parameter kurva untuk detail perhitungan).
     */
    private function fuzzyKlorin($freeChlorine, float $previousDosis = 0): array
    {
        $f    = (float) $freeChlorine;
        $calc = $this->calculateKlorin($f, $previousDosis);
        $mu   = $calc['mu'];

        $categories   = ['sangat_rendah' => 'Sangat Rendah', 'rendah' => 'Rendah', 'optimal' => 'Optimal', 'tinggi' => 'Tinggi', 'sangat_tinggi' => 'Sangat Tinggi'];
        $ruleCenters  = ['sangat_rendah' => 1.0, 'rendah' => 0.4, 'optimal' => 0.0, 'tinggi' => -0.7, 'sangat_tinggi' => -2.0];
        $mfParams     = [
            'sangat_rendah' => ['type' => 'left',     'a' => 0.0,  'b' => 0.20],
            'rendah'        => ['type' => 'triangle', 'a' => 0.15, 'b' => 0.26, 'c' => 0.30],
            'optimal'       => ['type' => 'triangle', 'a' => 0.31, 'b' => 0.37, 'c' => 0.46],
            'tinggi'        => ['type' => 'triangle', 'a' => 0.43, 'b' => 0.48, 'c' => 0.51],
            'sangat_tinggi' => ['type' => 'right',    'a' => 0.50, 'b' => 0.60],
        ];

        if ($calc['is_emergency']) {
            return [
                'status'         => 'Emergency - Matikan Pompa',
                'recommendation' => $calc['recommendation'],
                'message'        => "Free Chlor Reservoir <strong style='font-size:13px;color:#e74a3b;'>{$f} mg/L</strong> (Sangat Tinggi). <strong style='font-size:13px;color:#e74a3b;'>Matikan Pompa Dosing Chlorine!!!</strong><br><small class='font-weight-bold' style='color:#000;font-size:13px;'>Silahkan Cek Free Chlorine Air Reservoir Secara Berkala!!!</small>",
                'color'          => 'danger',
                'input_value'    => $f,
                'input_label'    => 'Free Chlorine Reservoir',
                'unit'           => 'mg/L',
                'mu'             => $mu,
                'delta'          => $calc['delta'],
                'previous_dosis' => $previousDosis,
                'clamp_min'      => 0.0,
                'clamp_max'      => 3.0,
                'categories'     => $categories,
                'rule_centers'   => $ruleCenters,
                'mf_params'      => $mfParams,
            ];
        }

        $dominant  = array_search(max($mu), $mu);
        // Cari himpunan dengan μ terbesar → menentukan status teks
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
            'recommendation' => $calc['recommendation'],
            'message'        => "Free Chlor Reservoir <strong style='font-size:13px;color:#{$hexColor};'>{$f} mg/L</strong>, Delta dosis: <strong>{$calc['delta']} ppm</strong> → Rekomendasi: <strong style='font-size:13px;color:#{$hexColor};'>{$calc['recommendation']} ppm</strong>",
            'color'          => $color,
            'input_value'    => $f,
            'input_label'    => 'Free Chlorine Reservoir',
            'unit'           => 'mg/L',
            'mu'             => $mu,
            'delta'          => $calc['delta'],
            'previous_dosis' => $previousDosis,
            'clamp_min'      => 0.0,
            'clamp_max'      => 3.0,
            'categories'     => $categories,
            'rule_centers'   => $ruleCenters,
            'mf_params'      => $mfParams,
        ];
    }

    // =========================================================================
    // FUZZY MAMDANI — Soda Ash (pH Adjuster)
    // Sumber: water_qualities.ph WHERE type='reservoir'
    // Prev  : pump_chemicals.dosage WHERE type='soda ash' AND status='running'
    // Output: rekomendasi dosis Soda Ash shift berikutnya, di-clamp 0–10 ppm
    // Catatan: Soda Ash HANYA untuk menaikkan pH. pH ≥ 6.5 → pompa standby
    // =========================================================================

    /**
     * Fungsi murni hitung-hitungan Soda Ash: μ → rules → delta → rekomendasi.
     * Termasuk deteksi kondisi standby (pH ≥ 6.5 atau delta = 0).
     * Tidak ada logika tampilan (status/warna/pesan) di sini sama sekali.
     */
    private function calculateSodaAsh(float $ph, float $previousDosis = 2.0): array
    {
        $mu = [
            'sangat_rendah'  => $this->triangularMF($ph, 3.0, 4.5, 5.2),
            'rendah'         => $this->triangularMF($ph, 4.8, 5.5, 6.1),
            'sedikit_rendah' => $this->triangularMF($ph, 5.8, 6.2, 6.5),
            'normal'         => $this->triangularMF($ph, 6.5, 7.0, 7.8),
        ];

        $rules = [
            [$mu['sangat_rendah'],  +3.0],
            [$mu['rendah'],         +2.0],
            [$mu['sedikit_rendah'], +1.0],
            [$mu['normal'],          0.0],
        ];

        $delta = $this->defuzzify($rules);

        // pH sudah normal (≥ 6.5) atau tidak ada delta → tidak perlu dosing
        $isStandby = ($ph >= 6.5 || $delta == 0);

        $recommendation = $isStandby
            ? 0.0
            : round(max(0.0, min(10.0, $previousDosis + $delta)), 2);

        return [
            'mu'             => $mu,
            'delta'          => $delta,
            'recommendation' => $recommendation,
            'previous_dosis' => $previousDosis,
            'is_standby'     => $isStandby,
        ];
    }

    /**
     * Bungkus hasil calculateSodaAsh() jadi data siap tampil ke Blade
     * (status, warna, pesan, dan parameter kurva untuk detail perhitungan).
     */
    private function fuzzySodaAsh($ph, float $previousDosis = 2.0): array
    {
        $p    = (float) $ph;
        $calc = $this->calculateSodaAsh($p, $previousDosis);
        $mu   = $calc['mu'];

        $categories  = ['sangat_rendah' => 'Sangat Rendah', 'rendah' => 'Rendah', 'sedikit_rendah' => 'Sedikit Rendah', 'normal' => 'Normal'];
        $ruleCenters = ['sangat_rendah' => 3.0, 'rendah' => 2.0, 'sedikit_rendah' => 1.0, 'normal' => 0.0];
        $mfParams    = [
            'sangat_rendah'  => ['type' => 'triangle', 'a' => 3.0, 'b' => 4.5, 'c' => 5.2],
            'rendah'         => ['type' => 'triangle', 'a' => 4.8, 'b' => 5.5, 'c' => 6.1],
            'sedikit_rendah' => ['type' => 'triangle', 'a' => 5.8, 'b' => 6.2, 'c' => 6.5],
            'normal'         => ['type' => 'triangle', 'a' => 6.5, 'b' => 7.0, 'c' => 7.8],
        ];

        if ($calc['is_standby']) {
            return [
                'status'         => 'Pompa Standby',
                'recommendation' => $calc['recommendation'],
                'message'        => "pH normal <strong>({$p})</strong>, Pompa Soda Ash Bisa Standby",
                'color'          => 'secondary',
                'input_value'    => $p,
                'input_label'    => 'pH Reservoir',
                'unit'           => '',
                'mu'             => $mu,
                'delta'          => $calc['delta'],
                'previous_dosis' => $previousDosis,
                'clamp_min'      => 0.0,
                'clamp_max'      => 10.0,
                'categories'     => $categories,
                'rule_centers'   => $ruleCenters,
                'mf_params'      => $mfParams,
            ];
        }

        $dominant  = array_search(max($mu), $mu);
        // Cari himpunan dengan μ terbesar → menentukan status teks
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
            'recommendation' => $calc['recommendation'],
            'message'        => "pH Reservoir <strong style='font-size:13px;color:#{$hexColor};'>{$p}</strong>, Delta dosis: <strong>{$calc['delta']} ppm</strong> → Rekomendasi: <strong style='font-size:13px;'>{$calc['recommendation']} ppm</strong>",
            'color'          => $color,
            'input_value'    => $p,
            'input_label'    => 'pH Reservoir',
            'unit'           => '',
            'mu'             => $mu,
            'delta'          => $calc['delta'],
            'previous_dosis' => $previousDosis,
            'clamp_min'      => 0.0,
            'clamp_max'      => 10.0,
            'categories'     => $categories,
            'rule_centers'   => $ruleCenters,
            'mf_params'      => [
                'sangat_rendah'  => ['type' => 'triangle', 'a' => 3.0, 'b' => 4.5, 'c' => 5.2],
                'rendah'         => ['type' => 'triangle', 'a' => 4.8, 'b' => 5.5, 'c' => 6.1],
                'sedikit_rendah' => ['type' => 'triangle', 'a' => 5.8, 'b' => 6.2, 'c' => 6.5],
                'normal'         => ['type' => 'triangle', 'a' => 6.5, 'b' => 7.0, 'c' => 7.8],
            ],
        ];
    }

    // =========================================================================
    // WMA — Weighted Moving Average
    // Rumus : WMA = (1×d1 + 3×d2 + 30×d3) / 34
    // Bobot : [1, 3, 30] → data terbaru dapat bobot 88.2% (air sungai cepat berubah)
    // Output: prediksi nilai parameter untuk shift berikutnya
    // =========================================================================

    private function calculateWMA(array $dataArray): float
    {
        $lastThree = array_slice($dataArray, -3);
        // Ambil 3 data terakhir: [d1=terlama, d2=tengah, d3=terbaru]
        // contoh turbidity: [57, 39, 33] dari shift 11:00, 13:00, 15:00

        $weights     = WmaSetting::getWeights('air_baku', [1, 3, 30]);
        $weightedSum = 0;
        $weightTotal = 0;

        foreach ($lastThree as $i => $value) {
            $weightedSum += $value * $weights[$i];
            // Kalikan tiap nilai dengan bobotnya
            // contoh: 1×57=57, 3×39=117, 30×33=990 → total = 1164
            $weightTotal += $weights[$i];
            // Akumulasi total bobot: 1+3+30 = 34
        }

        return round($weightedSum / $weightTotal, 2);
        // WMA = 1164 / 34 = 34.24 NTU → prediksi turbidity shift 17:00
    }

    // =========================================================================
    // AMBIL DATA HISTORIS UNTUK WMA
    // Sumber: tabel shifts, water_qualities, pump_chemicals
    // Output: array 7 parameter, masing-masing berisi 3 titik data [d1, d2, d3]
    // Return null jika data historis kurang dari 2 shift sebelumnya
    // =========================================================================

    private function getHistoricalWaterQualities($currentShiftId): ?array
    {
        $currentShift   = Shift::find($currentShiftId);
        $currentDate    = $currentShift->date;
        $currentEndTime = $currentShift->end_time;
        $currentHour    = (int) substr($currentEndTime, 0, 2);
        $baseTime       = strtotime($currentEndTime);
        // Parsing waktu shift sekarang untuk menentukan shift mana yang harus dicari

        if ($currentHour == 1) {
            // KASUS 1: end_time = 01:00 → shift tengah malam
            // Shift sebelumnya ada di hari kemarin (23:00 dan 21:00)
            $yesterdayDate = date('Y-m-d', strtotime('-1 day', strtotime($currentDate)));
            $shifts = Shift::where('id', '!=', $currentShiftId)
                ->where('date', $yesterdayDate)
                ->whereIn('end_time', ['23:00', '21:00'])
                ->orderBy('end_time', 'asc')
                ->with('waterQualities', 'pumpChemicals')
                ->get();

        } elseif ($currentHour == 3) {
            // KASUS 2: end_time = 03:00 → shift dini hari
            // d1 = shift 23:00 kemarin, d2 = shift 01:00 hari ini
            // Dicari terpisah karena beda tanggal, lalu digabung
            $yesterdayDate = date('Y-m-d', strtotime('-1 day', strtotime($currentDate)));

            $shift01Today = Shift::where('id', '!=', $currentShiftId)
                ->where('date', $currentDate)->where('end_time', '01:00')
                ->with('waterQualities', 'pumpChemicals')->first();
            // Shift 01:00 hari ini sebagai d2

            $shift23Yest = Shift::where('id', '!=', $currentShiftId)
                ->where('date', $yesterdayDate)->where('end_time', '23:00')
                ->with('waterQualities', 'pumpChemicals')->first();
            // Shift 23:00 kemarin sebagai d1

            $shifts = collect();
            if ($shift23Yest)  $shifts->push($shift23Yest);
            if ($shift01Today) $shifts->push($shift01Today);
            // Gabungkan secara manual dengan urutan kronologis [d1, d2]

        } else {
            // KASUS 3: end_time lainnya (05:00, 07:00, ..., 23:00)
            // Ambil 2 shift sebelumnya di hari yang sama
            $time2HoursAgo = date('H:i', strtotime('-2 hours', $baseTime));
            $time4HoursAgo = date('H:i', strtotime('-4 hours', $baseTime));
            // contoh shift 15:00 → cari 13:00 (d2) dan 11:00 (d1)

            $shifts = Shift::where('id', '!=', $currentShiftId)
                ->where('date', $currentDate)
                ->whereIn('end_time', [$time2HoursAgo, $time4HoursAgo])
                ->orderBy('end_time', 'asc')
                ->with('waterQualities', 'pumpChemicals')
                ->get();
        }

        if ($shifts->count() < 2) return null;
        // Butuh minimal 2 shift sebelumnya untuk membentuk array [d1, d2, d3]
        // Jika kurang → WMA tidak bisa dihitung → return null

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
        // Inisialisasi array kosong untuk 9 parameter yang akan diisi dari shift historis

        foreach ($shifts as $shift) {
            // Iterasi tiap shift sebelumnya (d1, d2) → ambil nilai parameter
            foreach ($shift->waterQualities as $wq) {
                if ($wq->type == 'air baku') {
                    $data['turbidityAB'][] = $wq->turbidity;
                    $data['phAB'][]        = $wq->ph;
                    $data['tdsAB'][]       = $wq->tds;
                    // Kualitas air baku (sebelum proses)
                } elseif ($wq->type == 'sedimentation') {
                    $data['turbiditySeq'][] = $wq->turbidity;
                    // Turbidity setelah sedimentasi (input Fuzzy PAC)
                } elseif ($wq->type == 'reservoir') {
                    $data['phRes'][]        = $wq->ph;
                    $data['freeChlorine'][] = $wq->free_chlor ?? 0;
                    // Kualitas air di reservoir (input Fuzzy Klorin & Soda Ash)
                }
            }

            $data['pACDosis'][]      = $shift->pumpChemicals()->where('type', 'pac')->first()?->dosage ?? 0;
            $data['chlorineDosis'][] = $shift->pumpChemicals()->where('type', 'chlorine/kaporit')->first()?->dosage ?? 0;
            $data['sodaAshDosis'][]  = $shift->pumpChemicals()->where('type', 'soda ash')->first()?->dosage ?? 0;
            // Dosis aktual pompa kimia dari shift sebelumnya (sebagai d1, d2)
        }

        foreach ($currentShift->waterQualities as $wq) {
            // Tambahkan data shift sekarang sebagai titik ke-3 (d3 = data terbaru)
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
        // Setelah loop ini, setiap array punya 3 elemen [d1, d2, d3] siap dihitung WMA

        if (count($data['turbidityAB']) < 3) return null;
        // Validasi akhir: pastikan turbidityAB punya 3 titik data
        // Jika tidak → kemungkinan ada shift yang tidak punya data water quality

        return $data;
    }

    // =========================================================================
    // RENDER
    // =========================================================================

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
        // Ambil data 2 shift sebelumnya → null jika tidak cukup

        $currentAB  = null;
        $currentSed = null;
        $currentRes = null;
        // Pisahkan water quality berdasarkan tipe untuk dipakai Fuzzy

        foreach ($shift->waterQualities as $wq) {
            if ($wq->type == 'air baku')          $currentAB  = $wq;
            elseif ($wq->type == 'sedimentation') $currentSed = $wq;
            elseif ($wq->type == 'reservoir')     $currentRes = $wq;
        }

        $dataIncomplete = $historicalData === null;
        // Flag: true jika data historis tidak cukup untuk WMA

        $wmaData = [
            'turbidityAB'   => null,
            'phAB'          => null,
            'tdsAB'         => null,
            'freeChlorine'  => null,
            'pacDosis'      => null,
            'chlorineDosis' => null,
            'sodaAshDosis'  => null,
        ];
        // Default null → tampil "-" di view jika WMA belum bisa dihitung

        $chartData  = [
            'historicalTurbidity' => [],
            'historicalPH'        => [],
            'historicalTDS'       => [],
            'wmaPredictions'      => $wmaData,
        ];
        $timeLabels = [];

        if (!$dataIncomplete) {
            // Data historis cukup → hitung WMA untuk semua parameter
            $wmaData = [
                'turbidityAB'   => $this->calculateWMA($historicalData['turbidityAB']),
                'phAB'          => $this->calculateWMA($historicalData['phAB']),
                'tdsAB'         => $this->calculateWMA($historicalData['tdsAB']),
            ];

            $chartData = [
                'historicalTurbidity' => $historicalData['turbidityAB'],
                'historicalPH'        => $historicalData['phAB'],
                'historicalTDS'       => $historicalData['tdsAB'],
                'wmaPredictions'      => $wmaData,
            ];
            // Data untuk chart: 3 titik historis + 1 titik prediksi WMA

            $baseTime     = strtotime($shift->end_time);
            $timeLabels[] = date('H:i', strtotime('-4 hours', $baseTime));
            $timeLabels[] = date('H:i', strtotime('-2 hours', $baseTime));
            $timeLabels[] = date('H:i', $baseTime);
            $timeLabels[] = 'Prediksi';
            // Label sumbu X chart: [11:00, 13:00, 15:00, Prediksi]
        }

        $pacChemical      = $shift->pumpChemicals()->where('type', 'pac')->where('status', 'running')->first()
                         ?? $shift->pumpChemicals()->where('type', 'pac')->first();
        $chlorineChemical = $shift->pumpChemicals()->where('type', 'chlorine/kaporit')->where('status', 'running')->first()
                         ?? $shift->pumpChemicals()->where('type', 'chlorine/kaporit')->first();
        $sodaAshChemical  = $shift->pumpChemicals()->where('type', 'soda ash')->where('status', 'running')->first()
                         ?? $shift->pumpChemicals()->where('type', 'soda ash')->first();
        // Prioritaskan pompa yang statusnya 'running', fallback ke pompa pertama jika tidak ada

        $sdsRecommendations = [
            'pac'     => $currentSed ? $this->fuzzyPAC($currentSed->turbidity, $pacChemical?->dosage ?? 10.0)              : null,
            'klorin'  => $currentRes ? $this->fuzzyKlorin($currentRes->free_chlor ?? 0, $chlorineChemical?->dosage ?? 1.5) : null,
            'sodaAsh' => $currentRes ? $this->fuzzySodaAsh($currentRes->ph, $sodaAshChemical?->dosage ?? 2.0)              : null,
        ];
        // Fuzzy berjalan independen dari WMA — tetap dihitung meski data historis kurang
        // null jika data water quality shift sekarang belum diisi operator

        $wmaWeights = WmaSetting::getWeights('air_baku', [1, 3, 30]);

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
            'wmaWeights'         => $wmaWeights,
        ])->layout('layouts.app');
    }
}

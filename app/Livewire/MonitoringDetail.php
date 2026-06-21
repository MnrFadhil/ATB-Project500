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

    // =========================================================================
    // FUZZY MEMBERSHIP FUNCTIONS
    // =========================================================================
    //
    // Tiga jenis fungsi keanggotaan (MF) yang digunakan dalam Fuzzy Mamdani:
    //
    // 1. leftShoulderMF  — himpunan "sisi kiri" (nilai rendah / sangat rendah)
    //    Bentuk: μ=1 dari -∞ sampai a, turun linier dari a ke b, μ=0 dari b ke ∞
    //    Contoh: sangat_rendah turbidity → leftShoulder(x, 0.0, 2.0)
    //      x=0.5  → μ = (2.0-0.5)/(2.0-0.0) = 0.75
    //      x=1.0  → μ = (2.0-1.0)/(2.0-0.0) = 0.50
    //      x=2.0  → μ = 0 (sudah di batas kanan)
    //      x=3.0  → μ = 0
    //
    // 2. triangularMF — himpunan segitiga (nilai tengah)
    //    Bentuk: naik dari a ke b (puncak μ=1), turun dari b ke c
    //    Contoh: rendah turbidity → triangular(x, 1.0, 2.5, 3.2)
    //      x=1.0  → μ = 0 (tepat di tepi kiri, belum naik)
    //      x=1.75 → μ = (1.75-1.0)/(2.5-1.0) = 0.50 (naik)
    //      x=2.5  → μ = 1.0 (puncak)
    //      x=2.85 → μ = (3.2-2.85)/(3.2-2.5) = 0.50 (turun)
    //      x=3.2  → μ = 0 (tepat di tepi kanan)
    //
    // 3. rightShoulderMF — himpunan "sisi kanan" (nilai tinggi / sangat tinggi)
    //    Bentuk: μ=0 dari -∞ sampai a, naik linier dari a ke b, μ=1 dari b ke ∞
    //    Contoh: sangat_tinggi turbidity → rightShoulder(x, 4.5, 6.0)
    //      x=4.5  → μ = 0
    //      x=5.25 → μ = (5.25-4.5)/(6.0-4.5) = 0.50
    //      x=6.0  → μ = 1.0
    //      x=8.0  → μ = 1.0 (tetap 1 sampai ∞)
    // =========================================================================

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

    // =========================================================================
    // DEFUZZIFIKASI — Metode Centroid
    // =========================================================================
    //
    // Mengubah nilai fuzzy (μ) kembali menjadi angka tegas (crisp output).
    // Metode: Centroid = Σ(μᵢ × centerᵢ) / Σ(μᵢ)
    //
    // Setiap rule punya 2 komponen: [μ, center]
    //   μ      = derajat keanggotaan hasil fuzzifikasi (0.0 – 1.0)
    //   center = titik tengah output himpunan (delta dosis dalam ppm)
    //
    // Contoh (Fuzzy PAC, turbidity=1.47 NTU, dosis_prev=11.4 ppm):
    //   Rules yang aktif:
    //     sangat_rendah: μ=0.265, center=-3.0 → kontribusi = 0.265 × (-3.0) = -0.795
    //     rendah:        μ=0.313, center=-1.0 → kontribusi = 0.313 × (-1.0) = -0.313
    //     optimal:       μ=0.000, center= 0.0 → kontribusi = 0
    //     tinggi:        μ=0.000, center=+1.5 → kontribusi = 0
    //     sangat_tinggi: μ=0.000, center=+3.5 → kontribusi = 0
    //
    //   Σ(μ×center) = -0.795 + (-0.313) = -1.108
    //   Σ(μ)        =  0.265 + 0.313    =  0.578
    //   delta        = -1.108 / 0.578   = -1.92 ppm
    //
    //   Rekomendasi  = clamp(11.4 + (-1.92), 5, 20) = clamp(9.48, 5, 20) = 9.48 ppm
    // =========================================================================

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

    // =========================================================================
    // FUZZY MAMDANI — PAC (Koagulan)
    // =========================================================================
    //
    // TUJUAN: Merekomendasikan dosis PAC berdasarkan turbidity air sedimentation.
    //
    // SUMBER DATA:
    //   Input  → water_qualities.turbidity WHERE type='sedimentation' (shift sekarang)
    //   Prev   → pump_chemicals.dosage WHERE type='pac' AND status='running' (shift sekarang)
    //   Output → rekomendasi dosis PAC untuk shift BERIKUTNYA (ppm)
    //
    // LOGIKA:
    //   Turbidity tinggi  → air keruh → butuh lebih banyak PAC (delta positif)
    //   Turbidity rendah  → air bersih → kurangi PAC (delta negatif)
    //   Output di-clamp antara 5–20 ppm (batas aman operasional)
    //
    // HIMPUNAN FUZZY INPUT (turbidity NTU):
    //   sangat_rendah  → leftShoulder(x, 0.0, 2.0)          → delta = -3.0 ppm
    //   rendah         → triangular(x, 1.0, 2.5, 3.2)       → delta = -1.0 ppm
    //   optimal        → triangular(x, 2.8, 3.3, 3.8)       → delta =  0.0 ppm
    //   tinggi         → triangular(x, 3.4, 4.1, 5.0)       → delta = +1.5 ppm
    //   sangat_tinggi  → rightShoulder(x, 4.5, 6.0)         → delta = +3.5 ppm
    //
    // CONTOH PERHITUNGAN (turbidity=1.47 NTU, dosis_prev=11.4 ppm):
    //   μ(sangat_rendah) = (2.0-1.47)/(2.0-0.0) = 0.265
    //   μ(rendah)        = (1.47-1.0)/(2.5-1.0) = 0.313
    //   μ(optimal)       = 0 (1.47 < 2.8, di luar range)
    //   μ(tinggi)        = 0
    //   μ(sangat_tinggi) = 0
    //   delta = (0.265×(-3) + 0.313×(-1)) / (0.265+0.313) = -1.108/0.578 = -1.92
    //   output = clamp(11.4 + (-1.92), 5, 20) = 9.48 ppm
    // =========================================================================

    private function fuzzyPAC($sedimentationTurbidity, float $previousDosis = 0): array
    {
        $t = (float) $sedimentationTurbidity;

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

    // =========================================================================
    // FUZZY MAMDANI — Klorin (Desinfektan)
    // =========================================================================
    //
    // TUJUAN: Merekomendasikan dosis Klorin berdasarkan free chlorine di reservoir.
    //
    // SUMBER DATA:
    //   Input  → water_qualities.free_chlor WHERE type='reservoir' (shift sekarang)
    //   Prev   → pump_chemicals.dosage WHERE type='chlorine/kaporit' AND status='running'
    //   Output → rekomendasi dosis Klorin untuk shift BERIKUTNYA (ppm)
    //
    // LOGIKA:
    //   Free chlorine rendah  → air kurang aman → tambah klorin (delta positif)
    //   Free chlorine tinggi  → sudah cukup → kurangi klorin (delta negatif)
    //   Output di-clamp antara 0–3 ppm
    //   KONDISI DARURAT: jika free_chlor >= 0.60 dan μ(sangat_tinggi)=1.0
    //                    → return 0 langsung (matikan pompa)
    //
    // HIMPUNAN FUZZY INPUT (free chlorine mg/L):
    //   sangat_rendah  → leftShoulder(x, 0.00, 0.20)         → delta = +1.0 ppm
    //   rendah         → triangular(x, 0.15, 0.26, 0.30)     → delta = +0.4 ppm
    //   optimal        → triangular(x, 0.31, 0.37, 0.46)     → delta =  0.0 ppm
    //   tinggi         → triangular(x, 0.43, 0.48, 0.51)     → delta = -0.7 ppm
    //   sangat_tinggi  → rightShoulder(x, 0.50, 0.60)        → delta = -2.0 ppm
    //
    // CONTOH PERHITUNGAN (free_chlor=0.32 mg/L, dosis_prev=1.4 ppm):
    //   μ(sangat_rendah) = 0 (0.32 > 0.20)
    //   μ(rendah)        = 0 (0.32 > 0.30, di luar range)
    //   μ(optimal)       = (0.32-0.31)/(0.37-0.31) = 0.167
    //   μ(tinggi)        = 0 (0.32 < 0.43)
    //   μ(sangat_tinggi) = 0 (0.32 < 0.50)
    //   delta = (0.167×0.0) / 0.167 = 0.0 ppm
    //   output = clamp(1.4 + 0.0, 0, 3) = 1.4 ppm (pertahankan)
    // =========================================================================

    private function fuzzyKlorin($freeChlorine, float $previousDosis = 0): array
    {
        $f = (float) $freeChlorine;

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

    // =========================================================================
    // FUZZY MAMDANI — Soda Ash (pH Adjuster)
    // =========================================================================
    //
    // TUJUAN: Merekomendasikan dosis Soda Ash berdasarkan pH reservoir.
    //         Soda Ash HANYA untuk MENAIKKAN pH (jika pH rendah/asam).
    //         Jika pH sudah normal (>= 6.5) → pompa standby, dosis = 0.
    //
    // SUMBER DATA:
    //   Input  → water_qualities.ph WHERE type='reservoir' (shift sekarang)
    //   Prev   → pump_chemicals.dosage WHERE type='soda ash' AND status='running'
    //   Output → rekomendasi dosis Soda Ash untuk shift BERIKUTNYA (ppm)
    //
    // LOGIKA:
    //   pH < 6.5  → air terlalu asam → tambah Soda Ash (delta positif)
    //   pH >= 6.5 → kondisi normal   → return 0 langsung (tidak perlu dosing)
    //   Output di-clamp antara 0–10 ppm
    //
    // HIMPUNAN FUZZY INPUT (pH):
    //   sangat_rendah   → triangular(x, 3.0, 4.5, 5.2)      → delta = +3.0 ppm
    //   rendah          → triangular(x, 4.8, 5.5, 6.1)      → delta = +2.0 ppm
    //   sedikit_rendah  → triangular(x, 5.8, 6.2, 6.5)      → delta = +1.0 ppm
    //   normal          → triangular(x, 6.5, 7.0, 7.8)      → delta =  0.0 ppm
    //
    // CONTOH PERHITUNGAN A (pH=6.0, dosis_prev=2.0 ppm):
    //   pH < 6.5, lanjut ke fuzzifikasi:
    //   μ(sangat_rendah)  = 0 (6.0 > 5.2)
    //   μ(rendah)         = (6.1-6.0)/(6.1-5.5) = 0.167
    //   μ(sedikit_rendah) = (6.0-5.8)/(6.2-5.8) = 0.500
    //   μ(normal)         = 0 (6.0 < 6.5)
    //   delta = (0.167×2.0 + 0.500×1.0) / (0.167+0.500)
    //         = (0.334 + 0.500) / 0.667 = 0.834/0.667 = 1.25 ppm
    //   output = clamp(2.0 + 1.25, 0, 10) = 3.25 ppm
    //
    // CONTOH PERHITUNGAN B (pH=7.45, dosis_prev=0):
    //   pH >= 6.5 → langsung return 0, pompa standby
    // =========================================================================

    private function fuzzySodaAsh($ph, float $previousDosis = 2.0): array
    {
        $p = (float) $ph;

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

    // =========================================================================
    // WMA — Weighted Moving Average
    // =========================================================================
    //
    // TUJUAN: Memprediksi nilai parameter air/dosis shift BERIKUTNYA
    //         berdasarkan 3 titik data historis (2 shift lalu + shift sekarang).
    //
    // RUMUS:
    //   WMA = (w1×d1 + w2×d2 + w3×d3) / (w1+w2+w3)
    //
    // BOBOT: [1, 3, 30] → total = 34
    //   w1=1  → data terlama  (shift -4 jam)   → pengaruh  2.9%
    //   w2=3  → data tengah   (shift -2 jam)   → pengaruh  8.8%
    //   w3=30 → data terbaru  (shift sekarang) → pengaruh 88.2%
    //
    // MENGAPA BOBOT 1:3:30?
    //   Data air sungai berubah cepat, sehingga kondisi terbaru
    //   jauh lebih relevan dibanding 4 jam lalu. Bobot besar di
    //   data terbaru membuat prediksi responsif terhadap perubahan.
    //
    // SUMBER DATA (diambil oleh getHistoricalWaterQualities):
    //   d1, d2 → dari 2 shift sebelumnya (tabel shifts + waterQualities + pumpChemicals)
    //   d3     → dari shift sekarang (shift yang sedang dibuka)
    //
    // PARAMETER YANG DIPREDIKSI:
    //   turbidityAB   → water_qualities.turbidity (type='air baku')
    //   phAB          → water_qualities.ph (type='air baku')
    //   tdsAB         → water_qualities.tds (type='air baku')
    //   freeChlorine  → water_qualities.free_chlor (type='reservoir')
    //   pACDosis      → pump_chemicals.dosage (type='pac')
    //   chlorineDosis → pump_chemicals.dosage (type='chlorine/kaporit')
    //   sodaAshDosis  → pump_chemicals.dosage (type='soda ash')
    //
    // CONTOH PERHITUNGAN (turbidity air baku):
    //   d1 = 57 NTU (shift 11:00), d2 = 39 NTU (shift 13:00), d3 = 33 NTU (shift 15:00)
    //   WMA = (1×57 + 3×39 + 30×33) / 34
    //       = (57 + 117 + 990) / 34
    //       = 1164 / 34
    //       = 34.24 NTU  ← prediksi untuk shift 17:00
    // =========================================================================

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

    // =========================================================================
    // AMBIL DATA HISTORIS UNTUK WMA
    // =========================================================================
    //
    // TUJUAN: Mengambil 2 shift sebelumnya agar dapat membentuk array 3 titik
    //         data [d1, d2, d3] yang dibutuhkan oleh calculateWMA().
    //
    // SUMBER DATA:
    //   Tabel: shifts, water_qualities, pump_chemicals
    //   Relasi: shifts → waterQualities (hasMany), shifts → pumpChemicals (hasMany)
    //
    // LOGIKA PENCARIAN SHIFT SEBELUMNYA (berdasarkan end_time shift sekarang):
    //
    //   KASUS 1 — end_time = 01:00 (shift tengah malam)
    //     → Ambil shift 23:00 dan 21:00 dari HARI KEMARIN
    //     → Karena shift 01:00 adalah shift pertama hari ini,
    //       shift sebelumnya ada di hari sebelumnya
    //
    //   KASUS 2 — end_time = 03:00 (shift dini hari)
    //     → Ambil shift 01:00 hari INI + shift 23:00 HARI KEMARIN
    //     → Dicari terpisah (2 query) lalu digabung dengan collect()
    //
    //   KASUS 3 — end_time lainnya (05:00, 07:00, ..., 23:00)
    //     → Ambil shift 2 jam sebelumnya dan 4 jam sebelumnya, hari yang SAMA
    //     → Contoh: shift 15:00 → ambil shift 13:00 dan 11:00
    //
    // OUTPUT: array berisi 7 key, masing-masing adalah array 3 elemen [d1, d2, d3]
    //   Elemen ke-1 dan ke-2 = dari shift sebelumnya
    //   Elemen ke-3 = dari shift sekarang (ditambahkan di bagian bawah method ini)
    //
    // RETURN null jika:
    //   - Shift sebelumnya kurang dari 2 (data tidak cukup untuk WMA)
    //   - turbidityAB kurang dari 3 titik
    // =========================================================================

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

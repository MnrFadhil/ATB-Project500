{{--
    Partial: fuzzy-detail.blade.php
    Variable $rec — array dari fuzzyPAC / fuzzyKlorin / fuzzySodaAsh
--}}
@php
    $mu         = $rec['mu']           ?? [];
    $cats       = $rec['categories']   ?? [];
    $centers    = $rec['rule_centers'] ?? [];
    $mfParams   = $rec['mf_params']    ?? [];
    $delta      = $rec['delta']        ?? 0;
    $prevDosis  = $rec['previous_dosis'] ?? 0;
    $rec_val    = $rec['recommendation'] ?? 0;
    $clampMin   = $rec['clamp_min']    ?? 0;
    $clampMax   = $rec['clamp_max']    ?? 10;
    $unit       = $rec['unit']         ?? 'ppm';
    $inputVal   = $rec['input_value']  ?? 0;
    $inputLabel = $rec['input_label']  ?? '';

    // Centroid numerator and denominator
    $sumMuCenter = 0;
    $sumMu       = 0;
    foreach ($mu as $key => $muVal) {
        $center = $centers[$key] ?? 0;
        $sumMuCenter += $muVal * $center;
        $sumMu       += $muVal;
    }

    // Helper: describe the MF calculation for display
    // Returns ['formula_steps' => [...lines], 'result' => float]
    $describeMF = function(string $type, array $p, float $x): array {
        $x = round($x, 4);
        if ($type === 'left') {
            $a = $p['a']; $b = $p['b'];
            if ($x <= $a) {
                return ['steps' => [
                    "Tipe: Bahu Kiri   [a={$a}, b={$b}]",
                    "Kondisi: x={$x} ≤ a={$a}  →  μ = 1",
                ], 'result' => 1.0];
            } elseif ($x >= $b) {
                return ['steps' => [
                    "Tipe: Bahu Kiri   [a={$a}, b={$b}]",
                    "Kondisi: x={$x} ≥ b={$b}  →  μ = 0",
                ], 'result' => 0.0];
            } else {
                $num = round($b - $x, 4);
                $den = round($b - $a, 4);
                $res = round($num / $den, 4);
                return ['steps' => [
                    "Tipe: Bahu Kiri   [a={$a}, b={$b}]",
                    "Kondisi: a={$a} < x={$x} < b={$b}",
                    "μ = (b − x) / (b − a)",
                    "μ = ({$b} − {$x}) / ({$b} − {$a})",
                    "μ = {$num} / {$den}",
                    "μ = {$res}",
                ], 'result' => $res];
            }
        } elseif ($type === 'right') {
            $a = $p['a']; $b = $p['b'];
            if ($x <= $a) {
                return ['steps' => [
                    "Tipe: Bahu Kanan   [a={$a}, b={$b}]",
                    "Kondisi: x={$x} ≤ a={$a}  →  μ = 0",
                ], 'result' => 0.0];
            } elseif ($x >= $b) {
                return ['steps' => [
                    "Tipe: Bahu Kanan   [a={$a}, b={$b}]",
                    "Kondisi: x={$x} ≥ b={$b}  →  μ = 1",
                ], 'result' => 1.0];
            } else {
                $num = round($x - $a, 4);
                $den = round($b - $a, 4);
                $res = round($num / $den, 4);
                return ['steps' => [
                    "Tipe: Bahu Kanan   [a={$a}, b={$b}]",
                    "Kondisi: a={$a} < x={$x} < b={$b}",
                    "μ = (x − a) / (b − a)",
                    "μ = ({$x} − {$a}) / ({$b} − {$a})",
                    "μ = {$num} / {$den}",
                    "μ = {$res}",
                ], 'result' => $res];
            }
        } else {
            // triangle
            $a = $p['a']; $b = $p['b']; $c = $p['c'];
            if ($x <= $a || $x >= $c) {
                $cond = $x <= $a ? "x={$x} ≤ a={$a}" : "x={$x} ≥ c={$c}";
                return ['steps' => [
                    "Tipe: Segitiga   [a={$a}, b={$b}, c={$c}]",
                    "Kondisi: {$cond}  →  μ = 0",
                ], 'result' => 0.0];
            } elseif ($x <= $b) {
                $num = round($x - $a, 4);
                $den = round($b - $a, 4);
                $res = round($num / $den, 4);
                return ['steps' => [
                    "Tipe: Segitiga   [a={$a}, b={$b}, c={$c}]",
                    "Kondisi: a={$a} < x={$x} ≤ b={$b}  (sisi naik)",
                    "μ = (x − a) / (b − a)",
                    "μ = ({$x} − {$a}) / ({$b} − {$a})",
                    "μ = {$num} / {$den}",
                    "μ = {$res}",
                ], 'result' => $res];
            } else {
                $num = round($c - $x, 4);
                $den = round($c - $b, 4);
                $res = round($num / $den, 4);
                return ['steps' => [
                    "Tipe: Segitiga   [a={$a}, b={$b}, c={$c}]",
                    "Kondisi: b={$b} < x={$x} < c={$c}  (sisi turun)",
                    "μ = (c − x) / (c − b)",
                    "μ = ({$c} − {$x}) / ({$c} − {$b})",
                    "μ = {$num} / {$den}",
                    "μ = {$res}",
                ], 'result' => $res];
            }
        }
    };
@endphp

<div style="font-size:11px; border-top:1px solid #dee2e6; padding-top:8px;">

    {{-- Input --}}
    <div class="mb-2 p-2 rounded" style="background:#e9f1ee; font-size:11px;">
        <strong>Input:</strong> {{ $inputLabel }} (x) = <strong>{{ $inputVal }} {{ $unit }}</strong>
    </div>

    {{-- Step 1: Fuzzifikasi --}}
    <p class="mb-1" style="font-size:11px; font-weight:bold;">Step 1 — Fuzzifikasi (μ tiap himpunan)</p>

    @foreach($mu as $key => $muVal)
    @php
        $p    = $mfParams[$key] ?? [];
        $type = $p['type'] ?? 'triangle';
        $desc = $mfParams ? $describeMF($type, $p, (float)$inputVal) : ['steps' => [], 'result' => $muVal];
        $active = $muVal > 0;
    @endphp
    <div class="mb-2 p-2 rounded" style="border:1px solid {{ $active ? '#f59e0b' : '#dee2e6' }}; background:{{ $active ? '#fffbeb' : '#f8f9fa' }};">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <strong style="color:{{ $active ? '#92400e' : '#6c757d' }};">
                {{ $cats[$key] ?? $key }}
            </strong>
            <span class="badge badge-{{ $active ? 'warning' : 'secondary' }}" style="font-size:11px;">
                μ = {{ number_format($muVal, 4) }}
            </span>
        </div>
        <div style="font-family:monospace; font-size:10.5px; line-height:1.7; color:#374151;">
            @foreach($desc['steps'] as $i => $line)
                @if($i === 0)
                    <span style="color:#6b7280;">{{ $line }}</span><br>
                @elseif($loop->last && count($desc['steps']) > 2)
                    <strong style="color:{{ $active ? '#b45309' : '#374151' }};">{{ $line }}</strong>
                @else
                    {{ $line }}<br>
                @endif
            @endforeach
        </div>
    </div>
    @endforeach

    {{-- Step 2: Evaluasi Aturan --}}
    <p class="mb-1 mt-2" style="font-size:11px; font-weight:bold;">Step 2 — Evaluasi Aturan (μ × output center)</p>
    <table class="table table-bordered table-sm mb-2" style="font-size:11px;">
        <thead class="thead-light">
            <tr>
                <th>Himpunan</th>
                <th class="text-center">μ</th>
                <th class="text-center">Center δ</th>
                <th class="text-center">μ × δ</th>
            </tr>
        </thead>
        <tbody>
            @foreach($mu as $key => $muVal)
            @php $center = $centers[$key] ?? 0; @endphp
            <tr @if($muVal > 0) style="background:#fffbeb;" @endif>
                <td>{{ $cats[$key] ?? $key }}</td>
                <td class="text-center">{{ number_format($muVal, 4) }}</td>
                <td class="text-center">{{ $center >= 0 ? '+' : '' }}{{ $center }}</td>
                <td class="text-center">{{ number_format($muVal * $center, 4) }}</td>
            </tr>
            @endforeach
            <tr class="font-weight-bold" style="background:#e9f1ee;">
                <td>Σ (Total)</td>
                <td class="text-center">{{ number_format($sumMu, 4) }}</td>
                <td class="text-center">—</td>
                <td class="text-center">{{ number_format($sumMuCenter, 4) }}</td>
            </tr>
        </tbody>
    </table>

    {{-- Step 3: Defuzzifikasi --}}
    <p class="mb-1" style="font-size:11px; font-weight:bold;">Step 3 — Defuzzifikasi (Metode Centroid)</p>
    <div class="p-2 rounded mb-2" style="background:#f8f9fa; font-family:monospace; font-size:11px; line-height:1.8;">
        δ = Σ(μᵢ × centerᵢ) / Σ(μᵢ)<br>
        δ = {{ number_format($sumMuCenter, 4) }} / {{ number_format($sumMu, 4) }}<br>
        <strong>δ = {{ $delta }} ppm</strong>
    </div>

    {{-- Step 4: Hasil Akhir --}}
    <p class="mb-1" style="font-size:11px; font-weight:bold;">Step 4 — Rekomendasi Dosis Akhir</p>
    <div class="p-2 rounded" style="background:#f8f9fa; font-family:monospace; font-size:11px; line-height:1.8;">
        Dosis sebelumnya (prev) = {{ $prevDosis }} ppm<br>
        Rekomendasi = clamp(prev + δ, {{ $clampMin }}, {{ $clampMax }})<br>
        Rekomendasi = clamp({{ $prevDosis }} + ({{ $delta }}), {{ $clampMin }}, {{ $clampMax }})<br>
        Rekomendasi = clamp({{ round($prevDosis + $delta, 4) }}, {{ $clampMin }}, {{ $clampMax }})<br>
        <strong>Rekomendasi = {{ $rec_val }} ppm</strong>
    </div>
</div>

<?php
use App\Models\Shift;
use Carbon\Carbon;

// ---- Tabel 4.1: avg turbidity air baku per month (Okt2025, Mar2026, Apr2026) ----
function monthlyAvgAirBaku($ym) {
    $start = $ym.'-01';
    $end = date('Y-m-t', strtotime($start));
    $shifts = Shift::with('waterQualities')->whereBetween('date',[$start,$end])->get();
    $vals=[];
    foreach($shifts as $s){
        $ab = $s->waterQualities->firstWhere('type','air baku');
        if($ab) $vals[]=(float)$ab->turbidity;
    }
    return count($vals) ? round(array_sum($vals)/count($vals),2) : null;
}
echo "=== Tabel 4.1 Turbidity Avg ===\n";
foreach(['2025-10'=>'Okt2025','2026-03'=>'Mar2026','2026-04'=>'Apr2026'] as $ym=>$label){
    echo "$label: ".monthlyAvgAirBaku($ym)."\n";
}

// ---- Tabel 4.2: avg Klorin & PAC per month ----
function monthlyAvgDosis($ym, $type) {
    $start=$ym.'-01'; $end=date('Y-m-t',strtotime($start));
    $shifts = Shift::with('pumpChemicals')->whereBetween('date',[$start,$end])->get();
    $total=0; $n=0;
    foreach($shifts as $s){
        $sum = $s->pumpChemicals->where('type',$type)->sum('dosage');
        if($sum>0){ $total+=$sum; $n++; }
    }
    return $n ? round($total/$n,2) : null;
}
echo "\n=== Tabel 4.2 Klorin/PAC Avg ===\n";
foreach(['2025-04'=>'Apr2025','2025-05'=>'Mei2025','2025-06'=>'Jun2025','2025-07'=>'Jul2025','2025-08'=>'Ags2025','2025-09'=>'Sep2025','2026-01'=>'Jan2026'] as $ym=>$label){
    echo "$label Klorin: ".monthlyAvgDosis($ym,'chlorine/kaporit')."\n";
}
echo "Nov2025 PAC: ".monthlyAvgDosis('2025-11','pac')."\n";

// ---- Tabel 4.13: Fuzzy Klorin MAPE Mei 2026 ----
// replicate fuzzyKlorin logic roughly via FuzzyEvaluationService if available

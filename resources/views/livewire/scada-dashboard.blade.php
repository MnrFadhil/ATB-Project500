<div class="scada-page">

{{-- ============================================================
     HEADER
     ============================================================ --}}
<div class="page-header">
    <div>
        <h1 class="page-title">Dashboard SCADA</h1>
        <div class="page-sub">
            <span>Instalasi Pengolahan Air · IPA Brayan</span>
            <span class="clock-dot"></span>
            @if($latest)
                <span>Data terakhir: <span id="header-ts">{{ \Carbon\Carbon::parse($latest->timestamp)->format('d M Y, H:i') }}</span></span>
            @else
                <span>Belum ada data</span>
            @endif
        </div>
    </div>
    <div class="status-cluster">
        @if($latest)
            <div class="status-badge {{ $isStale ? ($staleMinutes > 30 ? 'dead' : 'stale') : 'live' }}" id="status-badge">
                <span class="dot"></span>
                <span id="status-text">@if($isStale)Stale · {{ $staleMinutes }} mnt lalu@else Live @endif</span>
            </div>
        @endif
        <button class="refresh-btn" onclick="window.location.reload()">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 12a9 9 0 0 1 15-6.7L21 8"/><path d="M21 3v5h-5"/>
                <path d="M21 12a9 9 0 0 1-15 6.7L3 16"/><path d="M3 21v-5h5"/>
            </svg>
            Refresh
        </button>
    </div>
</div>

@if(!$latest)
<div class="alert alert-info">
    <i class="fas fa-info-circle mr-2"></i> Belum ada data sensor yang masuk.
</div>
@else

{{-- ============================================================
     WARNING STRIP
     ============================================================ --}}
@if($reservoirWarn)
<div class="warning-strip">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M10.3 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
        <path d="M12 9v4"/><path d="M12 17h.01"/>
    </svg>
    <div>
        <strong>Peringatan Level Reservoir.</strong>
        Salah satu reservoir mendekati batas operasional. Operator wajib mengecek katup distribusi.
    </div>
</div>
@endif

{{-- ============================================================
     SECTION 1 — FLOW & NERACA ALIRAN
     ============================================================ --}}
<div class="section">
    <div class="section-head">
        <h2 class="section-title">Flow &amp; Neraca Aliran</h2>
        <span class="section-meta">4 sensor · balance card</span>
    </div>

    {{-- Flow Balance Card --}}
    @php
        $fbIntake = $latest->flow_intake ?? 0;
        $fbYos    = $latest->flow_yos_sudarso ?? 0;
        $fbVet    = $latest->flow_veteran ?? 0;
        $fbDs     = round($fbYos + $fbVet, 1);
        $fbDiff   = round($fbIntake - $fbDs, 1);
        $fbPct    = $fbIntake > 0 ? abs($fbDiff / $fbIntake * 100) : 0;
        $fbState  = $fbPct > 2.8 ? 'KRITIS' : ($fbPct > 1.5 ? 'WASPADA' : 'NORMAL');
        $fbColor  = $fbPct > 2.8 ? '#e74a3b' : ($fbPct > 1.5 ? '#E7A52F' : '#1cc88a');
        $fbTone   = $fbPct > 2.8 ? 'rgba(231,74,59,.12)' : ($fbPct > 1.5 ? 'rgba(231,165,47,.14)' : 'rgba(28,200,138,.12)');
        $fbBarW   = $fbIntake > 0 ? min(100, $fbDs / $fbIntake * 100) : 0;
        $fbDir    = $fbDiff >= 0 ? 'kehilangan' : 'surplus';
    @endphp
    <div class="balance-card" id="fb-card" style="--diff-color:{{ $fbColor }}">
        <div class="balance-head">
            <div>
                <div class="balance-title">Neraca Aliran (Flow Balance)</div>
                <div class="balance-sub">Intake vs total distribusi (Yos Sudarso + Veteran)</div>
            </div>
            <div class="balance-state" id="fb-state" style="color:{{ $fbColor }};background:{{ $fbTone }}">
                <span style="width:6px;height:6px;border-radius:50%;background:{{ $fbColor }};display:inline-block;margin-right:6px;"></span>
                <span id="fb-state-label">{{ $fbState }}</span>
            </div>
        </div>
        <div class="balance-flow">
            <div class="balance-node intake">
                <div class="bn-label">Flow Intake</div>
                <div class="bn-val"><span id="fb-intake">{{ number_format($fbIntake, 1) }}</span><span>L/s</span></div>
                <div class="bn-foot">masuk dari sumber</div>
            </div>
            <div class="balance-arrow">
                <svg viewBox="0 0 100 60" preserveAspectRatio="none">
                    <defs>
                        <linearGradient id="fa-grad" x1="0" y1="0" x2="1" y2="0">
                            <stop offset="0%" stop-color="#4e73df"/>
                            <stop offset="100%" stop-color="#1cc88a"/>
                        </linearGradient>
                    </defs>
                    <path d="M 4 30 L 84 30" stroke="url(#fa-grad)" stroke-width="2.5" stroke-dasharray="6 4" fill="none">
                        <animate attributeName="stroke-dashoffset" values="0;-20" dur="1.5s" repeatCount="indefinite"/>
                    </path>
                    <path d="M 78 22 L 90 30 L 78 38 Z" fill="#1cc88a"/>
                </svg>
            </div>
            <div class="balance-node downstream">
                <div class="bn-label">Yos + Veteran</div>
                <div class="bn-val"><span id="fb-ds">{{ number_format($fbDs, 1) }}</span><span>L/s</span></div>
                <div class="bn-breakdown">
                    <span><b style="color:#1cc88a">●</b> Yos <span id="fb-yos">{{ number_format($fbYos, 1) }}</span></span>
                    <span><b style="color:#36b9cc">●</b> Vet <span id="fb-vet">{{ number_format($fbVet, 1) }}</span></span>
                </div>
            </div>
            <div class="balance-node diff" id="fb-diff-node" style="--diff-color:{{ $fbColor }};--diff-tone:{{ $fbTone }}">
                <div class="bn-label">Selisih</div>
                <div class="bn-val" id="fb-diff-val" style="color:{{ $fbColor }}">
                    <span id="fb-diff">{{ $fbDiff > 0 ? '−' : ($fbDiff < 0 ? '+' : '') }}{{ number_format(abs($fbDiff), 1) }}</span><span>L/s</span>
                </div>
                <div class="bn-foot">
                    <strong id="fb-pct" style="color:{{ $fbColor }}">{{ number_format($fbPct, 1) }}%</strong>
                    <span id="fb-dir"> {{ $fbDir }}</span>
                </div>
            </div>
        </div>
        <div class="balance-bar">
            <div class="balance-bar-track">
                <div class="balance-bar-fill" id="fb-bar-fill" style="width:{{ number_format($fbBarW, 1) }}%;background:{{ $fbColor }}"></div>
            </div>
            <div class="balance-bar-meta">
                <span>0</span>
                <span><span id="fb-bar-label">{{ number_format($fbDs, 1) }} / {{ number_format($fbIntake, 1) }}</span> L/s tersalurkan</span>
                <span>{{ number_format($fbIntake, 0) }}</span>
            </div>
        </div>
    </div>

    {{-- 4 Flow KPI cards --}}
    @php
    $flowCards = [
        ['field'=>'flow_intake',      'id'=>'flow-intake', 'label'=>'Flow Intake',      'accent'=>'#4e73df','soft'=>'rgba(78,115,223,.1)', 'icon'=>'drop','dec'=>1,'unit'=>'L/s'],
        ['field'=>'flow_yos_sudarso', 'id'=>'flow-yos',   'label'=>'Flow Yos Sudarso', 'accent'=>'#1cc88a','soft'=>'rgba(28,200,138,.1)','icon'=>'drop','dec'=>1,'unit'=>'L/s'],
        ['field'=>'flow_veteran',     'id'=>'flow-vet',   'label'=>'Flow Veteran',      'accent'=>'#36b9cc','soft'=>'rgba(54,185,204,.1)','icon'=>'drop','dec'=>1,'unit'=>'L/s'],
        ['field'=>'flow_backwash',    'id'=>'flow-bw',    'label'=>'Flow Backwash',     'accent'=>'#AF8CB1','soft'=>'rgba(175,140,177,.12)','icon'=>'drop','dec'=>1,'unit'=>'L/s'],
    ];
    @endphp
    <div class="kpi-grid">
        @foreach($flowCards as $c)
        <div class="kpi-card" style="--accent:{{ $c['accent'] }};--accent-soft:{{ $c['soft'] }}">
            <div class="kpi-head">
                <div class="kpi-label">{{ $c['label'] }}</div>
                <div class="kpi-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2.5c-3.5 4-7 7.5-7 11.5a7 7 0 0 0 14 0c0-4-3.5-7.5-7-11.5z"/>
                    </svg>
                </div>
            </div>
            <div class="kpi-value" id="kv-{{ $c['id'] }}">
                {{ number_format($latest->{$c['field']} ?? 0, $c['dec']) }}
                <span class="unit">{{ $c['unit'] }}</span>
            </div>
            <div class="kpi-meta">
                <span class="kpi-pulse" style="--accent:{{ $c['accent'] }}"></span>
                live · updated <span class="kpi-meta-time">—</span>
            </div>
            <div class="kpi-spark" id="spark-{{ $c['id'] }}"></div>
        </div>
        @endforeach
    </div>
</div>

{{-- ============================================================
     SECTION 2 — TURBIDITY
     ============================================================ --}}
<div class="section">
    <div class="section-head">
        <h2 class="section-title">Turbidity</h2>
        <span class="section-meta">4 titik pengukuran · NTU</span>
    </div>
    @php
    $turbCards = [
        ['field'=>'turbidity_baku',      'id'=>'turb-baku','label'=>'Air Baku',  'accent'=>'#f6c23e','soft'=>'rgba(246,194,62,.12)','dec'=>1],
        ['field'=>'turbidity_reservoir', 'id'=>'turb-res', 'label'=>'Reservoir', 'accent'=>'#36b9cc','soft'=>'rgba(54,185,204,.1)', 'dec'=>2],
        ['field'=>'turbidity_sedimen',   'id'=>'turb-sed', 'label'=>'Sedimen',   'accent'=>'#E4509A','soft'=>'rgba(228,80,154,.1)', 'dec'=>2],
        ['field'=>'turbidity_filter',    'id'=>'turb-fil', 'label'=>'Filter',    'accent'=>'#5FDA5A','soft'=>'rgba(95,218,90,.12)', 'dec'=>2],
    ];
    @endphp
    <div class="kpi-grid">
        @foreach($turbCards as $c)
        <div class="kpi-card" style="--accent:{{ $c['accent'] }};--accent-soft:{{ $c['soft'] }}">
            <div class="kpi-head">
                <div class="kpi-label">{{ $c['label'] }}</div>
                <div class="kpi-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 5h18l-7 9v6l-4-2v-4z"/>
                    </svg>
                </div>
            </div>
            <div class="kpi-value" id="kv-{{ $c['id'] }}">
                {{ number_format($latest->{$c['field']} ?? 0, $c['dec']) }}
                <span class="unit">NTU</span>
            </div>
            <div class="kpi-meta">
                <span class="kpi-pulse" style="--accent:{{ $c['accent'] }}"></span>
                live · updated <span class="kpi-meta-time">—</span>
            </div>
            <div class="kpi-spark" id="spark-{{ $c['id'] }}"></div>
        </div>
        @endforeach
    </div>
</div>

{{-- ============================================================
     SECTION 3 — KUALITAS AIR
     ============================================================ --}}
<div class="section">
    <div class="section-head">
        <h2 class="section-title">Kualitas Air</h2>
        <span class="section-meta">3 parameter mutu</span>
    </div>
    @php
    $qualCards = [
        ['field'=>'ph_baku',      'id'=>'ph-baku', 'label'=>'pH Air Baku',  'accent'=>'#4e73df','soft'=>'rgba(78,115,223,.1)', 'icon'=>'beaker','dec'=>2,'unit'=>''],
        ['field'=>'ph_reservoir', 'id'=>'ph-res',  'label'=>'pH Reservoir', 'accent'=>'#E4509A','soft'=>'rgba(228,80,154,.1)','icon'=>'beaker','dec'=>2,'unit'=>''],
        ['field'=>'free_chlorine','id'=>'free-cl', 'label'=>'Free Chlorine','accent'=>'#1cc88a','soft'=>'rgba(28,200,138,.1)','icon'=>'flask', 'dec'=>2,'unit'=>'mg/L'],
    ];
    @endphp
    <div class="kpi-grid kpi-grid-3">
        @foreach($qualCards as $c)
        <div class="kpi-card" style="--accent:{{ $c['accent'] }};--accent-soft:{{ $c['soft'] }}">
            <div class="kpi-head">
                <div class="kpi-label">{{ $c['label'] }}</div>
                <div class="kpi-icon">
                    @if($c['icon'] === 'beaker')
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4.5 3h15"/><path d="M6 3v12a4 4 0 0 0 4 4h4a4 4 0 0 0 4-4V3"/><path d="M6 13h12"/>
                    </svg>
                    @else
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 3h6"/><path d="M10 3v6L4.5 19a2 2 0 0 0 1.7 3h11.6a2 2 0 0 0 1.7-3L14 9V3"/>
                    </svg>
                    @endif
                </div>
            </div>
            <div class="kpi-value" id="kv-{{ $c['id'] }}">
                {{ number_format($latest->{$c['field']} ?? 0, $c['dec']) }}
                @if($c['unit'])<span class="unit">{{ $c['unit'] }}</span>@endif
            </div>
            <div class="kpi-meta">
                <span class="kpi-pulse" style="--accent:{{ $c['accent'] }}"></span>
                live · updated <span class="kpi-meta-time">—</span>
            </div>
            <div class="kpi-spark" id="spark-{{ $c['id'] }}"></div>
        </div>
        @endforeach
    </div>
</div>

{{-- ============================================================
     SECTION 4 — SCM
     ============================================================ --}}
<div class="section">
    <div class="section-head">
        <h2 class="section-title">SCM (Specific Conductivity Monitor)</h2>
        <span class="section-meta">range −20…+20 · aman −5…+2</span>
    </div>
    @php
        $scmVal    = $latest->scm ?? 0;
        $scmPct    = (($scmVal + 20) / 40) * 100;
        $scmSafe   = ($scmVal >= -5 && $scmVal <= 2);
        $scmWarn   = (!$scmSafe && (($scmVal > 2 && $scmVal <= 4) || ($scmVal < -5 && $scmVal >= -7)));
        $scmDanger = (!$scmSafe && !$scmWarn);
        $scmColor  = $scmDanger ? '#e74a3b' : ($scmWarn ? '#E7A52F' : '#1cc88a');
        $scmTone   = $scmDanger ? 'rgba(231,74,59,.14)' : ($scmWarn ? 'rgba(231,165,47,.14)' : 'rgba(28,200,138,.12)');
        $scmLabel  = $scmDanger ? 'BAHAYA' : ($scmWarn ? 'WASPADA' : 'AMAN');
        $scmTxt    = $scmDanger ? 'Nilai jauh di luar rentang aman. Hentikan proses &amp; periksa sistem.'
                   : ($scmWarn  ? 'Mendekati / melewati ambang. Operator wajib mengecek.'
                                : 'Beroperasi dalam rentang operasional aman.');
    @endphp
    <div class="scm-card" id="scm-card" style="--scm-state-color:{{ $scmColor }}">
        <div class="scm-head">
            <div>
                <div class="scm-title">SCM</div>
                <div class="scm-sub">Range sensor −20…+20 · operasional aman −5 sampai +2</div>
            </div>
            <div class="scm-state-badge" id="scm-state" style="background:{{ $scmTone }};color:{{ $scmColor }}">
                <span style="width:6px;height:6px;border-radius:50%;background:{{ $scmColor }};display:inline-block;margin-right:6px;"></span>
                <span id="scm-label">{{ $scmLabel }}</span>
            </div>
        </div>
        <div class="scm-value-row">
            <div class="scm-value" id="scm-value" style="color:{{ $scmColor }}">
                {{ $scmVal >= 0 ? '+' : '' }}{{ number_format($scmVal, 2) }}
            </div>
            <div class="scm-desc" id="scm-desc">{!! $scmTxt !!}</div>
        </div>
        <div class="scm-range">
            <div class="scm-track" id="scm-track">
                <div class="scm-safe" style="left:37.5%;width:17.5%"></div>
                @foreach([-20,-15,-10,-5,0,2,5,10,15,20] as $t)
                @php $tp = (($t + 20) / 40) * 100; $isSafe = ($t === -5 || $t === 2); @endphp
                <div class="scm-tick {{ $isSafe ? 'safe' : '' }}" style="left:{{ $tp }}%">
                    <span>{{ $t > 0 ? '+'.$t : $t }}</span>
                </div>
                @endforeach
                <div class="scm-needle" id="scm-needle" style="left:{{ number_format($scmPct, 2) }}%;background:{{ $scmColor }}">
                    <div class="scm-needle-head" style="border-top-color:{{ $scmColor }}"></div>
                </div>
            </div>
            <div class="scm-legend">
                <span><span class="sw" style="background:#1cc88a"></span>Zona Aman −5 … +2</span>
                <span><span class="sw" style="background:rgba(231,74,59,.55)"></span>Bahaya</span>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================
     SECTION 5 — PRESSURE GAUGES (ECharts)
     ============================================================ --}}
<div class="section">
    <div class="section-head">
        <h2 class="section-title">Pressure Gauges</h2>
        <span class="section-meta">5 titik tekanan · realtime</span>
    </div>
    <div class="gauges" wire:ignore>
        @php
        $gaugeDefs = [
            ['id'=>'gaugeIntake',     'label'=>'Pressure Intake',     'max'=>5],
            ['id'=>'gaugeDistribusi', 'label'=>'Pressure Distribusi', 'max'=>8],
            ['id'=>'gaugeService',    'label'=>'Pressure Service',    'max'=>5],
            ['id'=>'gaugeBackwash',   'label'=>'Pressure Backwash',   'max'=>5],
            ['id'=>'gaugeKompressor', 'label'=>'Pressure Kompressor', 'max'=>8],
        ];
        @endphp
        @foreach($gaugeDefs as $g)
        <div class="gauge-card">
            <div class="gauge-label">{{ $g['label'] }}</div>
            <div class="gauge-svg-wrap">
                <div id="{{ $g['id'] }}" style="width:100%;height:120px"></div>
            </div>
            <div class="gauge-foot">
                <span>0</span>
                <span class="gauge-state" id="{{ $g['id'] }}-state" style="color:#3D7E92;background:rgba(61,126,146,.1)">NORMAL</span>
                <span>{{ $g['max'] }}</span>
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- DEBUG: raw latest record --}}
<pre id="debug-raw" style="background:#1e1e2e;color:#cdd6f4;padding:16px;border-radius:10px;font-size:11px;line-height:1.6;overflow-x:auto;margin-bottom:16px">{{ json_encode($latest, JSON_PRETTY_PRINT) }}</pre>

@if(false) {{-- SECTION 6 — RESERVOIR & TREN HISTORIS (HIDDEN) --}}
<div class="section">
    <div class="section-head">
        <h2 class="section-title">Reservoir &amp; Tren Historis</h2>
        <span class="section-meta">100 titik terakhir</span>
    </div>
    <div class="split-row">
        {{-- Reservoir tanks --}}
        @php
        $maxLvl = 6;
        $resA   = $latest->level_reservoir_a ?? 0;
        $resB   = $latest->level_reservoir_b ?? 0;
        $resFn  = function($v) use ($maxLvl) {
            $pct = $maxLvl > 0 ? ($v / $maxLvl) * 100 : 0;
            if ($pct > 90 || $pct < 30) return '#B0473B';
            if ($pct < 50) return '#C28C2E';
            return '#3D7E92';
        };
        $pctA = round(min(100, $resA / $maxLvl * 100), 1);
        $pctB = round(min(100, $resB / $maxLvl * 100), 1);
        @endphp
        <div class="reservoirs" id="reservoir-section" wire:ignore>
            <h3>Level Reservoir</h3>
            <div class="reservoir-pair">
                <div class="res-card">
                    <div class="res-name">Reservoir A</div>
                    <div class="res-tank">
                        <div class="res-fill" id="res-fill-a" style="height:{{ $pctA }}%;--res-fill-color:{{ $resFn($resA) }}"></div>
                    </div>
                    <div class="res-val"><span id="res-val-a">{{ number_format($resA, 2) }}</span><span class="unit"> m</span></div>
                    <div class="res-pct" id="res-pct-a">{{ $pctA }}% / {{ $maxLvl }}m</div>
                </div>
                <div class="res-card">
                    <div class="res-name">Reservoir B</div>
                    <div class="res-tank">
                        <div class="res-fill" id="res-fill-b" style="height:{{ $pctB }}%;--res-fill-color:{{ $resFn($resB) }}"></div>
                    </div>
                    <div class="res-val"><span id="res-val-b">{{ number_format($resB, 2) }}</span><span class="unit"> m</span></div>
                    <div class="res-pct" id="res-pct-b">{{ $pctB }}% / {{ $maxLvl }}m</div>
                </div>
            </div>
            <div class="res-legend">
                <span><span class="res-legend-dot" style="background:#3D7E92"></span>Normal 50–90%</span>
                <span><span class="res-legend-dot" style="background:#C28C2E"></span>Rendah 30–50%</span>
                <span><span class="res-legend-dot" style="background:#B0473B"></span>Kritis</span>
            </div>
        </div>

        {{-- Area charts --}}
        <div class="chart-stack" wire:ignore>
            <div class="chart-card">
                <div class="chart-head">
                    <h4><span class="ic" style="background:var(--s1)"></span>Flow Rate</h4>
                    <div class="chart-legend">
                        <span class="legend-item"><span class="sw" style="background:var(--s1)"></span>Flow Intake</span>
                        <span class="legend-item"><span class="sw" style="background:var(--s2)"></span>Yos + Veteran</span>
                        <span style="color:var(--text-3)">L/s</span>
                    </div>
                </div>
                <div class="chart-wrap"><div id="chartFlow" style="width:100%;height:140px"></div></div>
            </div>
            <div class="chart-card">
                <div class="chart-head">
                    <h4><span class="ic" style="background:var(--s1)"></span>Turbidity</h4>
                    <div class="chart-legend">
                        <span class="legend-item"><span class="sw" style="background:var(--s1)"></span>Reservoir</span>
                        <span class="legend-item"><span class="sw" style="background:var(--s2)"></span>Sedimen</span>
                        <span class="legend-item"><span class="sw" style="background:var(--s3)"></span>Filter</span>
                        <span style="color:var(--text-3)">NTU</span>
                    </div>
                </div>
                <div class="chart-wrap"><div id="chartTurbidity" style="width:100%;height:140px"></div></div>
            </div>
            <div class="chart-card">
                <div class="chart-head">
                    <h4><span class="ic" style="background:var(--s1)"></span>pH</h4>
                    <div class="chart-legend">
                        <span class="legend-item"><span class="sw" style="background:var(--s1)"></span>pH Baku</span>
                        <span class="legend-item"><span class="sw" style="background:var(--s2)"></span>pH Reservoir</span>
                    </div>
                </div>
                <div class="chart-wrap"><div id="chartQuality" style="width:100%;height:140px"></div></div>
            </div>
        </div>
    </div>
</div>
@endif {{-- end SECTION 6 --}}

@if(false) {{-- SECTION 7 — LOG TABLE (HIDDEN) --}}
<div class="section" style="margin-bottom:0">
    <div class="section-head">
        <h2 class="section-title">Log Data Sensor</h2>
        <span class="section-meta">{{ $logs->count() }} baris · interval 30s</span>
    </div>
    <div class="log-card">
        <div class="log-head">
            <h3>Sensor Log</h3>
            <div class="log-tabs" id="log-tabs">
                <button class="log-tab active" data-tab="all"      onclick="scadaLogTab(this,'all')">Semua</button>
                <button class="log-tab"         data-tab="flow"     onclick="scadaLogTab(this,'flow')">Flow</button>
                <button class="log-tab"         data-tab="pressure" onclick="scadaLogTab(this,'pressure')">Pressure</button>
                <button class="log-tab"         data-tab="turb"     onclick="scadaLogTab(this,'turb')">Turbidity</button>
                <button class="log-tab"         data-tab="qual"     onclick="scadaLogTab(this,'qual')">Kualitas</button>
            </div>
            <div style="font-size:11.5px;color:var(--text-3);font-family:var(--f-mono)">{{ $logs->count() }} baris</div>
        </div>
        <div class="log-table-wrap">
            <table class="log-table" id="log-table">
                <thead>
                    <tr>
                        <th rowspan="2" class="t-stamp">Timestamp</th>
                        <th colspan="4" class="group-head log-group-flow">Flow (L/s)</th>
                        <th colspan="7" class="group-head log-group-pressure">Pressure (bar) &amp; Level</th>
                        <th colspan="4" class="group-head log-group-turb">Turbidity (NTU)</th>
                        <th colspan="3" class="group-head log-group-qual">Kualitas &amp; Lainnya</th>
                    </tr>
                    <tr>
                        <th class="log-group-flow">Intake</th>
                        <th class="log-group-flow">Yos</th>
                        <th class="log-group-flow">Veteran</th>
                        <th class="log-group-flow">Backwash</th>
                        <th class="log-group-pressure">Intake</th>
                        <th class="log-group-pressure">Res.A</th>
                        <th class="log-group-pressure">Res.B</th>
                        <th class="log-group-pressure">Distrib.</th>
                        <th class="log-group-pressure">Service</th>
                        <th class="log-group-pressure">Backwash</th>
                        <th class="log-group-pressure">Komp.</th>
                        <th class="log-group-turb">Baku</th>
                        <th class="log-group-turb">Reservoir</th>
                        <th class="log-group-turb">Sedimen</th>
                        <th class="log-group-turb">Filter</th>
                        <th class="log-group-qual">pH Baku</th>
                        <th class="log-group-qual">pH Res.</th>
                        <th class="log-group-qual">Cl₂</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs->take(100) as $log)
                    <tr>
                        <td class="t-stamp">{{ \Carbon\Carbon::parse($log->timestamp)->format('d/m H:i:s') }}</td>
                        <td class="log-group-flow">{{ $log->flow_intake !== null ? number_format($log->flow_intake,1) : '—' }}</td>
                        <td class="log-group-flow">{{ $log->flow_yos_sudarso !== null ? number_format($log->flow_yos_sudarso,1) : '—' }}</td>
                        <td class="log-group-flow">{{ $log->flow_veteran !== null ? number_format($log->flow_veteran,1) : '—' }}</td>
                        <td class="log-group-flow">{{ $log->flow_backwash !== null ? number_format($log->flow_backwash,1) : '—' }}</td>
                        <td class="log-group-pressure">{{ $log->pressure_intake !== null ? number_format($log->pressure_intake,2) : '—' }}</td>
                        <td class="log-group-pressure">{{ $log->level_reservoir_a !== null ? number_format($log->level_reservoir_a,2) : '—' }}</td>
                        <td class="log-group-pressure">{{ $log->level_reservoir_b !== null ? number_format($log->level_reservoir_b,2) : '—' }}</td>
                        <td class="log-group-pressure">{{ $log->pressure_distribusi !== null ? number_format($log->pressure_distribusi,2) : '—' }}</td>
                        <td class="log-group-pressure">{{ $log->pressure_service !== null ? number_format($log->pressure_service,2) : '—' }}</td>
                        <td class="log-group-pressure">{{ $log->pressure_backwash !== null ? number_format($log->pressure_backwash,2) : '—' }}</td>
                        <td class="log-group-pressure">{{ $log->pressure_kompressor !== null ? number_format($log->pressure_kompressor,2) : '—' }}</td>
                        @php $turbWarn = ($log->turbidity_baku ?? 0) > 25; $turbCaution = ($log->turbidity_baku ?? 0) > 5 && !$turbWarn; @endphp
                        <td class="log-group-turb {{ $turbWarn ? 'crit' : ($turbCaution ? 'warn' : '') }}">{{ $log->turbidity_baku !== null ? number_format($log->turbidity_baku,1) : '—' }}</td>
                        <td class="log-group-turb">{{ $log->turbidity_reservoir !== null ? number_format($log->turbidity_reservoir,2) : '—' }}</td>
                        <td class="log-group-turb">{{ $log->turbidity_sedimen !== null ? number_format($log->turbidity_sedimen,2) : '—' }}</td>
                        <td class="log-group-turb">{{ $log->turbidity_filter !== null ? number_format($log->turbidity_filter,2) : '—' }}</td>
                        <td class="log-group-qual">{{ $log->ph_baku !== null ? number_format($log->ph_baku,2) : '—' }}</td>
                        <td class="log-group-qual">{{ $log->ph_reservoir !== null ? number_format($log->ph_reservoir,2) : '—' }}</td>
                        @php $clWarn = ($log->free_chlorine ?? 1) < 0.5; @endphp
                        <td class="log-group-qual {{ $clWarn ? 'warn' : '' }}">{{ $log->free_chlorine !== null ? number_format($log->free_chlorine,2) : '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif {{-- end SECTION 7 --}}

@endif {{-- end @if($latest) --}}
</div>{{-- .scada-page --}}

{{-- ============================================================
     INIT SCRIPT (runs on each Livewire poll update)
     ============================================================ --}}
<script>
(function() {
    var _initialData    = @json($chartData);
    var _latestPressure = @json($pressure);
    var _last4          = @json($last4);
    var _sparkData      = @json($sparkData);
    window._scadaTs     = @json($latest ? \Carbon\Carbon::parse($latest->timestamp)->timestamp : null);

    function tryInit() {
        if (typeof echarts === 'undefined' || typeof window._scadaInit === 'undefined') {
            setTimeout(tryInit, 50);
            return;
        }
        window._scadaInit(_initialData, _latestPressure, _last4, _sparkData);
    }
    tryInit();
})();
</script>

@once
<script>
(function() {
'use strict';

/* ================================================================
   CONFIG
   ================================================================ */
var SERIES_COLORS = ['#52B0ED','#E4509A','#5FDA5A','#F4C133','#AF8CB1'];

var GAUGE_DEFS = [
    { id: 'gaugeIntake',     key: 'intake',     max: 5  },
    { id: 'gaugeDistribusi', key: 'distribusi', max: 8  },
    { id: 'gaugeService',    key: 'service',    max: 5  },
    { id: 'gaugeBackwash',   key: 'backwash',   max: 5  },
    { id: 'gaugeKompressor', key: 'kompressor', max: 8  },
];

var CARD_DEFS = [
    { id: 'flow-intake', field: 'flow_intake',         dec: 1 },
    { id: 'flow-yos',    field: 'flow_yos_sudarso',    dec: 1 },
    { id: 'flow-vet',    field: 'flow_veteran',         dec: 1 },
    { id: 'flow-bw',     field: 'flow_backwash',        dec: 1 },
    { id: 'turb-baku',   field: 'turbidity_baku',       dec: 1 },
    { id: 'turb-res',    field: 'turbidity_reservoir',  dec: 2 },
    { id: 'turb-sed',    field: 'turbidity_sedimen',    dec: 2 },
    { id: 'turb-fil',    field: 'turbidity_filter',     dec: 2 },
    { id: 'ph-baku',     field: 'ph_baku',              dec: 2 },
    { id: 'ph-res',      field: 'ph_reservoir',         dec: 2 },
    { id: 'free-cl',     field: 'free_chlorine',        dec: 2 },
];

var SPARK_COLORS = {
    'flow-intake': '#4e73df',
    'flow-yos':    '#1cc88a',
    'flow-vet':    '#36b9cc',
    'flow-bw':     '#AF8CB1',
    'turb-baku':   '#f6c23e',
    'turb-res':    '#36b9cc',
    'turb-sed':    '#E4509A',
    'turb-fil':    '#5FDA5A',
    'ph-baku':     '#4e73df',
    'ph-res':      '#E4509A',
    'free-cl':     '#1cc88a',
};

var FIELD_TO_SPARK_ID = {
    'flow_intake':         'flow-intake',
    'flow_yos_sudarso':    'flow-yos',
    'flow_veteran':        'flow-vet',
    'flow_backwash':       'flow-bw',
    'turbidity_baku':      'turb-baku',
    'turbidity_reservoir': 'turb-res',
    'turbidity_sedimen':   'turb-sed',
    'turbidity_filter':    'turb-fil',
    'ph_baku':             'ph-baku',
    'ph_reservoir':        'ph-res',
    'free_chlorine':       'free-cl',
};

/* ================================================================
   GAUGE COLOR
   ================================================================ */
function gaugeColor(value, max) {
    if (value === null || value === undefined || isNaN(value)) return '#c9ccd1';
    var pct = value / max * 100;
    if (pct > 90) return '#B0473B';
    if (pct < 30) return '#C28C2E';
    return '#3D7E92';
}

function gaugeStateLabel(value, max) {
    if (value === null || value === undefined || isNaN(value)) return 'NO DATA';
    var pct = value / max * 100;
    if (pct > 90) return 'KRITIS';
    if (pct < 30) return 'RENDAH';
    return 'NORMAL';
}

/* ================================================================
   ECHARTS GAUGES
   ================================================================ */
var _gauges = {};

function makeGaugeOption(value, max) {
    var val   = (value !== null && value !== undefined) ? parseFloat(value) : 0;
    var color = gaugeColor(val, max);
    return {
        series: [{
            type: 'gauge',
            startAngle: 200,
            endAngle: -20,
            min: 0,
            max: max,
            radius: '102%',
            center: ['50%', '78%'],
            progress: {
                show: true, width: 9, roundCap: true,
                itemStyle: { color: color }
            },
            axisLine: {
                lineStyle: { width: 9, color: [[1, 'rgba(15,23,42,.08)']] }
            },
            pointer: { show: false },
            axisTick: { show: false },
            splitLine: { show: false },
            axisLabel: { show: false },
            anchor: { show: false },
            title: { show: false },
            detail: {
                valueAnimation: true,
                offsetCenter: [0, '-18%'],
                formatter: function(v) {
                    return '{val|' + v.toFixed(2) + '}\n{unit|bar}';
                },
                rich: {
                    val:  { fontSize: 20, fontWeight: 600, color: color, fontFamily: 'IBM Plex Mono', lineHeight: 24, letterSpacing: -0.5 },
                    unit: { fontSize: 10, color: '#6b7280', fontFamily: 'IBM Plex Mono', letterSpacing: 1, padding: [4, 0, 0, 0] }
                }
            },
            data: [{ value: val }]
        }]
    };
}

function initGauges(p) {
    GAUGE_DEFS.forEach(function(def) {
        var dom = document.getElementById(def.id);
        if (!dom) return;
        var existing = echarts.getInstanceByDom(dom);
        if (existing) existing.dispose();
        var val = p ? (parseFloat(p[def.key]) || 0) : 0;
        _gauges[def.key] = echarts.init(dom, null, { renderer: 'svg' });
        _gauges[def.key].setOption(makeGaugeOption(val, def.max));
        var stateEl = document.getElementById(def.id + '-state');
        if (stateEl) {
            var color = gaugeColor(val, def.max);
            stateEl.textContent = gaugeStateLabel(val, def.max);
            stateEl.style.color = color;
            stateEl.style.background = color + '1a';
        }
    });
}

function updateGauges(p) {
    if (!p) return;
    GAUGE_DEFS.forEach(function(def) {
        if (!_gauges[def.key]) return;
        var val = parseFloat(p[def.key]);
        if (isNaN(val)) val = 0;
        var color = gaugeColor(val, def.max);
        _gauges[def.key].setOption({
            series: [{
                progress: { itemStyle: { color: color } },
                detail: {
                    formatter: function(v) { return '{val|' + v.toFixed(2) + '}\n{unit|bar}'; },
                    rich: {
                        val:  { fontSize: 20, fontWeight: 600, color: color, fontFamily: 'IBM Plex Mono', lineHeight: 24 },
                        unit: { fontSize: 10, color: '#6b7280', fontFamily: 'IBM Plex Mono' }
                    }
                },
                data: [{ value: val }]
            }]
        });
        var stateEl = document.getElementById(def.id + '-state');
        if (stateEl) {
            var lbl = gaugeStateLabel(val, def.max);
            stateEl.textContent = lbl;
            stateEl.style.color = color;
            stateEl.style.background = color + '1a';
        }
    });
}

/* ================================================================
   ECHARTS AREA CHARTS
   ================================================================ */
var _charts = {};

function makeAreaSeries(name, color, data) {
    var N = data.length;
    return {
        name: name,
        type: 'line',
        data: data,
        smooth: true,
        smoothMonotone: 'x',
        showSymbol: false,
        lineStyle: { color: color, width: 1.6 },
        itemStyle: { color: color },
        areaStyle: {
            color: {
                type: 'linear', x: 0, y: 0, x2: 0, y2: 1,
                colorStops: [
                    { offset: 0, color: color + '40' },
                    { offset: 1, color: color + '00' }
                ]
            }
        },
        markPoint: {
            symbol: 'circle', symbolSize: 7,
            itemStyle: { color: color, borderColor: '#fff', borderWidth: 2 },
            data: N > 0 ? [{ coord: [N - 1, data[N - 1]] }] : [],
            animation: false
        }
    };
}

function makeChartOption(seriesList, xData) {
    return {
        animation: true,
        animationDuration: 300,
        grid: { left: 40, right: 14, top: 10, bottom: 18 },
        tooltip: {
            trigger: 'axis',
            backgroundColor: 'rgba(255,255,255,.98)',
            borderColor: '#e3e6f0',
            borderWidth: 1,
            textStyle: { fontSize: 11, color: '#2b2d3a', fontFamily: 'IBM Plex Mono' },
            padding: [6, 10],
            formatter: function(params) {
                return params.map(function(p) {
                    return '<span style="color:' + p.color + '">●</span> ' + p.seriesName + ': <b>' + (+p.value).toFixed(2) + '</b>';
                }).join('<br/>');
            }
        },
        xAxis: {
            type: 'category',
            data: xData,
            boundaryGap: false,
            axisLine: { show: false },
            axisTick: { show: false },
            axisLabel: { show: false }
        },
        yAxis: {
            type: 'value',
            scale: true,
            splitNumber: 4,
            splitLine: { lineStyle: { color: '#eef0f5', type: 'dashed', width: 0.8 } },
            axisLine: { show: false },
            axisTick: { show: false },
            axisLabel: {
                color: '#858796',
                fontFamily: 'IBM Plex Mono',
                fontSize: 9.5,
                formatter: function(v) { return v.toFixed(1); },
                margin: 8
            }
        },
        series: seriesList
    };
}

function initCharts() {
    ['chartFlow','chartTurbidity','chartQuality'].forEach(function(id) {
        var dom = document.getElementById(id);
        if (!dom) return;
        var ex = echarts.getInstanceByDom(dom);
        if (ex) ex.dispose();
        _charts[id] = echarts.init(dom, null, { renderer: 'svg' });
    });
}

function updateCharts(d) {
    if (!d || !d.labels) return;
    var L = d.labels;
    var N = L.length;

    if (_charts.chartFlow) {
        _charts.chartFlow.setOption(makeChartOption([
            makeAreaSeries('Flow Intake',   '#52B0ED', d.flow.flow_intake),
            makeAreaSeries('Yos + Veteran', '#E4509A', d.flow.flow_total)
        ], L));
    }
    if (_charts.chartTurbidity) {
        _charts.chartTurbidity.setOption(makeChartOption([
            makeAreaSeries('Reservoir', '#52B0ED', d.turbidity.turbidity_reservoir),
            makeAreaSeries('Sedimen',   '#E4509A', d.turbidity.turbidity_sedimen),
            makeAreaSeries('Filter',    '#5FDA5A', d.turbidity.turbidity_filter)
        ], L));
    }
    if (_charts.chartQuality) {
        _charts.chartQuality.setOption(makeChartOption([
            makeAreaSeries('pH Baku',     '#52B0ED', d.quality.ph_baku),
            makeAreaSeries('pH Reservoir','#E4509A', d.quality.ph_reservoir)
        ], L));
    }
}

/* ================================================================
   SPARKLINES
   ================================================================ */
var _sparkBuf = {};

function renderSparkline(el, data, color) {
    if (!el || !data || data.length < 2) return;
    var w = 100, h = 100;
    var min = Math.min.apply(null, data);
    var max = Math.max.apply(null, data);
    var span = max - min || 1;
    var pts = data.map(function(v, i) {
        return [i / (data.length - 1) * w, h - (v - min) / span * (h * 0.9) - h * 0.05];
    });
    var line = pts.map(function(p, i) {
        return (i ? 'L' : 'M') + p[0].toFixed(1) + ',' + p[1].toFixed(1);
    }).join(' ');
    var area = line + ' L' + w + ',' + h + ' L0,' + h + ' Z';
    var gid  = 'sg' + color.replace(/[^a-zA-Z0-9]/g, '');
    el.innerHTML = '<svg viewBox="0 0 ' + w + ' ' + h + '" preserveAspectRatio="none" style="width:100%;height:30px;display:block">'
        + '<defs><linearGradient id="' + gid + '" x1="0" y1="0" x2="0" y2="1">'
        + '<stop offset="0%" stop-color="' + color + '" stop-opacity=".35"/>'
        + '<stop offset="100%" stop-color="' + color + '" stop-opacity="0"/>'
        + '</linearGradient></defs>'
        + '<path d="' + area + '" fill="url(#' + gid + ')"/>'
        + '<path d="' + line + '" fill="none" stroke="' + color + '" stroke-width="1.4" vector-effect="non-scaling-stroke"/>'
        + '</svg>';
}

function initSparklines(sparkData) {
    if (!sparkData) return;
    Object.keys(FIELD_TO_SPARK_ID).forEach(function(field) {
        var sid = FIELD_TO_SPARK_ID[field];
        var data = sparkData[field];
        if (!data || !data.length) return;
        _sparkBuf[sid] = data.slice();
        var el = document.getElementById('spark-' + sid);
        renderSparkline(el, _sparkBuf[sid], SPARK_COLORS[sid]);
    });
}

function updateSparkline(sid, newVal) {
    if (_sparkBuf[sid] === undefined) _sparkBuf[sid] = [];
    _sparkBuf[sid].push(newVal);
    if (_sparkBuf[sid].length > 30) _sparkBuf[sid].shift();
    var el = document.getElementById('spark-' + sid);
    renderSparkline(el, _sparkBuf[sid], SPARK_COLORS[sid]);
}

/* ================================================================
   FLOW BALANCE UPDATE
   ================================================================ */
function updateFlowBalance(intake, yos, vet) {
    var ds   = +(yos + vet).toFixed(1);
    var diff = +(intake - ds).toFixed(1);
    var pct  = intake > 0 ? Math.abs(diff / intake * 100) : 0;
    var color, label, tone;
    if (pct > 2.8) { color = '#e74a3b'; label = 'KRITIS'; tone = 'rgba(231,74,59,.12)'; }
    else if (pct > 1.5) { color = '#E7A52F'; label = 'WASPADA'; tone = 'rgba(231,165,47,.14)'; }
    else { color = '#1cc88a'; label = 'NORMAL'; tone = 'rgba(28,200,138,.12)'; }
    var dir = diff >= 0 ? 'kehilangan' : 'surplus';

    function _t(id) { return document.getElementById(id); }
    if (_t('fb-intake'))   _t('fb-intake').textContent   = intake.toFixed(1);
    if (_t('fb-yos'))      _t('fb-yos').textContent      = yos.toFixed(1);
    if (_t('fb-vet'))      _t('fb-vet').textContent      = vet.toFixed(1);
    if (_t('fb-ds'))       _t('fb-ds').textContent       = ds.toFixed(1);
    if (_t('fb-diff'))     _t('fb-diff').textContent     = (diff > 0 ? '−' : diff < 0 ? '+' : '') + Math.abs(diff).toFixed(1);
    if (_t('fb-pct'))  { _t('fb-pct').textContent = pct.toFixed(1) + '%'; _t('fb-pct').style.color = color; }
    if (_t('fb-dir'))      _t('fb-dir').textContent      = ' ' + dir;
    if (_t('fb-state-label')) _t('fb-state-label').textContent = label;
    var stateEl = _t('fb-state');
    if (stateEl) { stateEl.style.color = color; stateEl.style.background = tone; }
    var diffNode = _t('fb-diff-node');
    if (diffNode) { diffNode.style.setProperty('--diff-color', color); diffNode.style.setProperty('--diff-tone', tone); }
    var diffVal = _t('fb-diff-val');
    if (diffVal) diffVal.style.color = color;
    var card = _t('fb-card');
    if (card) card.style.setProperty('--diff-color', color);
    var fill = _t('fb-bar-fill');
    if (fill) {
        fill.style.width = (intake > 0 ? Math.min(100, ds / intake * 100).toFixed(1) : 0) + '%';
        fill.style.background = color;
    }
    var lbl = _t('fb-bar-label');
    if (lbl) lbl.textContent = ds.toFixed(1) + ' / ' + intake.toFixed(1);
}

/* ================================================================
   SCM UPDATE
   ================================================================ */
function updateScm(val) {
    var pct  = Math.max(0, Math.min(100, ((val + 20) / 40) * 100));
    var safe = (val >= -5 && val <= 2);
    var warn = (!safe && ((val > 2 && val <= 4) || (val < -5 && val >= -7)));
    var color, label, tone, txt;
    if (!safe && !warn) {
        color = '#e74a3b'; label = 'BAHAYA'; tone = 'rgba(231,74,59,.14)';
        txt = 'Nilai jauh di luar rentang aman. Hentikan proses & periksa sistem.';
    } else if (warn) {
        color = '#E7A52F'; label = 'WASPADA'; tone = 'rgba(231,165,47,.14)';
        txt = 'Mendekati / melewati ambang. Operator wajib mengecek.';
    } else {
        color = '#1cc88a'; label = 'AMAN'; tone = 'rgba(28,200,138,.12)';
        txt = 'Beroperasi dalam rentang operasional aman.';
    }

    var needle = document.getElementById('scm-needle');
    if (needle) { needle.style.left = pct.toFixed(2) + '%'; needle.style.background = color; }
    var nh = needle ? needle.querySelector('.scm-needle-head') : null;
    if (nh) nh.style.borderTopColor = color;

    var valEl = document.getElementById('scm-value');
    if (valEl) { valEl.textContent = (val >= 0 ? '+' : '') + val.toFixed(2); valEl.style.color = color; }
    var descEl = document.getElementById('scm-desc');
    if (descEl) descEl.textContent = txt;
    var lblEl = document.getElementById('scm-label');
    if (lblEl) lblEl.textContent = label;
    var stateEl = document.getElementById('scm-state');
    if (stateEl) { stateEl.style.background = tone; stateEl.style.color = color; }
    var card = document.getElementById('scm-card');
    if (card) card.style.setProperty('--scm-state-color', color);
}

/* ================================================================
   BUFFER & FAKE-LIVE — rolling 4 record terbaru, cycling tiap 2s
   ================================================================ */
var _buf4     = [];   /* array of complete record objects, newest first */
var _cycleIdx = 0;    /* index record yang sedang ditampilkan */

function showRecord(rec) {
    if (!rec) return;

    /* KPI cards */
    CARD_DEFS.forEach(function(c) {
        var val = parseFloat(rec[c.field]);
        if (isNaN(val)) return;
        var el = document.getElementById('kv-' + c.id);
        if (el && el.firstChild) {
            var nvStr = val.toFixed(c.dec);
            if (el.firstChild.textContent.trim() !== nvStr) {
                el.firstChild.textContent = nvStr;
                el.classList.remove('flash');
                void el.offsetWidth;
                el.classList.add('flash');
            }
        }
    });

    /* Pressure gauges — skip kalau field null di record ini */
    var pIntake = parseFloat(rec.pressure_intake);
    if (!isNaN(pIntake)) {
        updateGauges({
            intake:     pIntake,
            distribusi: parseFloat(rec.pressure_distribusi),
            service:    parseFloat(rec.pressure_service),
            backwash:   parseFloat(rec.pressure_backwash),
            kompressor: parseFloat(rec.pressure_kompressor),
        });
    }

    /* Flow Balance */
    var fi = parseFloat(rec.flow_intake);
    var fy = parseFloat(rec.flow_yos_sudarso);
    var fv = parseFloat(rec.flow_veteran);
    if (!isNaN(fi) && !isNaN(fy) && !isNaN(fv)) updateFlowBalance(fi, fy, fv);

    /* SCM */
    var scm = parseFloat(rec.scm);
    if (!isNaN(scm)) updateScm(scm);
}

function initBuffer(last4) {
    _buf4     = Array.isArray(last4) ? last4.slice() : [];
    _cycleIdx = 0;
    if (_buf4.length > 0) showRecord(_buf4[0]);
}

function _fakeLiveTick() {
    if (_buf4.length === 0) return;
    _cycleIdx = (_cycleIdx + 1) % _buf4.length;
    showRecord(_buf4[_cycleIdx]);
}

setInterval(_fakeLiveTick, 2000);

/* ================================================================
   LOG TABLE TAB FILTER
   ================================================================ */
window.scadaLogTab = function(btn, tab) {
    document.querySelectorAll('#log-tabs .log-tab').forEach(function(b) { b.classList.remove('active'); });
    btn.classList.add('active');

    var groups = { flow: true, pressure: true, turb: true, qual: true };
    if (tab !== 'all') {
        Object.keys(groups).forEach(function(k) { groups[k] = (k === tab); });
    }
    document.querySelectorAll('.log-table [class*="log-group-"]').forEach(function(cell) {
        var grp = null;
        ['flow','pressure','turb','qual'].forEach(function(g) {
            if (cell.classList.contains('log-group-' + g)) grp = g;
        });
        if (grp) cell.style.display = groups[grp] ? '' : 'none';
    });
};

/* ================================================================
   MAIN INIT — called on each Livewire render
   ================================================================ */
window._scadaInit = function(d, p, last4, sparkData) {
    initGauges(p);
    initCharts();
    updateCharts(d);
    initSparklines(sparkData);
    initBuffer(last4);
};

window.addEventListener('resize', function() {
    Object.values(_gauges).forEach(function(g) { if (g) g.resize(); });
    Object.values(_charts).forEach(function(c) { if (c) c.resize(); });
});

/* ================================================================
   APPEND CHART POINT — tambah titik baru, geser titik terlama
   ================================================================ */
function appendChartPoint(label, d) {
    var charts = [
        {
            inst: _charts.chartFlow,
            series: [
                parseFloat(d.flow_intake),
                +(( parseFloat(d.flow_yos_sudarso)||0) + (parseFloat(d.flow_veteran)||0)).toFixed(2),
            ]
        },
        {
            inst: _charts.chartTurbidity,
            series: [
                parseFloat(d.turbidity_reservoir),
                parseFloat(d.turbidity_sedimen),
                parseFloat(d.turbidity_filter),
            ]
        },
        {
            inst: _charts.chartQuality,
            series: [
                parseFloat(d.ph_baku),
                parseFloat(d.ph_reservoir),
            ]
        },
    ];

    charts.forEach(function(c) {
        if (!c.inst) return;
        try {
            var opt    = c.inst.getOption();
            var xData  = (opt.xAxis[0].data || []).slice();
            xData.push(label);
            if (xData.length > 100) xData.shift();

            var series = opt.series.map(function(s, i) {
                var val = c.series[i];
                if (isNaN(val)) return { data: s.data, markPoint: s.markPoint };
                var data = (s.data || []).slice();
                data.push(val);
                if (data.length > 100) data.shift();
                var N = data.length;
                return {
                    data: data,
                    markPoint: {
                        data: [{ coord: [N - 1, data[N - 1]] }]
                    }
                };
            });

            c.inst.setOption({ xAxis: { data: xData }, series: series });
        } catch(e) {}
    });
}

/* ================================================================
   ECHO — terima data real-time dari Reverb WebSocket
   ================================================================ */
function initEcho() {
    if (typeof window.Echo === 'undefined') {
        setTimeout(initEcho, 100);
        return;
    }

    window.Echo.channel('sensor')
        .listen('.sensor.update', function(payload) {
            var d = payload.data || payload;

            /* Timestamp untuk elapsed timer + header update */
            if (d.timestamp) {
                var tsDate = new Date(d.timestamp);
                var tsUnix = tsDate.getTime() / 1000;
                if (!isNaN(tsUnix)) {
                    window._scadaTs = tsUnix;
                    var headerTs = document.getElementById('header-ts');
                    if (headerTs) {
                        var pad = function(n) { return n < 10 ? '0' + n : '' + n; };
                        var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
                        headerTs.textContent = pad(tsDate.getDate()) + ' ' + months[tsDate.getMonth()] + ' ' + tsDate.getFullYear() + ', ' + pad(tsDate.getHours()) + ':' + pad(tsDate.getMinutes());
                    }
                    var badge = document.getElementById('status-badge');
                    var statusTxt = document.getElementById('status-text');
                    if (badge) { badge.className = badge.className.replace(/\b(live|stale|dead)\b/g, ''); badge.className = (badge.className.trim() + ' live').trim(); }
                    if (statusTxt) statusTxt.textContent = 'Live';
                }
            }

            /* Sparklines — update hanya pada data real baru */
            Object.keys(FIELD_TO_SPARK_ID).forEach(function(field) {
                var val = parseFloat(d[field]);
                if (!isNaN(val)) updateSparkline(FIELD_TO_SPARK_ID[field], val);
            });

            /* Prepend ke buffer, buang yang paling lama, tampil langsung */
            _buf4.unshift(d);
            if (_buf4.length > 4) _buf4.pop();
            _cycleIdx = 0;
            showRecord(d);

            /* Chart — append titik baru, geser titik terlama */
            var now = d.timestamp
                ? new Date(d.timestamp).toLocaleString('id-ID', { day:'2-digit', month:'2-digit', hour:'2-digit', minute:'2-digit' }).replace(',','')
                : '';
            appendChartPoint(now, d);

            /* Debug raw block */
            var dbg = document.getElementById('debug-raw');
            if (dbg) dbg.textContent = JSON.stringify(d, null, 2);
        });
}
initEcho();

/* Real-time elapsed ticker — update every second */
setInterval(function() {
    var ts = window._scadaTs;
    if (!ts) return;
    var diff = Math.max(0, Math.round(Date.now() / 1000 - ts));
    var txt;
    if (diff < 60) {
        txt = diff + 's lalu';
    } else if (diff < 3600) {
        var m = Math.floor(diff / 60), s = diff % 60;
        txt = m + 'm' + (s > 0 ? ' ' + s + 's' : '') + ' lalu';
    } else {
        var h = Math.floor(diff / 3600), rem = diff % 3600, hm = Math.floor(rem / 60);
        txt = h + 'j' + (hm > 0 ? ' ' + hm + 'm' : '') + ' lalu';
    }
    document.querySelectorAll('.kpi-meta-time').forEach(function(el) { el.textContent = txt; });
}, 1000);

/* Start fake-live loop */

})();
</script>
@endonce

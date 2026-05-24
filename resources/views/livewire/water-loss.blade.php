<div class="py-4">

    {{-- Header --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 font-weight-bold text-gray-800">
            Water Loss Monitoring
            @if ($period === 'daily') · Harian
            @elseif ($period === 'weekly') · Mingguan
            @else · Bulanan
            @endif
        </h1>
    </div>

    {{-- Tab Switcher --}}
    <div class="mb-3">
        <div class="btn-group" role="group" aria-label="Period selector">
            @php
                $btnActive  = 'background:#00664A; border-color:#00664A; color:#fff;';
                $btnInactive= 'background:transparent; border-color:#00664A; color:#00664A;';
            @endphp
            <button wire:click="setPeriod('daily')"
                    class="btn btn-sm"
                    style="{{ $period === 'daily' ? $btnActive : $btnInactive }}">
                <i class="fas fa-calendar-day mr-1"></i>Harian
            </button>
            <button wire:click="setPeriod('weekly')"
                    class="btn btn-sm"
                    style="{{ $period === 'weekly' ? $btnActive : $btnInactive }}">
                <i class="fas fa-calendar-week mr-1"></i>Mingguan
            </button>
            <button wire:click="setPeriod('monthly')"
                    class="btn btn-sm"
                    style="{{ $period === 'monthly' ? $btnActive : $btnInactive }}">
                <i class="fas fa-calendar-alt mr-1"></i>Bulanan
            </button>
        </div>
    </div>

    {{-- Period Navigator --}}
    <div class="card shadow mb-4">
        <div class="card-body py-3">
            <div class="d-flex align-items-center gap-2 flex-wrap">

                @if ($period === 'daily')
                    <button wire:click="prevPeriod" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <input wire:model.live="date" type="date" class="form-control form-control-sm" style="width:160px">
                    <button wire:click="nextPeriod" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                    <span class="font-weight-bold text-gray-700 ml-2">
                        {{ \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }}
                    </span>

                @elseif ($period === 'weekly')
                    @php
                        $wEnd = \Carbon\Carbon::parse($weekStart)->addDays(6);
                    @endphp
                    <button wire:click="prevPeriod" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <span class="font-weight-bold text-gray-700 mx-2">
                        <i class="fas fa-calendar-week mr-1" style="color:#00664A"></i>
                        {{ \Carbon\Carbon::parse($weekStart)->translatedFormat('d F') }}
                        &ndash;
                        {{ $wEnd->translatedFormat('d F Y') }}
                    </span>
                    <button wire:click="nextPeriod" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-chevron-right"></i>
                    </button>

                @elseif ($period === 'monthly')
                    <button wire:click="prevPeriod" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <span class="font-weight-bold text-gray-700 mx-2">
                        <i class="fas fa-calendar-alt mr-1" style="color:#00664A"></i>
                        {{ \Carbon\Carbon::parse($monthYear . '-01')->translatedFormat('F Y') }}
                    </span>
                    <button wire:click="nextPeriod" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                @endif

            </div>
        </div>
    </div>

    {{-- =====================================================================
         MODE HARIAN: tabel per-shift (implementasi asli, tidak diubah)
         ===================================================================== --}}
    @if ($period === 'daily')

        {{-- Table Card --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex align-items-center" style="background-color:#00664A;">
                <h6 class="m-0 font-weight-bold text-white">
                    <i class="fas fa-table mr-2"></i>
                    Monitoring Flow & Water Loss — {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}
                </h6>
            </div>
            <div class="card-body p-0">
                @if (empty($rows))
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-inbox fa-3x mb-3"></i>
                        <p>Tidak ada data untuk tanggal ini.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mb-0 text-center" style="font-size:0.85rem;">
                            <thead>
                                <tr style="background-color:#00664A; color:#fff;">
                                    <th rowspan="2" class="align-middle" style="min-width:55px">Shift</th>
                                    <th rowspan="2" class="align-middle" style="min-width:60px">Jam</th>
                                    <th colspan="3" class="align-middle">Water Meter Air Baku</th>
                                    <th colspan="2" class="align-middle">Yos Sudarso</th>
                                    <th colspan="2" class="align-middle">Veteran</th>
                                    <th colspan="3" class="align-middle">Total Distribusi</th>
                                    <th rowspan="2" class="align-middle" style="min-width:80px">Water Loss (%)</th>
                                    <th colspan="2" class="align-middle">Level Reservoir</th>
                                </tr>
                                <tr style="background-color:#00664A; color:#fff;">
                                    <th>Flow (lps)</th>
                                    <th>Totalizer (m³)</th>
                                    <th>Selisih (m³)</th>
                                    <th>Flow (lps)</th>
                                    <th>Totalizer (m³)</th>
                                    <th>Flow (lps)</th>
                                    <th>Totalizer (m³)</th>
                                    <th>Flow (lps)</th>
                                    <th>Totalizer (m³)</th>
                                    <th>Selisih (m³)</th>
                                    <th>Resv A (cm)</th>
                                    <th>Resv B (cm)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $shiftLabels = ['shift i' => 'Shift 1', 'shift ii' => 'Shift 2', 'shift iii' => 'Shift 3'];
                                    $shiftGroups = [];
                                    foreach ($rows as $i => $r) {
                                        if (empty($shiftGroups) || end($shiftGroups)['shift'] !== $r['shift']) {
                                            $shiftGroups[] = ['shift' => $r['shift'], 'start' => $i, 'count' => 1];
                                        } else {
                                            $shiftGroups[count($shiftGroups) - 1]['count']++;
                                        }
                                    }
                                    $rowGroupMap = [];
                                    foreach ($shiftGroups as $g) {
                                        for ($i = $g['start']; $i < $g['start'] + $g['count']; $i++) {
                                            $rowGroupMap[$i] = $g;
                                        }
                                    }
                                @endphp

                                @foreach ($rows as $idx => $row)
                                    @php
                                        $group        = $rowGroupMap[$idx];
                                        $isGroupStart = $group['start'] === $idx;
                                        $divider      = ($isGroupStart && $idx > 0) ? 'border-top:3px solid #00664A !important;' : '';
                                        $wl = $row['water_loss_pct'];
                                        if ($wl === null)  { $badge = 'secondary'; }
                                        elseif ($wl <= 3)  { $badge = 'success'; }
                                        else               { $badge = 'danger'; }
                                    @endphp
                                    <tr style="{{ $divider }}">
                                        @if ($isGroupStart)
                                            <td rowspan="{{ $group['count'] }}"
                                                class="align-middle font-weight-bold"
                                                style="background:#f0f7f4; color:#00664A; vertical-align:middle;">
                                                {{ $shiftLabels[$group['shift']] ?? $group['shift'] }}
                                            </td>
                                        @endif
                                        <td class="font-weight-bold">{{ $row['time'] }}</td>
                                        <td>{{ number_format($row['air_baku_flow'], 0) }}</td>
                                        <td>{{ $row['air_baku_totalizer'] !== null ? number_format($row['air_baku_totalizer'], 0) : '—' }}</td>
                                        <td>{{ number_format($row['air_baku_selisih'], 0) }}</td>
                                        <td>{{ number_format($row['yos_flow'], 0) }}</td>
                                        <td>{{ $row['yos_totalizer'] !== null ? number_format($row['yos_totalizer'], 0) : '—' }}</td>
                                        <td>{{ number_format($row['vet_flow'], 0) }}</td>
                                        <td>{{ $row['vet_totalizer'] !== null ? number_format($row['vet_totalizer'], 0) : '—' }}</td>
                                        <td>{{ number_format($row['total_flow'], 0) }}</td>
                                        <td>{{ $row['total_totalizer'] !== null ? number_format($row['total_totalizer'], 0) : '—' }}</td>
                                        <td>{{ number_format($row['total_selisih'], 0) }}</td>
                                        <td>
                                            @if ($wl !== null)
                                                <span class="badge badge-{{ $badge }} px-2 py-1" style="font-size:0.82rem;">
                                                    {{ number_format($wl, 2) }}%
                                                </span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>{{ $row['level_a'] !== null ? number_format($row['level_a'], 2) : '—' }}</td>
                                        <td>{{ $row['level_b'] !== null ? number_format($row['level_b'], 2) : '—' }}</td>
                                    </tr>
                                @endforeach

                                {{-- Grand Total --}}
                                @if (!empty($grandTotal))
                                    @php
                                        $gtWl = $grandTotal['water_loss_pct'];
                                        if ($gtWl === null)    $gtBadge = 'secondary';
                                        elseif ($gtWl <= 3)   $gtBadge = 'success';
                                        else                  $gtBadge = 'danger';
                                        $lastRow = end($rows);
                                    @endphp
                                    <tr style="background-color:#e8f5e9; font-weight:bold; border-top:2px solid #00664A;">
                                        <td colspan="2">Grand Total</td>
                                        <td>{{ number_format($grandTotal['avg_air_baku_flow'], 1) }}</td>
                                        <td class="text-muted" style="font-size:0.8rem;">{{ $lastRow['air_baku_totalizer'] !== null ? number_format($lastRow['air_baku_totalizer'], 0) : '—' }}</td>
                                        <td style="color:#00664A; font-weight:600;">{{ number_format($grandTotal['total_air_baku_selisih'], 0) }}</td>
                                        <td>{{ number_format($grandTotal['avg_yos_flow'], 1) }}</td>
                                        <td class="text-muted" style="font-size:0.8rem;">{{ $lastRow['yos_totalizer'] !== null ? number_format($lastRow['yos_totalizer'], 0) : '—' }}</td>
                                        <td>{{ number_format($grandTotal['avg_vet_flow'], 1) }}</td>
                                        <td class="text-muted" style="font-size:0.8rem;">{{ $lastRow['vet_totalizer'] !== null ? number_format($lastRow['vet_totalizer'], 0) : '—' }}</td>
                                        <td>{{ number_format($grandTotal['avg_total_flow'], 1) }}</td>
                                        <td class="text-muted" style="font-size:0.8rem;">{{ $lastRow['total_totalizer'] !== null ? number_format($lastRow['total_totalizer'], 0) : '—' }}</td>
                                        <td style="color:#00664A; font-weight:600;">{{ number_format($grandTotal['total_dist_selisih'], 0) }}</td>
                                        <td>
                                            @if ($gtWl !== null)
                                                <span class="badge badge-{{ $gtBadge }} px-2 py-1" style="font-size:0.85rem;">
                                                    {{ number_format($gtWl, 2) }}%
                                                </span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>{{ $grandTotal['avg_level_a'] !== null ? number_format($grandTotal['avg_level_a'], 2) : '—' }}</td>
                                        <td>{{ $grandTotal['avg_level_b'] !== null ? number_format($grandTotal['avg_level_b'], 2) : '—' }}</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    {{-- Summary Cards Harian --}}
                    @if (!empty($grandTotal))
                        <div class="px-4 py-3 border-top">
                            <div class="row text-center">
                                <div class="col-6 col-md-3 mb-3">
                                    <div class="text-xs font-weight-bold text-uppercase text-muted mb-1">Produksi Air Baku</div>
                                    <div class="h5 font-weight-bold text-gray-800">
                                        {{ number_format($grandTotal['total_air_baku_selisih']) }} <small class="text-muted">m³</small>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3 mb-3">
                                    <div class="text-xs font-weight-bold text-uppercase text-muted mb-1">Distribusi</div>
                                    <div class="h5 font-weight-bold text-gray-800">
                                        {{ number_format($grandTotal['total_dist_selisih']) }} <small class="text-muted">m³</small>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3 mb-3">
                                    <div class="text-xs font-weight-bold text-uppercase text-muted mb-1">Volume Losses</div>
                                    <div class="h5 font-weight-bold text-danger">
                                        {{ number_format($grandTotal['total_air_baku_selisih'] - $grandTotal['total_dist_selisih']) }} <small class="text-muted">m³</small>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3 mb-3">
                                    <div class="text-xs font-weight-bold text-uppercase text-muted mb-1">Water Loss</div>
                                    @php $gtWl2 = $grandTotal['water_loss_pct']; @endphp
                                    <div class="h5 font-weight-bold text-{{ ($gtWl2 !== null && $gtWl2 > 3) ? 'danger' : 'success' }}">
                                        {{ $gtWl2 !== null ? number_format($gtWl2, 2) . '%' : '—' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endif
            </div>
        </div>

        {{-- Per-Shift Water Loss --}}
        @if (!empty($shiftSummary))
            <div class="card shadow mb-4">
                <div class="card-header py-3" style="background-color:#00664A;">
                    <h6 class="m-0 font-weight-bold text-white">
                        <i class="fas fa-layer-group mr-2"></i>Water Loss per Shift
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="row no-gutters">
                        @foreach ($shiftSummary as $i => $s)
                            @php
                                $wl = $s['water_loss_pct'];
                                if ($wl <= 3)  { $bg = '#d4edda'; $color = '#155724'; }
                                else           { $bg = '#f8d7da'; $color = '#721c24'; }
                                $border = $i < count($shiftSummary) - 1 ? 'border-right:1px solid #dee2e6;' : '';
                            @endphp
                            <div class="col-12 col-md-4 text-center py-4" style="background:{{ $bg }}; {{ $border }}">
                                <div class="text-xs font-weight-bold text-uppercase mb-1"
                                     style="color:{{ $color }}; letter-spacing:.05em;">
                                    Water Loss {{ $s['label'] }}
                                </div>
                                <div style="font-size:2rem; font-weight:700; color:{{ $color }}; line-height:1.1;">
                                    {{ number_format($wl, 2) }}%
                                </div>
                                @if ($s['count'] > 0)
                                    <div class="mt-1" style="font-size:0.72rem; color:{{ $color }}; opacity:.75;">
                                        {{ $s['count'] }} pembacaan
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

    @else
    {{-- =====================================================================
         MODE PERIODIK: tabel ringkasan per hari (Mingguan / Bulanan)
         ===================================================================== --}}

        {{-- Tabel Periodik --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex align-items-center justify-content-between" style="background-color:#00664A;">
                <h6 class="m-0 font-weight-bold text-white">
                    <i class="fas fa-table mr-2"></i>
                    @if ($period === 'weekly')
                        Ringkasan Mingguan —
                        {{ \Carbon\Carbon::parse($weekStart)->translatedFormat('d F') }}
                        &ndash;
                        {{ \Carbon\Carbon::parse($weekStart)->addDays(6)->translatedFormat('d F Y') }}
                    @else
                        Ringkasan Bulanan —
                        {{ \Carbon\Carbon::parse($monthYear . '-01')->translatedFormat('F Y') }}
                    @endif
                </h6>
                @if (!empty($periodicSummary))
                    <span class="badge badge-light" style="font-size:0.8rem;">
                        {{ $periodicSummary['days_with_data'] }} / {{ $periodicSummary['total_days'] }} hari ada data
                    </span>
                @endif
            </div>
            <div class="card-body p-0">
                @if (empty($periodicRows))
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-inbox fa-3x mb-3"></i>
                        <p>Tidak ada data untuk periode ini.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mb-0 text-center" style="font-size:0.82rem;">
                            <thead>
                                <tr style="background-color:#00664A; color:#fff;">
                                    <th class="text-left" rowspan="2" style="min-width:120px; vertical-align:middle;">Tanggal</th>
                                    <th colspan="2">Air Baku</th>
                                    <th colspan="2">Yos Sudarso</th>
                                    <th colspan="2">Veteran</th>
                                    <th colspan="2">Total Distribusi</th>
                                    <th rowspan="2" style="min-width:100px; vertical-align:middle;">Water Loss (%)</th>
                                </tr>
                                <tr style="background-color:#005a3f; color:#fff;">
                                    <th>Flow (lps)</th><th>Selisih (m³)</th>
                                    <th>Flow (lps)</th><th>Selisih (m³)</th>
                                    <th>Flow (lps)</th><th>Selisih (m³)</th>
                                    <th>Flow (lps)</th><th>Selisih (m³)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($periodicRows as $row)
                                    @php
                                        $wl = $row['water_loss_pct'];
                                        if (! $row['data_available']) {
                                            $rowStyle = 'color:#aaa; background:#fafafa;';
                                            $badge    = null;
                                        } elseif ($wl === null) {
                                            $rowStyle = '';
                                            $badge    = null;
                                        } elseif ($wl <= 3) {
                                            $rowStyle = '';
                                            $badge    = 'success';
                                        } elseif ($wl <= 5) {
                                            $rowStyle = '';
                                            $badge    = 'warning';
                                        } else {
                                            $rowStyle = '';
                                            $badge    = 'danger';
                                        }
                                        $fAb  = $row['flow_ab'];
                                        $fYos = $row['flow_yos'];
                                        $fVet = $row['flow_vet'];
                                        $fDst = $row['flow_dist'];
                                    @endphp
                                    <tr style="{{ $rowStyle }}">
                                        <td class="text-left font-weight-bold">{{ $row['date_label'] }}</td>
                                        <td>{{ $fAb  !== null ? number_format($fAb,  1) : '—' }}</td>
                                        <td>{{ $row['data_available'] ? number_format($row['air_baku_m3']) : '—' }}</td>
                                        <td>{{ $fYos !== null ? number_format($fYos, 1) : '—' }}</td>
                                        <td>{{ $row['data_available'] ? number_format($row['yos_m3']) : '—' }}</td>
                                        <td>{{ $fVet !== null ? number_format($fVet, 1) : '—' }}</td>
                                        <td>{{ $row['data_available'] ? number_format($row['vet_m3']) : '—' }}</td>
                                        <td>{{ $fDst !== null ? number_format($fDst, 1) : '—' }}</td>
                                        <td>{{ $row['data_available'] ? number_format($row['dist_m3']) : '—' }}</td>
                                        <td>
                                            @if ($badge !== null)
                                                <span class="badge badge-{{ $badge }} px-2 py-1" style="font-size:0.8rem;">
                                                    {{ number_format($wl, 2) }}%
                                                </span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach

                                {{-- Summary Row (Excel-style) --}}
                                @if (!empty($periodicSummary))
                                    @php
                                        $totalWl = $periodicSummary['overall_wl'];
                                        if ($totalWl === null)    $totalBadge = 'secondary';
                                        elseif ($totalWl <= 3)   $totalBadge = 'success';
                                        elseif ($totalWl <= 5)   $totalBadge = 'warning';
                                        else                     $totalBadge = 'danger';
                                    @endphp
                                    <tr style="background-color:#e8f5e9; font-weight:bold; border-top:2px solid #00664A; font-size:0.83rem;">
                                        <td class="text-left" style="color:#00664A;">Rata-rata / Total</td>
                                        <td>{{ $periodicSummary['avg_flow_ab']   ?? '—' }}</td>
                                        <td>{{ number_format($periodicSummary['total_air_baku']) }}</td>
                                        <td>{{ $periodicSummary['avg_flow_yos']  ?? '—' }}</td>
                                        <td>{{ number_format($periodicSummary['total_yos']) }}</td>
                                        <td>{{ $periodicSummary['avg_flow_vet']  ?? '—' }}</td>
                                        <td>{{ number_format($periodicSummary['total_vet']) }}</td>
                                        <td>{{ $periodicSummary['avg_flow_dist'] ?? '—' }}</td>
                                        <td>{{ number_format($periodicSummary['total_dist']) }}</td>
                                        <td>
                                            @if ($totalWl !== null)
                                                <span class="badge badge-{{ $totalBadge }} px-2 py-1" style="font-size:0.85rem;">
                                                    {{ number_format($totalWl, 2) }}%
                                                </span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    {{-- Summary Cards Periodik --}}
                    @if (!empty($periodicSummary))
                        <div class="px-4 py-3 border-top">
                            <div class="row text-center">
                                <div class="col-6 col-md-3 mb-3">
                                    <div class="text-xs font-weight-bold text-uppercase text-muted mb-1">Total Produksi</div>
                                    <div class="h5 font-weight-bold text-gray-800">
                                        {{ number_format($periodicSummary['total_air_baku']) }}
                                        <small class="text-muted">m³</small>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3 mb-3">
                                    <div class="text-xs font-weight-bold text-uppercase text-muted mb-1">Total Distribusi</div>
                                    <div class="h5 font-weight-bold text-gray-800">
                                        {{ number_format($periodicSummary['total_dist']) }}
                                        <small class="text-muted">m³</small>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3 mb-3">
                                    <div class="text-xs font-weight-bold text-uppercase text-muted mb-1">Total Kehilangan</div>
                                    <div class="h5 font-weight-bold text-danger">
                                        {{ number_format($periodicSummary['total_loss']) }}
                                        <small class="text-muted">m³</small>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3 mb-3">
                                    <div class="text-xs font-weight-bold text-uppercase text-muted mb-1">Water Loss</div>
                                    @php $owlPct = $periodicSummary['overall_wl']; @endphp
                                    <div class="h5 font-weight-bold text-{{ ($owlPct !== null && $owlPct > 3) ? 'danger' : 'success' }}">
                                        {{ $owlPct !== null ? number_format($owlPct, 2) . '%' : '—' }}
                                    </div>
                                    <div class="text-xs text-muted">
                                        dari {{ $periodicSummary['days_with_data'] }} hari data
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endif
            </div>
        </div>

    @endif {{-- end period check --}}

    {{-- Legend --}}
    <div class="text-right mb-4" style="font-size:0.78rem;">
        <span class="badge badge-success mr-1">≤ 3%</span> Normal &nbsp;
        @if ($period !== 'daily')
            <span class="badge badge-warning mr-1">3 – 5%</span> Waspada &nbsp;
        @endif
        <span class="badge badge-danger mr-1">{{ $period === 'daily' ? '> 3%' : '> 5%' }}</span> Tinggi
    </div>

    {{-- Chart --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold" style="color:#00664A;">
                <i class="fas fa-chart-line mr-2"></i>
                @if ($period === 'daily')
                    Water Loss (%) &amp; Level Reservoir A &amp; B (cm)
                @else
                    Tren Water Loss per Hari (%)
                @endif
            </h6>
        </div>
        <div class="card-body">
            <div class="chart-area">
                <canvas id="wlCombinedChart"></canvas>
            </div>
        </div>
    </div>

</div>

@script
<script>
    $wire.on('waterloss-chart-ready', function(data) {
        var d = data.chartData;
        if (!d || !d.labels || d.labels.length === 0) return;

        var base = {
            lineTension: 0.3,
            pointRadius: 4,
            pointHoverRadius: 6,
            pointBorderWidth: 2,
            pointHitRadius: 10,
            fill: false,
        };

        var cv = document.getElementById('wlCombinedChart');
        var existing = Chart.getChart(cv);
        if (existing) existing.destroy();

        var datasets, scales;

        if (d.mode === 'daily') {
            // Mode harian: water loss + level reservoir A & B
            datasets = [
                Object.assign({}, base, {
                    label: 'Water Loss',
                    borderColor: 'rgba(231,74,59,1)',
                    pointBackgroundColor: 'rgba(231,74,59,1)',
                    data: d.water_loss,
                    yAxisID: 'yWl',
                }),
                Object.assign({}, base, {
                    label: 'Level Resv A',
                    borderColor: 'rgba(78,115,223,1)',
                    pointBackgroundColor: 'rgba(78,115,223,1)',
                    data: d.level_a,
                    yAxisID: 'yResv',
                }),
                Object.assign({}, base, {
                    label: 'Level Resv B',
                    borderColor: 'rgba(28,200,138,1)',
                    pointBackgroundColor: 'rgba(28,200,138,1)',
                    data: d.level_b,
                    yAxisID: 'yResv',
                }),
            ];
            scales = {
                yWl: {
                    type: 'linear',
                    position: 'left',
                    title: { display: true, text: 'Water Loss (%)' },
                    ticks: { callback: function(v) { return v + '%'; } },
                },
                yResv: {
                    type: 'linear',
                    position: 'right',
                    title: { display: true, text: 'Level (cm)' },
                    grid: { drawOnChartArea: false },
                    ticks: { callback: function(v) { return parseFloat(v).toFixed(2) + ' cm'; } },
                },
            };
        } else {
            // Mode periodik: water loss + garis referensi 3%
            var refLine = d.labels.map(function() { return 3; });
            datasets = [
                Object.assign({}, base, {
                    label: 'Water Loss (%)',
                    borderColor: 'rgba(231,74,59,1)',
                    backgroundColor: 'rgba(231,74,59,0.08)',
                    pointBackgroundColor: 'rgba(231,74,59,1)',
                    data: d.water_loss,
                    fill: true,
                    yAxisID: 'yWl',
                    spanGaps: false,
                }),
                {
                    label: 'Batas Normal (3%)',
                    borderColor: 'rgba(40,167,69,0.75)',
                    borderDash: [6, 3],
                    borderWidth: 1.5,
                    pointRadius: 0,
                    data: refLine,
                    fill: false,
                    yAxisID: 'yWl',
                },
            ];
            scales = {
                yWl: {
                    type: 'linear',
                    position: 'left',
                    min: 0,
                    title: { display: true, text: 'Water Loss (%)' },
                    ticks: { callback: function(v) { return v + '%'; } },
                },
            };
        }

        new Chart(cv, {
            type: 'line',
            data: { labels: d.labels, datasets: datasets },
            options: {
                maintainAspectRatio: false,
                scales: scales,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(ctx) {
                                if (ctx.dataset.label === 'Batas Normal (3%)') {
                                    return ctx.dataset.label;
                                }
                                if (ctx.parsed.y === null) return ctx.dataset.label + ': —';
                                var unit = (ctx.dataset.yAxisID === 'yResv') ? ' cm' : '%';
                                return ctx.dataset.label + ': ' + ctx.parsed.y.toFixed(2) + unit;
                            },
                        },
                    },
                },
            },
        });
    });
</script>
@endscript

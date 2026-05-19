<div class="py-4">

    {{-- Header --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 font-weight-bold text-gray-800">
            Daily Water Loss Monitoring
        </h1>
    </div>

    {{-- Date Picker Card --}}
    <div class="card shadow mb-4">
        <div class="card-body py-3">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <button wire:click="prevDay" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <input wire:model.live="date" type="date" class="form-control form-control-sm" style="width:160px">
                <button wire:click="nextDay" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-chevron-right"></i>
                </button>
                <span class="font-weight-bold text-gray-700 ml-2">
                    {{ \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }}
                </span>
            </div>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex align-items-center" style="background-color:#00664A;">
            <h6 class="m-0 font-weight-bold text-white">
                <i class="fas fa-table mr-2"></i>
                Monitoring Flow & Water Loss — {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}
            </h6>
        </div>
        <div class="card-body p-0">
            @if(empty($rows))
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-inbox fa-3x mb-3"></i>
                    <p>Tidak ada data untuk tanggal ini.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-hover mb-0 text-center" style="font-size:0.85rem;">
                        <thead>
                            {{-- Group header --}}
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
                                // Build groups: [['shift'=>..., 'start'=>idx, 'count'=>n], ...]
                                $shiftGroups = [];
                                foreach ($rows as $i => $r) {
                                    if (empty($shiftGroups) || end($shiftGroups)['shift'] !== $r['shift']) {
                                        $shiftGroups[] = ['shift' => $r['shift'], 'start' => $i, 'count' => 1];
                                    } else {
                                        $shiftGroups[count($shiftGroups) - 1]['count']++;
                                    }
                                }
                                $groupStartIdx = array_column($shiftGroups, 'start', 'start');
                                $rowGroupMap   = [];
                                foreach ($shiftGroups as $g) {
                                    for ($i = $g['start']; $i < $g['start'] + $g['count']; $i++) {
                                        $rowGroupMap[$i] = $g;
                                    }
                                }
                            @endphp

                            @foreach($rows as $idx => $row)
                                @php
                                    $group       = $rowGroupMap[$idx];
                                    $isGroupStart = $group['start'] === $idx;
                                    $divider     = ($isGroupStart && $idx > 0) ? 'border-top:3px solid #00664A !important;' : '';
                                    $wl = $row['water_loss_pct'];
                                    if ($wl === null)   { $badge = 'secondary'; }
                                    elseif ($wl <= 3)   { $badge = 'success'; }
                                    else                { $badge = 'danger'; }
                                @endphp
                                <tr style="{{ $divider }}">
                                    @if($isGroupStart)
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
                                        @if($wl !== null)
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

                            {{-- Grand Total Row --}}
                            @if(!empty($grandTotal))
                                @php
                                    $gtWl = $grandTotal['water_loss_pct'];
                                    if ($gtWl === null)     $gtBadge = 'secondary';
                                    elseif ($gtWl <= 3)    $gtBadge = 'success';
                                    else                   $gtBadge = 'danger';
                                @endphp
                                @php
                                    $lastRow = end($rows);
                                @endphp
                                <tr style="background-color:#e8f5e9; font-weight:bold; border-top:2px solid #00664A;">
                                    <td colspan="2">Grand Total</td>
                                    <td>{{ number_format($grandTotal['avg_air_baku_flow'], 1) }}</td>
                                    <td class="text-muted" style="font-size:0.8rem;">{{ $lastRow['air_baku_totalizer'] !== null ? number_format($lastRow['air_baku_totalizer'], 0) : '—' }}</td>
                                    <td class="text-primary">{{ number_format($grandTotal['total_air_baku_selisih'], 0) }}</td>
                                    <td>{{ number_format($grandTotal['avg_yos_flow'], 1) }}</td>
                                    <td class="text-muted" style="font-size:0.8rem;">{{ $lastRow['yos_totalizer'] !== null ? number_format($lastRow['yos_totalizer'], 0) : '—' }}</td>
                                    <td>{{ number_format($grandTotal['avg_vet_flow'], 1) }}</td>
                                    <td class="text-muted" style="font-size:0.8rem;">{{ $lastRow['vet_totalizer'] !== null ? number_format($lastRow['vet_totalizer'], 0) : '—' }}</td>
                                    <td>{{ number_format($grandTotal['avg_total_flow'], 1) }}</td>
                                    <td class="text-muted" style="font-size:0.8rem;">{{ $lastRow['total_totalizer'] !== null ? number_format($lastRow['total_totalizer'], 0) : '—' }}</td>
                                    <td class="text-primary">{{ number_format($grandTotal['total_dist_selisih'], 0) }}</td>
                                    <td>
                                        @if($gtWl !== null)
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

                {{-- Summary Cards --}}
                @if(!empty($grandTotal))
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
    @if(!empty($shiftSummary))
        <div class="card shadow mb-4">
            <div class="card-header py-3" style="background-color:#00664A;">
                <h6 class="m-0 font-weight-bold text-white">
                    <i class="fas fa-layer-group mr-2"></i>Water Loss per Shift
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="row no-gutters">
                    @foreach($shiftSummary as $i => $s)
                        @php
                            $wl = $s['water_loss_pct'];
                            if ($wl <= 3)   { $bg = '#d4edda'; $color = '#155724'; }
                            else            { $bg = '#f8d7da'; $color = '#721c24'; }
                            $border = $i < count($shiftSummary)-1 ? 'border-right:1px solid #dee2e6;' : '';
                        @endphp
                        <div class="col-12 col-md-4 text-center py-4" style="background:{{ $bg }}; {{ $border }}">
                            <div class="text-xs font-weight-bold text-uppercase mb-1" style="color:{{ $color }}; letter-spacing:.05em;">
                                Water Loss {{ $s['label'] }}
                            </div>
                            <div style="font-size:2rem; font-weight:700; color:{{ $color }}; line-height:1.1;">
                                {{ number_format($wl, 2) }}%
                            </div>
                            @if($s['count'] > 0)
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

    {{-- Legend --}}
    <div class="text-right mb-4" style="font-size:0.78rem;">
        <span class="badge badge-success mr-1">≤ 3%</span> Normal &nbsp;
        <span class="badge badge-danger mr-1">> 3%</span> Tinggi
    </div>

    {{-- Charts --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold" style="color:#00664A;">
                <i class="fas fa-chart-line mr-2"></i>Water Loss (%) &amp; Level Reservoir A &amp; B (cm)
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

        new Chart(cv, {
            type: 'line',
            data: {
                labels: d.labels,
                datasets: [
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
                ],
            },
            options: {
                maintainAspectRatio: false,
                scales: {
                    yWl: {
                        type: 'linear',
                        position: 'left',
                        title: { display: true, text: 'Water Loss (%)' },
                        ticks: {
                            callback: function(v) { return v + '%'; },
                        },
                    },
                    yResv: {
                        type: 'linear',
                        position: 'right',
                        title: { display: true, text: 'Level (cm)' },
                        grid: { drawOnChartArea: false },
                        ticks: {
                            callback: function(v) { return parseFloat(v).toFixed(2) + ' cm'; },
                        },
                    },
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(ctx) {
                                var unit = ctx.dataset.yAxisID === 'yWl' ? '%' : ' cm';
                                return ctx.dataset.label + ': ' + (ctx.parsed.y !== null ? ctx.parsed.y.toFixed(2) + unit : '—');
                            },
                        },
                    },
                },
            },
        });
    });
</script>
@endscript

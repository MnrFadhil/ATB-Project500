<div wire:poll.30s>
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard SCADA</h1>
        @if($latest)
            <span class="badge badge-success px-3 py-2" style="font-size:0.85rem;">
                <i class="fas fa-circle mr-1" style="font-size:0.6rem;"></i>
                Data terakhir: {{ \Carbon\Carbon::parse($latest->timestamp)->format('d M Y, H:i') }}
                &nbsp;<span class="badge badge-light text-success">Live</span>
            </span>
        @endif
    </div>

    {{-- ===== LATEST VALUE CARDS ===== --}}
    @if($latest)
    <div class="row mb-2">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Flow Intake</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($latest->flow_intake, 2) }} <small class="text-muted">L/s</small></div>
                        </div>
                        <div class="col-auto"><i class="fas fa-water fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Flow Yos Sudarso</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($latest->flow_yos_sudarso, 2) }} <small class="text-muted">L/s</small></div>
                        </div>
                        <div class="col-auto"><i class="fas fa-tint fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Flow Veteran</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($latest->flow_veteran, 2) }} <small class="text-muted">L/s</small></div>
                        </div>
                        <div class="col-auto"><i class="fas fa-tint fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Turbidity Air Baku</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($latest->turbidity_baku, 3) }} <small class="text-muted">NTU</small></div>
                        </div>
                        <div class="col-auto"><i class="fas fa-eye fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Pressure Distribusi</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($latest->pressure_distribusi, 3) }} <small class="text-muted">bar</small></div>
                        </div>
                        <div class="col-auto"><i class="fas fa-compress-alt fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">pH Air Baku</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($latest->ph_baku, 3) }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-flask fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Free Chlorine</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($latest->free_chlorine, 3) }} <small class="text-muted">mg/L</small></div>
                        </div>
                        <div class="col-auto"><i class="fas fa-atom fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Turbidity Reservoir</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($latest->turbidity_reservoir, 3) }} <small class="text-muted">NTU</small></div>
                        </div>
                        <div class="col-auto"><i class="fas fa-eye fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="alert alert-info mb-4">
        <i class="fas fa-info-circle mr-2"></i> Belum ada data sensor yang masuk.
    </div>
    @endif

    <hr class="my-4" style="border-color:#00664A; border-width:2px;">

    {{-- ===== CHARTS (wire:ignore agar canvas tidak di-replace saat poll) ===== --}}
    <div class="row" wire:ignore>
        <div class="col-md-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold" style="color:#00664A">💧 Flow Rate (L/s)</h6>
                </div>
                <div class="card-body"><div class="chart-area"><canvas id="chartFlow"></canvas></div></div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold" style="color:#00664A">🔵 Turbidity (NTU)</h6>
                </div>
                <div class="card-body"><div class="chart-area"><canvas id="chartTurbidity"></canvas></div></div>
            </div>
        </div>
    </div>
    <div class="row" wire:ignore>
        <div class="col-md-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold" style="color:#00664A">⚙️ Pressure (bar)</h6>
                </div>
                <div class="card-body"><div class="chart-area"><canvas id="chartPressure"></canvas></div></div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold" style="color:#00664A">🧪 pH & Free Chlorine</h6>
                </div>
                <div class="card-body"><div class="chart-area"><canvas id="chartQuality"></canvas></div></div>
            </div>
        </div>
    </div>

    <hr class="my-4" style="border-color:#00664A; border-width:2px;">

    {{-- ===== DATA TABLE ===== --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold" style="color:#00664A"><i class="fas fa-table mr-2"></i>Data Log Sensor (100 terbaru)</h6>
            <span class="badge badge-secondary">{{ $logs->count() }} record</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-sm table-hover mb-0" style="font-size:0.78rem;">
                    <thead style="background-color:#00664A; color:white; position:sticky; top:0;">
                        <tr>
                            <th class="text-center" rowspan="2">Timestamp</th>
                            <th class="text-center" colspan="4">Flow (L/s)</th>
                            <th class="text-center" colspan="7">Pressure (bar)</th>
                            <th class="text-center" colspan="4">Turbidity (NTU)</th>
                            <th class="text-center" colspan="3">Kualitas</th>
                            <th class="text-center" colspan="3">Lainnya</th>
                        </tr>
                        <tr style="background-color:#00664A; color:white;">
                            <th class="text-center">Intake</th>
                            <th class="text-center">Yos Sudarso</th>
                            <th class="text-center">Veteran</th>
                            <th class="text-center">Backwash</th>
                            <th class="text-center">Intake</th>
                            <th class="text-center">Res. A</th>
                            <th class="text-center">Res. B</th>
                            <th class="text-center">Distrib.</th>
                            <th class="text-center">Service</th>
                            <th class="text-center">Backwash</th>
                            <th class="text-center">Kompressor</th>
                            <th class="text-center">Air Baku</th>
                            <th class="text-center">Reservoir</th>
                            <th class="text-center">Sedimen</th>
                            <th class="text-center">Filter</th>
                            <th class="text-center">pH Baku</th>
                            <th class="text-center">pH Res.</th>
                            <th class="text-center">Free Cl</th>
                            <th class="text-center">SCM</th>
                            <th class="text-center">Freq A</th>
                            <th class="text-center">Freq C</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr>
                            <td class="text-nowrap">{{ \Carbon\Carbon::parse($log->timestamp)->format('d/m/Y H:i') }}</td>
                            <td class="text-right">{{ $log->flow_intake !== null ? number_format($log->flow_intake,2) : '-' }}</td>
                            <td class="text-right">{{ $log->flow_yos_sudarso !== null ? number_format($log->flow_yos_sudarso,2) : '-' }}</td>
                            <td class="text-right">{{ $log->flow_veteran !== null ? number_format($log->flow_veteran,2) : '-' }}</td>
                            <td class="text-right">{{ $log->flow_backwash !== null ? number_format($log->flow_backwash,2) : '-' }}</td>
                            <td class="text-right">{{ $log->pressure_intake !== null ? number_format($log->pressure_intake,3) : '-' }}</td>
                            <td class="text-right">{{ $log->pressure_reservoir_a !== null ? number_format($log->pressure_reservoir_a,3) : '-' }}</td>
                            <td class="text-right">{{ $log->pressure_reservoir_b !== null ? number_format($log->pressure_reservoir_b,3) : '-' }}</td>
                            <td class="text-right">{{ $log->pressure_distribusi !== null ? number_format($log->pressure_distribusi,3) : '-' }}</td>
                            <td class="text-right">{{ $log->pressure_service !== null ? number_format($log->pressure_service,3) : '-' }}</td>
                            <td class="text-right">{{ $log->pressure_backwash !== null ? number_format($log->pressure_backwash,3) : '-' }}</td>
                            <td class="text-right">{{ $log->pressure_kompressor !== null ? number_format($log->pressure_kompressor,3) : '-' }}</td>
                            <td class="text-right">{{ $log->turbidity_baku !== null ? number_format($log->turbidity_baku,3) : '-' }}</td>
                            <td class="text-right">{{ $log->turbidity_reservoir !== null ? number_format($log->turbidity_reservoir,3) : '-' }}</td>
                            <td class="text-right">{{ $log->turbidity_sedimen !== null ? number_format($log->turbidity_sedimen,3) : '-' }}</td>
                            <td class="text-right">{{ $log->turbidity_filter !== null ? number_format($log->turbidity_filter,3) : '-' }}</td>
                            <td class="text-right">{{ $log->ph_baku !== null ? number_format($log->ph_baku,3) : '-' }}</td>
                            <td class="text-right">{{ $log->ph_reservoir !== null ? number_format($log->ph_reservoir,3) : '-' }}</td>
                            <td class="text-right">{{ $log->free_chlorine !== null ? number_format($log->free_chlorine,3) : '-' }}</td>
                            <td class="text-right">{{ $log->scm !== null ? number_format($log->scm,3) : '-' }}</td>
                            <td class="text-right">{{ $log->freq_distribusi_a !== null ? number_format($log->freq_distribusi_a,1) : '-' }}</td>
                            <td class="text-right">{{ $log->freq_distribusi_c !== null ? number_format($log->freq_distribusi_c,1) : '-' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="22" class="text-center py-4 text-muted">Belum ada data sensor.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@once
<script>
(function() {
    var lineBase = {
        lineTension: 0.3,
        pointRadius: 2,
        pointHoverRadius: 4,
        pointBorderWidth: 1,
        fill: false,
    };

    function makeChart(id, datasets) {
        var canvas = document.getElementById(id);
        if (!canvas) return null;
        var existing = Chart.getChart(canvas);
        if (existing) existing.destroy();
        return new Chart(canvas, {
            type: 'line',
            data: { labels: [], datasets: datasets },
            options: {
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } } },
                scales: {
                    y: { ticks: { font: { size: 10 } } },
                    x: { ticks: { font: { size: 10 }, maxTicksLimit: 12 } }
                },
            }
        });
    }

    function initScadaCharts() {
        window._scadaChartFlow = makeChart('chartFlow', [
            Object.assign({}, lineBase, { label: 'Flow Intake',  borderColor: 'rgba(78,115,223,1)',  pointBackgroundColor: 'rgba(78,115,223,1)' }),
            Object.assign({}, lineBase, { label: 'Yos Sudarso',  borderColor: 'rgba(28,200,138,1)',  pointBackgroundColor: 'rgba(28,200,138,1)' }),
            Object.assign({}, lineBase, { label: 'Veteran',      borderColor: 'rgba(231,74,59,1)',   pointBackgroundColor: 'rgba(231,74,59,1)' }),
            Object.assign({}, lineBase, { label: 'Backwash',     borderColor: 'rgba(246,194,62,1)',  pointBackgroundColor: 'rgba(246,194,62,1)' }),
        ]);
        window._scadaChartTurbidity = makeChart('chartTurbidity', [
            Object.assign({}, lineBase, { label: 'Air Baku',  borderColor: 'rgba(78,115,223,1)',  pointBackgroundColor: 'rgba(78,115,223,1)' }),
            Object.assign({}, lineBase, { label: 'Reservoir', borderColor: 'rgba(28,200,138,1)',  pointBackgroundColor: 'rgba(28,200,138,1)' }),
            Object.assign({}, lineBase, { label: 'Sedimen',   borderColor: 'rgba(231,74,59,1)',   pointBackgroundColor: 'rgba(231,74,59,1)' }),
            Object.assign({}, lineBase, { label: 'Filter',    borderColor: 'rgba(246,194,62,1)',  pointBackgroundColor: 'rgba(246,194,62,1)' }),
        ]);
        window._scadaChartPressure = makeChart('chartPressure', [
            Object.assign({}, lineBase, { label: 'Intake',      borderColor: 'rgba(78,115,223,1)',  pointBackgroundColor: 'rgba(78,115,223,1)' }),
            Object.assign({}, lineBase, { label: 'Reservoir A', borderColor: 'rgba(28,200,138,1)',  pointBackgroundColor: 'rgba(28,200,138,1)' }),
            Object.assign({}, lineBase, { label: 'Reservoir B', borderColor: 'rgba(54,185,204,1)',  pointBackgroundColor: 'rgba(54,185,204,1)' }),
            Object.assign({}, lineBase, { label: 'Distribusi',  borderColor: 'rgba(231,74,59,1)',   pointBackgroundColor: 'rgba(231,74,59,1)' }),
            Object.assign({}, lineBase, { label: 'Service',     borderColor: 'rgba(246,194,62,1)',  pointBackgroundColor: 'rgba(246,194,62,1)' }),
            Object.assign({}, lineBase, { label: 'Backwash',    borderColor: 'rgba(133,100,4,1)',   pointBackgroundColor: 'rgba(133,100,4,1)' }),
            Object.assign({}, lineBase, { label: 'Kompressor',  borderColor: 'rgba(153,102,255,1)', pointBackgroundColor: 'rgba(153,102,255,1)' }),
        ]);
        window._scadaChartQuality = makeChart('chartQuality', [
            Object.assign({}, lineBase, { label: 'pH Air Baku',   borderColor: 'rgba(78,115,223,1)', pointBackgroundColor: 'rgba(78,115,223,1)' }),
            Object.assign({}, lineBase, { label: 'pH Reservoir',  borderColor: 'rgba(28,200,138,1)', pointBackgroundColor: 'rgba(28,200,138,1)' }),
            Object.assign({}, lineBase, { label: 'Free Chlorine', borderColor: 'rgba(231,74,59,1)',  pointBackgroundColor: 'rgba(231,74,59,1)' }),
        ]);
    }

    function updateScadaCharts(d) {
        if (!d || !d.labels) return;
        var cF = window._scadaChartFlow;
        var cT = window._scadaChartTurbidity;
        var cP = window._scadaChartPressure;
        var cQ = window._scadaChartQuality;
        if (!cF || !cT || !cP || !cQ) { initScadaCharts(); return; }

        cF.data.labels = d.labels;
        cF.data.datasets[0].data = d.flow.flow_intake;
        cF.data.datasets[1].data = d.flow.flow_yos_sudarso;
        cF.data.datasets[2].data = d.flow.flow_veteran;
        cF.data.datasets[3].data = d.flow.flow_backwash;
        cF.update('none');

        cT.data.labels = d.labels;
        cT.data.datasets[0].data = d.turbidity.turbidity_baku;
        cT.data.datasets[1].data = d.turbidity.turbidity_reservoir;
        cT.data.datasets[2].data = d.turbidity.turbidity_sedimen;
        cT.data.datasets[3].data = d.turbidity.turbidity_filter;
        cT.update('none');

        cP.data.labels = d.labels;
        cP.data.datasets[0].data = d.pressure.pressure_intake;
        cP.data.datasets[1].data = d.pressure.pressure_reservoir_a;
        cP.data.datasets[2].data = d.pressure.pressure_reservoir_b;
        cP.data.datasets[3].data = d.pressure.pressure_distribusi;
        cP.data.datasets[4].data = d.pressure.pressure_service;
        cP.data.datasets[5].data = d.pressure.pressure_backwash;
        cP.data.datasets[6].data = d.pressure.pressure_kompressor;
        cP.update('none');

        cQ.data.labels = d.labels;
        cQ.data.datasets[0].data = d.quality.ph_baku;
        cQ.data.datasets[1].data = d.quality.ph_reservoir;
        cQ.data.datasets[2].data = d.quality.free_chlorine;
        cQ.update('none');
    }

    document.addEventListener('livewire:init', function() {
        initScadaCharts();

        Livewire.on('scada-charts-ready', function(payload) {
            updateScadaCharts(payload.chartData);
        });
    });

    document.addEventListener('livewire:navigated', function() {
        initScadaCharts();
    });
})();
</script>
@endonce

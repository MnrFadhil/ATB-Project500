<div>
    <!-- Page Heading -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
        <button onclick="window.location.reload()" class="btn btn-sm btn-outline-secondary flex-shrink-0" title="Refresh halaman">
            <i class="fas fa-sync-alt mr-1"></i> Refresh
        </button>
    </div>

    <hr class="my-4">

    {{-- ===== SESI PERBULAN ===== --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-3">
        <h5 class="h5 mb-0 font-weight-bold text-gray-700">
            <i class="fas fa-calendar-alt mr-2"></i>Data Perbulan
        </h5>
        <div class="mt-2 mt-md-0">
            <input wire:model.live="selectedMonth" type="month" class="form-control" />
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold" style="color: #00664A">Debit Air Baku (L/s)</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="monthlyDebitAirBaku"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold" style="color: #00664A">Total Flow Yos Sudarso & Veteran (L/s)</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="monthlyTotalFlow"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold" style="color: #00664A">NTU Air Baku</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="monthlyNtuAirBaku"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold" style="color: #00664A">NTU Reservoir</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="monthlyNtuReservoir"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <hr class="my-4">

    {{-- ===== SESI PERHARI ===== --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-3">
        <h5 class="h5 mb-0 font-weight-bold text-gray-700">
            <i class="fas fa-calendar-day mr-2"></i>Data Perhari
        </h5>
        <div class="mt-2 mt-md-0">
            <input wire:model.live="date" type="date" class="form-control" id="date" required>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card shadow ">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold" style="color: #00664A">Debit Air Baku & Total Flow Distribusi</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="airbakuDanTotalFlow"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card shadow ">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold" style="color: #00664A">Turbidity Reservoir & Turbidity Sedimentasi</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="TReservoirdanTSedimentasi"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card shadow ">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold" style="color: #00664A">Turbidity Sedimentasi & Air Baku</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="TSedimentasidanAirBaku"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card shadow ">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold" style="color: #00664A">Reservoir A & Reservoir B</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="ReservoirAdanReservoirB"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold" style="color: #00664A">Turbidity Air Baku & Dosis PAC</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="turbidAirBakuDosisPac"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== PIE CHARTS ===== --}}
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold" style="color:#00664A">
                        <i class="fas fa-chart-pie mr-1"></i> Proporsi Distribusi
                    </h6>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    <div style="position:relative; width:100%; max-width:280px;">
                        <canvas id="pieDistribusi"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold" style="color:#00664A">
                        <i class="fas fa-chart-pie mr-1"></i> Komposisi Water Loss
                    </h6>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    <div style="position:relative; width:100%; max-width:280px;">
                        <canvas id="pieWaterLoss"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold" style="color:#00664A">
                        <i class="fas fa-chart-pie mr-1"></i> Pemakaian Kimia
                    </h6>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    <div style="position:relative; width:100%; max-width:280px;">
                        <canvas id="pieKimia"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@script
    <script>
        // ===== MONTHLY CHARTS =====
        $wire.on('monthly-data-ready', (data) => {
            if (!data || !data.monthlyData || data.monthlyData.length === 0) return;
            var rows   = data.monthlyData;
            var labels = rows.map(function(d) { return d.label; });
            var base   = { lineTension: 0.3, pointRadius: 3, pointHoverRadius: 5, pointBorderWidth: 2, pointHitRadius: 10, fill: false };
            var specs  = [
                { id: 'monthlyDebitAirBaku',  label: 'Debit Air Baku',          color: 'rgba(78,115,223,1)', vals: rows.map(function(d){ return d.debit_air_baku; }),  unit: 'L/s' },
                { id: 'monthlyTotalFlow',     label: 'Total Flow Yos & Veteran', color: 'rgba(223,109,78,1)', vals: rows.map(function(d){ return d.total_flow; }),     unit: 'L/s' },
                { id: 'monthlyNtuAirBaku',    label: 'NTU Air Baku',             color: 'rgba(78,115,223,1)', vals: rows.map(function(d){ return d.ntu_air_baku; }),   unit: 'NTU' },
                { id: 'monthlyNtuReservoir',  label: 'NTU Reservoir',            color: 'rgb(241,196,15)',    vals: rows.map(function(d){ return d.ntu_reservoir; }),  unit: 'NTU' },
            ];
            specs.forEach(function(s) {
                var canvas  = document.getElementById(s.id);
                var existing = Chart.getChart(canvas);
                if (existing) existing.destroy();
                new Chart(canvas, {
                    type: 'line',
                    data: { labels: labels, datasets: [Object.assign({}, base, { label: s.label, borderColor: s.color, pointBackgroundColor: s.color, data: s.vals })] },
                    options: { maintainAspectRatio: false, scales: { y: { ticks: { callback: function(v){ return v + ' ' + s.unit; } } } } },
                });
            });
        });

        // ===== DAILY CHARTS =====
        let myLineChart7 = null;
        let myLineChart8 = null;
        let myLineChart9 = null;
        let myLineChart10 = null;
        let myLineChart11 = null;
        let shiftChartData = @json($shiftChart);

        $wire.on('post-created', (data) => {
            if (data.shiftChartData) shiftChartData = data.shiftChartData

            var labelsChart = shiftChartData.map(data => data.end_time)
            var dataDebitAirBaku = shiftChartData.map(data1 => data1.flow_meters.filter(data2 => data2.location == null)[0].flow)
            var totalFlow = shiftChartData.map(data1 => data1.flow_meters.filter(data2 => data2.location !== null))
                .map(data3 => { let c = 0; data3.forEach(d => c += d.flow); return c; })
            var reservoirAchart = shiftChartData.map(data1 => data1.reservoir_levels.level_a)
            var reservoirBchart = shiftChartData.map(data1 => data1.reservoir_levels.level_b)
            var turbiditySedimentasiChart = shiftChartData.map(data1 => data1.water_qualities.find(val => val.type == 'sedimentation')).map(val => val.turbidity)
            var turbidityReservoirChart = shiftChartData.map(data1 => data1.water_qualities.find(val => val.type == 'reservoir')).map(val => val.turbidity)

            // Airbaku Dan TotalFlow
            if (myLineChart7) myLineChart7.destroy();
            var ctx = document.getElementById("airbakuDanTotalFlow");

            myLineChart7 = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labelsChart,
                    datasets: [{
                            label: "Debit Air",
                            lineTension: 0.3,
                            borderColor: "rgba(78, 115, 223, 1)",
                            pointBackgroundColor: "rgba(78, 115, 223, 1)",
                            pointBorderColor: "rgba(78, 115, 223, 1)",
                            pointHoverRadius: 3,
                            pointHitRadius: 10,
                            pointBorderWidth: 2,
                            data: dataDebitAirBaku,
                        },
                        {
                            label: "Total Flow",
                            lineTension: 0.3,
                            borderColor: "rgba(223, 109, 78, 1)",
                            pointBackgroundColor: "rgba(223, 109, 78, 1)",
                            pointBorderColor: "rgba(223, 109, 78, 1)",
                            pointHoverRadius: 3,
                            pointHitRadius: 10,
                            pointBorderWidth: 2,
                            data: totalFlow,
                        }
                    ],
                },
                options: {
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            ticks: {
                                callback: function(value) {
                                    return value + ' L/s';
                                }
                            }
                        }
                    }
                },
            });

            // TReservoir Dan TSedimentasi
            if (myLineChart8) myLineChart8.destroy();
            var ctx = document.getElementById("TReservoirdanTSedimentasi");

            myLineChart8 = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labelsChart,
                    datasets: [{
                            label: "Turbidity Reservoir",
                            lineTension: 0.3,
                            borderColor: "rgb(241, 196, 15)",
                            pointBackgroundColor: "rgb(241, 196, 15)",
                            pointBorderColor: "rgb(241, 196, 15)",
                            pointHoverRadius: 3,
                            pointHitRadius: 10,
                            pointBorderWidth: 2,
                            data: turbidityReservoirChart,
                        },
                        {
                            label: "Turbidity Sedimentasi",
                            lineTension: 0.3,
                            borderColor: "rgb(231, 76, 60)",
                            pointBackgroundColor: "rgb(231, 76, 60)",
                            pointBorderColor: "rgb(231, 76, 60)",
                            pointHoverRadius: 3,
                            pointHitRadius: 10,
                            pointBorderWidth: 2,
                            data: turbiditySedimentasiChart,
                        }
                    ],
                },
                options: {
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            ticks: {
                                callback: function(value) {
                                    return value + ' NTU';
                                }
                            }
                        }
                    }
                },
            });

            // TSedimentasi Dan AirBaku
            if (myLineChart9) myLineChart9.destroy();
            var ctx = document.getElementById("TSedimentasidanAirBaku");
            var turbidityAirbakuChart = shiftChartData.map(data1 => data1.water_qualities.find(val => val.type == 'air baku')).map(val => val.turbidity)

            myLineChart9 = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labelsChart,
                    datasets: [{
                            label: "Turbidity Sedimentasi",
                            lineTension: 0.3,
                            borderColor: "rgb(231, 76, 60)",
                            pointBackgroundColor: "rgb(231, 76, 60)",
                            pointBorderColor: "rgb(231, 76, 60)",
                            pointHoverRadius: 3,
                            pointHitRadius: 10,
                            pointBorderWidth: 2,
                            data: turbiditySedimentasiChart,
                        },
                        {
                            label: "Air Baku",
                            lineTension: 0.3,
                            borderColor: "rgba(78, 115, 223, 1)",
                            pointBackgroundColor: "rgba(78, 115, 223, 1)",
                            pointBorderColor: "rgba(78, 115, 223, 1)",
                            pointHoverRadius: 3,
                            pointHitRadius: 10,
                            pointBorderWidth: 2,
                            data: turbidityAirbakuChart,
                        }
                    ],
                },
                options: {
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            ticks: {
                                callback: function(value) {
                                    return value + ' NTU';
                                }
                            }
                        }
                    }
                },
            });

            // ReservoirA Dan ReservoirB
            if (myLineChart10) myLineChart10.destroy();
            var ctx = document.getElementById("ReservoirAdanReservoirB");

            myLineChart10 = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labelsChart,
                    datasets: [{
                            label: "Reservoir A",
                            lineTension: 0.3,
                            borderColor: "rgba(78, 223, 163, 1)",
                            pointBackgroundColor: "rgba(78, 223, 163, 1)",
                            pointBorderColor: "rgba(78, 223, 163, 1)",
                            pointHoverRadius: 3,
                            pointHitRadius: 10,
                            pointBorderWidth: 2,
                            data: reservoirAchart,
                        },
                        {
                            label: "Reservoir B",
                            lineTension: 0.3,
                            borderColor: "rgba(138, 78, 223, 1)",
                            pointBackgroundColor: "rgba(138, 78, 223, 1)",
                            pointBorderColor: "rgba(138, 78, 223, 1)",
                            pointHoverRadius: 3,
                            pointHitRadius: 10,
                            pointBorderWidth: 2,
                            data: reservoirBchart,
                        }
                    ],
                },
                options: {
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            ticks: {
                                callback: function(value) {
                                    return parseFloat(value).toFixed(2) + ' m';
                                }
                            }
                        }
                    }
                },
            });

            // Turbidity Air Baku & Dosis PAC
            if (myLineChart11) myLineChart11.destroy();
            var ctx = document.getElementById("turbidAirBakuDosisPac");
            var turbidityAirBakuData = shiftChartData.map(data1 => {
                const ab = data1.water_qualities.find(val => val.type == 'air baku');
                return ab ? ab.turbidity : null;
            });
            var dosisPacData = shiftChartData.map(data1 => {
                const pacRunning = data1.pump_chemicals.find(val => val.type == 'pac' && val.status == 'running');
                const pacAny = data1.pump_chemicals.find(val => val.type == 'pac');
                const pac = pacRunning || pacAny;
                return pac ? pac.dosage : null;
            });

            myLineChart11 = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labelsChart,
                    datasets: [{
                            label: "Turbidity Air Baku",
                            lineTension: 0.3,
                            borderColor: "rgba(78, 115, 223, 1)",
                            pointBackgroundColor: "rgba(78, 115, 223, 1)",
                            pointBorderColor: "rgba(78, 115, 223, 1)",
                            pointHoverRadius: 3,
                            pointHitRadius: 10,
                            pointBorderWidth: 2,
                            data: turbidityAirBakuData,
                            yAxisID: 'yTurbid',
                        },
                        {
                            label: "Dosis PAC",
                            lineTension: 0.3,
                            borderColor: "rgba(231, 76, 60, 1)",
                            pointBackgroundColor: "rgba(231, 76, 60, 1)",
                            pointBorderColor: "rgba(231, 76, 60, 1)",
                            pointHoverRadius: 3,
                            pointHitRadius: 10,
                            pointBorderWidth: 2,
                            data: dosisPacData,
                            yAxisID: 'yPac',
                        }
                    ],
                },
                options: {
                    maintainAspectRatio: false,
                    scales: {
                        yTurbid: {
                            type: 'linear',
                            position: 'left',
                            ticks: {
                                callback: function(value) {
                                    return value + ' NTU';
                                }
                            },
                            title: { display: true, text: 'Turbidity (NTU)' }
                        },
                        yPac: {
                            type: 'linear',
                            position: 'right',
                            grid: { drawOnChartArea: false },
                            ticks: {
                                callback: function(value) {
                                    return value + ' ppm';
                                }
                            },
                            title: { display: true, text: 'Dosis PAC (ppm)' }
                        }
                    }
                },
            });
        });

        // ===== PIE CHARTS =====
        $wire.on('pie-charts-ready', function(data) {
            var p = data.pieData;
            if (!p) return;

            var pieBase = {
                borderWidth: 2,
                hoverOffset: 6,
            };

            var pieColors = {
                distribusi: ['rgba(78,115,223,0.85)', 'rgba(223,109,78,0.85)'],
                waterLoss:  ['rgba(28,200,138,0.85)', 'rgba(231,74,59,0.85)'],
                kimia:      [
                    'rgba(78,115,223,0.85)',
                    'rgba(231,74,59,0.85)',
                    'rgba(241,196,15,0.85)',
                    'rgba(138,78,223,0.85)',
                ],
            };

            var pieOpts = {
                maintainAspectRatio: true,
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: {
                        callbacks: {
                            label: function(ctx) {
                                var total = ctx.dataset.data.reduce(function(a, b) { return a + b; }, 0);
                                var pct   = total > 0 ? ((ctx.parsed / total) * 100).toFixed(1) : 0;
                                return ctx.label + ': ' + ctx.parsed.toLocaleString() + ' (' + pct + '%)';
                            },
                        },
                    },
                },
            };

            var specs = [
                { id: 'pieDistribusi', labels: p.distribution.labels, values: p.distribution.values, colors: pieColors.distribusi },
                { id: 'pieWaterLoss',  labels: p.water_loss.labels,   values: p.water_loss.values,   colors: pieColors.waterLoss },
                { id: 'pieKimia',      labels: p.chemicals.labels,     values: p.chemicals.values,    colors: pieColors.kimia },
            ];

            specs.forEach(function(s) {
                var cv  = document.getElementById(s.id);
                var old = Chart.getChart(cv);
                if (old) old.destroy();
                if (!s.values || s.values.length === 0) return;
                new Chart(cv, {
                    type: 'pie',
                    data: {
                        labels: s.labels,
                        datasets: [Object.assign({}, pieBase, {
                            data: s.values,
                            backgroundColor: s.colors,
                            borderColor: s.colors.map(function(c) { return c.replace('0.85', '1'); }),
                        })],
                    },
                    options: pieOpts,
                });
            });
        });
    </script>
@endscript

<div>
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
        <div class="mt-4 mt-md-0">
            <input wire:model.live="date" type="date" class="form-control" id="date" required>
        </div>
    </div>

    <div class="row">
        {{-- Charts --}}
        <div class="col-md-6 mb-4">
            <div class="card shadow ">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold" style="color: #00664A">Debit Air Baku & Total Flow Distribusi</h6>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="airbakuDanTotalFlow"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Charts --}}
        <div class="col-md-6 mb-4">
            <div class="card shadow ">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold" style="color: #00664A">Turbidity Reservoir & Turbidity Sedimentasi
                    </h6>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="TReservoirdanTSedimentasi"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Charts --}}
        <div class="col-md-6 mb-4">
            <div class="card shadow ">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold" style="color: #00664A">Turbidity Sedimentasi & Air Baku</h6>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="TSedimentasidanAirBaku"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Charts --}}
        <div class="col-md-6 mb-4">
            <div class="card shadow ">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold" style="color: #00664A">Reservoir A & Reservoir B</h6>
                </div>
                <!-- Card Body -->
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
</div>

@script
    <script>
        let myLineChart7 = null;
        let myLineChart8 = null;
        let myLineChart9 = null;
        let myLineChart10 = null;
        let myLineChart11 = null;
        let shiftChartData = @json($shiftChart);

        $wire.on('post-created', (data) => {
            if (data.shiftChartData) shiftChartData = data.shiftChartData

            console.log(shiftChartData)


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
            var turbidityAirbakuChart = shiftChartData.map(data1 => data1.water_qualities.find(val => val
                .type == 'air baku')).map(val => val.turbidity)

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
    </script>
@endscript

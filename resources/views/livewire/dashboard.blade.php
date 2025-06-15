<div>
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
        <div class="mt-4 mt-md-0">
            <input wire:model.live="date" type="date" class="form-control" id="date" required>
        </div>
    </div>

    <div class="row">
        <!-- Chart 1 -->
        <div class="col-md-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold" style="color: #00664A">Debit Air Baku</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="airBakuChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart 2 -->
        <div class="col-md-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold" style="color: #00664A">Total Flow Distribusi (Yos Sudarso & Veteran)
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="totalFlowChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="row">
        {{-- Charts --}}
        <div class="col-md-6 mb-4">
            <div class="card shadow">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold" style="color: #00664A">Reservoir A</h6>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="reservoirA"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Charts --}}
        <div class="col-md-6 mb-4">
            <div class="card shadow">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold" style="color: #00664A">Reservoir B</h6>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="reservoirB"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Charts --}}
        <div class="col-md-6 mb-4">
            <div class="card shadow">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold" style="color: #00664A">Turbidity Sedimentasi</h6>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="turbiditySedimentasi"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Charts --}}
        <div class="col-md-6 mb-4">
            <div class="card shadow">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold" style="color: #00664A">Turbidity Reservoir</h6>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="turbidityReservoir"></canvas>
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
</div>

@script
    <script>
        let myLineChart1 = null;
        let myLineChart2 = null;
        let myLineChart3 = null;
        let myLineChart4 = null;
        let myLineChart5 = null;
        let myLineChart6 = null;
        let myLineChart7 = null;
        let myLineChart8 = null;
        let myLineChart9 = null;
        let myLineChart10 = null;
        let shiftChartData = @json($shiftChart);

        $wire.on('post-created', (data) => {
            if (data.shiftChartData) shiftChartData = data.shiftChartData

            console.log(shiftChartData)


            // Debit air baku chart
            if (myLineChart1) myLineChart1.destroy();
            var labelsChart = shiftChartData.map(data => data.end_time)
            var ctx = document.getElementById("airBakuChart");
            var dataDebitAirBaku = shiftChartData.map(data1 => data1.flow_meters.filter(data2 => data2.location ==
                    null)[0]
                .flow)

            myLineChart1 = new Chart(ctx, {
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
                    }],
                },
                options: {
                    maintainAspectRatio: false,
                },
            });


            // Total Flow Air Sudarso dan Veteran
            // Debit air baku chart
            if (myLineChart2) myLineChart2.destroy();
            var ctx = document.getElementById("totalFlowChart");
            var totalFlow = shiftChartData.map(data1 => data1.flow_meters.filter(data2 => data2.location !== null))
                .map(
                    data3 => {
                        let countTotal = 0;
                        data3.forEach(data4 => {
                            countTotal += data4.flow
                        });
                        return countTotal;
                    })

            myLineChart2 = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labelsChart,
                    datasets: [{
                        label: "Total Flow Yos Sudarso & Veteran",
                        lineTension: 0.3,
                        borderColor: "rgba(223, 109, 78, 1)",
                        pointBackgroundColor: "rgba(223, 109, 78, 1)",
                        pointBorderColor: "rgba(223, 109, 78, 1)",
                        pointHoverRadius: 3,
                        pointHitRadius: 10,
                        pointBorderWidth: 2,
                        data: totalFlow,
                    }],
                },
                options: {
                    maintainAspectRatio: false,
                },
            });


            // Level reservoir A
            if (myLineChart3) myLineChart3.destroy();
            var ctx = document.getElementById("reservoirA");
            var reservoirAchart = shiftChartData.map(data1 => data1.reservoir_levels.level_a)

            myLineChart3 = new Chart(ctx, {
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
                    }],
                },
                options: {
                    maintainAspectRatio: false,
                },
            });

            // Level Reservoir B
            if (myLineChart4) myLineChart4.destroy();
            var ctx = document.getElementById("reservoirB");
            var reservoirBchart = shiftChartData.map(data1 => data1.reservoir_levels.level_b)

            myLineChart4 = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labelsChart,
                    datasets: [{
                        label: "Reservoir B",
                        lineTension: 0.3,
                        borderColor: "rgba(138, 78, 223, 1)",
                        pointBackgroundColor: "rgba(138, 78, 223, 1)",
                        pointBorderColor: "rgba(138, 78, 223, 1)",
                        pointHoverRadius: 3,
                        pointHitRadius: 10,
                        pointBorderWidth: 2,
                        data: reservoirBchart,
                    }],
                },
                options: {
                    maintainAspectRatio: false,
                },
            });

            // Turbidity Sedimentasi
            if (myLineChart5) myLineChart5.destroy();
            var ctx = document.getElementById("turbiditySedimentasi");
            var turbiditySedimentasiChart = shiftChartData.map(data1 => data1.water_qualities.find(val => val
                .type == 'sedimentation')).map(val => val.turbidity)

            myLineChart5 = new Chart(ctx, {
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
                    }],
                },
                options: {
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            min: 5,
                            max: 0,
                            ticks: {
                                callback: function(value) {
                                    return value + ' NTU';
                                }
                            }
                        }
                    }
                },
            });

            // Turbidity Reservoir
            if (myLineChart6) myLineChart6.destroy();
            var ctx = document.getElementById("turbidityReservoir");
            var turbidityReservoirChart = shiftChartData.map(data1 => data1.water_qualities.find(val => val
                .type == 'reservoir')).map(val => val.turbidity)

            myLineChart6 = new Chart(ctx, {
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
                    }],
                },
                options: {
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            min: 2,
                            max: 0,
                            ticks: {
                                callback: function(value) {
                                    return value + ' NTU';
                                }
                            }
                        }
                    }
                },
            });

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
                                    return value + ' m';
                                }
                            }
                        }
                    }
                },
            });
        });
    </script>
@endscript

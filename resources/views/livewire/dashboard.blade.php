<div>
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
        <div class="mt-4 mt-md-0">
            <input wire:model.live="date" type="date" class="form-control" id="date" required>
        </div>
    </div>


    {{-- Charts --}}
    <div class="card shadow mb-4">
        <!-- Card Header - Dropdown -->
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Debit Air Baku</h6>
        </div>
        <!-- Card Body -->
        <div class="card-body">
            <div class="chart-area">
                <canvas id="airBakuChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Charts --}}
    <div class="card shadow mb-4">
        <!-- Card Header - Dropdown -->
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Total Flow Distribusi (Yos Sudarso & Veteran)</h6>
        </div>
        <!-- Card Body -->
        <div class="card-body">
            <div class="chart-area">
                <canvas id="totalFlowChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Charts --}}
    <div class="card shadow mb-4">
        <!-- Card Header - Dropdown -->
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Reservoir A</h6>
        </div>
        <!-- Card Body -->
        <div class="card-body">
            <div class="chart-area">
                <canvas id="reservoirA"></canvas>
            </div>
        </div>
    </div>

    {{-- Charts --}}
    <div class="card shadow mb-4">
        <!-- Card Header - Dropdown -->
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Reservoir B</h6>
        </div>
        <!-- Card Body -->
        <div class="card-body">
            <div class="chart-area">
                <canvas id="reservoirB"></canvas>
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
        let shiftChartData = @json($shiftChart);
        $wire.on('post-created', (data) => {
            if (data.shiftChartData) shiftChartData = data.shiftChartData


            // Debit air baku chart
            if (myLineChart1) myLineChart1.destroy();
            var labelsChart = shiftChartData.map(data => data.start_time)
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
                        data: reservoirAchart,
                    }],
                },
                options: {
                    maintainAspectRatio: false,
                },
            });
        });
    </script>
@endscript

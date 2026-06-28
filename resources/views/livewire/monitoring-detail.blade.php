<div>
    <!-- Page Heading -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Record</h1>
        <a class="btn btn-secondary" href="/monitoring-index" wire:navigated type="button">Back</a>
    </div>
    <div class="d-flex justify-content-end mb-4">
        <div class="btn-group" role="group">
            <button type="button" wire:click="$js.copyPdam" class="btn btn-sm btn-info mr-2">
                <i class="fa fa-copy"></i> Copy Report Perumda
            </button>
            <button type="button" wire:click="$js.copyClipboard" class="btn btn-sm btn-success">
                <i class="fa fa-copy"></i> Copy Report Adaro
            </button>
        </div>
    </div>
    

    {{-- Form --}}

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold" style="color: #00664A">Details</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12 font-weight-bold mb-2 text-uppercase">{{ $shifts->shift }}</div>
                <div class="col-md-8 mb-3">
                    <div class="row">
                        <div class="col-5 col-md-4">Date</div>
                        <div class="col-7 col-md-8">: {{ $shifts->date }}</div>
                    </div>

                    <div class="row">
                        <div class="col-5 col-md-4">Operator</div>
                        <div class="col-7 col-md-8">:
                            @foreach ($shifts->shiftOperators as $operator => $index)
                                @if ($operator == 1)
                                    &
                                @endif
                                {{ $shifts->shiftOperators[$operator]->name }}
                            @endforeach
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-5 col-md-4">Time</div>
                        <div class="col-7 col-md-8">: {{ substr($shifts->start_time, 0, 5) }} -
                            {{ substr($shifts->end_time, 0, 5) }}</div>
                    </div>

                    <div class="row">
                        <div class="col-5 col-md-4">Notes</div>
                        <div class="col-7 col-md-8">
                            @if($shifts->notes)
                                @php
                                    $lines = array_filter(array_map('trim', explode("\n", $shifts->notes)));
                                @endphp
                                : - {{ array_shift($lines) }}
                                @foreach($lines as $line)
                                    <div style="padding-left: 8px;">- {{ $line }}</div>
                                @endforeach
                            @else
                                : -
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <hr>

            <div class="row">
                @foreach ($shifts->waterQualities as $waterQuality)
                    <div class="col-md-6">
                        <div class="font-weight-bold mb-2 text-capitalize" style="color: #000000;">{{ $waterQuality->type }}</div>
                        <div class="mb-3">
                            <div class="row">
                                <div class="col-5 col-md-4">Ph</div>
                                <div class="col-7 col-md-8">: {{ $waterQuality->ph }}</div>
                            </div>
                            <div class="row">
                                <div class="col-5 col-md-4">Turbidity</div>
                                <div class="col-7 col-md-8">: {{ number_format($waterQuality->turbidity, 2) }} NTU</div>
                            </div>
                            <div class="row">
                                <div class="col-5 col-md-4">Warna</div>
                                <div class="col-7 col-md-8">: {{ $waterQuality->color }} PCU</div>
                            </div>
                            <div class="row">
                                <div class="col-5 col-md-4">TDS</div>
                                <div class="col-7 col-md-8">: {{ $waterQuality->tds }}</div>
                            </div>

                            @if ($waterQuality->type == 'reservoir')
                                <div class="row">
                                    <div class="col-5 col-md-4">Free Chlor</div>
                                    <div class="col-7 col-md-8">: {{ number_format($waterQuality->free_chlor, 2) }} mg/L</div>
                                </div>
                                <div class="row">
                                    <div class="col-5 col-md-4">ORP</div>
                                    <div class="col-7 col-md-8">: {{ $waterQuality->orp }} mV</div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach

            </div>

            <hr>

            {{-- ========== PREDIKSI WMA ========== --}}
            @if($dataIncomplete)
                {{-- Alert Data Belum Lengkap --}}
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="alert alert-warning alert-dismissible fade show" role="alert" style="border-left: 4px solid #f59e0b;">
                            <h6 class="alert-heading" style="color: #92400e; font-weight: bold; margin-bottom: 1rem;">
                                ⚠️ Analisis Prediksi WMA Tidak ada
                            </h6>
                            <p style="color: #92400e; margin: 0;">Prediksi WMA memerlukan minimal data sebelumnya 2-4 jam dari hari yang sama.</p>
                            <p style="color: #92400e; margin: 0;">Silakan tunggu data 2-4 jam kedepan tercatat untuk melihat analisis.</p>
                        </div>
                    </div>
                </div>
            @else
                {{-- WMA Predictions & Charts --}}
                <div class="row mb-4">
                    <div class="col-12">
                        <h6 class="font-weight-bold" style="color: #00664A; margin-bottom: 5px;">
                            📊 Prediksi Kualitas Air Baku (Weighted Moving Avarage) Pada Jam
                            {{ date('H:i', strtotime(substr($shifts->start_time, 0, 5)) + 7200) }} 
                            - {{ date('H:i', strtotime(substr($shifts->end_time, 0, 5)) + 7200) }}
                        </h6>
                        <small class="text-muted">Prediksi Menggunakan data sekarang dan data 2-4 jam sebelumnya</small>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-4 mb-3">
                        <div class="card border-left-success">
                            <div class="card-body">
                                <h6 class="font-weight-bold margin-bottom: 5px;" style="color: #000000;">Prediksi NTU Air Baku 
                                    <br> 
                                    <small class="text-muted">
                                        Jam
                                        {{ date('H:i', strtotime(substr($shifts->start_time, 0, 5)) + 7200) }} 
                                        - {{ date('H:i', strtotime(substr($shifts->end_time, 0, 5)) + 7200) }}
                                    </small>
                                </h6>
                                <div style="font-size: 28px; font-weight: bold; color: #10b981;">
                                    {{ $wmaData['turbidityAB'] ?? '-' }} NTU
                                </div>
                                <canvas id="chartTurbidity" height="220"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <div class="card border-left-primary">
                            <div class="card-body">
                                <h6 class="text font-weight-bold" style="color: #000000;">Prediksi pH Air Baku
                                    <br> 
                                    <small class="text-muted">
                                        Jam
                                        {{ date('H:i', strtotime(substr($shifts->start_time, 0, 5)) + 7200) }} 
                                        - {{ date('H:i', strtotime(substr($shifts->end_time, 0, 5)) + 7200) }}
                                    </small>
                                </h6>
                                <div style="font-size: 28px; font-weight: bold; color: #3b82f6;">
                                    {{ $wmaData['phAB'] ?? '-' }}
                                </div>
                                <canvas id="chartPH" height="220"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <div class="card border-left-warning">
                            <div class="card-body">
                                <h6 class="text font-weight-bold" style="color: #000000;">Prediksi TDS Air Baku
                                    <br> 
                                    <small class="text-muted">
                                        Jam
                                        {{ date('H:i', strtotime(substr($shifts->start_time, 0, 5)) + 7200) }} 
                                        - {{ date('H:i', strtotime(substr($shifts->end_time, 0, 5)) + 7200) }}
                                    </small>
                                </h6>
                                <div style="font-size: 28px; font-weight: bold; color: #f59e0b;">
                                    {{ $wmaData['tdsAB'] ?? '-' }}
                                </div>
                                <canvas id="chartTDS" height="220"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>

                {{-- ========== CHART.JS SCRIPTS ========== --}}
                <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
                <script>
                    const chartData = {!! json_encode($chartData) !!};
                    const timeLabels = {!! json_encode($timeLabels) !!};

                    // Chart Turbidity (NTU)
                    const ctxTurbidity = document.getElementById('chartTurbidity').getContext('2d');
                    const labels = Array.from({ length: chartData.historicalTurbidity.length + 1 }, (_, i) => 
                        i < chartData.historicalTurbidity.length ? `T-${chartData.historicalTurbidity.length - i}` : 'Prediksi'
                    );

                    new Chart(ctxTurbidity, {
                        type: 'line',
                        data: {
                            labels: timeLabels,
                            datasets: [{
                                label: 'NTU Air Baku',
                                data: [...chartData.historicalTurbidity, chartData.wmaPredictions.turbidityAB],
                                borderColor: '#10b981',
                                backgroundColor: '#10b9810d',
                                borderWidth: 2,
                                fill: true,
                                pointRadius: 6,
                                pointBackgroundColor: function(context) {
                                    return context.dataIndex === chartData.historicalTurbidity.length ? '#ff0000' : '#10b981';
                                },
                                pointBorderColor: '#ffffff00',
                                pointBorderWidth: 2,
                                tension: 0.2,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            plugins: {
                                legend: { display: false }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    grid: { color: 'rgba(0, 0, 0, 0.05)' }
                                }
                            }
                        }
                    });

                    // Chart pH
                    const ctxPH = document.getElementById('chartPH').getContext('2d');
                    new Chart(ctxPH, {
                        type: 'line',
                        data: {
                            labels: timeLabels,
                            datasets: [{
                                label: 'pH Air Baku',
                                data: [...chartData.historicalPH, chartData.wmaPredictions.phAB],
                                borderColor: '#3b82f6',
                                backgroundColor: '#1032b910',
                                borderWidth: 2,
                                fill: true,
                                pointRadius: 6,
                                pointBackgroundColor: function(context) {
                                    return context.dataIndex === chartData.historicalPH.length ? '#ff0000' : '#3b82f6';
                                },
                                pointBorderColor: '#ffffff00',
                                pointBorderWidth: 2,
                                tension: 0.2,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            plugins: {
                                legend: { display: false }
                            },
                            scales: {
                                y: {
                                    beginAtZero: false,
                                    min: 5.5,
                                    max: 8,
                                    grid: { color: 'rgba(0, 0, 0, 0.05)' }
                                }
                            }
                        }
                    });

                    // Chart TDS
                    const ctxTDS = document.getElementById('chartTDS').getContext('2d');
                    new Chart(ctxTDS, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'TDS Air Baku',
                                data: [...chartData.historicalTDS, chartData.wmaPredictions.tdsAB],
                                borderColor: '#f59e0b',
                                backgroundColor: '#f59f0b0b',
                                borderWidth: 2,
                                fill: true,
                                pointRadius: 6,
                                pointBackgroundColor: function(context) {
                                    return context.dataIndex === chartData.historicalTDS.length ? '#ff0000' : '#f59e0b';
                                },
                                pointBorderColor: '#ffffff00',
                                pointBorderWidth: 2,
                                tension: 0.2,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            plugins: {
                                legend: { display: false }
                            },
                            scales: {
                                y: {
                                    beginAtZero: false,
                                    grid: { color: 'rgba(0, 0, 0, 0.05)' }
                                }
                            }
                        }
                    });
                </script>
            @endif

            {{-- ========== REKOMENDASI FUZZY MAMDANI (SELALU TAMPIL) ========== --}}
            <div class="row mb-4">
                <div class="col-12">
                    <h6 class="font-weight-bold" style="color: #00664A; margin-bottom: 5px;">
                        💊 Rekomendasi Dosis Bahan Kimia (Fuzzy Mamdani) Untuk Jam
                        {{ date('H:i', strtotime(substr($shifts->start_time, 0, 5)) + 7200) }} 
                        - {{ date('H:i', strtotime(substr($shifts->end_time, 0, 5)) + 7200) }}
                    </h6>
                    <small class="text-muted">Rekomendasi Bedasarkan Data Sekarang Dan Regulasi Target Produksi Perusahaan</small>
                </div>
            </div>

            <div class="row">
                {{-- PAC --}}
                <div class="col-md-4 mb-3">
                    <div class="card border-left-{{ $sdsRecommendations['pac']['color'] ?? 'secondary' }}">
                        <div class="card-body">
                            <h6 class="font-weight-bold" style="color: #000000;">🧴 PAC (Polyaluminum Chloride)</h6>
                            <small class="text-muted">Berdasarkan Turbidity Air Sedimentasi</small>

                            <div style="margin-top: 12px;">
                                <div class="alert alert-{{ $sdsRecommendations['pac']['color'] ?? 'secondary' }}" role="alert" style="margin-bottom: 10px;">
                                    <strong>{{ $sdsRecommendations['pac']['status'] ?? '-' }}</strong>
                                </div>
                                <div style="font-size: 20px; font-weight: bold; color: #1e293b; margin-bottom:5px;">
                                    Rekomendasi: <span style="color: #f59e0b;">{{ $sdsRecommendations['pac']['recommendation'] ?? '-' }} ppm</span>
                                </div>
                                <div style="line-height: 1 !important;">
                                    <small>{!! $sdsRecommendations['pac']['message'] ?? '-' !!}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- KLORIN --}}
                <div class="col-md-4 mb-3">
                    <div class="card border-left-{{ $sdsRecommendations['klorin']['color'] ?? 'secondary' }}">
                        <div class="card-body">
                            <h6 class="font-weight-bold" style="color: #000000;">🧪 Klorin</h6>
                            <small class="text-muted">Berdasarkan Free Chlorine Reservoir</small>

                            <div style="margin-top: 12px;">
                                <div class="alert alert-{{ $sdsRecommendations['klorin']['color'] ?? 'secondary' }}" role="alert" style="margin-bottom: 10px;">
                                    <strong>{{ $sdsRecommendations['klorin']['status'] ?? '-' }}</strong>
                                </div>
                                <div style="font-size: 20px; font-weight: bold; color: #1e293b; margin-bottom:5px;">
                                    Rekomendasi: <span style="color: #3b82f6;">{{ $sdsRecommendations['klorin']['recommendation'] ?? '-' }} ppm</span>
                                </div>
                                <div style="line-height: 1 !important;">
                                    <small>{!! $sdsRecommendations['klorin']['message'] ?? '-' !!}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SODA ASH --}}
                <div class="col-md-4 mb-3">
                    <div class="card border-left-{{ $sdsRecommendations['sodaAsh']['color'] ?? 'secondary' }}">
                        <div class="card-body">
                            <h6 class="font-weight-bold" style="color: #000000;">⚗️ Soda Ash</h6>
                            <small class="text-muted">Berdasarkan pH Reservoir</small>

                            <div style="margin-top: 12px;">
                                <div class="alert alert-{{ $sdsRecommendations['sodaAsh']['color'] ?? 'secondary' }}" role="alert" style="margin-bottom: 10px;">
                                    <strong>{{ $sdsRecommendations['sodaAsh']['status'] ?? '-' }}</strong>
                                </div>
                                <div style="font-size: 20px; font-weight: bold; color: #1e293b;">
                                    Rekomendasi: <span style="color: #10b981;">{{ $sdsRecommendations['sodaAsh']['recommendation'] ?? '-' }} ppm</span>
                                </div>
                                <div style="line-height: 1 !important;">
                                    <small>{!! $sdsRecommendations['sodaAsh']['message'] ?? '-' !!}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <hr>

            <div class="row">
                <!-- FLOWMETER INTAKE (AIR BAKU) -->
                @php
                    $flowIntake = $shifts->flowMeters->firstWhere('location', null);
                @endphp
                @if ($flowIntake)
                    <div class="col-md-6">
                        <div class="font-weight-bold mb-2 text-capitalize" style="color: #000000;">Flowmeter Intake</div>
                        <div class="mb-3">
                            <div class="row">
                                <div class="col-5 col-md-4">Flow</div>
                                <div class="col-7 col-md-8">: {{ $flowIntake->flow }} L/s</div>
                            </div>
                            <div class="row align-items-center">
                                <div class="col-5 col-md-4">Totalizer</div>
                                <div class="col-7 col-md-8 d-flex align-items-center gap-2">
                                    : {{ $flowIntake->totalizer }} m³
                                    <button onclick="copyTotalizer(this, '{{ $flowIntake->totalizer }}')" class="btn btn-sm btn-link py-0 px-1 ml-1" title="Copy">
                                        <i class="fa fa-copy"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- FLOWMETER DISTRIBUSI HEADER -->
                @if ($shifts->flowMeters->whereNotNull('location')->count() > 0)
                    <div class="col-md-12">
                        <div class="font-weight-bold mb-2 text-capitalize" style="color: #000000;">Flowmeter Distribusi</div>
                    </div>
                @endif

                <!-- FLOWMETER YOS SUDARSO -->
                @php
                    $flowSudarso = $shifts->flowMeters->firstWhere('location', 'yos sudarso');
                @endphp
                @if ($flowSudarso)
                    <div class="col-md-6">
                        <div class="font-weight-bold mb-2 text-capitalize">Yos Sudarso</div>
                        <div class="mb-3">
                            <div class="row">
                                <div class="col-5 col-md-4">Flow</div>
                                <div class="col-7 col-md-8">: {{ $flowSudarso->flow }} L/s</div>
                            </div>
                            <div class="row align-items-center">
                                <div class="col-5 col-md-4">Totalizer</div>
                                <div class="col-7 col-md-8 d-flex align-items-center gap-2">
                                    : {{ $flowSudarso->totalizer }} m³
                                    <button onclick="copyTotalizer(this, '{{ $flowSudarso->totalizer }}')" class="btn btn-sm btn-link py-0 px-1 ml-1" title="Copy">
                                        <i class="fa fa-copy"></i>
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>
                @endif

                <!-- FLOWMETER VETERAN -->
                @php
                    $flowVeteran = $shifts->flowMeters->firstWhere('location', 'veteran');
                @endphp
                @if ($flowVeteran)
                    <div class="col-md-6">
                        <div class="font-weight-bold mb-2 text-capitalize">Veteran</div>
                        <div class="mb-3">
                            <div class="row">
                                <div class="col-5 col-md-4">Flow</div>
                                <div class="col-7 col-md-8">: {{ $flowVeteran->flow }} L/s</div>
                            </div>
                            <div class="row align-items-center">
                                <div class="col-5 col-md-4">Totalizer</div>
                                <div class="col-7 col-md-8 d-flex align-items-center gap-2">
                                    : {{ $flowVeteran->totalizer }} m³
                                    <button onclick="copyTotalizer(this, '{{ $flowVeteran->totalizer }}')" class="btn btn-sm btn-link py-0 px-1 ml-1" title="Copy">
                                        <i class="fa fa-copy"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <hr>

            <div class="row">
                <div class="col-md-6">
                    <div class="font-weight-bold mb-2 text-capitalize" style="color: #000000;">Level Reservoir</div>
                    <div class="mb-3">
                        <div class="row align-items-center">
                            <div class="col-5 col-md-4">Reservoir A</div>
                            <div class="col-7 col-md-8 d-flex align-items-center">
                                : {{ number_format($shifts->reservoirLevels->level_a, 2) }} m
                                <button onclick="copyTotalizer(this, '{{ number_format($shifts->reservoirLevels->level_a, 2) }}')" class="btn btn-sm btn-link py-0 px-1 ml-1" title="Copy">
                                    <i class="fa fa-copy"></i>
                                </button>
                            </div>
                        </div>
                        <div class="row align-items-center">
                            <div class="col-5 col-md-4">Reservoir B</div>
                            <div class="col-7 col-md-8 d-flex align-items-center">
                                : {{ number_format($shifts->reservoirLevels->level_b, 2) }} m
                                <button onclick="copyTotalizer(this, '{{ number_format($shifts->reservoirLevels->level_b, 2) }}')" class="btn btn-sm btn-link py-0 px-1 ml-1" title="Copy">
                                    <i class="fa fa-copy"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <hr>

            <div class="row">
                <div class="col-md-6">
                    <div class="font-weight-bold mb-2 text-capitalize" style="color: #000000;">In Comer MDP Panel</div>
                    <div>
                        <div class="row">
                            <div class="col-5 col-md-4">Total Kwh</div>
                            <div class="col-7 col-md-8">: {{ number_format($shifts->mdpPanels->kwh_total, 2, '.', '') }} kwh</div>
                        </div>
                        <div class="row">
                            <div class="col-5 col-md-4">Wbp</div>
                            <div class="col-7 col-md-8">: {{ number_format($shifts->mdpPanels->wdp, 2, '.', '') }}</div>
                        </div>
                        <div class="row">
                            <div class="col-5 col-md-4">Lwbp</div>
                            <div class="col-7 col-md-8">: {{ number_format($shifts->mdpPanels->lwbp, 2, '.', '') }}</div>
                        </div>
                        <div class="row">
                            <div class="col-5 col-md-4">Kvar</div>
                            <div class="col-7 col-md-8">: {{ number_format($shifts->mdpPanels->kvar, 2, '.', '') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <hr>

            <div class="row">
                <div class="col-md-6">
                    <div>
                        <div class="row">
                            <div class="col-5 col-md-4">Level Air Bak Pengumpul</div>
                            <div class="col-7 col-md-8">: {{ $shifts->collection_tank }} m</div>
                        </div>
                    </div>
                </div>
            </div>

            <hr>

            <div class="row">
                <div class="col-md-6">
                    <div class="font-weight-bold mb-2 text-capitalize" style="color: #000000;">⁠Pressure Static Mixer</div>
                    <div class="mb-3">
                        <div class="row">
                            <div class="col-5 col-md-4">Inlet</div>
                            <div class="col-7 col-md-8">: {{ $shifts->pressureStaticMixer->inlet }} Bar</div>
                        </div>
                        <div class="row">
                            <div class="col-5 col-md-4">Outlet</div>
                            <div class="col-7 col-md-8">: {{ $shifts->pressureStaticMixer->outlet }} Bar</div>
                        </div>
                    </div>
                </div>
            </div>

        <hr>

        <div class="row">
            <!-- POMPA INTAKE HEADER -->
            <div class="col-md-12">
                <div class="font-weight-bold mb-2 text-capitalize" style="color: #000000;">Pompa Intake</div>
            </div>

            <!-- POMPA INTAKE A -->
            @php
                $pumpIntakeA = $shifts->pumpProccess->firstWhere('type', 'intake a');
            @endphp
            @if ($pumpIntakeA)
                <div class="col-md-6">
                    <div class="font-weight-bold mb-2 text-capitalize">Pompa Intake A</div>
                    <div class="mb-3">
                        <div class="row">
                            <div class="col-5 col-md-4">Status</div>
                            <div class="col-7 col-md-8">
                                @if ($pumpIntakeA->status == 'running')
                                    <h6 style="margin-bottom: 0px;">:<span class="badge badge-success ml-1">Running</span></h6>
                                @elseif ($pumpIntakeA->status == 'standby')
                                    <h6>:<span class="badge badge-info ml-1">Standby</span></h6>
                                @else
                                    <h6>:<span class="badge badge-warning ml-1">Maintenance</span></h6>
                                @endif
                            </div>
                        </div>
                        @if ($pumpIntakeA->status == 'running')
                            <div class="row">
                                <div class="col-5 col-md-4">Ampere</div>
                                <div class="col-7 col-md-8">: {{ $pumpIntakeA->ampere }} A</div>
                            </div>
                            <div class="row">
                                <div class="col-5 col-md-4">Frekuensi</div>
                                <div class="col-7 col-md-8">: {{ $pumpIntakeA->frequency }} Hz</div>
                            </div>
                            <div class="row">
                                <div class="col-5 col-md-4">Pressure</div>
                                <div class="col-7 col-md-8">: {{ $pumpIntakeA->pressure }} Bar</div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- POMPA INTAKE B -->
            @php
                $pumpIntakeB = $shifts->pumpProccess->firstWhere('type', 'intake b');
            @endphp
            @if ($pumpIntakeB)
                <div class="col-md-6">
                    <div class="font-weight-bold mb-2 text-capitalize">Pompa Intake B</div>
                    <div class="mb-3">
                        <div class="row">
                            <div class="col-5 col-md-4">Status</div>
                            <div class="col-7 col-md-8">
                                @if ($pumpIntakeB->status == 'running')
                                    <h6 style="margin-bottom: 0px;">:<span class="badge badge-success ml-1">Running</span></h6>
                                @elseif ($pumpIntakeB->status == 'standby')
                                    <h6>:<span class="badge badge-info ml-1">Standby</span></h6>
                                @else
                                    <h6>:<span class="badge badge-warning ml-1">Maintenance</span></h6>
                                @endif
                            </div>
                        </div>
                        @if ($pumpIntakeB->status == 'running')
                            <div class="row">
                                <div class="col-5 col-md-4">Ampere</div>
                                <div class="col-7 col-md-8">: {{ $pumpIntakeB->ampere }} A</div>
                            </div>
                            <div class="row">
                                <div class="col-5 col-md-4">Frekuensi</div>
                                <div class="col-7 col-md-8">: {{ $pumpIntakeB->frequency }} Hz</div>
                            </div>
                            <div class="row">
                                <div class="col-5 col-md-4">Pressure</div>
                                <div class="col-7 col-md-8">: {{ $pumpIntakeB->pressure }} Bar</div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- POMPA INTAKE C -->
            @php
                $pumpIntakeC = $shifts->pumpProccess->firstWhere('type', 'intake c');
            @endphp
            @if ($pumpIntakeC)
                <div class="col-md-6">
                    <div class="font-weight-bold mb-2 text-capitalize">Pompa Intake C</div>
                    <div class="mb-3">
                        <div class="row">
                            <div class="col-5 col-md-4">Status</div>
                            <div class="col-7 col-md-8">
                                @if ($pumpIntakeC->status == 'running')
                                    <h6 style="margin-bottom: 0px;">:<span class="badge badge-success ml-1">Running</span></h6>
                                @elseif ($pumpIntakeC->status == 'standby')
                                    <h6>:<span class="badge badge-info ml-1">Standby</span></h6>
                                @else
                                    <h6>:<span class="badge badge-warning ml-1">Maintenance</span></h6>
                                @endif
                            </div>
                        </div>
                        @if ($pumpIntakeC->status == 'running')
                            <div class="row">
                                <div class="col-5 col-md-4">Ampere</div>
                                <div class="col-7 col-md-8">: {{ $pumpIntakeC->ampere }} A</div>
                            </div>
                            <div class="row">
                                <div class="col-5 col-md-4">Frekuensi</div>
                                <div class="col-7 col-md-8">: {{ $pumpIntakeC->frequency }} Hz</div>
                            </div>
                            <div class="row">
                                <div class="col-5 col-md-4">Pressure</div>
                                <div class="col-7 col-md-8">: {{ $pumpIntakeC->pressure }} Bar</div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- POMPA DISTRIBUSI HEADER -->
            <div class="col-md-12">
                <hr>
                <div class="font-weight-bold mb-2 text-capitalize" style="color: #000000;">Pompa Distribusi</div>
            </div>

            <!-- POMPA DISTRIBUSI A -->
            @php
                $pumpDistriA = $shifts->pumpProccess->firstWhere('type', 'distribusi a');
            @endphp
            @if ($pumpDistriA)
                <div class="col-md-6">
                    <div class="font-weight-bold mb-2 text-capitalize">Pompa Distribusi A</div>
                    <div class="mb-3">
                        <div class="row">
                            <div class="col-5 col-md-4">Status</div>
                            <div class="col-7 col-md-8">
                                @if ($pumpDistriA->status == 'running')
                                    <h6 style="margin-bottom: 0px;">:<span class="badge badge-success ml-1">Running</span></h6>
                                @elseif ($pumpDistriA->status == 'standby')
                                    <h6>:<span class="badge badge-info ml-1">Standby</span></h6>
                                @else
                                    <h6>:<span class="badge badge-warning ml-1">Maintenance</span></h6>
                                @endif
                            </div>
                        </div>
                        @if ($pumpDistriA->status == 'running')
                            <div class="row">
                                <div class="col-5 col-md-4">Ampere</div>
                                <div class="col-7 col-md-8">: {{ $pumpDistriA->ampere }} A</div>
                            </div>
                            <div class="row">
                                <div class="col-5 col-md-4">Frekuensi</div>
                                <div class="col-7 col-md-8">: {{ $pumpDistriA->frequency }} Hz</div>
                            </div>
                            <div class="row">
                                <div class="col-5 col-md-4">Pressure</div>
                                <div class="col-7 col-md-8">: {{ $pumpDistriA->pressure }} Bar</div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- POMPA DISTRIBUSI B -->
            @php
                $pumpDistriB = $shifts->pumpProccess->firstWhere('type', 'distribusi b');
            @endphp
            @if ($pumpDistriB)
                <div class="col-md-6">
                    <div class="font-weight-bold mb-2 text-capitalize">Pompa Distribusi B</div>
                    <div class="mb-3">
                        <div class="row">
                            <div class="col-5 col-md-4">Status</div>
                            <div class="col-7 col-md-8">
                                @if ($pumpDistriB->status == 'running')
                                    <h6 style="margin-bottom: 0px;">:<span class="badge badge-success ml-1">Running</span></h6>
                                @elseif ($pumpDistriB->status == 'standby')
                                    <h6>:<span class="badge badge-info ml-1">Standby</span></h6>
                                @else
                                    <h6>:<span class="badge badge-warning ml-1">Maintenance</span></h6>
                                @endif
                            </div>
                        </div>
                        @if ($pumpDistriB->status == 'running')
                            <div class="row">
                                <div class="col-5 col-md-4">Ampere</div>
                                <div class="col-7 col-md-8">: {{ $pumpDistriB->ampere }} A</div>
                            </div>
                            <div class="row">
                                <div class="col-5 col-md-4">Frekuensi</div>
                                <div class="col-7 col-md-8">: {{ $pumpDistriB->frequency }} Hz</div>
                            </div>
                            <div class="row">
                                <div class="col-5 col-md-4">Pressure</div>
                                <div class="col-7 col-md-8">: {{ $pumpDistriB->pressure }} Bar</div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- POMPA DISTRIBUSI C -->
            @php
                $pumpDistriC = $shifts->pumpProccess->firstWhere('type', 'distribusi c');
            @endphp
            @if ($pumpDistriC)
                <div class="col-md-6">
                    <div class="font-weight-bold mb-2 text-capitalize">Pompa Distribusi C</div>
                    <div class="mb-3">
                        <div class="row">
                            <div class="col-5 col-md-4">Status</div>
                            <div class="col-7 col-md-8">
                                @if ($pumpDistriC->status == 'running')
                                    <h6 style="margin-bottom: 0px;">:<span class="badge badge-success ml-1">Running</span></h6>
                                @elseif ($pumpDistriC->status == 'standby')
                                    <h6>:<span class="badge badge-info ml-1">Standby</span></h6>
                                @else
                                    <h6>:<span class="badge badge-warning ml-1">Maintenance</span></h6>
                                @endif
                            </div>
                        </div>
                        @if ($pumpDistriC->status == 'running')
                            <div class="row">
                                <div class="col-5 col-md-4">Ampere</div>
                                <div class="col-7 col-md-8">: {{ $pumpDistriC->ampere }} A</div>
                            </div>
                            <div class="row">
                                <div class="col-5 col-md-4">Frekuensi</div>
                                <div class="col-7 col-md-8">: {{ $pumpDistriC->frequency }} Hz</div>
                            </div>
                            <div class="row">
                                <div class="col-5 col-md-4">Pressure</div>
                                <div class="col-7 col-md-8">: {{ $pumpDistriC->pressure }} Bar</div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- POMPA DISTRIBUSI D -->
            @php
                $pumpDistriD = $shifts->pumpProccess->firstWhere('type', 'distribusi d');
            @endphp
            @if ($pumpDistriD)
                <div class="col-md-6">
                    <div class="font-weight-bold mb-2 text-capitalize">Pompa Distribusi D</div>
                    <div class="mb-3">
                        <div class="row">
                            <div class="col-5 col-md-4">Status</div>
                            <div class="col-7 col-md-8">
                                @if ($pumpDistriD->status == 'running')
                                    <h6 style="margin-bottom: 0px;">:<span class="badge badge-success ml-1">Running</span></h6>
                                @elseif ($pumpDistriD->status == 'standby')
                                    <h6>:<span class="badge badge-info ml-1">Standby</span></h6>
                                @else
                                    <h6>:<span class="badge badge-warning ml-1">Maintenance</span></h6>
                                @endif
                            </div>
                        </div>
                        @if ($pumpDistriD->status == 'running')
                            <div class="row">
                                <div class="col-5 col-md-4">Ampere</div>
                                <div class="col-7 col-md-8">: {{ $pumpDistriD->ampere }} A</div>
                            </div>
                            <div class="row">
                                <div class="col-5 col-md-4">Frekuensi</div>
                                <div class="col-7 col-md-8">: {{ $pumpDistriD->frequency }} Hz</div>
                            </div>
                            <div class="row">
                                <div class="col-5 col-md-4">Pressure</div>
                                <div class="col-7 col-md-8">: {{ $pumpDistriD->pressure }} Bar</div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

            <hr>

            {{-- ========== POMPA DOSING PAC ========== --}}
            <div class="row mb-3">
                <div class="col-12">
                    <div class="font-weight-bold mb-2 text-capitalize" style="color: #000000;">Pompa Dosing PAC</div>
                </div>
                
                @php
                    $pacA = $shifts->pumpChemicals->where('type', 'pac')->where('pump_unit', 'A')->first();
                    $pacB = $shifts->pumpChemicals->where('type', 'pac')->where('pump_unit', 'B')->first();
                @endphp
                
                {{-- Pompa A --}}
                <div class="col-md-6">
                    <div class="font-weight-bold mb-2">Pompa PAC A</div>
                    <div class="mb-3">
                        @if($pacA)
                            {{-- Status --}}
                            <div class="row">
                                <div class="col-5 col-md-4">Status</div>
                                <div class="col-7 col-md-8 d-flex">:
                                    @if ($pacA->status == 'running')
                                        <h6><span class="badge badge-success ml-1">Running</span></h6>
                                    @elseif ($pacA->status == 'standby')
                                        <h6><span class="badge badge-info ml-1">Standby</span></h6>
                                    @else
                                        <h6><span class="badge badge-warning ml-1">Maintenance</span></h6>
                                    @endif
                                </div>
                            </div>

                            {{-- Detail Fields (hanya tampil kalau Running) --}}
                            @if($pacA->status == 'running')
                                <div class="row">
                                    <div class="col-5 col-md-4">Frekuensi</div>
                                    <div class="col-7 col-md-8">: {{ $pacA->frequency }} Hz</div>
                                </div>
                                <div class="row">
                                    <div class="col-5 col-md-4">Konsentrasi</div>
                                    <div class="col-7 col-md-8">: {{ $pacA->concentration }} %</div>
                                </div>
                                <div class="row">
                                    <div class="col-5 col-md-4">Dosis</div>
                                    <div class="col-7 col-md-8">: {{ $pacA->dosage }} ppm</div>
                                </div>
                                <div class="row">
                                    <div class="col-5 col-md-4">Level Tangki</div>
                                    <div class="col-7 col-md-8">: {{ $pacA->tank_level }} cm</div>
                                </div>
                            @endif
                        @else
                            <p><span class="badge badge-info">Standby</span></p>
                        @endif
                    </div>
                </div>
                
                {{-- Pompa B --}}
                <div class="col-md-6">
                    <div class="font-weight-bold mb-2">Pompa PAC B</div>
                    <div class="mb-3">
                        @if($pacB)
                            {{-- Status --}}
                            <div class="row">
                                <div class="col-5 col-md-4">Status</div>
                                <div class="col-7 col-md-8 d-flex">:
                                    @if ($pacB->status == 'running')
                                        <h6><span class="badge badge-success ml-1">Running</span></h6>
                                    @elseif ($pacB->status == 'standby')
                                        <h6><span class="badge badge-info ml-1">Standby</span></h6>
                                    @else
                                        <h6><span class="badge badge-warning ml-1">Maintenance</span></h6>
                                    @endif
                                </div>
                            </div>

                            {{-- Detail Fields (hanya tampil kalau Running) --}}
                            @if($pacB->status == 'running')
                                <div class="row">
                                    <div class="col-5 col-md-4">Frekuensi</div>
                                    <div class="col-7 col-md-8">: {{ $pacB->frequency }} Hz</div>
                                </div>
                                <div class="row">
                                    <div class="col-5 col-md-4">Konsentrasi</div>
                                    <div class="col-7 col-md-8">: {{ $pacB->concentration }} %</div>
                                </div>
                                <div class="row">
                                    <div class="col-5 col-md-4">Dosis</div>
                                    <div class="col-7 col-md-8">: {{ $pacB->dosage }} ppm</div>
                                </div>
                                <div class="row">
                                    <div class="col-5 col-md-4">Level Tangki</div>
                                    <div class="col-7 col-md-8">: {{ $pacB->tank_level }} cm</div>
                                </div>
                            @endif
                        @else
                            <p><span class="badge badge-info">Standby</span></p>
                        @endif
                    </div>
                </div>
            </div>

            <hr>

            {{-- ========== POMPA DOSING CHLORINE/KAPORIT ========== --}}
            <div class="row mb-3">
                <div class="col-12">
                    <div class="font-weight-bold mb-2 text-capitalize" style="color: #000000;">⁠Pompa Dosing Chlorine</div>
                </div>
                
                @php
                    $chlorA = $shifts->pumpChemicals->where('type', 'chlorine/kaporit')->where('pump_unit', 'A')->first();
                    $chlorB = $shifts->pumpChemicals->where('type', 'chlorine/kaporit')->where('pump_unit', 'B')->first();
                @endphp
                
                {{-- Pompa A --}}
                <div class="col-md-6">
                    <div class="font-weight-bold mb-2">Pompa Chlorine A</div>
                    <div class="mb-3">
                        @if($chlorA)
                            {{-- Status --}}
                            <div class="row">
                                <div class="col-5 col-md-4">Status</div>
                                <div class="col-7 col-md-8 d-flex">:
                                    @if ($chlorA->status == 'running')
                                        <h6 style="margin-bottom: 0px;"><span class="badge badge-success ml-1">Running</span></h6>
                                    @elseif ($chlorA->status == 'standby')
                                        <h6><span class="badge badge-info ml-1">Standby</span></h6>
                                    @else
                                        <h6><span class="badge badge-warning ml-1">Maintenance</span></h6>
                                    @endif
                                </div>
                            </div>

                            {{-- Detail Fields (hanya tampil kalau Running) --}}
                            @if($chlorA->status == 'running')
                                <div class="row">
                                    <div class="col-5 col-md-4">Flow Rate</div>
                                    <div class="col-7 col-md-8">: {{ $chlorA->flow_rate }} l/h</div>
                                </div>
                                <div class="row">
                                    <div class="col-5 col-md-4">Konsentrasi</div>
                                    <div class="col-7 col-md-8">: {{ $chlorA->concentration }} %</div>
                                </div>
                                <div class="row">
                                    <div class="col-5 col-md-4">Dosis</div>
                                    <div class="col-7 col-md-8">: {{ $chlorA->dosage }} ppm</div>
                                </div>
                                <div class="row">
                                    <div class="col-5 col-md-4">Level Tangki</div>
                                    <div class="col-7 col-md-8">: {{ $chlorA->tank_level }} cm</div>
                                </div>
                            @endif
                        @else
                            <p><span class="badge badge-info">Standby</span></p>
                        @endif
                    </div>
                </div>
                
                {{-- Pompa B --}}
                <div class="col-md-6">
                    <div class="font-weight-bold mb-2">Pompa Chlorine B</div>
                    <div class="mb-3">
                        @if($chlorB)
                            {{-- Status --}}
                            <div class="row">
                                <div class="col-5 col-md-4">Status</div>
                                <div class="col-7 col-md-8 d-flex">:
                                    @if ($chlorB->status == 'running')
                                        <h6 style="margin-bottom: 0px;"><span class="badge badge-success ml-1">Running</span></h6>
                                    @elseif ($chlorB->status == 'standby')
                                        <h6><span class="badge badge-info ml-1">Standby</span></h6>
                                    @else
                                        <h6><span class="badge badge-warning ml-1">Maintenance</span></h6>
                                    @endif
                                </div>
                            </div>

                            {{-- Detail Fields (hanya tampil kalau Running) --}}
                            @if($chlorB->status == 'running')
                                <div class="row">
                                    <div class="col-5 col-md-4">Flow Rate</div>
                                    <div class="col-7 col-md-8">: {{ $chlorB->flow_rate }} l/h</div>
                                </div>
                                <div class="row">
                                    <div class="col-5 col-md-4">Konsentrasi</div>
                                    <div class="col-7 col-md-8">: {{ $chlorB->concentration }} %</div>
                                </div>
                                <div class="row">
                                    <div class="col-5 col-md-4">Dosis</div>
                                    <div class="col-7 col-md-8">: {{ $chlorB->dosage }} ppm</div>
                                </div>
                                <div class="row">
                                    <div class="col-5 col-md-4">Level Tangki</div>
                                    <div class="col-7 col-md-8">: {{ $chlorB->tank_level }} cm</div>
                                </div>
                            @endif
                        @else
                            <p><span class="badge badge-info">Standby</span></p>
                        @endif
                    </div>
                </div>
            </div>

            <hr>

            {{-- ========== POMPA DOSING SODA ASH ========== --}}
            <div class="row mb-3">
                <div class="col-12">
                    <div class="font-weight-bold mb-2 text-capitalize" style="color: #000000;">Pompa Dosing Soda Ash</div>
                </div>
                
                @php
                    $sodaA = $shifts->pumpChemicals->where('type', 'soda ash')->where('pump_unit', 'A')->first();
                    $sodaB = $shifts->pumpChemicals->where('type', 'soda ash')->where('pump_unit', 'B')->first();
                @endphp
                
                {{-- Pompa A --}}
                <div class="col-md-6">
                    <div class="font-weight-bold mb-2">Pompa Soda Ash A</div>
                    <div class="mb-3">
                        @if($sodaA)
                            {{-- Status --}}
                            <div class="row">
                                <div class="col-5 col-md-4">Status</div>
                                <div class="col-7 col-md-8 d-flex">:
                                    @if ($sodaA->status == 'running')
                                        <h6 style="margin-bottom: 0px;"><span class="badge badge-success ml-1">Running</span></h6>
                                    @elseif ($sodaA->status == 'standby')
                                        <h6><span class="badge badge-info ml-1">Standby</span></h6>
                                    @else
                                        <h6><span class="badge badge-warning ml-1">Maintenance</span></h6>
                                    @endif
                                </div>
                            </div>

                            {{-- Detail Fields (hanya tampil kalau Running) --}}
                            @if($sodaA->status == 'running')
                                <div class="row">
                                    <div class="col-5 col-md-4">Flow Rate</div>
                                    <div class="col-7 col-md-8">: {{ $sodaA->flow_rate }} %</div>
                                </div>
                                <div class="row">
                                    <div class="col-5 col-md-4">Konsentrasi</div>
                                    <div class="col-7 col-md-8">: {{ $sodaA->concentration }} %</div>
                                </div>
                                <div class="row">
                                    <div class="col-5 col-md-4">Dosis</div>
                                    <div class="col-7 col-md-8">: {{ $sodaA->dosage }} ppm</div>
                                </div>
                                <div class="row">
                                    <div class="col-5 col-md-4">Level Tangki</div>
                                    <div class="col-7 col-md-8">: {{ $sodaA->tank_level }} cm</div>
                                </div>
                            @endif
                        @else
                            <p><span class="badge badge-info">Standby</span></p>
                        @endif
                    </div>
                </div>
                
                {{-- Pompa B --}}
                <div class="col-md-6">
                    <div class="font-weight-bold mb-2">Pompa Soda Ash B</div>
                    <div class="mb-3">
                        @if($sodaB)
                            {{-- Status --}}
                            <div class="row">
                                <div class="col-5 col-md-4">Status</div>
                                <div class="col-7 col-md-8 d-flex">:
                                    @if ($sodaB->status == 'running')
                                        <h6 style="margin-bottom: 0px;"><span class="badge badge-success ml-1">Running</span></h6>
                                    @elseif ($sodaB->status == 'standby')
                                        <h6><span class="badge badge-info ml-1">Standby</span></h6>
                                    @else
                                        <h6><span class="badge badge-warning ml-1">Maintenance</span></h6>
                                    @endif
                                </div>
                            </div>

                            {{-- Detail Fields (hanya tampil kalau Running) --}}
                            @if($sodaB->status == 'running')
                                <div class="row">
                                    <div class="col-5 col-md-4">Flow Rate</div>
                                    <div class="col-7 col-md-8">: {{ $sodaB->flow_rate }} %</div>
                                </div>
                                <div class="row">
                                    <div class="col-5 col-md-4">Konsentrasi</div>
                                    <div class="col-7 col-md-8">: {{ $sodaB->concentration }} %</div>
                                </div>
                                <div class="row">
                                    <div class="col-5 col-md-4">Dosis</div>
                                    <div class="col-7 col-md-8">: {{ $sodaB->dosage }} ppm</div>
                                </div>
                                <div class="row">
                                    <div class="col-5 col-md-4">Level Tangki</div>
                                    <div class="col-7 col-md-8">: {{ $sodaB->tank_level }} cm</div>
                                </div>
                            @endif
                        @else
                            <p><span class="badge badge-info">Standby</span></p>
                        @endif
                    </div>
                </div>
            </div>

            <hr>

            {{-- ========== POMPA DOSING POLYMER ========== --}}
            <div class="row mb-3">
                <div class="col-12">
                    <div class="font-weight-bold mb-2 text-capitalize" style="color: #000000;">Pompa Dosing Polymer</div>
                </div>
                
                @php
                    $polyA = $shifts->pumpChemicals->where('type', 'polymer')->where('pump_unit', 'A')->first();
                    $polyB = $shifts->pumpChemicals->where('type', 'polymer')->where('pump_unit', 'B')->first();
                @endphp
                
                {{-- Pompa A --}}
                <div class="col-md-6">
                    <div class="font-weight-bold mb-2">Pompa Polymer A</div>
                    <div class="mb-3">
                        @if($polyA)
                            {{-- Status --}}
                            <div class="row">
                                <div class="col-5 col-md-4">Status</div>
                                <div class="col-7 col-md-8 d-flex">:
                                    @if ($polyA->status == 'running')
                                        <h6 style="margin-bottom: 0px;"><span class="badge badge-success ml-1">Running</span></h6>
                                    @elseif ($polyA->status == 'standby')
                                        <h6><span class="badge badge-info ml-1">Standby</span></h6>
                                    @else
                                        <h6><span class="badge badge-warning ml-1">Maintenance</span></h6>
                                    @endif
                                </div>
                            </div>

                            {{-- Detail Fields (hanya tampil kalau Running) --}}
                            @if($polyA->status == 'running')
                                <div class="row">
                                    <div class="col-5 col-md-4">Flow Rate</div>
                                    <div class="col-7 col-md-8">: {{ $polyA->flow_rate }} %</div>
                                </div>
                                <div class="row">
                                    <div class="col-5 col-md-4">Konsentrasi</div>
                                    <div class="col-7 col-md-8">: {{ $polyA->concentration }} %</div>
                                </div>
                                <div class="row">
                                    <div class="col-5 col-md-4">Dosis</div>
                                    <div class="col-7 col-md-8">: {{ number_format($polyA->dosage, 3) }} ppm</div>
                                </div>
                                <div class="row">
                                    <div class="col-5 col-md-4">Level Tangki</div>
                                    <div class="col-7 col-md-8">: {{ $polyA->tank_level }} cm</div>
                                </div>
                            @endif
                        @else
                            <p><span class="badge badge-info">Standby</span></p>
                        @endif
                    </div>
                </div>
                
                {{-- Pompa B --}}
                <div class="col-md-6">
                    <div class="font-weight-bold mb-2">Pompa Polymer B</div>
                    <div class="mb-3">
                        @if($polyB)
                            {{-- Status --}}
                            <div class="row">
                                <div class="col-5 col-md-4">Status</div>
                                <div class="col-7 col-md-8 d-flex">:
                                    @if ($polyB->status == 'running')
                                        <h6 style="margin-bottom: 0px;"><span class="badge badge-success ml-1">Running</span></h6>
                                    @elseif ($polyB->status == 'standby')
                                        <h6><span class="badge badge-info ml-1">Standby</span></h6>
                                    @else
                                        <h6><span class="badge badge-warning ml-1">Maintenance</span></h6>
                                    @endif
                                </div>
                            </div>

                            {{-- Detail Fields (hanya tampil kalau Running) --}}
                            @if($polyB->status == 'running')
                                <div class="row">
                                    <div class="col-5 col-md-4">Flow Rate</div>
                                    <div class="col-7 col-md-8">: {{ $polyB->flow_rate }} %</div>
                                </div>
                                <div class="row">
                                    <div class="col-5 col-md-4">Konsentrasi</div>
                                    <div class="col-7 col-md-8">: {{ $polyB->concentration }} %</div>
                                </div>
                                <div class="row">
                                    <div class="col-5 col-md-4">Dosis</div>
                                    <div class="col-7 col-md-8">: {{ number_format($polyB->dosage, 3) }} ppm</div>
                                </div>
                                <div class="row">
                                    <div class="col-5 col-md-4">Level Tangki</div>
                                    <div class="col-7 col-md-8">: {{ $polyB->tank_level }} cm</div>
                                </div>
                            @endif
                        @else
                            <p><span class="badge badge-info">Standby</span></p>
                        @endif
                    </div>
                </div>
            </div>

            <hr>

            <div class="d-flex justify-content-between">
                @if (!$isAdmin)
                    <a href="/monitoring/{{ $id }}/edit" class="btn btn-warning ml-2" type="submit">
                        <i class="fas fa-pencil" style="color: white"></i>
                        Edit
                    </a>
                @endif
                <a class="btn btn-secondary" href="/monitoring-index" wire:navigated type="button">Back</a>
            </div>
        </div>
    </div>
</div>

@script
    <script>
        window.copyTotalizer = function(btn, value) {
            navigator.clipboard.writeText(value).then(() => {
                const icon = btn.querySelector('i');
                icon.classList.replace('fa-copy', 'fa-check');
                setTimeout(() => icon.classList.replace('fa-check', 'fa-copy'), 1500);
            }).catch(() => {
                const textarea = document.createElement('textarea');
                textarea.value = value;
                textarea.style.position = 'fixed';
                document.body.appendChild(textarea);
                textarea.select();
                document.execCommand('copy');
                document.body.removeChild(textarea);
            });
        }

        let shiftDetail = @json($shifts);
        let airBaku = shiftDetail.water_qualities.find(data => data.type == 'air baku')
        let sedimentasi = shiftDetail.water_qualities.find(data => data.type == 'sedimentation')
        let reservoir = shiftDetail.water_qualities.find(data => data.type == 'reservoir')
        let flowAirBaku = shiftDetail.flow_meters.find(data => data.location == null)
        let flowSudarso = shiftDetail.flow_meters.find(data => data.location == 'yos sudarso')
        let flowVeteran = shiftDetail.flow_meters.find(data => data.location == 'veteran')
        let pumpProccessIntake = shiftDetail.pump_proccess.filter(data => data.type.includes('intake'))
        let pumpProccessDistribusi = shiftDetail.pump_proccess.filter(data => data.type.includes('distribusi'))

        // Filter pompa chemical berdasarkan type
        let pumpPacAll = shiftDetail.pump_chemicals.filter(data => data.type == 'pac')
        let pumpChlorineAll = shiftDetail.pump_chemicals.filter(data => data.type == 'chlorine/kaporit')
        let pumpSodaAshAll = shiftDetail.pump_chemicals.filter(data => data.type == 'soda ash')
        let pumpPolymerAll = shiftDetail.pump_chemicals.filter(data => data.type == 'polymer')

        // Transform untuk chemical pumps (format sama seperti intake/distribusi)
        let pac = transformChemicalText(pumpPacAll, {
            tittle: [],
            tittle1: '',
            tittle2: '',
            tittle3: '',
            frequency: '',
            flowRate: '',
            dosage: '',
            concentration: '',
            tankLevel: ''
        })

        let chlorine = transformChemicalText(pumpChlorineAll, {
            tittle: [],
            tittle1: '',
            tittle2: '',
            tittle3: '',
            frequency: '',
            flowRate: '',
            dosage: '',
            concentration: '',
            tankLevel: ''
        })

        let sodaAsh = transformChemicalText(pumpSodaAshAll, {
            tittle: [],
            tittle1: '',
            tittle2: '',
            tittle3: '',
            frequency: '',
            flowRate: '',
            dosage: '',
            concentration: '',
            tankLevel: ''
        })

        let polymer = transformChemicalText(pumpPolymerAll, {
            tittle: [],
            tittle1: '',
            tittle2: '',
            tittle3: '',
            frequency: '',
            flowRate: '',
            dosage: '',
            concentration: '',
            tankLevel: ''
        })

        let intake = transformText(pumpProccessIntake, {
            tittle: [],
            tittle1: '',
            tittle2: '',
            tittle3: '',
            ampere: '',
            frequency: '',
            pressure: '',
        })


        let distri = transformText(pumpProccessDistribusi, {
            tittle: [],
            tittle1: '',
            tittle2: '',
            tittle3: '',
            ampere: '',
            frequency: '',
            pressure: '',
        })

        function capitalizeFirstLetter(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        }

        function transformText(dataArray, tempData) {
            dataArray.forEach((data, i) => {
                const idx = data.type.split(' ')[1].toUpperCase()

                if (data.status == 'running') {
                    if (tempData.tittle1 == '') {
                        tempData.tittle1 = `${idx} Running`
                    } else {
                        let test = tempData.tittle1.split(' ')
                        test[test.length - 2] += ` & ${idx}`
                        tempData.tittle1 = test.join(' ')
                    }

                    if (tempData.ampere == '') {
                        tempData.ampere = `${data.ampere} A (${idx})`
                    } else {
                        tempData.ampere += `; ${data.ampere} A (${idx})`
                    }
                    if (tempData.frequency == '') {
                        tempData.frequency = `${data.frequency} Hz (${idx})`
                    } else {
                        tempData.frequency += `; ${data.frequency} Hz (${idx})`
                    }
                    if (tempData.pressure == '') {
                        tempData.pressure = `${data.pressure.toString().replace('.', ',')} Bar (${idx})`
                    } else {
                        tempData.pressure += `; ${data.pressure.toString().replace('.', ',')} Bar (${idx})`
                    }
                } else if (data.status == 'standby') {
                    if (tempData.tittle2 == '') {
                        tempData.tittle2 = `${idx} Standby`
                    } else {
                        let test = tempData.tittle2.split(' ')
                        test[test.length - 2] += ` & ${idx}`
                        tempData.tittle2 = test.join(' ')
                    }
                } else {
                    if (tempData.tittle3 == '') {
                        tempData.tittle3 = `${idx} Maintenance`
                    } else {
                        let test = tempData.tittle3.split(' ')
                        test[test.length - 2] += `, ${idx}`
                        tempData.tittle3 = test.join(' ')
                    }
                }
            });

            if (tempData.tittle1 !== '') tempData.tittle.push(tempData.tittle1)
            if (tempData.tittle2 !== '') tempData.tittle.push(tempData.tittle2)
            if (tempData.tittle3 !== '') tempData.tittle.push(tempData.tittle3)

            return tempData;
        }

        // Function untuk transform chemical pumps (PAC, Chlorine, Soda, Polymer)
        function transformChemicalText(dataArray, tempData) {
            dataArray.forEach((data, i) => {
                const idx = data.pump_unit.toUpperCase() // A atau B

                if (data.status == 'running') {
                    if (tempData.tittle1 == '') {
                        tempData.tittle1 = `${idx} Running`
                    } else {
                        let test = tempData.tittle1.split(' ')
                        test[test.length - 2] += ` & ${idx}`
                        tempData.tittle1 = test.join(' ')
                    }

                    // Frequency (untuk PAC)
                    if (data.frequency && data.frequency != 0) {
                        if (tempData.frequency == '') {
                            tempData.frequency = `${data.frequency} Hz (${idx})`
                        } else {
                            tempData.frequency += `; ${data.frequency} Hz (${idx})`
                        }
                    }

                    // Flow Rate (untuk Chlorine, Soda, Polymer)
                    if (data.flow_rate && data.flow_rate != 0) {
                        // Tentukan satuan berdasarkan type
                        let unit = '';
                        if (data.type == 'chlorine/kaporit') {
                            unit = 'l/h';
                        } else if (data.type == 'soda ash' || data.type == 'polymer') {
                            unit = '%';
                        }

                        if (tempData.flowRate == '') {
                            tempData.flowRate = `${data.flow_rate} ${unit} (${idx})`
                        } else {
                            tempData.flowRate += `; ${data.flow_rate} ${unit} (${idx})`
                        }
                    }

                    // Dosage
                    if (data.dosage && data.dosage != 0) {
                        const dosageDisplay = data.type === 'polymer'
                            ? Number(data.dosage).toFixed(3)
                            : data.dosage;
                        if (tempData.dosage == '') {
                            tempData.dosage = `${dosageDisplay} ppm (${idx})`
                        } else {
                            tempData.dosage += `; ${dosageDisplay} ppm (${idx})`
                        }
                    }

                    // Concentration
                    if (data.concentration && data.concentration != 0) {
                        if (tempData.concentration == '') {
                            tempData.concentration = `${data.concentration} % (${idx})`
                        } else {
                            tempData.concentration += `; ${data.concentration} % (${idx})`
                        }
                    }

                    // Tank Level
                    if (data.tank_level && data.tank_level != 0) {
                        if (tempData.tankLevel == '') {
                            tempData.tankLevel = `${data.tank_level} cm`
                        } else {
                            tempData.tankLevel += `; ${data.tank_level} cm`
                        }
                    }

                } else if (data.status == 'standby') {
                    if (tempData.tittle2 == '') {
                        tempData.tittle2 = `${idx} Standby`
                    } else {
                        let test = tempData.tittle2.split(' ')
                        test[test.length - 2] += ` & ${idx}`
                        tempData.tittle2 = test.join(' ')
                    }
                } else {
                    if (tempData.tittle3 == '') {
                        tempData.tittle3 = `${idx} Maintenance`
                    } else {
                        let test = tempData.tittle3.split(' ')
                        test[test.length - 2] += `, ${idx}`
                        tempData.tittle3 = test.join(' ')
                    }
                }
            });

            if (tempData.tittle1 !== '') tempData.tittle.push(tempData.tittle1)
            if (tempData.tittle2 !== '') tempData.tittle.push(tempData.tittle2)
            if (tempData.tittle3 !== '') tempData.tittle.push(tempData.tittle3)

            return tempData;
        }

        /**
         * Function to copy all monitoring data values from HTML to clipboard
         * This function extracts all measurement values from the monitoring report
         * and copies them as text in a structured format with section headers
         */


        $js('copyClipboard', () => {
            copyAllMonitoringValues();
        })

        function copyAllMonitoringValues() {
// Format tanggal ke bahasa Indonesia
const dateObj = new Date(shiftDetail.date);
const shiftNum = shiftDetail.shift.toLowerCase().replace('shift ', '').trim();
if (shiftNum === 'iii') {
    dateObj.setDate(dateObj.getDate() - 1);
}
const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', "Jum'at", 'Sabtu'];
const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
const dayName = days[dateObj.getDay()];
const day = dateObj.getDate();
const monthName = months[dateObj.getMonth()];
const year = dateObj.getFullYear();
const formattedDate = `${dayName}, ${day} ${monthName} ${year}`;

// Format shift (Shift I, Shift II, Shift III)
const shiftFormatted = shiftDetail.shift.toUpperCase().replace('SHIFT ', '');

const reportText =
`*UPDATE DAILY REPORT AND MONITORING SCADA*
${formattedDate}
Shift : ${shiftFormatted}
Operator : ${shiftDetail.shift_operators[0].name}${shiftDetail.shift_operators.length >1 ? ' & '+shiftDetail.shift_operators?.[1]?.name:''}
Jam : ${shiftDetail.start_time.slice(0,5)}-${shiftDetail.end_time.slice(0,5)} WIB

- Air Baku :
pH : ${Number(airBaku.ph).toFixed(2).replace('.', ',')}
Turbidity : ${(Number(airBaku.turbidity) % 1 === 0 ? Number(airBaku.turbidity).toFixed(0) : Number(airBaku.turbidity).toFixed(2)).replace('.', ',')} NTU
Warna :  ${airBaku.color} PCU
TDS : ${airBaku.tds}

- Sedimentation :
pH : ${Number(sedimentasi.ph).toFixed(2).replace('.', ',')}
Turbidity : ${Number(sedimentasi.turbidity).toFixed(2).replace('.', ',')} NTU
Warna :  ${sedimentasi.color} PCU
TDS : ${sedimentasi.tds}

- Reservoir :
pH : ${Number(reservoir.ph).toFixed(2).replace('.', ',')}
Turbidity : ${Number(reservoir.turbidity).toFixed(2).replace('.', ',')} NTU
Warna :  ${reservoir.color} PCU
TDS : ${reservoir.tds}
Free Chlorine : - mg/L

- Flowmeter Air Baku
Flow : ${flowAirBaku.flow} l/s
Totalizer : *${flowAirBaku.totalizer}*  m³

- Flowmeter Distribusi :
Yos Sudarso
Flow : ${flowSudarso.flow} l/s
Totalizer : *${flowSudarso.totalizer}*  m³

- Veteran
Flow : ${flowVeteran.flow} l/s
Totalizer : *${flowVeteran.totalizer}*  m³

- Level Reservoir :
Level A : ${Number(shiftDetail.reservoir_levels.level_a).toFixed(2).replace('.', ',')} m
Level B : ${Number(shiftDetail.reservoir_levels.level_b).toFixed(2).replace('.', ',')} m

- In Comer MDP Panel :
Kwh total : ${Number(shiftDetail.mdp_panels.kwh_total).toFixed(2).replace('.', ',')}
Wbp : ${Number(shiftDetail.mdp_panels.wdp).toFixed(2).replace('.', ',')}
Lwbp : ${Number(shiftDetail.mdp_panels.lwbp).toFixed(2).replace('.', ',')}
Kvar : ${Number(shiftDetail.mdp_panels.kvar).toFixed(2).replace('.', ',')}

- Level air bak pengumpul: ${Number(shiftDetail.collection_tank).toFixed(1).replace('.', ',')} m

- Pompa Intake : ${intake.tittle.join(', ')}
Ampere : ${intake.ampere == ''?'-':intake.ampere}
Frekuensi : ${intake.frequency == ''?'-':intake.frequency.toString().replace('.', ',')}
Pressure : ${intake.pressure== ''?'-':intake.pressure.toString().replace('.', ',')}

- Pompa Distribusi : ${distri.tittle.join(', ')}
Ampere : ${distri.ampere == ''?'-':distri.ampere}
Frekuensi : ${distri.frequency == ''?'-':distri.frequency.toString().replace('.', ',')}
Pressure : ${distri.pressure== ''?'-':distri.pressure.toString().replace('.', ',')}

- Pompa Dosing PAC : ${pac.tittle.join(', ')}
Frekuensi : ${pac.frequency == '' ? '-' : pac.frequency}
Dosis : ${pac.dosage == '' ? '-' : pac.dosage.toString().replace('.', ',')}
Konsentrasi : ${pac.concentration == '' ? '-' : pac.concentration.toString().replace('.', ',')}

- Pompa Dosing Clorine : ${chlorine.tittle.join(', ')}
Flow Rate : ${chlorine.flowRate == '' ? '-' : chlorine.flowRate}
Dosis : ${chlorine.dosage == '' ? '-' : chlorine.dosage.toString().replace('.', ',')}
Konsentrasi : ${chlorine.concentration == '' ? '-' : chlorine.concentration.toString().replace('.', ',')}

- Pompa Dosing Polymer : ${polymer.tittle.join(', ')}
Flow Rate : ${polymer.flowRate == '' ? '-' : polymer.flowRate}
Dosis : ${polymer.dosage == '' ? '-' : polymer.dosage.toString().replace('.', ',')}
Konsentrasi : ${polymer.concentration == '' ? '-' : polymer.concentration.toString().replace('.', ',')}

- Pompa Dosing Soda Ash : ${sodaAsh.tittle.join(', ')}
Flow Rate : ${sodaAsh.flowRate == '' ? '-' : sodaAsh.flowRate}
Dosis : ${sodaAsh.dosage == '' ? '-' : sodaAsh.dosage.toString().replace('.', ',')}
Konsentrasi : ${sodaAsh.concentration == '' ? '-' : sodaAsh.concentration.toString().replace('.', ',')}

Catatan : ${shiftDetail?.notes ? '\n' + shiftDetail.notes.split('\n').map(line => line.trim() ? '- ' + line.trim() : '').filter(line => line).join('\n') : '-'}`;


            const textarea = document.createElement('textarea');
            textarea.value = reportText;
            textarea.style.position = 'fixed'; // Prevent scrolling
            document.body.appendChild(textarea);

            textarea.select();
            try {
                const success = document.execCommand('copy');
                if (success) {
                    alert('Report copied to clipboard!');
                } else {
                    throw new Error('Copy failed');
                }
            } catch (err) {
                alert('Failed to copy. Please manually select and copy (Ctrl+C).');
                console.error('Copy error:', err);
            }
            document.body.removeChild(textarea);

            // navigator.clipboard.writeText(reportText)
            //     .then(() => alert('Daily report copied to clipboard!'))
            //     .catch(err => {
            //         console.error('Failed to copy text: ', err);
            //         alert('Please click the button again to copy');
            //     });
        }



        $js('copyPdam', () => {
            copyPerumda();
        })


        function copyPerumda() {
            // Format tanggal
            const dateObj = new Date(shiftDetail.date);
            const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', "Jum'at", 'Sabtu'];
            const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            const dayName = days[dateObj.getDay()];
            const day = dateObj.getDate();
            const monthName = months[dateObj.getMonth()];
            const year = dateObj.getFullYear();
            
            // Ambil jam dari end_time
            const endTime = shiftDetail.end_time.slice(0, 5);

            // Get Reservoir Level (convert dari m ke cm)
            const levelCm = Math.round(shiftDetail.reservoir_levels.level_a * 100);

        // Get pressure dari pompa distribusi yang sudah di-transform
        // Veteran : Pompa A & B
        let pressureVeteran = '-';
        let pumpA = pumpProccessDistribusi.find(p => p.type === 'distribusi a' && p.status === 'running');
        let pumpB = pumpProccessDistribusi.find(p => p.type === 'distribusi b' && p.status === 'running');
        if (pumpA) {
            pressureVeteran = Number(pumpA.pressure).toFixed(1).replace('.', ',');
        } else if (pumpB) {
            pressureVeteran = Number(pumpB.pressure).toFixed(1).replace('.', ',');
        }

        // Yos Sudarso : Pompa C & D
        let pressureYos = '-';
        let pumpC = pumpProccessDistribusi.find(p => p.type === 'distribusi c' && p.status === 'running');
        let pumpD = pumpProccessDistribusi.find(p => p.type === 'distribusi d' && p.status === 'running');
        if (pumpC) {
            pressureYos = Number(pumpC.pressure).toFixed(1).replace('.', ',');
        } else if (pumpD) {
            pressureYos = Number(pumpD.pressure).toFixed(1).replace('.', ',');
        }

            const waReportText = `Laporan Produksi IPA Brayan
${day} ${monthName} ${year} jam ${endTime} WIB
=======================
*Air Baku
- Turb : *${Number(airBaku.turbidity).toFixed(0)}* NTU
- Debit : *${flowAirBaku.flow}* lpd

*Air Reservoir IPA Brayan :
- Level : *${levelCm}* Cm
- Turb : *${Number(reservoir.turbidity).toFixed(2).replace('.', ',')}* NTU
- pH : *${Number(reservoir.ph).toFixed(2).replace('.', ',')}*
- Sisa Chlor : *${reservoir.free_chlor == 0 ? '-' : Number(reservoir.free_chlor).toFixed(2).replace('.', ',')}* ppm

1. Pompa Jl. Yos Sudarso :
- Press Header : *${pressureYos}* bar
- Debit : *${flowSudarso.flow}* lpd
- Stand meter : *${flowSudarso.totalizer}* m³

2. Pompa Jl. Veteran :
- Press : *${pressureVeteran}* bar
- Debit : *${flowVeteran.flow}* lpd
- Stand Meter : *${flowVeteran.totalizer}* m³`;

            const textarea = document.createElement('textarea');
            textarea.value = waReportText;
            textarea.style.position = 'fixed';
            document.body.appendChild(textarea);

            textarea.select();
            try {
                const success = document.execCommand('copy');
                if (success) {
                    alert('WhatsApp report copied to clipboard!');
                } else {
                    throw new Error('Copy failed');
                }
            } catch (err) {
                alert('Failed to copy. Please manually select and copy (Ctrl+C).');
                console.error('Copy error:', err);
            }
            document.body.removeChild(textarea);
        }
    </script>
@endscript
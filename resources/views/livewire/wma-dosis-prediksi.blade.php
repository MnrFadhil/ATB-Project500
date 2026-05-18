<div class="container-fluid">

    {{-- Header --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Prediksi Dosis Kimia WMA</h1>
    </div>

 {{-- Filter Bulan --}}
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">🗓 Pilih Bulan Prediksi</h6>
    </div>
    <div class="card-body" x-data="{
        bulan: '{{ $selectedMonth ? substr($selectedMonth, 5, 2) : date('m') }}',
        tahun: '{{ $selectedMonth ? substr($selectedMonth, 0, 4) : date('Y') }}',
        get month() { return this.tahun + '-' + this.bulan; }
    }">
        <div class="row align-items-end">
            <div class="col-md-3">
                <label class="font-weight-bold">Bulan</label>
                <select x-model="bulan" class="form-control">
                    <option value="01">Januari</option>
                    <option value="02">Februari</option>
                    <option value="03">Maret</option>
                    <option value="04">April</option>
                    <option value="05">Mei</option>
                    <option value="06">Juni</option>
                    <option value="07">Juli</option>
                    <option value="08">Agustus</option>
                    <option value="09">September</option>
                    <option value="10">Oktober</option>
                    <option value="11">November</option>
                    <option value="12">Desember</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="font-weight-bold">Tahun</label>
                <select x-model="tahun" class="form-control">
                    @for ($y = date('Y') - 2; $y <= date('Y') + 2; $y++)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-4">
                <label class="font-weight-bold">Periode Historis (otomatis)</label>
                <input type="text" value="{{ $historisLabel ?: 'Belum dipilih' }}" class="form-control" readonly style="background: #f8f9fc;">
            </div>
            <div class="col-md-3">
                <button @click="$wire.applyFilter(month)" wire:loading.attr="disabled" class="btn btn-primary btn-block">
                    <span wire:loading.remove wire:target="applyFilter"><i class="fas fa-flask"></i> Prediksi Bulan Ini</span>
                    <span wire:loading wire:target="applyFilter"><i class="fas fa-spinner fa-spin"></i> Memuat...</span>
                </button>
            </div>
        </div>
        @if($filteredMonth && $weightInfo)
            <small class="text-muted mt-2 d-block">
                Bobot WMA: W₁={{ $weightInfo['w1'] }} (terbaru), W₂={{ $weightInfo['w2'] }}, W₃={{ $weightInfo['w3'] }} (terlama) | Total={{ $weightInfo['total'] }}
                | Sistem mengambil <strong>3 bulan sebelumnya</strong> → rata-rata per minggu → prediksi <strong>{{ \Carbon\Carbon::parse($filteredMonth.'-01')->translatedFormat('F Y') }}</strong>
            </small>
        @endif
    </div>
</div>

    {{-- Loading --}}
    <div wire:loading wire:target="applyFilter" class="text-center py-4">
        <div class="spinner-border text-primary"><span class="sr-only">Loading...</span></div>
        <p class="mt-2 text-primary font-weight-bold">Menghitung prediksi dosis...</p>
    </div>

    <div wire:loading.remove wire:target="applyFilter">
        @if($filteredMonth && $chemLabels)
            @foreach ($chemLabels as $chemKey => $chemLabel)
                @if(count($weekly[$chemKey] ?? []) > 0)
                @php
                    $hist = $weekly[$chemKey];
                    $preds = $predictions[$chemKey] ?? [];
                    $labels = []; $hVals = []; $pVals = [];
                    foreach ($hist as $w) {
                        $labels[] = 'Mg ' . $w['week_num'];
                        $hVals[] = $w['avg_dosage'];
                        $pVals[] = 'null';
                    }
                    // Bridge
                    $lastH = end($hist);
                    $pVals[count($hist)-1] = $lastH['avg_dosage'];
                    foreach ($preds as $i => $p) {
                        $labels[] = 'Pred ' . ($i+1);
                        $hVals[] = 'null';
                        $pVals[] = $p['avg_dosage'];
                    }
                @endphp

                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">📊 {{ $chemLabel }} — Prediksi {{ \Carbon\Carbon::parse($filteredMonth.'-01')->translatedFormat('F Y') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            {{-- Chart --}}
                            <div class="col-md-8">
                                <canvas id="chart_{{ $chemKey }}" height="220"></canvas>
                            </div>
                            {{-- Table --}}
                            <div class="col-md-4">
                                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                    <table class="table table-sm table-bordered" style="font-size: 11px;">
                                        <thead class="thead-light text-center">
                                            <tr><th>Minggu</th><th>Dosis (ppm)</th><th>Data</th></tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($hist as $w)
                                                <tr class="text-center">
                                                    <td>Mg {{ $w['week_num'] }}</td>
                                                    <td><strong>{{ $w['avg_dosage'] }}</strong></td>
                                                    <td>{{ $w['count'] }}</td>
                                                </tr>
                                            @endforeach
                                            @foreach ($preds as $i => $p)
                                                <tr class="text-center table-warning">
                                                    <td><strong>🔮 Pred {{ $i+1 }}</strong></td>
                                                    <td><strong class="text-danger">{{ $p['avg_dosage'] }}</strong></td>
                                                    <td><small>WMA</small></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        {{-- Detail Perhitungan --}}
                        @if(count($preds) > 0)
                        <button class="btn btn-sm btn-outline-primary mt-3" type="button" data-toggle="collapse" data-target="#calc_{{ $chemKey }}">
                            🧮 Detail Perhitungan WMA
                        </button>
                        <div class="collapse mt-2" id="calc_{{ $chemKey }}">
                            <div class="bg-light p-3 rounded" style="font-size: 13px; line-height: 2.2; font-family: 'Courier New', monospace;">
                                @foreach ($preds as $i => $p)
                                    <strong>Prediksi Minggu {{ $i+1 }}:</strong><br>
                                    <div style="margin-left: 16px; font-size: 14px; font-weight: 600; margin-bottom: 12px;">
                                        WMA = (<span style="color:#28a745">{{ $p['prior_data'][0] }}</span> × <span style="color:#007bff">{{ $weightInfo['w3'] }}</span> + <span style="color:#28a745">{{ $p['prior_data'][1] }}</span> × <span style="color:#007bff">{{ $weightInfo['w2'] }}</span> + <span style="color:#28a745">{{ $p['prior_data'][2] }}</span> × <span style="color:#007bff">{{ $weightInfo['w1'] }}</span>) / {{ $weightInfo['total'] }}<br>
                                        WMA = (<span style="color:#28a745">{{ round($p['prior_data'][0] * $weightInfo['w3'], 2) }}</span> + <span style="color:#28a745">{{ round($p['prior_data'][1] * $weightInfo['w2'], 2) }}</span> + <span style="color:#28a745">{{ round($p['prior_data'][2] * $weightInfo['w1'], 2) }}</span>) / {{ $weightInfo['total'] }}<br>
                                        <span style="color:#0c5460; background:#d1ecf1; padding:4px 10px; border-radius:4px;">WMA = {{ $p['avg_dosage'] }} ppm</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                        {{-- Ringkasan Total Bulanan --}}
@if(count($preds) > 0)
<div class="alert alert-warning mt-3 mb-0 d-flex align-items-center justify-content-between flex-wrap" style="border-left: 5px solid #f6c23e;">
    <div>
        <i class="fas fa-calculator text-warning mr-2"></i>
        <strong>Estimasi Dosis Sebulan — {{ \Carbon\Carbon::parse($filteredMonth.'-01')->translatedFormat('F Y') }}</strong>
        <br>
        <small class="text-muted">
            Rata-rata dari {{ count($preds) }} minggu prediksi:
            @foreach ($preds as $i => $p)
                <strong>{{ $p['avg_dosage'] }}</strong>{{ !$loop->last ? ' + ' : '' }}
            @endforeach
            = <strong>{{ round(array_sum(array_column($preds, 'avg_dosage')) / count($preds), 2) }}</strong> ppm (rata-rata/minggu)
        </small>
    </div>
    <div class="text-right mt-2 mt-md-0">
        <div class="text-muted" style="font-size: 12px;">Total estimasi sebulan</div>
        <div style="font-size: 28px; font-weight: 700; color: #e74a3b; line-height: 1;">
            {{ round(array_sum(array_column($preds, 'avg_dosage')) / count($preds), 2) }}
            <span style="font-size: 14px; font-weight: 400;">ppm</span>
        </div>
        <small class="text-muted">(rata-rata mingguan × 1 bulan)</small>
    </div>
</div>
@endif

                    </div>
                </div>
                @endif
            @endforeach

        @else
            <div class="alert alert-info mt-2">
                <i class="fas fa-info-circle"></i>
                <strong>Pilih bulan dan klik "Prediksi Bulan Ini"</strong> untuk melihat prediksi dosis kimia 1 bulan kedepan.
                <br><small>Sistem akan mengambil data 3 bulan sebelumnya sebagai data historis.</small>
            </div>
        @endif
    </div>

</div>

@script
<script>
    let chartInstances = {};
    
    $wire.on('charts-ready', (data) => {
        console.log('🚀 Charts ready event received');
        
        if (!data || !data.chartsData) return;
        
        // Delay untuk tunggu DOM render selesai
        setTimeout(() => {
            const chartsData = data.chartsData;
            console.log('📊 Charts data:', chartsData);
            
            Object.keys(chartsData).forEach(key => {
                const canvasEl = document.getElementById('chart_' + key);
                if (!canvasEl) {
                    console.warn('⚠️ Canvas not found: chart_' + key);
                    return;
                }
                
                if (chartInstances[key]) {
                    chartInstances[key].destroy();
                }
                
                const d = chartsData[key];
                const labels = [...d.labels];
                for (let i = 0; i < d.pred.length; i++) {
                    labels.push('Pred ' + (i + 1));
                }
                
                const hist = [...d.hist, ...Array(d.pred.length).fill(null)];
                const pred = [...Array(d.hist.length - 1).fill(null), d.hist[d.hist.length - 1], ...d.pred];
                
                try {
                    chartInstances[key] = new Chart(canvasEl, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Historis (ppm)',
                                data: hist,
                                borderColor: '#4e73df',
                                backgroundColor: 'rgba(78,115,223,0.1)',
                                fill: true,
                                tension: 0.4,
                                pointRadius: 5,
                                borderWidth: 2.5,
                                spanGaps: false
                            }, {
                                label: 'Prediksi WMA (ppm)',
                                data: pred,
                                borderColor: '#e74a3b',
                                backgroundColor: 'rgba(231,74,59,0.1)',
                                borderDash: [5,5],
                                fill: true,
                                tension: 0.4,
                                pointRadius: 6,
                                pointStyle: 'triangle',
                                borderWidth: 2.5,
                                spanGaps: false
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            plugins: { legend: { display: true } },
                            scales: { y: { beginAtZero: false } }
                        }
                    });
                    console.log('✅ Chart created:', key);
                } catch(e) {
                    console.error('❌ Error:', key, e);
                }
            });
        }, 500); // tunggu 500ms untuk DOM render
    });
</script>
@endscript
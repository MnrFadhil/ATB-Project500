<div class="container-fluid" style="position:relative;min-height:300px;">

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
                | Sistem mengambil <strong>3 bulan sebelumnya</strong> → rata-rata per bulan kalender → 1x hitung WMA → prediksi <strong>{{ \Carbon\Carbon::parse($filteredMonth.'-01')->translatedFormat('F Y') }}</strong>
            </small>
        @endif
    </div>
</div>

    {{-- Loading --}}
    <div wire:loading.delay wire:target="applyFilter" style="position:absolute;top:0;left:0;width:100%;min-height:100%;height:calc(100vh - 70px);background:rgba(255,255,255,0.85);z-index:100;border-radius:4px;">
        <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;height:100%;min-height:calc(100vh - 70px);">
            <div class="spinner-border text-primary" style="width:3rem;height:3rem;"><span class="sr-only">Loading...</span></div>
            <p class="mt-3 text-primary font-weight-bold" style="font-size:16px;">Menghitung prediksi dosis...</p>
        </div>
    </div>

    <div wire:loading.remove wire:target="applyFilter">
        @if($filteredMonth && $chemLabels)

            {{-- Konfigurasi Bobot WMA --}}
            @php $isAdmin = auth()->user()->role === 'admin'; @endphp
            <div class="card shadow mb-4">
                <div class="card-body py-2">
                    <div class="d-flex align-items-center flex-wrap" style="gap:10px;">
                        <span class="font-weight-bold text-warning mr-2">
                            <i class="fas fa-sliders-h"></i> Bobot WMA Dosis Kimia
                        </span>
                        <small class="text-muted mr-2">W₁ (terlama) → W₃ (terbaru):</small>
                        @if($isAdmin)
                            <div class="d-flex align-items-center" style="gap:6px;">
                                <input wire:model="inputW1" type="number" min="1" max="100"
                                       class="form-control form-control-sm text-center" style="width:65px;" placeholder="W1">
                                <input wire:model="inputW2" type="number" min="1" max="100"
                                       class="form-control form-control-sm text-center" style="width:65px;" placeholder="W2">
                                <input wire:model="inputW3" type="number" min="1" max="100"
                                       class="form-control form-control-sm text-center" style="width:65px;" placeholder="W3">
                                <button wire:click="saveWeights" wire:loading.attr="disabled"
                                        class="btn btn-warning btn-sm">
                                    <span wire:loading.remove wire:target="saveWeights">
                                        <i class="fas fa-check"></i> Terapkan
                                    </span>
                                    <span wire:loading wire:target="saveWeights">
                                        <i class="fas fa-spinner fa-spin"></i>
                                    </span>
                                </button>
                            </div>
                        @else
                            <span class="font-weight-bold">[{{ $inputW1 }}, {{ $inputW2 }}, {{ $inputW3 }}]</span>
                        @endif
                        <small class="text-muted ml-2">Total: {{ $inputW1 + $inputW2 + $inputW3 }}</small>
                    </div>
                </div>
            </div>

            @foreach ($chemLabels as $chemKey => $chemLabel)
                @php $hist = $monthly[$chemKey] ?? []; @endphp
                @if(count($hist) > 0)
                @php
                    $pred = $predictions[$chemKey] ?? null;
                @endphp

                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">&#128202; {{ $chemLabel }} &mdash; Prediksi {{ \Carbon\Carbon::parse($filteredMonth.'-01')->translatedFormat('F Y') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            {{-- Chart --}}
                            <div class="col-md-8">
                                <canvas id="chart_{{ $chemKey }}" height="220"></canvas>
                            </div>
                            {{-- Table --}}
                            <div class="col-md-4">
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered" style="font-size: 11px;">
                                        <thead class="thead-light text-center">
                                            <tr><th>Bulan</th><th>Dosis (ppm)</th><th>Data</th></tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($hist as $m)
                                                <tr class="text-center">
                                                    <td>{{ $m['label'] }}</td>
                                                    <td><strong>{{ $m['avg_dosage'] }}</strong></td>
                                                    <td>{{ $m['count'] }}</td>
                                                </tr>
                                            @endforeach
                                            @if($pred)
                                                <tr class="text-center table-warning">
                                                    <td><strong>{{ $pred['label'] }} (Prediksi)</strong></td>
                                                    <td><strong class="text-danger">{{ $pred['avg_dosage'] }}</strong></td>
                                                    <td><small>WMA</small></td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        {{-- Detail Perhitungan --}}
                        @if($pred)
                        <button class="btn btn-sm btn-outline-primary mt-3" type="button" data-toggle="collapse" data-target="#calc_{{ $chemKey }}">
                            Detail Perhitungan WMA
                        </button>
                        <div class="collapse mt-2" id="calc_{{ $chemKey }}">
                            <div class="bg-light p-3 rounded" style="font-size: 12px;">

                                {{-- Step 1: Detail data per bulan --}}
                                <p class="font-weight-bold mb-2" style="font-size:13px;">Step 1 — Data per Bulan (3 Bulan Sebelumnya)</p>
                                @php $threeMonths = array_slice($hist, -3); @endphp
                                @foreach($threeMonths as $mi => $mData)
                                @php
                                    $bobotIdx  = count($threeMonths) - 1 - $mi; // 2,1,0
                                    $bobotVal  = $bobotIdx === 2 ? $weightInfo['w3'] : ($bobotIdx === 1 ? $weightInfo['w2'] : $weightInfo['w1']);
                                    $bobotLabel = $bobotIdx === 2 ? 'W₃ (terbaru)' : ($bobotIdx === 1 ? 'W₂' : 'W₁ (terlama)');
                                @endphp
                                <div class="card mb-2" style="border-left:3px solid #4e73df;">
                                    <div class="card-body py-2 px-3">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <strong style="font-size:12px;">{{ $mData['label'] }}</strong>
                                            <span class="badge badge-primary">Bobot {{ $bobotVal }} ({{ $bobotLabel }})</span>
                                        </div>
                                        @if(!empty($mData['records']))
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered mb-1" style="font-size:11px;">
                                                <thead class="thead-light">
                                                    <tr class="text-center">
                                                        <th>No</th>
                                                        <th>Tanggal</th>
                                                        <th>Shift</th>
                                                        <th>Dosis (ppm)</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach(array_slice($mData['records'], 0, 5) as $ri => $rec)
                                                    <tr class="text-center">
                                                        <td>{{ $ri + 1 }}</td>
                                                        <td>{{ $rec['date'] }}</td>
                                                        <td class="text-uppercase">{{ $rec['shift'] }}</td>
                                                        <td><strong>{{ $rec['dosage'] }}</strong></td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                                <tfoot class="thead-light">
                                                    @if($mData['count'] > 5)
                                                    <tr>
                                                        <td colspan="4" class="text-center text-muted" style="font-size:10px;">
                                                            ... dan {{ $mData['count'] - 5 }} data lainnya (total {{ $mData['count'] }} shift)
                                                        </td>
                                                    </tr>
                                                    @endif
                                                    <tr class="text-center font-weight-bold">
                                                        <td colspan="3">Jumlah data: {{ $mData['count'] }} shift &rarr; Rata-rata</td>
                                                        <td style="color:#28a745;">{{ $mData['avg_dosage'] }} ppm</td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                        @else
                                        <small class="text-muted">Tidak ada data individual tersedia.</small>
                                        @endif
                                    </div>
                                </div>
                                @endforeach

                                {{-- Step 2: Formula WMA --}}
                                <p class="font-weight-bold mb-1 mt-3" style="font-size:13px;">Step 2 — Rumus WMA</p>
                                <div style="font-family: 'Courier New', monospace; font-size: 13px; line-height: 2.2; background:#fff; border-radius:4px; padding:10px 14px; border:1px solid #dee2e6;">
                                    <strong>Prediksi {{ $pred['label'] }}:</strong><br>
                                    WMA = (<span style="color:#28a745">{{ $pred['prior_data'][0] }}</span> &times; <span style="color:#007bff">{{ $weightInfo['w3'] }}</span> + <span style="color:#28a745">{{ $pred['prior_data'][1] }}</span> &times; <span style="color:#007bff">{{ $weightInfo['w2'] }}</span> + <span style="color:#28a745">{{ $pred['prior_data'][2] }}</span> &times; <span style="color:#007bff">{{ $weightInfo['w1'] }}</span>) / {{ $weightInfo['total'] }}<br>
                                    WMA = (<span style="color:#28a745">{{ round($pred['prior_data'][0] * $weightInfo['w3'], 2) }}</span> + <span style="color:#28a745">{{ round($pred['prior_data'][1] * $weightInfo['w2'], 2) }}</span> + <span style="color:#28a745">{{ round($pred['prior_data'][2] * $weightInfo['w1'], 2) }}</span>) / {{ $weightInfo['total'] }}<br>
                                    <span style="color:#0c5460; background:#d1ecf1; padding:4px 10px; border-radius:4px;">WMA = {{ $pred['avg_dosage'] }} ppm</span>
                                </div>
                                <small class="text-muted d-block mt-2">Dihitung 1x langsung dari 3 rata-rata bulanan asli (non-rekursif) &mdash; tidak menggunakan hasil prediksi lain sebagai input.</small>
                            </div>
                        </div>
                        @endif

                        {{-- Ringkasan --}}
                        @if($pred)
                        <div class="alert alert-warning mt-3 mb-0 d-flex align-items-center justify-content-between flex-wrap" style="border-left: 5px solid #f6c23e;">
                            <div>
                                <i class="fas fa-calculator text-warning mr-2"></i>
                                <strong>Estimasi Dosis &mdash; {{ \Carbon\Carbon::parse($filteredMonth.'-01')->translatedFormat('F Y') }}</strong>
                                <br>
                                <small class="text-muted">
                                    WMA dari 3 bulan sebelumnya ({{ $pred['prior_data'][0] }}, {{ $pred['prior_data'][1] }}, {{ $pred['prior_data'][2] }} ppm)
                                </small>
                            </div>
                            <div class="text-right mt-2 mt-md-0">
                                <div class="text-muted" style="font-size: 12px;">Estimasi rata-rata dosis sebulan</div>
                                <div style="font-size: 28px; font-weight: 700; color: #e74a3b; line-height: 1;">
                                    {{ $pred['avg_dosage'] }}
                                    <span style="font-size: 14px; font-weight: 400;">ppm</span>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                @else
                {{-- Tidak ada data historis untuk chemical ini --}}
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">&#128202; {{ $chemLabel }} &mdash; Prediksi {{ \Carbon\Carbon::parse($filteredMonth.'-01')->translatedFormat('F Y') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning mb-0">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            <strong>Tidak ada data pemakaian {{ $chemLabel }}</strong> dalam 3 bulan sebelum
                            <strong>{{ \Carbon\Carbon::parse($filteredMonth.'-01')->translatedFormat('F Y') }}</strong>
                            ({{ \Carbon\Carbon::parse($filteredMonth.'-01')->subMonths(3)->translatedFormat('F Y') }}
                            &ndash; {{ \Carbon\Carbon::parse($filteredMonth.'-01')->subMonth()->translatedFormat('F Y') }}).
                            <br><small class="text-muted mt-1 d-block">Prediksi tidak dapat dihitung karena tidak ada data historis dosis pada periode tersebut.</small>
                        </div>
                    </div>
                </div>
                @endif
            @endforeach

            {{-- ===== EVALUASI WMA DOSIS ===== --}}
            <hr class="my-4">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h4 class="h4 mb-0 text-gray-800">Evaluasi Akurasi Prediksi WMA Dosis &mdash; {{ \Carbon\Carbon::parse($filteredMonth.'-01')->translatedFormat('F Y') }}</h4>
            </div>

            @php
                $hasEvalData = !empty($evaluation) && collect($evaluation)->contains(fn($e) => count($e['rows']) > 0);
                $evalRows    = $evaluation['pac']['rows'] ?? [];
            @endphp

            @if($hasEvalData)

                {{-- Metric Summary Cards --}}
                <div class="row">
                    @php
                        $borderMap = ['pac'=>'warning','chlorine'=>'info','soda_ash'=>'success'];
                        $textMap   = ['pac'=>'warning','chlorine'=>'info','soda_ash'=>'success'];
                    @endphp
                    @foreach($chemLabels as $chemKey => $chemLabel)
                        @php
                            $m  = $evaluation[$chemKey]['metrics'] ?? null;
                            $bc = $borderMap[$chemKey] ?? 'primary';
                            $noData = !$m || $m['n'] === 0;
                        @endphp
                        <div class="col-md-4 mb-4">
                            <div class="card border-left-{{ $noData ? 'secondary' : $bc }} shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="text-xs font-weight-bold text-{{ $noData ? 'secondary' : $bc }} text-uppercase mb-2">{{ $chemLabel }}</div>
                                    @if($noData)
                                        <div class="text-muted" style="font-size:13px;">
                                            <i class="fas fa-exclamation-triangle text-warning mr-1"></i>
                                            Tidak ada data pemakaian {{ $chemLabel }} dalam 3 bulan sebelum
                                            <strong>{{ \Carbon\Carbon::parse($filteredMonth.'-01')->translatedFormat('F Y') }}</strong>.
                                            Evaluasi tidak dapat dihitung.
                                        </div>
                                    @else
                                        <div class="row no-gutters align-items-center">
                                            <div class="col-7">
                                                <small class="text-muted">MAPE</small>
                                                <div class="h4 mb-0 font-weight-bold">{{ $m['mape'] }}%</div>
                                            </div>
                                            <div class="col-5 text-right">
                                                <small class="text-muted d-block">Interpretasi</small>
                                                <span class="badge badge-pill px-2 py-1
                                                    @if($m['mape'] < 10) badge-success
                                                    @elseif($m['mape'] < 20) badge-warning
                                                    @elseif($m['mape'] < 50) badge-info
                                                    @else badge-danger @endif" style="font-size:11px;">
                                                    {{ $m['interpretasi'] }}
                                                </span>
                                            </div>
                                        </div>
                                        {{-- RMSE & MAE dinonaktifkan sementara --}}
                                        {{-- <div class="col-6"><small class="text-muted">RMSE</small><div class="h5 mb-0">{{ $m['rmse'] }}</div></div> --}}
                                        {{-- <div class="col-6"><small class="text-muted">MAE</small><div class="h5 mb-0">{{ $m['mae'] }}</div></div> --}}
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @php
                    $evalRowsKlorin = $evaluation['chlorine']['rows'] ?? [];
                @endphp

                {{-- Rumus Evaluasi PAC --}}
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">&#128208; Rumus Evaluasi WMA Dosis (dengan Data Real &mdash; PAC)</h6>
                        <button class="btn btn-sm btn-outline-primary" type="button" data-toggle="collapse" data-target="#rumusDosisCollapse">
                            <i class="fas fa-chevron-down"></i> Tampilkan/Sembunyikan
                        </button>
                    </div>
                    <div class="collapse" id="rumusDosisCollapse">
                        <div class="card-body">
                            <div class="row">

                                {{-- Step 1: Data Historis PAC --}}
                                <div class="col-md-6 mb-4">
                                    <div class="card border-left-warning h-100">
                                        <div class="card-body">
                                            <h6 class="font-weight-bold text-warning">1. Data Historis PAC (3 Bulan Sebelumnya)</h6>
                                            <p class="small text-muted mb-2">Sistem mengambil rata-rata dosis aktual per bulan selama 3 bulan sebelum bulan prediksi sebagai input WMA.</p>
                                            @if(isset($predictions['pac']['prior_data']) && count($predictions['pac']['prior_data']) > 0)
                                                @php
                                                    $pacPrior = $predictions['pac']['prior_data'];
                                                    $pacMonths = array_slice($monthly['pac'] ?? [], -3);
                                                @endphp
                                                <table class="table table-sm table-bordered" style="font-size:13px;">
                                                    <thead class="thead-light">
                                                        <tr><th>Urutan</th><th>Bulan</th><th>Rata-rata Dosis Aktual</th><th>Bobot</th></tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($pacPrior as $i => $val)
                                                            @php
                                                                $bulan = isset($pacMonths[$i]['label']) ? $pacMonths[$i]['label'] : 'Bulan ke-' . ($i+1);
                                                                $bobot = $i === 0 ? $weightInfo['w3'] : ($i === 1 ? $weightInfo['w2'] : $weightInfo['w1']);
                                                                $bobotLabel = $i === 0 ? 'W₁ (terlama)' : ($i === 1 ? 'W₂' : 'W₃ (terbaru)');
                                                            @endphp
                                                            <tr>
                                                                <td>{{ $i+1 }}</td>
                                                                <td>{{ $bulan }}</td>
                                                                <td><strong>{{ $val }} ppm</strong></td>
                                                                <td>{{ $bobot }} ({{ $bobotLabel }})</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            @else
                                                <div class="alert alert-warning"><small>Tidak ada data historis PAC tersedia.</small></div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- Step 2: Perhitungan WMA Prediksi PAC --}}
                                <div class="col-md-6 mb-4">
                                    <div class="card border-left-primary h-100">
                                        <div class="card-body">
                                            <h6 class="font-weight-bold text-primary">2. Perhitungan WMA Prediksi PAC</h6>
                                            <div class="bg-light p-3 rounded text-center mb-3">
                                                <code style="font-size:13px;">WMA = (D₁×W₁ + D₂×W₂ + D₃×W₃) / (W₁+W₂+W₃)</code>
                                            </div>
                                            @if(isset($predictions['pac']['prior_data']) && count($predictions['pac']['prior_data']) > 0)
                                                @php
                                                    $d = $predictions['pac']['prior_data'];
                                                    $w1 = $weightInfo['w3']; $w2 = $weightInfo['w2']; $w3 = $weightInfo['w1'];
                                                    $wTotal = $weightInfo['total'];
                                                @endphp
                                                <div style="font-size:13px;line-height:2.2;font-family:'Courier New',monospace;background:#f8f9fa;padding:12px;border-radius:4px;">
                                                    WMA = (<span style="color:#28a745">{{ $d[0] }}</span>×<span style="color:#007bff">{{ $w1 }}</span> + <span style="color:#28a745">{{ $d[1] }}</span>×<span style="color:#007bff">{{ $w2 }}</span> + <span style="color:#28a745">{{ $d[2] }}</span>×<span style="color:#007bff">{{ $w3 }}</span>) / {{ $wTotal }}<br>
                                                    WMA = (<span style="color:#28a745">{{ round($d[0]*$w1,2) }}</span> + <span style="color:#28a745">{{ round($d[1]*$w2,2) }}</span> + <span style="color:#28a745">{{ round($d[2]*$w3,2) }}</span>) / {{ $wTotal }}<br>
                                                    WMA = <span style="color:#856404">{{ round($d[0]*$w1 + $d[1]*$w2 + $d[2]*$w3, 2) }}</span> / {{ $wTotal }}<br>
                                                    <span style="color:#155724;background:#d4edda;padding:8px 12px;border-radius:4px;display:inline-block;margin-top:4px;">
                                                        WMA = {{ $predictions['pac']['avg_dosage'] ?? '-' }} ppm
                                                    </span>
                                                </div>
                                                <p class="small text-muted mt-2 mb-0">
                                                    D₁={{ $d[0] }} (terlama, W={{ $w1 }}), D₂={{ $d[1] }} (W={{ $w2 }}), D₃={{ $d[2] }} (terbaru, W={{ $w3 }})
                                                </p>
                                            @else
                                                <div class="alert alert-warning"><small>Data prediksi PAC tidak tersedia.</small></div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- MAPE PAC --}}
                                <div class="col-md-6 mb-4">
                                    <div class="card border-left-success h-100">
                                        <div class="card-body">
                                            <h6 class="font-weight-bold text-success">3. MAPE (Mean Absolute Percentage Error)</h6>
                                            <div class="bg-light p-3 rounded text-center mb-3">
                                                <code style="font-size:14px;">MAPE = (&Sigma;|Prediksi &minus; Aktual| / |Aktual|) / n &times; 100%</code>
                                            </div>
                                            @if(count($evalRows) > 0)
                                                @php
                                                    $mapePctTot = 0; $mapeCount = 0; $mapeDetails = [];
                                                    foreach($evalRows as $r) {
                                                        if($r['aktual'] != 0) {
                                                            $abs = abs($r['prediksi'] - $r['aktual']); $pct = $abs / $r['aktual'];
                                                            $mapePctTot += $pct; $mapeCount++;
                                                            $mapeDetails[] = ['pred'=>$r['prediksi'],'aktual'=>$r['aktual'],'abs'=>$abs,'pct'=>$pct,'wk'=>$r['label'] ?? ($r['week_num'] ?? '')];
                                                        }
                                                    }
                                                    $mapeVal = $mapeCount > 0 ? ($mapePctTot / $mapeCount) * 100 : 0;
                                                @endphp
                                                <div class="alert alert-info p-2 mb-3"><small><strong>Perhitungan dari {{ $mapeCount }} data PAC (aktual &ne; 0):</strong></small></div>
                                                <div class="bg-light p-3 rounded mb-3" style="font-size:13px;line-height:2;">
                                                    <strong>Persentase error:</strong><br>
                                                    @foreach($mapeDetails as $d)
                                                        {{ $d['wk'] }}: |{{ $d['pred'] }} &minus; {{ $d['aktual'] }}| / {{ $d['aktual'] }}<br>
                                                        = <span style="color:#28a745;font-weight:bold;">{{ round($d['abs'],4) }}</span> / <span style="color:#007bff;font-weight:bold;">{{ $d['aktual'] }}</span>
                                                        = <span style="color:#856404;font-weight:bold;">{{ round($d['pct'],4) }}</span> (<span style="color:#dc3545;font-weight:bold;">{{ round($d['pct']*100,2) }}%</span>)<br><br>
                                                    @endforeach
                                                    <div style="font-size:16px;font-weight:bold;color:#333;line-height:2.2;font-family:'Courier New',monospace;background:#f8f9fa;padding:12px;border-radius:4px;">
                                                        MAPE = ({{ round($mapePctTot,4) }} / {{ $mapeCount }}) &times; 100%<br>
                                                        MAPE = {{ $mapeCount > 0 ? round($mapePctTot/$mapeCount,4) : 0 }} &times; 100%<br>
                                                        <span style="color:#155724;background:#d4edda;padding:8px 12px;border-radius:4px;display:inline-block;">MAPE = {{ round($mapeVal,2) }}%</span>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- Interpretasi --}}
                                <div class="col-md-6 mb-4">
                                    <div class="card border-left-info h-100">
                                        <div class="card-body">
                                            <h6 class="font-weight-bold text-info">4. Interpretasi Akurasi (Khusmiawati et al., 2025)</h6>
                                            <table class="table table-sm table-bordered mt-2" style="font-size:13px;">
                                                <thead class="thead-light">
                                                    <tr><th>MAPE</th><th>Interpretasi</th></tr>
                                                </thead>
                                                <tbody>
                                                    <tr class="table-success"><td>&lt; 10%</td><td><strong>Sangat Akurat</strong></td></tr>
                                                    <tr class="table-warning"><td>10% &ndash; 20%</td><td><strong>Akurat / Baik</strong></td></tr>
                                                    <tr class="table-info"><td>20% &ndash; 50%</td><td><strong>Cukup / Wajar</strong></td></tr>
                                                    <tr class="table-danger"><td>&gt; 50%</td><td><strong>Tidak Akurat</strong></td></tr>
                                                </tbody>
                                            </table>
                                            <div class="mt-3">
                                                <strong>Hasil Evaluasi {{ \Carbon\Carbon::parse($filteredMonth.'-01')->translatedFormat('F Y') }}:</strong>
                                                <ul class="mt-2 mb-0" style="font-size:13px;">
                                                    @foreach($chemLabels as $ck => $cl)
                                                        @php $em = $evaluation[$ck]['metrics'] ?? null; @endphp
                                                        <li>
                                                            <strong>{{ $cl }}:</strong>
                                                            @if(!$em || $em['n'] === 0)
                                                                <span class="text-muted">Tidak ada data pemakaian dalam 3 bulan sebelum {{ \Carbon\Carbon::parse($filteredMonth.'-01')->translatedFormat('F Y') }}</span>
                                                            @else
                                                                MAPE = {{ $em['mape'] }}%
                                                                &rarr; <span class="font-weight-bold
                                                                    @if($em['mape'] < 10) text-success
                                                                    @elseif($em['mape'] < 20) text-warning
                                                                    @elseif($em['mape'] < 50) text-info
                                                                    @else text-danger @endif">
                                                                    {{ $em['interpretasi'] }}
                                                                </span>
                                                            @endif
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                            <small class="text-muted mt-3 d-block"><i class="fas fa-info-circle"></i> Sumber: Khusmiawati et al. (2025). Perbandingan antara prediksi WMA dengan rata-rata dosis aktual bulan {{ \Carbon\Carbon::parse($filteredMonth.'-01')->translatedFormat('F Y') }}.</small>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                {{-- Rumus Evaluasi Klorin --}}
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-info">&#128208; Rumus Evaluasi WMA Dosis (dengan Data Real &mdash; Klorin)</h6>
                        <button class="btn btn-sm btn-outline-info" type="button" data-toggle="collapse" data-target="#rumusDosisKlorinCollapse">
                            <i class="fas fa-chevron-down"></i> Tampilkan/Sembunyikan
                        </button>
                    </div>
                    <div class="collapse" id="rumusDosisKlorinCollapse">
                        <div class="card-body">
                            <div class="row">

                                {{-- Step 1: Data Historis Klorin --}}
                                <div class="col-md-6 mb-4">
                                    <div class="card border-left-warning h-100">
                                        <div class="card-body">
                                            <h6 class="font-weight-bold text-warning">1. Data Historis Klorin (3 Bulan Sebelumnya)</h6>
                                            <p class="small text-muted mb-2">Sistem mengambil rata-rata dosis aktual per bulan selama 3 bulan sebelum bulan prediksi sebagai input WMA.</p>
                                            @if(isset($predictions['chlorine']['prior_data']) && count($predictions['chlorine']['prior_data']) > 0)
                                                @php
                                                    $klPrior = $predictions['chlorine']['prior_data'];
                                                    $klMonths = array_slice($monthly['chlorine'] ?? [], -3);
                                                @endphp
                                                <table class="table table-sm table-bordered" style="font-size:13px;">
                                                    <thead class="thead-light">
                                                        <tr><th>Urutan</th><th>Bulan</th><th>Rata-rata Dosis Aktual</th><th>Bobot</th></tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($klPrior as $i => $val)
                                                            @php
                                                                $bulanKl = isset($klMonths[$i]['label']) ? $klMonths[$i]['label'] : 'Bulan ke-' . ($i+1);
                                                                $bobotKl = $i === 0 ? $weightInfo['w3'] : ($i === 1 ? $weightInfo['w2'] : $weightInfo['w1']);
                                                                $bobotKlLabel = $i === 0 ? 'W₁ (terlama)' : ($i === 1 ? 'W₂' : 'W₃ (terbaru)');
                                                            @endphp
                                                            <tr>
                                                                <td>{{ $i+1 }}</td>
                                                                <td>{{ $bulanKl }}</td>
                                                                <td><strong>{{ $val }} ppm</strong></td>
                                                                <td>{{ $bobotKl }} ({{ $bobotKlLabel }})</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            @else
                                                <div class="alert alert-warning"><small>Tidak ada data historis Klorin tersedia.</small></div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- Step 2: Perhitungan WMA Prediksi Klorin --}}
                                <div class="col-md-6 mb-4">
                                    <div class="card border-left-primary h-100">
                                        <div class="card-body">
                                            <h6 class="font-weight-bold text-primary">2. Perhitungan WMA Prediksi Klorin</h6>
                                            <div class="bg-light p-3 rounded text-center mb-3">
                                                <code style="font-size:13px;">WMA = (D₁×W₁ + D₂×W₂ + D₃×W₃) / (W₁+W₂+W₃)</code>
                                            </div>
                                            @if(isset($predictions['chlorine']['prior_data']) && count($predictions['chlorine']['prior_data']) > 0)
                                                @php
                                                    $dk = $predictions['chlorine']['prior_data'];
                                                    $wk1 = $weightInfo['w3']; $wk2 = $weightInfo['w2']; $wk3 = $weightInfo['w1'];
                                                    $wkTotal = $weightInfo['total'];
                                                @endphp
                                                <div style="font-size:13px;line-height:2.2;font-family:'Courier New',monospace;background:#f8f9fa;padding:12px;border-radius:4px;">
                                                    WMA = (<span style="color:#28a745">{{ $dk[0] }}</span>×<span style="color:#007bff">{{ $wk1 }}</span> + <span style="color:#28a745">{{ $dk[1] }}</span>×<span style="color:#007bff">{{ $wk2 }}</span> + <span style="color:#28a745">{{ $dk[2] }}</span>×<span style="color:#007bff">{{ $wk3 }}</span>) / {{ $wkTotal }}<br>
                                                    WMA = (<span style="color:#28a745">{{ round($dk[0]*$wk1,2) }}</span> + <span style="color:#28a745">{{ round($dk[1]*$wk2,2) }}</span> + <span style="color:#28a745">{{ round($dk[2]*$wk3,2) }}</span>) / {{ $wkTotal }}<br>
                                                    WMA = <span style="color:#856404">{{ round($dk[0]*$wk1 + $dk[1]*$wk2 + $dk[2]*$wk3, 2) }}</span> / {{ $wkTotal }}<br>
                                                    <span style="color:#155724;background:#d4edda;padding:8px 12px;border-radius:4px;display:inline-block;margin-top:4px;">
                                                        WMA = {{ $predictions['chlorine']['avg_dosage'] ?? '-' }} ppm
                                                    </span>
                                                </div>
                                                <p class="small text-muted mt-2 mb-0">
                                                    D₁={{ $dk[0] }} (terlama, W={{ $wk1 }}), D₂={{ $dk[1] }} (W={{ $wk2 }}), D₃={{ $dk[2] }} (terbaru, W={{ $wk3 }})
                                                </p>
                                            @else
                                                <div class="alert alert-warning"><small>Data prediksi Klorin tidak tersedia.</small></div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- MAPE Klorin --}}
                                <div class="col-md-6 mb-4">
                                    <div class="card border-left-success h-100">
                                        <div class="card-body">
                                            <h6 class="font-weight-bold text-success">3. MAPE (Mean Absolute Percentage Error)</h6>
                                            <div class="bg-light p-3 rounded text-center mb-3">
                                                <code style="font-size:14px;">MAPE = (&Sigma;|Prediksi &minus; Aktual| / |Aktual|) / n &times; 100%</code>
                                            </div>
                                            @if(count($evalRowsKlorin) > 0)
                                                @php
                                                    $mapeKPctTot = 0; $mapeKCount = 0; $mapeKDetails = [];
                                                    foreach($evalRowsKlorin as $r) {
                                                        if($r['aktual'] != 0) {
                                                            $abs = abs($r['prediksi'] - $r['aktual']); $pct = $abs / $r['aktual'];
                                                            $mapeKPctTot += $pct; $mapeKCount++;
                                                            $mapeKDetails[] = ['pred'=>$r['prediksi'],'aktual'=>$r['aktual'],'abs'=>$abs,'pct'=>$pct,'wk'=>$r['label'] ?? ($r['week_num'] ?? '')];
                                                        }
                                                    }
                                                    $mapeKVal = $mapeKCount > 0 ? ($mapeKPctTot / $mapeKCount) * 100 : 0;
                                                @endphp
                                                <div class="alert alert-info p-2 mb-3"><small><strong>Perhitungan dari {{ $mapeKCount }} data Klorin (aktual &ne; 0):</strong></small></div>
                                                <div class="bg-light p-3 rounded mb-3" style="font-size:13px;line-height:2;">
                                                    <strong>Persentase error:</strong><br>
                                                    @foreach($mapeKDetails as $d)
                                                        {{ $d['wk'] }}: |{{ $d['pred'] }} &minus; {{ $d['aktual'] }}| / {{ $d['aktual'] }}<br>
                                                        = <span style="color:#28a745;font-weight:bold;">{{ round($d['abs'],4) }}</span> / <span style="color:#007bff;font-weight:bold;">{{ $d['aktual'] }}</span>
                                                        = <span style="color:#856404;font-weight:bold;">{{ round($d['pct'],4) }}</span> (<span style="color:#dc3545;font-weight:bold;">{{ round($d['pct']*100,2) }}%</span>)<br><br>
                                                    @endforeach
                                                    <div style="font-size:16px;font-weight:bold;color:#333;line-height:2.2;font-family:'Courier New',monospace;background:#f8f9fa;padding:12px;border-radius:4px;">
                                                        MAPE = ({{ round($mapeKPctTot,4) }} / {{ $mapeKCount }}) &times; 100%<br>
                                                        MAPE = {{ $mapeKCount > 0 ? round($mapeKPctTot/$mapeKCount,4) : 0 }} &times; 100%<br>
                                                        <span style="color:#155724;background:#d4edda;padding:8px 12px;border-radius:4px;display:inline-block;">MAPE = {{ round($mapeKVal,2) }}%</span>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="alert alert-warning mb-0"><i class="fas fa-exclamation-triangle mr-1"></i> Tidak ada data evaluasi Klorin.</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- Interpretasi Klorin --}}
                                <div class="col-md-6 mb-4">
                                    <div class="card border-left-info h-100">
                                        <div class="card-body">
                                            <h6 class="font-weight-bold text-info">4. Interpretasi Akurasi — Klorin (Khusmiawati et al., 2025)</h6>
                                            <table class="table table-sm table-bordered mt-2" style="font-size:13px;">
                                                <thead class="thead-light">
                                                    <tr><th>MAPE</th><th>Interpretasi</th></tr>
                                                </thead>
                                                <tbody>
                                                    <tr class="table-success"><td>&lt; 10%</td><td><strong>Sangat Akurat</strong></td></tr>
                                                    <tr class="table-warning"><td>10% &ndash; 20%</td><td><strong>Akurat / Baik</strong></td></tr>
                                                    <tr class="table-info"><td>20% &ndash; 50%</td><td><strong>Cukup / Wajar</strong></td></tr>
                                                    <tr class="table-danger"><td>&gt; 50%</td><td><strong>Tidak Akurat</strong></td></tr>
                                                </tbody>
                                            </table>
                                            @php $emK = $evaluation['chlorine']['metrics'] ?? null; @endphp
                                            <div class="mt-3" style="font-size:13px;">
                                                <strong>Hasil Evaluasi Klorin &mdash; {{ \Carbon\Carbon::parse($filteredMonth.'-01')->translatedFormat('F Y') }}:</strong><br>
                                                @if($emK && $emK['n'] > 0)
                                                    MAPE = <strong>{{ $emK['mape'] }}%</strong>
                                                    &rarr; <span class="font-weight-bold
                                                        @if($emK['mape'] < 10) text-success
                                                        @elseif($emK['mape'] < 20) text-warning
                                                        @elseif($emK['mape'] < 50) text-info
                                                        @else text-danger @endif">
                                                        {{ $emK['interpretasi'] }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">Tidak ada data evaluasi Klorin.</span>
                                                @endif
                                            </div>
                                            <small class="text-muted mt-3 d-block"><i class="fas fa-info-circle"></i> Sumber: Khusmiawati et al. (2025). Perbandingan prediksi WMA Klorin dengan rata-rata dosis aktual di bulan {{ \Carbon\Carbon::parse($filteredMonth.'-01')->translatedFormat('F Y') }}.</small>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tabel Perbandingan --}}
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">&#128202; Perbandingan Prediksi vs Aktual Dosis Kimia &mdash; {{ \Carbon\Carbon::parse($filteredMonth.'-01')->translatedFormat('F Y') }}</h6>
                    </div>
                    <div class="card-body">
                        @php
                    $nonEmptyEval = array_filter($evaluation, fn($e) => count($e['rows']) > 0);
                    $maxWeeks = !empty($nonEmptyEval) ? max(array_map(fn($e) => count($e['rows']), $nonEmptyEval)) : 0;
                    $sodaAshEmpty = empty($evaluation['soda_ash']['rows']);
                @endphp
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm" style="font-size:12px;">
                                <thead class="thead-light text-center">
                                    <tr>
                                        <th rowspan="2">No</th>
                                        <th rowspan="2">Periode</th>
                                        <th colspan="3">PAC (ppm)</th>
                                        <th colspan="3">Klorin (ppm)</th>
                                        <th colspan="3">Soda Ash (ppm)</th>
                                    </tr>
                                    <tr>
                                        <th>Pred</th><th>Aktual</th><th>Error</th>
                                        <th>Pred</th><th>Aktual</th><th>Error</th>
                                        <th>Pred</th><th>Aktual</th><th>Error</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($maxWeeks > 0)
                                        @for($wi = 0; $wi < $maxWeeks; $wi++)
                                            <tr class="text-center">
                                                <td>{{ $wi + 1 }}</td>
                                                <td>{{ $evaluation['pac']['rows'][$wi]['label'] ?? ($evaluation['chlorine']['rows'][$wi]['label'] ?? ('Periode ' . ($wi + 1))) }}</td>
                                                @foreach(['pac','chlorine'] as $ck)
                                                    @php $erow = $evaluation[$ck]['rows'][$wi] ?? null; @endphp
                                                    @if($erow)
                                                        <td>{{ $erow['prediksi'] }}</td>
                                                        <td>{{ $erow['aktual'] }}</td>
                                                        <td class="font-weight-bold
                                                            @if(abs($erow['error']) <= 1) text-success
                                                            @elseif(abs($erow['error']) <= 3) text-warning
                                                            @else text-danger @endif">
                                                            {{ $erow['error'] }}
                                                        </td>
                                                    @else
                                                        <td colspan="3" class="text-muted">-</td>
                                                    @endif
                                                @endforeach
                                                {{-- Soda Ash --}}
                                                @if($sodaAshEmpty)
                                                    @if($wi === 0)
                                                        <td colspan="3" rowspan="{{ $maxWeeks }}" class="text-center text-muted align-middle" style="font-size:11px; background:#f8f9fa;">
                                                            <i class="fas fa-exclamation-triangle text-warning"></i><br>
                                                            Tidak ada data pemakaian Soda Ash dalam 3 bulan sebelum
                                                            {{ \Carbon\Carbon::parse($filteredMonth.'-01')->translatedFormat('F Y') }}
                                                        </td>
                                                    @endif
                                                @else
                                                    @php $erow = $evaluation['soda_ash']['rows'][$wi] ?? null; @endphp
                                                    @if($erow)
                                                        <td>{{ $erow['prediksi'] }}</td>
                                                        <td>{{ $erow['aktual'] }}</td>
                                                        <td class="font-weight-bold
                                                            @if(abs($erow['error']) <= 1) text-success
                                                            @elseif(abs($erow['error']) <= 3) text-warning
                                                            @else text-danger @endif">
                                                            {{ $erow['error'] }}
                                                        </td>
                                                    @else
                                                        <td colspan="3" class="text-muted">-</td>
                                                    @endif
                                                @endif
                                            </tr>
                                        @endfor
                                    @else
                                        <tr><td colspan="11" class="text-center text-muted py-3">Tidak ada data evaluasi.</td></tr>
                                    @endif
                                </tbody>
                                <tfoot class="thead-light text-center font-weight-bold">
                                    <tr>
                                        <td colspan="2">Metrik</td>
                                        @foreach(['pac','chlorine'] as $ck)
                                            @php $em = $evaluation[$ck]['metrics'] ?? ['rmse'=>'-','mae'=>'-','mape'=>'-','interpretasi'=>'-']; @endphp
                                            <td colspan="2" class="text-left" style="font-size:11px;">
                                                MAPE: {{ $em['mape'] }}%
                                            </td>
                                            <td class="font-weight-bold
                                                @if(isset($em['mape']) && is_numeric($em['mape']) && $em['mape'] < 10) text-success
                                                @elseif(isset($em['mape']) && is_numeric($em['mape']) && $em['mape'] < 20) text-warning
                                                @elseif(isset($em['mape']) && is_numeric($em['mape']) && $em['mape'] < 50) text-info
                                                @else text-danger @endif"
                                                style="font-size:10px;">
                                                {{ $em['interpretasi'] ?? '-' }}
                                            </td>
                                        @endforeach
                                        {{-- Soda Ash footer --}}
                                        @if($sodaAshEmpty)
                                            <td colspan="3" class="text-muted" style="font-size:11px;">Tidak ada data</td>
                                        @else
                                            @php $em = $evaluation['soda_ash']['metrics'] ?? ['rmse'=>'-','mae'=>'-','mape'=>'-','interpretasi'=>'-']; @endphp
                                            <td colspan="2" class="text-left" style="font-size:11px;">
                                                MAPE: {{ $em['mape'] }}%
                                            </td>
                                            <td class="font-weight-bold
                                                @if(isset($em['mape']) && is_numeric($em['mape']) && $em['mape'] < 10) text-success
                                                @elseif(isset($em['mape']) && is_numeric($em['mape']) && $em['mape'] < 20) text-warning
                                                @elseif(isset($em['mape']) && is_numeric($em['mape']) && $em['mape'] < 50) text-info
                                                @else text-danger @endif"
                                                style="font-size:10px;">
                                                {{ $em['interpretasi'] ?? '-' }}
                                            </td>
                                        @endif
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class="mt-3">
                            <small>
                                <strong>Keterangan Warna Error:</strong>
                                <span class="text-success font-weight-bold ml-2">&#9632; Kecil (&le;1 ppm)</span>
                                <span class="text-warning font-weight-bold ml-2">&#9632; Sedang (&le;3 ppm)</span>
                                <span class="text-danger font-weight-bold ml-2">&#9632; Besar (&gt;3 ppm)</span>
                            </small>
                        </div>
                    </div>
                </div>


            @else
                <div class="alert alert-info mt-2">
                    <i class="fas fa-info-circle"></i>
                    <strong>Data aktual untuk {{ \Carbon\Carbon::parse($filteredMonth.'-01')->translatedFormat('F Y') }} belum tersedia.</strong>
                    <br><small>Evaluasi akurasi hanya dapat dilakukan jika data dosis aktual bulan tersebut sudah ada di sistem.</small>
                </div>
            @endif

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
        if (!data || !data.chartsData) return;

        setTimeout(() => {
            const chartsData = data.chartsData;

            Object.keys(chartsData).forEach(key => {
                const canvasEl = document.getElementById('chart_' + key);
                if (!canvasEl) return;

                if (chartInstances[key]) {
                    chartInstances[key].destroy();
                }

                const d = chartsData[key];
                const labels = d.labels;

                // hist = titik historis + null di posisi prediksi
                const hist = [...d.hist, ...Array(d.pred.length).fill(null)];
                // pred = null di semua titik historis kecuali titik terakhir (jembatan), lalu nilai prediksi
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
                } catch(e) {
                    // chart init failed silently
                }
            });
        }, 500);
    });
</script>
@endscript

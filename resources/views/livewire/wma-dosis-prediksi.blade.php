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
                | Sistem mengambil <strong>3 bulan sebelumnya</strong> → rata-rata per minggu → prediksi <strong>{{ \Carbon\Carbon::parse($filteredMonth.'-01')->translatedFormat('F Y') }}</strong>
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
                                                    <td><strong>Pred {{ $i+1 }}</strong></td>
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
                            Detail Perhitungan WMA
                        </button>
                        <div class="collapse mt-2" id="calc_{{ $chemKey }}">
                            <div class="bg-light p-3 rounded" style="font-size: 13px; line-height: 2.2; font-family: 'Courier New', monospace;">
                                @foreach ($preds as $i => $p)
                                    <strong>Prediksi Minggu {{ $i+1 }}:</strong><br>
                                    <div style="margin-left: 16px; font-size: 14px; font-weight: 600; margin-bottom: 12px;">
                                        WMA = (<span style="color:#28a745">{{ $p['prior_data'][0] }}</span> &times; <span style="color:#007bff">{{ $weightInfo['w3'] }}</span> + <span style="color:#28a745">{{ $p['prior_data'][1] }}</span> &times; <span style="color:#007bff">{{ $weightInfo['w2'] }}</span> + <span style="color:#28a745">{{ $p['prior_data'][2] }}</span> &times; <span style="color:#007bff">{{ $weightInfo['w1'] }}</span>) / {{ $weightInfo['total'] }}<br>
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
                                <strong>Estimasi Dosis Sebulan &mdash; {{ \Carbon\Carbon::parse($filteredMonth.'-01')->translatedFormat('F Y') }}</strong>
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
                                <small class="text-muted">(rata-rata mingguan &times; 1 bulan)</small>
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
                                        <div class="row no-gutters">
                                            <div class="col-6">
                                                <small class="text-muted">RMSE</small>
                                                <div class="h5 mb-0 font-weight-bold">{{ $m['rmse'] }}</div>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted">MAE</small>
                                                <div class="h5 mb-0 font-weight-bold">{{ $m['mae'] }}</div>
                                            </div>
                                            <div class="col-6 mt-2">
                                                <small class="text-muted">MAPE</small>
                                                <div class="h5 mb-0 font-weight-bold">{{ $m['mape'] }}%</div>
                                            </div>
                                            <div class="col-6 mt-2">
                                                <small class="text-muted">Interpretasi</small>
                                                <div class="h6 mb-0 font-weight-bold
                                                    @if($m['mape'] < 10) text-success
                                                    @elseif($m['mape'] < 20) text-warning
                                                    @elseif($m['mape'] < 50) text-info
                                                    @else text-danger @endif">
                                                    {{ $m['interpretasi'] }}
                                                </div>
                                            </div>
                                        </div>
                                        <small class="text-muted mt-1 d-block">n = {{ $m['n'] }} minggu</small>
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

                                {{-- RMSE PAC --}}
                                <div class="col-md-6 mb-4">
                                    <div class="card border-left-danger h-100">
                                        <div class="card-body">
                                            <h6 class="font-weight-bold text-danger">1. RMSE (Root Mean Square Error)</h6>
                                            <div class="bg-light p-3 rounded text-center mb-3">
                                                <code style="font-size:14px;">RMSE = &radic;( &Sigma;(Prediksi &minus; Aktual)&sup2; / n )</code>
                                            </div>
                                            @if(count($evalRows) > 0)
                                                @php
                                                    $rmseTot = 0; $rmseDetails = [];
                                                    foreach($evalRows as $r) {
                                                        $err = $r['prediksi'] - $r['aktual']; $sq = $err * $err; $rmseTot += $sq;
                                                        $rmseDetails[] = ['pred'=>$r['prediksi'],'aktual'=>$r['aktual'],'err'=>$err,'sq'=>$sq,'wk'=>$r['week_num']];
                                                    }
                                                    $rmseVal = sqrt($rmseTot / count($evalRows));
                                                @endphp
                                                <div class="alert alert-info p-2 mb-3"><small><strong>Perhitungan dari {{ count($evalRows) }} data evaluasi PAC:</strong></small></div>
                                                <div class="bg-light p-3 rounded mb-3" style="font-size:13px;line-height:2;">
                                                    <strong>Error setiap minggu (Prediksi &minus; Aktual)&sup2;:</strong><br>
                                                    @foreach($rmseDetails as $d)
                                                        Mg {{ $d['wk'] }}: ({{ $d['pred'] }} &minus; {{ $d['aktual'] }}) = <span style="color:#e74c3c;font-weight:bold;">{{ round($d['err'],2) }}</span><br>
                                                        {{ round($d['err'],2) }} &times; {{ round($d['err'],2) }} = <span style="color:#3498db;font-weight:bold;">{{ round($d['sq'],4) }}</span><br><br>
                                                    @endforeach
                                                    <strong>Total &Sigma;(Error&sup2;) = {{ round($rmseTot,4) }}</strong><br><br>
                                                    <div style="font-size:16px;font-weight:bold;color:#333;line-height:2.2;font-family:'Courier New',monospace;background:#f8f9fa;padding:12px;border-radius:4px;">
                                                        RMSE = &radic;( {{ round($rmseTot,4) }} / {{ count($evalRows) }} )<br>
                                                        RMSE = &radic;{{ round($rmseTot/count($evalRows),4) }}<br>
                                                        <span style="color:#721c24;background:#f5c6cb;padding:8px 12px;border-radius:4px;display:inline-block;">RMSE = {{ round($rmseVal,4) }} ppm</span>
                                                    </div>
                                                </div>
                                            @endif
                                            <small class="text-muted"><i class="fas fa-info-circle"></i> Semakin kecil RMSE, semakin akurat prediksi. Satuan: ppm.</small>
                                        </div>
                                    </div>
                                </div>

                                {{-- MAE PAC --}}
                                <div class="col-md-6 mb-4">
                                    <div class="card border-left-warning h-100">
                                        <div class="card-body">
                                            <h6 class="font-weight-bold text-warning">2. MAE (Mean Absolute Error)</h6>
                                            <div class="bg-light p-3 rounded text-center mb-3">
                                                <code style="font-size:14px;">MAE = &Sigma;|Prediksi &minus; Aktual| / n</code>
                                            </div>
                                            @if(count($evalRows) > 0)
                                                @php
                                                    $maeTot = 0; $maeDetails = [];
                                                    foreach($evalRows as $r) {
                                                        $abs = abs($r['prediksi'] - $r['aktual']); $maeTot += $abs;
                                                        $maeDetails[] = ['pred'=>$r['prediksi'],'aktual'=>$r['aktual'],'abs'=>$abs,'wk'=>$r['week_num']];
                                                    }
                                                    $maeVal = $maeTot / count($evalRows);
                                                @endphp
                                                <div class="alert alert-info p-2 mb-3"><small><strong>Perhitungan dari {{ count($evalRows) }} data evaluasi PAC:</strong></small></div>
                                                <div class="bg-light p-3 rounded mb-3" style="font-size:13px;line-height:2;">
                                                    <strong>Error absolut setiap minggu |Prediksi &minus; Aktual|:</strong><br>
                                                    @foreach($maeDetails as $d)
                                                        Mg {{ $d['wk'] }}: |{{ $d['pred'] }} &minus; {{ $d['aktual'] }}| = <span style="color:#28a745;font-weight:bold;">{{ round($d['abs'],4) }}</span><br>
                                                    @endforeach
                                                    <br>
                                                    <div style="font-size:16px;font-weight:bold;color:#333;line-height:2.2;font-family:'Courier New',monospace;background:#f8f9fa;padding:12px;border-radius:4px;">
                                                        MAE = {{ round($maeTot,4) }} / {{ count($evalRows) }}<br>
                                                        <span style="color:#856404;background:#fff3cd;padding:8px 12px;border-radius:4px;display:inline-block;">MAE = {{ round($maeVal,4) }} ppm</span>
                                                    </div>
                                                </div>
                                            @endif
                                            <small class="text-muted"><i class="fas fa-info-circle"></i> Rata-rata besar kesalahan prediksi dalam satuan ppm.</small>
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
                                                            $mapeDetails[] = ['pred'=>$r['prediksi'],'aktual'=>$r['aktual'],'abs'=>$abs,'pct'=>$pct,'wk'=>$r['week_num']];
                                                        }
                                                    }
                                                    $mapeVal = $mapeCount > 0 ? ($mapePctTot / $mapeCount) * 100 : 0;
                                                @endphp
                                                <div class="alert alert-info p-2 mb-3"><small><strong>Perhitungan dari {{ $mapeCount }} data PAC (aktual &ne; 0):</strong></small></div>
                                                <div class="bg-light p-3 rounded mb-3" style="font-size:13px;line-height:2;">
                                                    <strong>Persentase error setiap minggu:</strong><br>
                                                    @foreach($mapeDetails as $d)
                                                        Mg {{ $d['wk'] }}: |{{ $d['pred'] }} &minus; {{ $d['aktual'] }}| / {{ $d['aktual'] }}<br>
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
                                            <h6 class="font-weight-bold text-info">4. Interpretasi Akurasi (Lewis, 1982)</h6>
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
                                            <small class="text-muted mt-3 d-block"><i class="fas fa-info-circle"></i> Sumber: Lewis (1982). Perbandingan antara prediksi WMA minggu ke-1 s/d 4 dengan rata-rata dosis aktual minggu yang bersangkutan di bulan {{ \Carbon\Carbon::parse($filteredMonth.'-01')->translatedFormat('F Y') }}.</small>
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

                                {{-- RMSE Klorin --}}
                                <div class="col-md-6 mb-4">
                                    <div class="card border-left-danger h-100">
                                        <div class="card-body">
                                            <h6 class="font-weight-bold text-danger">1. RMSE (Root Mean Square Error)</h6>
                                            <div class="bg-light p-3 rounded text-center mb-3">
                                                <code style="font-size:14px;">RMSE = &radic;( &Sigma;(Prediksi &minus; Aktual)&sup2; / n )</code>
                                            </div>
                                            @if(count($evalRowsKlorin) > 0)
                                                @php
                                                    $rmseKTot = 0; $rmseKDetails = [];
                                                    foreach($evalRowsKlorin as $r) {
                                                        $err = $r['prediksi'] - $r['aktual']; $sq = $err * $err; $rmseKTot += $sq;
                                                        $rmseKDetails[] = ['pred'=>$r['prediksi'],'aktual'=>$r['aktual'],'err'=>$err,'sq'=>$sq,'wk'=>$r['week_num']];
                                                    }
                                                    $rmseKVal = sqrt($rmseKTot / count($evalRowsKlorin));
                                                @endphp
                                                <div class="alert alert-info p-2 mb-3"><small><strong>Perhitungan dari {{ count($evalRowsKlorin) }} data evaluasi Klorin:</strong></small></div>
                                                <div class="bg-light p-3 rounded mb-3" style="font-size:13px;line-height:2;">
                                                    <strong>Error setiap minggu (Prediksi &minus; Aktual)&sup2;:</strong><br>
                                                    @foreach($rmseKDetails as $d)
                                                        Mg {{ $d['wk'] }}: ({{ $d['pred'] }} &minus; {{ $d['aktual'] }}) = <span style="color:#e74c3c;font-weight:bold;">{{ round($d['err'],2) }}</span><br>
                                                        {{ round($d['err'],2) }} &times; {{ round($d['err'],2) }} = <span style="color:#3498db;font-weight:bold;">{{ round($d['sq'],4) }}</span><br><br>
                                                    @endforeach
                                                    <strong>Total &Sigma;(Error&sup2;) = {{ round($rmseKTot,4) }}</strong><br><br>
                                                    <div style="font-size:16px;font-weight:bold;color:#333;line-height:2.2;font-family:'Courier New',monospace;background:#f8f9fa;padding:12px;border-radius:4px;">
                                                        RMSE = &radic;( {{ round($rmseKTot,4) }} / {{ count($evalRowsKlorin) }} )<br>
                                                        RMSE = &radic;{{ round($rmseKTot/count($evalRowsKlorin),4) }}<br>
                                                        <span style="color:#721c24;background:#f5c6cb;padding:8px 12px;border-radius:4px;display:inline-block;">RMSE = {{ round($rmseKVal,4) }} ppm</span>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="alert alert-warning mb-0"><i class="fas fa-exclamation-triangle mr-1"></i> Tidak ada data evaluasi Klorin.</div>
                                            @endif
                                            <small class="text-muted"><i class="fas fa-info-circle"></i> Semakin kecil RMSE, semakin akurat prediksi. Satuan: ppm.</small>
                                        </div>
                                    </div>
                                </div>

                                {{-- MAE Klorin --}}
                                <div class="col-md-6 mb-4">
                                    <div class="card border-left-warning h-100">
                                        <div class="card-body">
                                            <h6 class="font-weight-bold text-warning">2. MAE (Mean Absolute Error)</h6>
                                            <div class="bg-light p-3 rounded text-center mb-3">
                                                <code style="font-size:14px;">MAE = &Sigma;|Prediksi &minus; Aktual| / n</code>
                                            </div>
                                            @if(count($evalRowsKlorin) > 0)
                                                @php
                                                    $maeKTot = 0; $maeKDetails = [];
                                                    foreach($evalRowsKlorin as $r) {
                                                        $abs = abs($r['prediksi'] - $r['aktual']); $maeKTot += $abs;
                                                        $maeKDetails[] = ['pred'=>$r['prediksi'],'aktual'=>$r['aktual'],'abs'=>$abs,'wk'=>$r['week_num']];
                                                    }
                                                    $maeKVal = $maeKTot / count($evalRowsKlorin);
                                                @endphp
                                                <div class="alert alert-info p-2 mb-3"><small><strong>Perhitungan dari {{ count($evalRowsKlorin) }} data evaluasi Klorin:</strong></small></div>
                                                <div class="bg-light p-3 rounded mb-3" style="font-size:13px;line-height:2;">
                                                    <strong>Error absolut setiap minggu |Prediksi &minus; Aktual|:</strong><br>
                                                    @foreach($maeKDetails as $d)
                                                        Mg {{ $d['wk'] }}: |{{ $d['pred'] }} &minus; {{ $d['aktual'] }}| = <span style="color:#28a745;font-weight:bold;">{{ round($d['abs'],4) }}</span><br>
                                                    @endforeach
                                                    <br>
                                                    <div style="font-size:16px;font-weight:bold;color:#333;line-height:2.2;font-family:'Courier New',monospace;background:#f8f9fa;padding:12px;border-radius:4px;">
                                                        MAE = {{ round($maeKTot,4) }} / {{ count($evalRowsKlorin) }}<br>
                                                        <span style="color:#856404;background:#fff3cd;padding:8px 12px;border-radius:4px;display:inline-block;">MAE = {{ round($maeKVal,4) }} ppm</span>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="alert alert-warning mb-0"><i class="fas fa-exclamation-triangle mr-1"></i> Tidak ada data evaluasi Klorin.</div>
                                            @endif
                                            <small class="text-muted"><i class="fas fa-info-circle"></i> Rata-rata besar kesalahan prediksi dalam satuan ppm.</small>
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
                                                            $mapeKDetails[] = ['pred'=>$r['prediksi'],'aktual'=>$r['aktual'],'abs'=>$abs,'pct'=>$pct,'wk'=>$r['week_num']];
                                                        }
                                                    }
                                                    $mapeKVal = $mapeKCount > 0 ? ($mapeKPctTot / $mapeKCount) * 100 : 0;
                                                @endphp
                                                <div class="alert alert-info p-2 mb-3"><small><strong>Perhitungan dari {{ $mapeKCount }} data Klorin (aktual &ne; 0):</strong></small></div>
                                                <div class="bg-light p-3 rounded mb-3" style="font-size:13px;line-height:2;">
                                                    <strong>Persentase error setiap minggu:</strong><br>
                                                    @foreach($mapeKDetails as $d)
                                                        Mg {{ $d['wk'] }}: |{{ $d['pred'] }} &minus; {{ $d['aktual'] }}| / {{ $d['aktual'] }}<br>
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
                                            <h6 class="font-weight-bold text-info">4. Interpretasi Akurasi — Klorin (Lewis, 1982)</h6>
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
                                                    RMSE = <strong>{{ $emK['rmse'] }}</strong> ppm &nbsp;|&nbsp;
                                                    MAE = <strong>{{ $emK['mae'] }}</strong> ppm &nbsp;|&nbsp;
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
                                            <small class="text-muted mt-3 d-block"><i class="fas fa-info-circle"></i> Sumber: Lewis (1982). Perbandingan prediksi WMA Klorin minggu ke-1 s/d 4 dengan rata-rata dosis aktual di bulan {{ \Carbon\Carbon::parse($filteredMonth.'-01')->translatedFormat('F Y') }}.</small>
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
                                        <th rowspan="2">Minggu</th>
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
                                                <td>Minggu {{ $wi + 1 }}</td>
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
                                                RMSE: {{ $em['rmse'] }}<br>
                                                MAE: {{ $em['mae'] }}<br>
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
                                                RMSE: {{ $em['rmse'] }}<br>
                                                MAE: {{ $em['mae'] }}<br>
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
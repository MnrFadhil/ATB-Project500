<div class="container-fluid" style="position:relative;min-height:300px;">

    {{-- Header --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Evaluasi Akurasi Rekomendasi Fuzzy Mamdani</h1>
        <div>
            <button wire:click="exportExcel" wire:loading.attr="disabled" class="btn btn-success btn-sm shadow-sm" @if(!$filteredStart || !$filteredEnd) disabled @endif>
                <span wire:loading.remove wire:target="exportExcel">
                    <i class="fas fa-file-excel fa-sm text-white"></i> Export Excel
                </span>
                <span wire:loading wire:target="exportExcel">
                    <i class="fas fa-spinner fa-spin fa-sm text-white"></i> Loading...
                </span>
            </button>
            <button wire:click="exportPdf" wire:loading.attr="disabled" class="btn btn-danger btn-sm shadow-sm" @if(!$filteredStart || !$filteredEnd) disabled @endif>
                <span wire:loading.remove wire:target="exportPdf">
                    <i class="fas fa-file-pdf fa-sm text-white"></i> Export PDF
                </span>
                <span wire:loading wire:target="exportPdf">
                    <i class="fas fa-spinner fa-spin fa-sm text-white"></i> Loading...
                </span>
            </button>
        </div>
    </div>

    {{-- Filter --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Periode Evaluasi</h6>
        </div>
        <div class="card-body" x-data="{ start: '{{ $startDate }}', end: '{{ $endDate }}' }">
            <div class="row align-items-end">
                <div class="col-md-4">
                    <label class="font-weight-bold">Tanggal Mulai</label>
                    <input type="date" x-model="start" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="font-weight-bold">Tanggal Akhir</label>
                    <input type="date" x-model="end" class="form-control">
                </div>
                <div class="col-md-4">
                    <button
                        @click="$wire.applyFilter(start, end)"
                        wire:loading.attr="disabled"
                        class="btn btn-primary btn-block">
                        <span wire:loading.remove wire:target="applyFilter">
                            <i class="fas fa-filter"></i> Tampilkan Data
                        </span>
                        <span wire:loading wire:target="applyFilter">
                            <i class="fas fa-spinner fa-spin"></i> Memuat Data...
                        </span>
                    </button>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-12">
                    <small class="text-muted">
                        <i class="fas fa-info-circle"></i>
                        Rekomendasi dosis fuzzy pada shift <strong>T</strong> dibandingkan dengan dosis aktual yang digunakan operator pada shift <strong>T+1</strong> (2 jam berikutnya).
                        Total data evaluasi: <strong>{{ count($rows) }}</strong> shift
                        @if($filteredStart && $filteredEnd)
                            | <strong>Filter aktif: {{ $filteredStart }} s/d {{ $filteredEnd }}</strong>
                        @endif
                    </small>
                </div>
            </div>
        </div>
    </div>

    {{-- Loading Overlay --}}
    <div wire:loading.delay wire:target="applyFilter" style="position:absolute;top:0;left:0;width:100%;min-height:100%;height:calc(100vh - 70px);background:rgba(255,255,255,0.85);z-index:100;border-radius:4px;">
        <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;height:100%;min-height:calc(100vh - 70px);">
            <div class="spinner-border text-primary" style="width:3rem;height:3rem;"><span class="sr-only">Loading...</span></div>
            <p class="mt-3 text-primary font-weight-bold" style="font-size:16px;">Menghitung evaluasi Fuzzy Mamdani...</p>
        </div>
    </div>

    <div wire:loading.remove wire:target="applyFilter">

        @if($filteredStart && $filteredEnd)

            {{-- Ringkasan Metrik --}}
            <div class="row">
                @foreach (['pac' => 'border-left-warning', 'klorin' => 'border-left-info', 'sodaash' => 'border-left-success'] as $key => $border)
                    <div class="col-md-4 mb-4">
                        <div class="card {{ $border }} shadow h-100 py-2">
                            <div class="card-body">
                                <div class="text-xs font-weight-bold text-uppercase mb-2
                                    @if($key === 'pac') text-warning
                                    @elseif($key === 'klorin') text-info
                                    @else text-success
                                    @endif">
                                    {{ $metricLabels[$key] }}
                                </div>
                                <div class="row no-gutters align-items-center">
                                    <div class="col-7">
                                        <small class="text-muted">MAPE</small>
                                        <div class="h4 mb-0 font-weight-bold">{{ $metrics[$key]['mape'] }}%</div>
                                    </div>
                                    <div class="col-5 text-right">
                                        <small class="text-muted d-block">Interpretasi</small>
                                        <span class="badge badge-pill px-2 py-1
                                            @if($metrics[$key]['mape'] < 10) badge-success
                                            @elseif($metrics[$key]['mape'] < 20) badge-warning
                                            @elseif($metrics[$key]['mape'] < 50) badge-info
                                            @else badge-danger
                                            @endif" style="font-size:11px;">
                                            {{ $metrics[$key]['interpretasi'] }}
                                        </span>
                                    </div>
                                </div>
                                {{-- RMSE & MAE dinonaktifkan sementara --}}
                                {{-- <div class="col-6"><small class="text-muted">RMSE</small><div class="h5 mb-0 font-weight-bold">{{ $metrics[$key]['rmse'] }}</div></div> --}}
                                {{-- <div class="col-6"><small class="text-muted">MAE</small><div class="h5 mb-0 font-weight-bold">{{ $metrics[$key]['mae'] }}</div></div> --}}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Penjelasan Metode Evaluasi --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">📐 Metode Evaluasi Fuzzy Mamdani</h6>
                    <button class="btn btn-sm btn-outline-primary" type="button" data-toggle="collapse" data-target="#rumusFuzzyCollapse">
                        <i class="fas fa-chevron-down"></i> Tampilkan/Sembunyikan
                    </button>
                </div>
                <div class="collapse" id="rumusFuzzyCollapse">
                    <div class="card-body">
                        <div class="row">

                            {{-- Penjelasan Logika Perbandingan --}}
                            <div class="col-md-6 mb-4">
                                <div class="card border-left-primary h-100">
                                    <div class="card-body">
                                        <h6 class="font-weight-bold text-primary">1. Logika Perbandingan Data</h6>
                                        <div class="bg-light p-3 rounded mb-3" style="font-size: 13px; line-height: 2;">
                                            <strong>Skema Evaluasi:</strong><br>
                                            Shift <strong>T</strong> (misal 17:00–19:00) → Data kualitas air dibaca → Fuzzy Mamdani menghitung rekomendasi dosis kimia <br><br>
                                            Rekomendasi tersebut dibandingkan dengan dosis aktual yang digunakan operator pada shift <strong>T+1</strong> (19:00–21:00)<br><br>
                                            <strong>Kasus Khusus (23:00 → 01:00):</strong><br>
                                            Rekomendasi shift yang berakhir <strong>23:00</strong> dibandingkan dengan dosis aktual shift <strong>01:00 hari berikutnya</strong>
                                        </div>

                                        @if(count($rows) > 0)
                                            @php $contoh = $rows[0]; @endphp
                                            <div class="alert alert-info p-2">
                                                <small><strong>Contoh dari data pertama:</strong></small><br>
                                                <small>
                                                    Shift {{ strtoupper($contoh['shift']) }} jam {{ $contoh['end_time'] }} ({{ $contoh['date'] }})<br>
                                                    Input: Turbidity Sed = <strong>{{ $contoh['turb_sed'] }} NTU</strong>,
                                                    Free Chlor = <strong>{{ $contoh['free_chlor'] }} mg/L</strong>,
                                                    pH = <strong>{{ $contoh['ph_res'] }}</strong><br>
                                                    → Rekomendasi PAC: <strong>{{ $contoh['rekomen_pac'] }} ppm</strong><br>
                                                    → Aktual PAC shift {{ $contoh['next_end_time'] }}: <strong>{{ $contoh['aktual_pac'] }} ppm</strong><br>
                                                    → Error: {{ $contoh['rekomen_pac'] }} - {{ $contoh['aktual_pac'] }} = <strong class="{{ abs($contoh['error_pac']) <= 2 ? 'text-success' : 'text-danger' }}">{{ $contoh['error_pac'] }} ppm</strong>
                                                </small>
                                            </div>
                                        @endif

                                        <small class="text-muted mt-2 d-block">
                                            <strong>Input Fuzzy per kimia:</strong><br>
                                            • <strong>PAC</strong>: Turbidity Sedimentation (NTU)<br>
                                            • <strong>Klorin</strong>: Free Chlorine Reservoir (mg/L)<br>
                                            • <strong>Soda Ash</strong>: pH Reservoir
                                        </small>
                                    </div>
                                </div>
                            </div>

                            {{-- RMSE (dinonaktifkan sementara) --}}
                            {{-- <div class="col-md-6 mb-4">
                                <div class="card border-left-danger h-100">
                                    <div class="card-body">
                                        <h6 class="font-weight-bold text-danger">2. RMSE (Root Mean Square Error)</h6>
                                        <div class="bg-light p-3 rounded text-center mb-3">
                                            <code style="font-size: 14px;">
                                                RMSE = √( Σ(Rekomendasi - Aktual)² / n )
                                            </code>
                                        </div>

                                        @if(count($rows) > 0)
                                            @php
                                                $totalSquarePac = 0;
                                                $errorDetailsPac = [];
                                                foreach($rows as $r) {
                                                    $err = $r['rekomen_pac'] - $r['aktual_pac'];
                                                    $errSquared = $err * $err;
                                                    $totalSquarePac += $errSquared;
                                                    $errorDetailsPac[] = [
                                                        'rekomen'      => $r['rekomen_pac'],
                                                        'aktual'       => $r['aktual_pac'],
                                                        'error'        => round($err, 2),
                                                        'errorSquared' => round($errSquared, 4),
                                                    ];
                                                }
                                                $rmseCalcPac = sqrt($totalSquarePac / count($rows));
                                            @endphp

                                            <div class="alert alert-info p-2 mb-3">
                                                <small><strong>Perhitungan dari {{ count($rows) }} data evaluasi PAC:</strong></small>
                                            </div>

                                            <div class="bg-light p-3 rounded mb-3" style="font-size: 13px; line-height: 2;">
                                                <strong>Error setiap baris (Rekomendasi - Aktual)²:</strong><br>
                                                @foreach(array_slice($errorDetailsPac, 0, 5) as $detail)
                                                    ({{ $detail['rekomen'] }} - {{ $detail['aktual'] }}) = <span style="color: #e74c3c; font-weight: bold;">{{ $detail['error'] }}</span><br>
                                                    {{ $detail['error'] }} × {{ $detail['error'] }} = <span style="color: #3498db; font-weight: bold;">{{ $detail['errorSquared'] }}</span><br><br>
                                                @endforeach
                                                @if(count($rows) > 5)
                                                    ... ({{ count($rows) - 5 }} baris lainnya) ...<br><br>
                                                @endif

                                                <strong>Total Σ(Error²) = </strong> {{ round($totalSquarePac, 4) }}<br><br>

                                                <div style="font-size: 16px; font-weight: bold; color: #333; line-height: 2.2; font-family: 'Courier New', monospace; background-color: #f8f9fa; padding: 12px; border-radius: 4px;">
                                                    RMSE = √( Σ(Rekomendasi - Aktual)² / n )<br>
                                                    <br>
                                                    RMSE = √( {{ round($totalSquarePac, 4) }} / {{ count($rows) }} )<br>
                                                    <br>
                                                    RMSE = √{{ round($totalSquarePac / count($rows), 4) }}<br>
                                                    <br>
                                                    <span style="color: #721c24; background-color: #f5c6cb; padding: 8px 12px; border-radius: 4px; display: inline-block;">RMSE = {{ round($rmseCalcPac, 4) }} ppm</span>
                                                </div>
                                            </div>
                                        @endif

                                        <table class="table table-sm table-bordered mt-2" style="font-size: 12px;">
                                            <thead class="thead-light">
                                                <tr><th>Simbol</th><th>Keterangan</th></tr>
                                            </thead>
                                            <tbody>
                                                <tr><td>Rekomendasi</td><td>Dosis hasil perhitungan Fuzzy Mamdani</td></tr>
                                                <tr><td>Aktual</td><td>Dosis yang digunakan operator pada shift T+1</td></tr>
                                                <tr><td>n</td><td>Jumlah data evaluasi</td></tr>
                                                <tr><td>Σ</td><td>Jumlah dari semua data</td></tr>
                                            </tbody>
                                        </table>
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle"></i>
                                            Satuan = ppm. Semakin kecil semakin akurat.
                                        </small>
                                    </div>
                                </div>
                            </div> --}}

                            {{-- MAE (dinonaktifkan sementara) --}}
                            {{-- <div class="col-md-6 mb-4">
                                <div class="card border-left-warning h-100">
                                    <div class="card-body">
                                        <h6 class="font-weight-bold text-warning">3. MAE (Mean Absolute Error)</h6>
                                        <div class="bg-light p-3 rounded text-center mb-3">
                                            <code style="font-size: 14px;">
                                                MAE = Σ|Rekomendasi - Aktual| / n
                                            </code>
                                        </div>

                                        @if(count($rows) > 0)
                                            @php
                                                $totalAbsPac = 0;
                                                $absDetailsPac = [];
                                                foreach($rows as $r) {
                                                    $err = $r['rekomen_pac'] - $r['aktual_pac'];
                                                    $absErr = abs($err);
                                                    $totalAbsPac += $absErr;
                                                    $absDetailsPac[] = [
                                                        'rekomen'  => $r['rekomen_pac'],
                                                        'aktual'   => $r['aktual_pac'],
                                                        'error'    => round($err, 2),
                                                        'absError' => round($absErr, 4),
                                                    ];
                                                }
                                                $maeCalcPac = $totalAbsPac / count($rows);
                                            @endphp

                                            <div class="alert alert-info p-2 mb-3">
                                                <small><strong>Perhitungan dari {{ count($rows) }} data evaluasi PAC:</strong></small>
                                            </div>

                                            <div class="bg-light p-3 rounded mb-3" style="font-size: 13px; line-height: 2;">
                                                <strong>Error absolut setiap baris |Rekomendasi - Aktual|:</strong><br>
                                                @foreach(array_slice($absDetailsPac, 0, 5) as $detail)
                                                    |{{ $detail['rekomen'] }} - {{ $detail['aktual'] }}| = |<span style="color: #dc3545; font-weight: bold;">{{ $detail['error'] }}</span>| = <span style="color: #28a745; font-weight: bold;">{{ $detail['absError'] }}</span><br>
                                                @endforeach
                                                @if(count($rows) > 5)
                                                    ... ({{ count($rows) - 5 }} baris lainnya) ...<br>
                                                @endif
                                                <br>

                                                <div style="font-size: 16px; font-weight: bold; color: #333; line-height: 2.2; font-family: 'Courier New', monospace; background-color: #f8f9fa; padding: 12px; border-radius: 4px;">
                                                    MAE = Σ|Rekomendasi - Aktual| / n<br>
                                                    <br>
                                                    MAE = {{ round($totalAbsPac, 4) }} / {{ count($rows) }}<br>
                                                    <br>
                                                    <span style="color: #856404; background-color: #fff3cd; padding: 8px 12px; border-radius: 4px; display: inline-block;">MAE = {{ round($maeCalcPac, 4) }} ppm</span>
                                                </div>
                                            </div>
                                        @endif

                                        <table class="table table-sm table-bordered mt-2" style="font-size: 12px;">
                                            <thead class="thead-light">
                                                <tr><th>Simbol</th><th>Keterangan</th></tr>
                                            </thead>
                                            <tbody>
                                                <tr><td>|...|</td><td>Nilai absolut (selalu positif)</td></tr>
                                                <tr><td>n</td><td>Jumlah data evaluasi</td></tr>
                                            </tbody>
                                        </table>
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle"></i>
                                            Lebih intuitif dari RMSE. Artinya rata-rata besar selisih rekomendasi vs dosis aktual.
                                        </small>
                                    </div>
                                </div>
                            </div> --}}

                            {{-- MAPE --}}
                            <div class="col-md-6 mb-4">
                                <div class="card border-left-success h-100">
                                    <div class="card-body">
                                        <h6 class="font-weight-bold text-success">4. MAPE (Mean Absolute Percentage Error)</h6>
                                        <div class="bg-light p-3 rounded text-center mb-3">
                                            <code style="font-size: 14px;">
                                                MAPE = (Σ|Rekomendasi - Aktual| / |Aktual|) / n × 100%
                                            </code>
                                        </div>

                                        @if(count($rows) > 0)
                                            @php
                                                $totalPctPac = 0;
                                                $countPctPac = 0;
                                                $mapeDetailsPac = [];
                                                foreach($rows as $r) {
                                                    if($r['aktual_pac'] != 0) {
                                                        $err = abs($r['rekomen_pac'] - $r['aktual_pac']);
                                                        $pct = $err / $r['aktual_pac'];
                                                        $totalPctPac += $pct;
                                                        $countPctPac++;
                                                        $mapeDetailsPac[] = [
                                                            'rekomen'          => $r['rekomen_pac'],
                                                            'aktual'           => $r['aktual_pac'],
                                                            'error'            => round($err, 4),
                                                            'percentage'       => round($pct, 4),
                                                            'percentageValue'  => round($pct * 100, 2),
                                                        ];
                                                    }
                                                }
                                                $mapeCalcPac = $countPctPac > 0 ? ($totalPctPac / $countPctPac) * 100 : 0;
                                            @endphp

                                            <div class="alert alert-info p-2 mb-3">
                                                <small><strong>Perhitungan dari {{ $countPctPac }} data evaluasi PAC (aktual ≠ 0):</strong></small>
                                            </div>

                                            <div class="bg-light p-3 rounded mb-3" style="font-size: 13px; line-height: 2;">
                                                <strong>Persentase error setiap baris |Rekomendasi - Aktual| / |Aktual|:</strong><br>
                                                @foreach(array_slice($mapeDetailsPac, 0, 5) as $detail)
                                                    |{{ $detail['rekomen'] }} - {{ $detail['aktual'] }}| / |{{ $detail['aktual'] }}|<br>
                                                    = <span style="color: #28a745; font-weight: bold;">{{ $detail['error'] }}</span> / <span style="color: #007bff; font-weight: bold;">{{ $detail['aktual'] }}</span><br>
                                                    = <span style="color: #856404; font-weight: bold;">{{ $detail['percentage'] }}</span> (<span style="color: #dc3545; font-weight: bold;">{{ $detail['percentageValue'] }}%</span>)<br><br>
                                                @endforeach
                                                @if(count($mapeDetailsPac) > 5)
                                                    ... ({{ count($mapeDetailsPac) - 5 }} baris lainnya) ...<br>
                                                @endif
                                                <br>

                                                <div style="font-size: 16px; font-weight: bold; color: #333; line-height: 2.2; font-family: 'Courier New', monospace; background-color: #f8f9fa; padding: 12px; border-radius: 4px;">
                                                    MAPE = (Σ|Rekomendasi - Aktual| / |Aktual|) / n × 100%<br>
                                                    <br>
                                                    MAPE = ({{ round($totalPctPac, 4) }} / {{ $countPctPac }}) × 100%<br>
                                                    <br>
                                                    MAPE = {{ $countPctPac > 0 ? round($totalPctPac / $countPctPac, 4) : 0 }} × 100%<br>
                                                    <br>
                                                    <span style="color: #155724; background-color: #d4edda; padding: 8px 12px; border-radius: 4px; display: inline-block;">MAPE = {{ round($mapeCalcPac, 2) }}%</span>
                                                </div>
                                            </div>
                                        @endif

                                        <table class="table table-sm table-bordered mt-2" style="font-size: 12px;">
                                            <thead class="thead-light">
                                                <tr><th>MAPE</th><th>Interpretasi</th></tr>
                                            </thead>
                                            <tbody>
                                                <tr class="table-success"><td>&lt; 10%</td><td><strong>Sangat Akurat</strong></td></tr>
                                                <tr class="table-warning"><td>10% – 20%</td><td><strong>Akurat / Baik</strong></td></tr>
                                                <tr class="table-info"><td>20% – 50%</td><td><strong>Cukup / Wajar</strong></td></tr>
                                                <tr class="table-danger"><td>&gt; 50%</td><td><strong>Tidak Akurat</strong></td></tr>
                                            </tbody>
                                        </table>
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle"></i>
                                            Sumber: Khusmiawati et al. (2025). Satuan dalam persen (%). Metrik utama untuk menilai kelayakan model.
                                        </small>
                                    </div>
                                </div>
                            </div>

                            {{-- Range MF --}}
                            <div class="col-md-6 mb-4">
                                <div class="card border-left-warning h-100">
                                    <div class="card-body">
                                        <h6 class="font-weight-bold text-warning">5. Range Membership Function</h6>
                                        <table class="table table-sm table-bordered" style="font-size: 11px;">
                                            <thead class="thead-light">
                                                <tr><th>Kimia</th><th>Kategori</th><th>Range Input</th><th>Delta Dosis</th></tr>
                                            </thead>
                                            <tbody>
                                                <tr><td rowspan="5"><strong>PAC</strong><br><small>(NTU)</small></td><td>Sangat Rendah</td><td>≤ 2.0</td><td>−3.0</td></tr>
                                                <tr><td>Rendah</td><td>1.0 – 3.2</td><td>−1.0</td></tr>
                                                <tr><td>Optimal</td><td>2.8 – 3.8</td><td>0.0</td></tr>
                                                <tr><td>Tinggi</td><td>3.4 – 5.0</td><td>+1.5</td></tr>
                                                <tr><td>Sangat Tinggi</td><td>≥ 4.5</td><td>+3.5</td></tr>
                                                <tr><td rowspan="5"><strong>Klorin</strong><br><small>(mg/L)</small></td><td>Sangat Rendah</td><td>≤ 0.20</td><td>+1.0</td></tr>
                                                <tr><td>Rendah</td><td>0.15 – 0.30</td><td>+0.4</td></tr>
                                                <tr><td>Optimal</td><td>0.28 – 0.46</td><td>0.0</td></tr>
                                                <tr><td>Tinggi</td><td>0.43 – 0.52</td><td>−0.7</td></tr>
                                                <tr><td>Sangat Tinggi</td><td>≥ 0.50</td><td>−2.0</td></tr>
                                                <tr><td rowspan="4"><strong>Soda Ash</strong><br><small>(pH)</small></td><td>Sangat Rendah</td><td>3.0 – 5.2</td><td>+3.0</td></tr>
                                                <tr><td>Rendah</td><td>4.8 – 6.1</td><td>+2.0</td></tr>
                                                <tr><td>Sedikit Rendah</td><td>5.8 – 6.5</td><td>+1.0</td></tr>
                                                <tr><td>Normal (≥6.5)</td><td>≥ 6.3</td><td>Standby</td></tr>
                                            </tbody>
                                        </table>
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
                    <h6 class="m-0 font-weight-bold text-primary">Perbandingan Rekomendasi Fuzzy vs Dosis Aktual Operator</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm" style="font-size: 11px;">
                            <thead class="thead-light text-center">
                                <tr>
                                    <th rowspan="3">No</th>
                                    <th rowspan="3">Tanggal</th>
                                    <th rowspan="3">Shift</th>
                                    <th rowspan="3">Jam Rekomen<br><small>(T)</small></th>
                                    <th rowspan="3">Jam Aktual<br><small>(T+1)</small></th>
                                    <th rowspan="3">Turb Sed<br><small>(NTU)</small></th>
                                    <th rowspan="3">Free Chlor<br><small>(mg/L)</small></th>
                                    <th rowspan="3">pH Res</th>
                                    <th colspan="3">PAC (ppm)</th>
                                    <th colspan="3">Klorin (ppm)</th>
                                    <th colspan="3">Soda Ash (ppm)</th>
                                </tr>
                                <tr>
                                    <th>Rekomen</th><th>Aktual</th><th>Error</th>
                                    <th>Rekomen</th><th>Aktual</th><th>Error</th>
                                    <th>Rekomen</th><th>Aktual</th><th>Error</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($rows as $r)
                                    <tr class="text-center">
                                        <td>{{ $r['no'] }}</td>
                                        <td>{{ $r['date'] }}</td>
                                        <td class="text-uppercase">{{ $r['shift'] }}</td>
                                        <td>{{ $r['end_time'] }}</td>
                                        <td>{{ $r['next_end_time'] }}</td>
                                        <td>{{ $r['turb_sed'] }}</td>
                                        <td>{{ $r['free_chlor'] }}</td>
                                        <td>{{ $r['ph_res'] }}</td>

                                        {{-- PAC --}}
                                        <td>{{ $r['rekomen_pac'] }}</td>
                                        <td>{{ $r['aktual_pac'] }}</td>
                                        <td class="font-weight-bold
                                            @if(abs($r['error_pac']) <= 1) text-success
                                            @elseif(abs($r['error_pac']) <= 3) text-warning
                                            @else text-danger
                                            @endif">
                                            {{ $r['error_pac'] }}
                                        </td>

                                        {{-- Klorin --}}
                                        <td>{{ $r['rekomen_klorin'] }}</td>
                                        <td>{{ $r['aktual_klorin'] }}</td>
                                        <td class="font-weight-bold
                                            @if(abs($r['error_klorin']) <= 0.2) text-success
                                            @elseif(abs($r['error_klorin']) <= 0.5) text-warning
                                            @else text-danger
                                            @endif">
                                            {{ $r['error_klorin'] }}
                                        </td>

                                        {{-- Soda Ash --}}
                                        <td>{{ $r['rekomen_sodaash'] }}</td>
                                        <td>{{ $r['aktual_sodaash'] }}</td>
                                        <td class="font-weight-bold
                                            @if(abs($r['error_sodaash']) <= 0.5) text-success
                                            @elseif(abs($r['error_sodaash']) <= 1.5) text-warning
                                            @else text-danger
                                            @endif">
                                            {{ $r['error_sodaash'] }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="17" class="text-center text-muted py-3">
                                            <i class="fas fa-exclamation-circle"></i>
                                            Tidak ada data. Pastikan shift pada periode ini memiliki data kualitas air (sedimentation & reservoir) dan data kimia lengkap.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        <small>
                            <strong>Keterangan Warna Error:</strong>
                            <span class="text-success font-weight-bold ml-2">■ Kecil (Akurat)</span>
                            <span class="text-warning font-weight-bold ml-2">■ Sedang (Wajar)</span>
                            <span class="text-danger font-weight-bold ml-2">■ Besar (Perlu Perhatian)</span>
                            <span class="ml-3 text-muted">| Error = Rekomendasi Fuzzy − Dosis Aktual Operator</span>
                        </small>
                    </div>
                </div>
            </div>

        @else
            <div class="alert alert-info fade show mt-4" role="alert">
                <i class="fas fa-info-circle"></i>
                <strong>Silakan pilih tanggal dan klik "Tampilkan Data"</strong> untuk melihat hasil evaluasi Fuzzy Mamdani.
            </div>
        @endif

    </div>{{-- end wire:loading.remove --}}

</div>

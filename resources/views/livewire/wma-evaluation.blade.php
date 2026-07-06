<div class="container-fluid" style="position:relative;min-height:300px;">

    {{-- Header --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Evaluasi Akurasi Prediksi WMA</h1>
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
                        Bobot WMA: W₁={{ $weightInfo['w1'] }} (terbaru), W₂={{ $weightInfo['w2'] }}, W₃={{ $weightInfo['w3'] }} (terlama) | Total bobot={{ $weightInfo['total'] }} | Total data evaluasi: <strong>{{ count($rows) }}</strong> shift
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
            <p class="mt-3 text-primary font-weight-bold" style="font-size:16px;">Menghitung evaluasi WMA...</p>
        </div>
    </div>

    <div wire:loading.remove wire:target="applyFilter">

        @if($filteredStart && $filteredEnd)

            {{-- Ringkasan Metrik --}}
            <div class="row">
                @foreach (['turb', 'ph', 'tds'] as $key)
                    @php $label = $metricLabels[$key] ?? 'Unknown'; @endphp
                    <div class="col-md-4 mb-4">
                        <div class="card border-left-info shadow h-100 py-2">
                            <div class="card-body">
                                <div class="text-xs font-weight-bold text-info text mb-2">{{ $label }}</div>
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

            {{-- Rumus Evaluasi --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">📐 Rumus Evaluasi WMA (dengan Data Real)</h6>
                    <button class="btn btn-sm btn-outline-primary" type="button" data-toggle="collapse" data-target="#rumusCollapse">
                        <i class="fas fa-chevron-down"></i> Tampilkan/Sembunyikan
                    </button>
                </div>
                <div class="collapse" id="rumusCollapse">
                    <div class="card-body">
                        <div class="row">

                            {{-- WMA EXAMPLE --}}
                            <div class="col-md-6 mb-4">
                                <div class="card border-left-primary h-100">
                                    <div class="card-body">
                                        <h6 class="font-weight-bold text-primary">1. Weighted Moving Average (WMA)</h6>
                                        <div class="bg-light p-3 rounded text-center mb-3">
                                            <code style="font-size: 14px;">
                                                WMA = (W₁×D₁ + W₂×D₂ + W₃×D₃) / (W₁+W₂+W₃)
                                            </code>
                                        </div>

                                        @if(count($rows) >= 3)
                                            @php
                                                // Ambil 4 baris pertama dari tabel untuk contoh
                                                $sample = array_slice($rows, 0, 1);
                                                $row = $sample[0] ?? null;
                                            @endphp

                                            @if(count($rows) >= 4)
                                                @php
                                                    // Ambil baris ke-4 (index 3) sebagai contoh, karena dia punya 3 prior data
                                                    $exampleRow = $rows[3] ?? null;
                                                    $prior1 = $rows[0] ?? null;
                                                    $prior2 = $rows[1] ?? null;
                                                    $prior3 = $rows[2] ?? null;
                                                @endphp

                                                @if($exampleRow && $prior1 && $prior2 && $prior3)
                                                <div class="alert alert-info p-2 mb-3">
                                                    <small><strong>Contoh Perhitungan {{ strtoupper($exampleRow['shift']) }} {{ $exampleRow['date'] }}:</strong></small>
                                                </div>

                                                <div class="bg-light p-3 rounded mb-3" style="font-size: 13px; line-height: 1.8;">
                                                    <strong>Data dari 3 jam sebelumnya ({{ strtoupper($prior1['shift']) }}):</strong><br>
                                                    • Jam {{ $prior1['end_time'] }}: Turbidity = <strong>{{ $prior1['aktual_turb'] }}</strong> NTU<br>
                                                    • Jam {{ $prior2['end_time'] }}: Turbidity = <strong>{{ $prior2['aktual_turb'] }}</strong> NTU<br>
                                                    • Jam {{ $prior3['end_time'] }}: Turbidity = <strong>{{ $prior3['aktual_turb'] }}</strong> NTU<br><br>

                                                <strong>Prediksi untuk {{ strtoupper($exampleRow['shift']) }} jam {{ $exampleRow['end_time'] }}:</strong><br><br>

                                                <div style="font-size: 16px; font-weight: bold; color: #333; line-height: 2.2; font-family: 'Courier New', monospace;">
                                                    WMA = (W₁×D₁ + W₂×D₂ + W₃×D₃) / (W₁+W₂+W₃)<br>
                                                <br>
                                                    WMA = (<span style="color: #28a745; font-weight: bold;">{{ $prior1['aktual_turb'] }}</span> × <span style="color: #007bff; font-weight: bold;">{{ $weightInfo['w3'] }}</span> + <span style="color: #28a745; font-weight: bold;">{{ $prior2['aktual_turb'] }}</span> × <span style="color: #007bff; font-weight: bold;">{{ $weightInfo['w2'] }}</span> + <span style="color: #28a745; font-weight: bold;">{{ $prior3['aktual_turb'] }}</span> × <span style="color: #007bff; font-weight: bold;">{{ $weightInfo['w1'] }}</span>) / ({{ $weightInfo['w3'] }}+{{ $weightInfo['w2'] }}+{{ $weightInfo['w1'] }})<br>
                                                <br>
                                                    WMA = (<span style="color: #28a745; font-weight: bold;">{{ $prior1['aktual_turb'] }}</span> + <span style="color: #28a745; font-weight: bold;">{{ $prior2['aktual_turb'] * $weightInfo['w2'] }}</span> + <span style="color: #28a745; font-weight: bold;">{{ $prior3['aktual_turb'] * $weightInfo['w1'] }}</span>) / <span style="color: #ff00ea; font-weight: bold;">{{ $weightInfo['total'] }}</span><br>
                                                <br>
                                                    WMA = <span style="color: #28a745; font-weight: bold;">{{ ($prior1['aktual_turb'] + $prior2['aktual_turb'] * 2 + $prior3['aktual_turb'] * 8) }}</span> / {{ $weightInfo['total'] }}<br>
                                                <br>
                                                    <span style="color: #0c5460; background-color: #d1ecf1; padding: 8px 12px; border-radius: 4px; display: inline-block; font-size: 18px;">WMA = {{ $exampleRow['prediksi_turb'] }} NTU</span>
                                                </div>

                                                <br>
                                                <strong>Nilai Aktual {{ strtoupper($exampleRow['shift']) }} jam {{ $exampleRow['end_time'] }}:</strong> {{ $exampleRow['aktual_turb'] }} NTU<br>
                                                <strong>Prediksi (WMA):</strong> {{ $exampleRow['prediksi_turb'] }} NTU<br>
                                                <strong>Error:</strong> {{ $exampleRow['aktual_turb'] }} - {{ $exampleRow['prediksi_turb'] }} = <span class="@if(abs($exampleRow['error_turb']) <= 2) text-success @elseif(abs($exampleRow['error_turb']) <= 5) text-warning @else text-danger @endif font-weight-bold">{{ $exampleRow['error_turb'] }} NTU</span>
                                                </div>
                                                @endif
                                            @endif
                                        @endif

                                        <table class="table table-sm table-bordered mt-2" style="font-size: 12px;">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>Simbol</th>
                                                    <th>Keterangan</th>
                                                    <th>Nilai</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>W₁</td>
                                                    <td>Bobot data terbaru (shift t-1)</td>
                                                    <td><strong class="text-primary">{{ $weightInfo['w1'] }}</strong></td>
                                                </tr>
                                                <tr>
                                                    <td>W₂</td>
                                                    <td>Bobot data tengah (shift t-2)</td>
                                                    <td><strong class="text-primary">{{ $weightInfo['w2'] }}</strong></td>
                                                </tr>
                                                <tr>
                                                    <td>W₃</td>
                                                    <td>Bobot data terlama (shift t-3)</td>
                                                    <td><strong class="text-primary">{{ $weightInfo['w3'] }}</strong></td>
                                                </tr>
                                                <tr>
                                                    <td>D₁,D₂,D₃</td>
                                                    <td>Nilai aktual tiap shift</td>
                                                    <td>-</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <small class="text-muted" style="line-height: 1.8;">
                                            <i class="fas fa-info-circle"></i>
                                            <strong>Penjelasan Bobot WMA:</strong><br>
                                            • <strong style="color: #007bff;">W₁ = {{ $weightInfo['w1'] }}</strong> (Data terbaru): Bobot tertinggi karena data jam terakhir paling akurat mencerminkan kondisi air <strong>saat ini</strong>.<br>
                                            • <strong style="color: #007bff;">W₂ = {{ $weightInfo['w2'] }}</strong> (Data tengah): Bobot sedang karena masih relevan tapi kondisi air mungkin sudah berubah.<br>
                                            • <strong style="color: #007bff;">W₃ = {{ $weightInfo['w3'] }}</strong> (Data terlama): Bobot terendah karena data sudah lama, pengaruh kondisi sekarang lebih kecil.<br><br>
                                            <strong>Total bobot = {{ $weightInfo['w3'] }}+{{ $weightInfo['w2'] }}+{{ $weightInfo['w1'] }} = {{ $weightInfo['total'] }}</strong> digunakan sebagai pembagi untuk normalisasi hasil WMA agar nilai prediksi tetap dalam range yang wajar.
                                        </small>
                                    </div>
                                </div>
                            </div>

                            {{-- RMSE EXAMPLE (dinonaktifkan sementara) --}}
                            {{-- <div class="col-md-6 mb-4">
                                <div class="card border-left-danger h-100">
                                    <div class="card-body">
                                        <h6 class="font-weight-bold text-danger">2. RMSE (Root Mean Square Error)</h6>
                                        <div class="bg-light p-3 rounded text-center mb-3">
                                            <code style="font-size: 14px;">
                                                RMSE = √( Σ(Aktual - Prediksi)² / n )
                                            </code>
                                        </div>

                                            @if(count($rows) > 0)
                                                @php
                                                    $totalSquare = 0;
                                                    $errorDetails = [];
                                                    foreach($rows as $r) {
                                                        $err = $r['aktual_turb'] - $r['prediksi_turb'];
                                                        $errSquared = $err * $err;
                                                        $totalSquare += $errSquared;
                                                        $errorDetails[] = [
                                                            'aktual' => $r['aktual_turb'],
                                                            'prediksi' => $r['prediksi_turb'],
                                                            'error' => $err,
                                                            'errorSquared' => $errSquared
                                                        ];
                                                    }
                                                    $rmseCalc = count($rows) > 0 ? sqrt($totalSquare / count($rows)) : 0;
                                                @endphp

                                                <div class="alert alert-info p-2 mb-3">
                                                    <small><strong>Perhitungan dari {{ count($rows) }} data evaluasi Turbidity:</strong></small>
                                                </div>

                                                <div class="bg-light p-3 rounded mb-3" style="font-size: 13px; line-height: 2;">
                                                    <strong>Error setiap baris (Aktual - Prediksi)²:</strong><br>
                                                    @foreach(array_slice($errorDetails, 0, 5) as $i => $detail)
                                                        ({{ $detail['aktual'] }} - {{ $detail['prediksi'] }}) = <span style="color: #e74c3c; font-weight: bold;">{{ $detail['error'] }}</span><br>
                                                        {{ $detail['error'] }} × {{ $detail['error'] }} = <span style="color: #3498db; font-weight: bold;">{{ round($detail['errorSquared'], 2) }}</span><br><br>
                                                    @endforeach
                                                    @if(count($rows) > 5)
                                                        ... ({{ count($rows) - 5 }} baris lainnya) ...<br><br>
                                                    @endif

                                                    <strong>Total Σ(Error²) = </strong> {{ round($totalSquare, 2) }}<br><br>
                                                    
                                                    <div style="font-size: 16px; font-weight: bold; color: #333; line-height: 2.2; font-family: 'Courier New', monospace; background-color: #f8f9fa; padding: 12px; border-radius: 4px;">
                                                        RMSE = √( Σ(Aktual - Prediksi)² / n )<br>
                                                        <br>
                                                        RMSE = √( {{ round($totalSquare, 2) }} / {{ count($rows) }} )<br>
                                                        <br>
                                                        RMSE = √{{ round($totalSquare / count($rows), 2) }}<br>
                                                        <br>
                                                        <span style="color: #721c24; background-color: #f5c6cb; padding: 8px 12px; border-radius: 4px; display: inline-block;">RMSE = {{ round($rmseCalc, 4) }} NTU</span>
                                                    </div>
                                                </div>
                                            @endif

                                        <table class="table table-sm table-bordered mt-2" style="font-size: 12px;">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>Simbol</th>
                                                    <th>Keterangan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Aktual</td>
                                                    <td>Nilai yang terukur pada shift berikutnya</td>
                                                </tr>
                                                <tr>
                                                    <td>Prediksi</td>
                                                    <td>Nilai hasil perhitungan WMA</td>
                                                </tr>
                                                <tr>
                                                    <td>n</td>
                                                    <td>Jumlah data evaluasi</td>
                                                </tr>
                                                <tr>
                                                    <td>Σ</td>
                                                    <td>Jumlah dari semua data</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle"></i>
                                            Satuan sama dengan parameter (NTU/mg/L). Semakin kecil semakin akurat.
                                        </small>
                                    </div>
                                </div>
                            </div> --}}

                            {{-- MAE EXAMPLE (dinonaktifkan sementara) --}}
                            {{-- <div class="col-md-6 mb-4">
                                <div class="card border-left-warning h-100">
                                    <div class="card-body">
                                        <h6 class="font-weight-bold text-warning">3. MAE (Mean Absolute Error)</h6>
                                        <div class="bg-light p-3 rounded text-center mb-3">
                                            <code style="font-size: 14px;">
                                                MAE = Σ|Aktual - Prediksi| / n
                                            </code>
                                        </div>

                                        @if(count($rows) > 0)
                                            @php
                                                $totalAbs = 0;
                                                $absDetails = [];
                                                foreach($rows as $r) {
                                                    $err = abs($r['aktual_turb'] - $r['prediksi_turb']);
                                                    $totalAbs += $err;
                                                    $absDetails[] = [
                                                        'aktual' => $r['aktual_turb'],
                                                        'prediksi' => $r['prediksi_turb'],
                                                        'absError' => $err
                                                    ];
                                                }
                                                $maeCalc = count($rows) > 0 ? $totalAbs / count($rows) : 0;
                                            @endphp

                                            <div class="alert alert-info p-2 mb-3">
                                                <small><strong>Perhitungan dari {{ count($rows) }} data evaluasi Turbidity:</strong></small>
                                            </div>

                                            <div class="bg-light p-3 rounded mb-3" style="font-size: 13px; line-height: 2;">
                                                <strong>Error absolut setiap baris |Aktual - Prediksi|:</strong><br>
                                                @foreach(array_slice($absDetails, 0, 5) as $i => $detail)
                                                    |{{ $detail['aktual'] }} - {{ $detail['prediksi'] }}| = |<span style="color: #dc3545; font-weight: bold;">{{ round($detail['aktual'] - $detail['prediksi'], 2) }}</span>| = <span style="color: #28a745; font-weight: bold;">{{ round($detail['absError'], 2) }}</span><br>
                                                @endforeach
                                                @if(count($rows) > 5)
                                                    ... ({{ count($rows) - 5 }} baris lainnya) ...<br>
                                                @endif
                                                <br>

                                                <div style="font-size: 16px; font-weight: bold; color: #333; line-height: 2.2; font-family: 'Courier New', monospace; background-color: #f8f9fa; padding: 12px; border-radius: 4px;">
                                                   MAE = (|<span style="color: #dc3545; font-weight: bold;">-20.61</span>| + |<span style="color: #dc3545; font-weight: bold;">-4.7</span>| + |<span style="color: #dc3545; font-weight: bold;">-1.29</span>| + |<span style="color: #dc3545; font-weight: bold;">-0.93</span>| + |<span style="color: #dc3545; font-weight: bold;">-0.56</span>| + ...) / 11<br>
                                                    <br>
                                                    MAE = (<span style="color: #28a745; font-weight: bold;">20.61</span> + <span style="color: #28a745; font-weight: bold;">4.7</span> + <span style="color: #28a745; font-weight: bold;">1.29</span> + <span style="color: #28a745; font-weight: bold;">0.93</span> + <span style="color: #28a745; font-weight: bold;">0.56</span> + ...) / 11<br>
                                                    <br>
                                                    MAE = {{ round($totalAbs, 2) }} / {{ count($rows) }}<br>
                                                    <br>
                                                    <span style="color: #856404; background-color: #fff3cd; padding: 8px 12px; border-radius: 4px; display: inline-block;">MAE = {{ round($maeCalc, 4) }} NTU</span>
                                                </div>
                                            </div>
                                        @endif

                                        <table class="table table-sm table-bordered mt-2" style="font-size: 12px;">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>Simbol</th>
                                                    <th>Keterangan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>|...|</td>
                                                    <td>Nilai absolut (selalu positif)</td>
                                                </tr>
                                                <tr>
                                                    <td>n</td>
                                                    <td>Jumlah data evaluasi</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle"></i>
                                            Lebih intuitif dari RMSE. Artinya rata-rata besar kesalahan prediksi dalam satuan aslinya.
                                        </small>
                                    </div>
                                </div>
                            </div> --}}

                            {{-- MAPE EXAMPLE --}}
                            <div class="col-md-6 mb-4">
                                <div class="card border-left-success h-100">
                                    <div class="card-body">
                                        <h6 class="font-weight-bold text-success">4. MAPE (Mean Absolute Percentage Error)</h6>
                                        <div class="bg-light p-3 rounded text-center mb-3">
                                            <code style="font-size: 14px;">
                                                MAPE = (Σ|Aktual - Prediksi| / |Aktual|) / n × 100%
                                            </code>
                                        </div>

                                     @if(count($rows) > 0)
                                        @php
                                            $totalPct = 0;
                                            $countPct = 0;
                                            $mapeDetails = [];
                                            foreach($rows as $r) {
                                                if($r['aktual_turb'] != 0) {
                                                    $err = abs($r['aktual_turb'] - $r['prediksi_turb']);
                                                    $pct = ($err / $r['aktual_turb']);
                                                    $totalPct += $pct;
                                                    $countPct++;
                                                    $mapeDetails[] = [
                                                        'aktual' => $r['aktual_turb'],
                                                        'prediksi' => $r['prediksi_turb'],
                                                        'error' => $err,
                                                        'percentage' => $pct,
                                                        'percentageValue' => $pct * 100
                                                    ];
                                                }
                                            }
                                            $mapeCalc = $countPct > 0 ? ($totalPct / $countPct) * 100 : 0;
                                        @endphp

                                        <div class="alert alert-info p-2 mb-3">
                                            <small><strong>Perhitungan dari {{ count($rows) }} data evaluasi Turbidity:</strong></small>
                                        </div>

                                        <div class="bg-light p-3 rounded mb-3" style="font-size: 13px; line-height: 2;">
                                            <strong>Persentase error setiap baris |Aktual - Prediksi| / |Aktual|:</strong><br>
                                            @foreach(array_slice($mapeDetails, 0, 5) as $i => $detail)
                                                |{{ $detail['aktual'] }} - {{ $detail['prediksi'] }}| / |{{ $detail['aktual'] }}|<br>
                                                = <span style="color: #28a745; font-weight: bold;">{{ round($detail['error'], 2) }}</span> / <span style="color: #007bff; font-weight: bold;">{{ $detail['aktual'] }}</span><br>
                                                = <span style="color: #856404; font-weight: bold;">{{ round($detail['percentage'], 4) }}</span> (<span style="color: #dc3545; font-weight: bold;">{{ round($detail['percentageValue'], 2) }}%</span>)<br><br>
                                            @endforeach
                                            @if(count($mapeDetails) > 5)
                                                ... ({{ count($mapeDetails) - 5 }} baris lainnya) ...<br>
                                            @endif
                                            <br>

                                            <div style="font-size: 16px; font-weight: bold; color: #333; line-height: 2.2; font-family: 'Courier New', monospace; background-color: #f8f9fa; padding: 12px; border-radius: 4px;">
                                                MAPE = (Σ|Aktual - Prediksi| / |Aktual|) / n × 100%<br>
                                                <br>
                                                MAPE = ({{ round($totalPct, 4) }} / {{ $countPct }}) × 100%<br>
                                                <br>
                                                MAPE = {{ round($totalPct / $countPct, 4) }} × 100%<br>
                                                <br>
                                                <span style="color: #155724; background-color: #d4edda; padding: 8px 12px; border-radius: 4px; display: inline-block;">MAPE = {{ round($mapeCalc, 2) }}%</span>
                                            </div>
                                        </div>
                                    @endif

                                        <table class="table table-sm table-bordered mt-2" style="font-size: 12px;">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>MAPE</th>
                                                    <th>Interpretasi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr class="table-success">
                                                    <td>&lt; 10%</td>
                                                    <td><strong>Sangat Akurat</strong></td>
                                                </tr>
                                                <tr class="table-warning">
                                                    <td>10% - 20%</td>
                                                    <td><strong>Akurat / Baik</strong></td>
                                                </tr>
                                                <tr class="table-info">
                                                    <td>20% - 50%</td>
                                                    <td><strong>Cukup / Wajar</strong></td>
                                                </tr>
                                                <tr class="table-danger">
                                                    <td>&gt; 50%</td>
                                                    <td><strong>Tidak Akurat</strong></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle"></i>
                                            Sumber: Khusmiawati et al. (2025). Satuan dalam persen (%). Metrik utama untuk menilai kelayakan model prediksi.
                                        </small>
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
                    <h6 class="m-0 font-weight-bold text-primary">Perbandingan Nilai Aktual vs Prediksi WMA</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm" style="font-size: 12px;">
                            <thead class="thead-light text-center">
                                <tr>
                                    <th rowspan="2">No</th>
                                    <th rowspan="2">Tanggal</th>
                                    <th rowspan="2">Shift</th>
                                    <th rowspan="2">End Time</th>
                                    <th colspan="3">Turbidity (NTU)</th>
                                    <th colspan="3">pH</th>
                                    <th colspan="3">TDS (mg/L)</th>
                                </tr>
                                <tr>
                                    <th>Aktual</th><th>Prediksi</th><th>Error</th>
                                    <th>Aktual</th><th>Prediksi</th><th>Error</th>
                                    <th>Aktual</th><th>Prediksi</th><th>Error</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($rows as $r)
                                    <tr class="text-center">
                                        <td>{{ $r['no'] }}</td>
                                        <td>{{ $r['date'] }}</td>
                                        <td class="text-uppercase">{{ $r['shift'] }}</td>
                                        <td>{{ $r['end_time'] }}</td>

                                        {{-- Turbidity --}}
                                        <td>{{ $r['aktual_turb'] }}</td>
                                        <td>{{ $r['prediksi_turb'] }}</td>
                                        <td class="
                                            @if(abs($r['error_turb']) <= 2) text-success font-weight-bold
                                            @elseif(abs($r['error_turb']) <= 5) text-warning font-weight-bold
                                            @else text-danger font-weight-bold
                                            @endif">
                                            {{ $r['error_turb'] }}
                                        </td>

                                        {{-- pH --}}
                                        <td>{{ $r['aktual_ph'] }}</td>
                                        <td>{{ $r['prediksi_ph'] }}</td>
                                        <td class="
                                            @if(abs($r['error_ph']) <= 0.05) text-success font-weight-bold
                                            @elseif(abs($r['error_ph']) <= 0.1) text-warning font-weight-bold
                                            @else text-danger font-weight-bold
                                            @endif">
                                            {{ $r['error_ph'] }}
                                        </td>

                                        {{-- TDS --}}
                                        <td>{{ $r['aktual_tds'] }}</td>
                                        <td>{{ $r['prediksi_tds'] }}</td>
                                        <td class="
                                            @if(abs($r['error_tds']) <= 10) text-success font-weight-bold
                                            @elseif(abs($r['error_tds']) <= 20) text-warning font-weight-bold
                                            @else text-danger font-weight-bold
                                            @endif">
                                            {{ $r['error_tds'] }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="13" class="text-center text-muted py-3">
                                            <i class="fas fa-exclamation-circle"></i>
                                            Data tidak cukup. Minimal butuh 4 shift berurutan untuk evaluasi.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Keterangan Warna --}}
                    <div class="mt-3">
                        <small>
                            <strong>Keterangan Warna Error:</strong>
                            <span class="text-success font-weight-bold ml-2">■ Kecil (Akurat)</span>
                            <span class="text-warning font-weight-bold ml-2">■ Sedang (Wajar)</span>
                            <span class="text-danger font-weight-bold ml-2">■ Besar (Perlu Perhatian)</span>
                        </small>
                    </div>

                </div>
            </div>

        @else
        <div class="alert alert-info alert-dismissible fade show mt-4" role="alert">
            <i class="fas fa-info-circle"></i>
            <strong>Silakan pilih tanggal dan klik "Tampilkan Data"</strong> untuk melihat hasil evaluasi WMA.
        </div>
        @endif

    </div>{{-- end wire:loading.remove --}}

</div>
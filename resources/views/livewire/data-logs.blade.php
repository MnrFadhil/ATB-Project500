<div class="data-logs-page" x-data="{ openCat: null }">

    @if (!$submitted)
    {{-- ====================================================
         FASE 1 — iOS Form (sebelum submit)
         ==================================================== --}}

        <div class="ios-large-title">
            <h1>Data Logs Scada</h1>
            <p>Pilih periode dan kategori data yang ingin diambil dari historis <code>scada monitoring</code>.</p>
        </div>

        <form wire:submit.prevent="submit" class="ios-form">

            {{-- SECTION 1 — Quick Range --}}
            <div class="ios-section">
                <div class="ios-section-header">Rentang Cepat</div>
                <div class="ios-segmented">
                    @foreach ($quickRanges as [$key, $label])
                        <button type="button" class="ios-seg-btn"
                            wire:click="setQuickRange('{{ $key }}')">{{ $label }}</button>
                    @endforeach
                </div>
                <div class="ios-section-footer">Tombol cepat untuk mengisi rentang tanggal di bawah.</div>
            </div>

            {{-- SECTION 2 — Periode --}}
            <div class="ios-section">
                <div class="ios-section-header">Periode</div>
                <div class="ios-group">
                    <label class="ios-row">
                        <span class="ios-row-icon" style="background:#34C759">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="18" rx="3"/><path d="M3 10h18"/><path d="M8 2v4"/><path d="M16 2v4"/>
                            </svg>
                        </span>
                        <span class="ios-row-label">Dari</span>
                        <input type="datetime-local" class="ios-row-input"
                            wire:model.live="dateFrom"
                            max="{{ $dateTo }}">
                    </label>
                    <label class="ios-row">
                        <span class="ios-row-icon" style="background:#FF3B30">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="18" rx="3"/><path d="M3 10h18"/><path d="M8 2v4"/><path d="M16 2v4"/>
                            </svg>
                        </span>
                        <span class="ios-row-label">Sampai</span>
                        <input type="datetime-local" class="ios-row-input"
                            wire:model.live="dateTo"
                            min="{{ $dateFrom }}">
                    </label>
                </div>
                <div class="ios-section-footer">
                    {{ $fmtDateFrom }} — {{ $fmtDateTo }}
                </div>
                @error('dateFrom') <div class="ios-section-footer" style="color:#FF3B30">{{ $message }}</div> @enderror
                @error('dateTo')   <div class="ios-section-footer" style="color:#FF3B30">{{ $message }}</div> @enderror
            </div>

            {{-- SECTION 3 — Kategori + inline sensor checklist --}}
            <div class="ios-section">
                <div class="ios-section-header">Kategori Sensor</div>
                <div class="ios-group">
                    @foreach ($groupItems as [$gKey, $gLabel, $gDesc, $gColor])
                        @php
                            $sensorsInCat = array_values(array_filter($allFields, fn($f) => $gKey === 'all' || $f[2] === $gKey));
                            $selectedInCat = count(array_intersect(array_column($sensorsInCat, 0), $selectedFields));
                            $isSelected = $groupFilter === $gKey;
                        @endphp
                        <div
                            :class="{ 'is-open': openCat === '{{ $gKey }}', 'is-selected': '{{ $groupFilter }}' === '{{ $gKey }}' }"
                            class="ios-cat-block {{ $isSelected ? 'is-selected' : '' }}">

                            <button type="button"
                                class="ios-row ios-row-tap {{ $isSelected ? 'selected' : '' }}"
                                @click="
                                    if (openCat === '{{ $gKey }}') {
                                        openCat = null;
                                    } else {
                                        openCat = '{{ $gKey }}';
                                        $wire.setGroup('{{ $gKey }}');
                                    }
                                ">
                                <span class="ios-row-icon" style="background:{{ $gColor }}">
                                    @if ($gKey === 'all')
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><circle cx="12" cy="12" r="9"/></svg>
                                    @elseif ($gKey === 'flow')
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2s6 7 6 12a6 6 0 1 1-12 0c0-5 6-12 6-12z"/></svg>
                                    @elseif ($gKey === 'pressure')
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="13" r="8"/><path d="m12 13 4-4"/><path d="M9 3h6"/></svg>
                                    @elseif ($gKey === 'turb')
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12h18"/><path d="M3 6h18"/><path d="M3 18h18"/></svg>
                                    @elseif ($gKey === 'qual')
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 2v6l-5 9a3 3 0 0 0 3 4h8a3 3 0 0 0 3-4l-5-9V2"/><path d="M8 2h8"/></svg>
                                    @endif
                                </span>
                                <span class="ios-row-body">
                                    <span class="ios-row-title">{{ $gLabel }}</span>
                                    <span class="ios-row-sub">
                                        @if ($isSelected)
                                            {{ $selectedInCat }} dari {{ count($sensorsInCat) }} sensor dipilih
                                        @else
                                            {{ $gDesc }}
                                        @endif
                                    </span>
                                </span>
                                <span class="ios-row-trailing">
                                    @if ($isSelected)
                                        <span class="ios-row-value" style="font-variant-numeric:tabular-nums;min-width:0">
                                            {{ $selectedInCat === count($sensorsInCat) ? 'Semua' : $selectedInCat }}
                                        </span>
                                    @endif
                                    <svg class="ios-chevron" :class="{ 'open': openCat === '{{ $gKey }}' }"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="m6 9 6 6 6-6"/>
                                    </svg>
                                </span>
                            </button>

                            {{-- Inline sensor checklist (diperluas saat openCat === gKey) --}}
                            <div class="ios-sensor-list" x-show="openCat === '{{ $gKey }}'"
                                style="transition:opacity .2s ease">
                                <div class="ios-sensor-tools">
                                    <button type="button" class="ios-mini-btn" wire:click="selectAll">Pilih Semua</button>
                                    <button type="button" class="ios-mini-btn" wire:click="clearAll">Hapus Semua</button>
                                </div>
                                @foreach ($sensorsInCat as [$sField, $sLabel])
                                    @php $isChecked = in_array($sField, $selectedFields); @endphp
                                    <label class="ios-row ios-row-tap ios-checkrow {{ $isChecked ? 'checked' : '' }}">
                                        <span class="ios-checkbox {{ $isChecked ? 'checked' : '' }}">
                                            @if ($isChecked)
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M20 6 9 17l-5-5"/>
                                                </svg>
                                            @endif
                                        </span>
                                        <span class="ios-row-body">
                                            <span class="ios-row-title">{{ $sLabel }}</span>
                                            <span class="ios-row-sub">{{ $sField }}</span>
                                        </span>
                                        <input type="checkbox"
                                            class="ios-hidden-check"
                                            wire:model.live="selectedFields"
                                            value="{{ $sField }}">
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                @error('selectedFields')
                    <div class="ios-section-footer" style="color:#FF3B30">{{ $message }}</div>
                @enderror
                @if (empty($selectedFields))
                    <div class="ios-section-footer" style="color:#FF3B30">Pilih setidaknya satu sensor untuk menampilkan data.</div>
                @endif
            </div>

            {{-- SECTION 4 — Ringkasan + Submit --}}
            <div class="ios-section">
                <div class="ios-section-header">Ringkasan</div>
                <div class="ios-group">
                    <div class="ios-row ios-row-static">
                        <span class="ios-row-label">Periode</span>
                        <span class="ios-row-value">{{ $fmtDateFrom }} → {{ $fmtDateTo }}</span>
                    </div>
                    <div class="ios-row ios-row-static">
                        <span class="ios-row-label">Kategori</span>
                        <span class="ios-row-value">{{ $groupLabels[$groupFilter] }}</span>
                    </div>
                    <div class="ios-row ios-row-static">
                        <span class="ios-row-label">Sensor</span>
                        <span class="ios-row-value">{{ count($selectedFields) }} dari {{ count($groupFields) }}</span>
                    </div>
                </div>
            </div>

            <div class="ios-cta-wrap">
                <button type="submit" class="ios-cta-btn" @disabled(empty($selectedFields))>
                    <span wire:loading.remove wire:target="submit">Tampilkan Data</span>
                    <span wire:loading wire:target="submit">Memuat...</span>
                </button>
                <div class="ios-cta-hint">Data akan diambil histori <code>scada monitoring</code>.</div>
            </div>

        </form>

    @else
    {{-- ====================================================
         FASE 2 — Table View (setelah submit)
         ==================================================== --}}

        <div class="logs-result-shell">

            {{-- iOS back button --}}
            <button type="button" class="ios-back-btn" wire:click="resetForm">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m15 18-6-6 6-6"/>
                </svg>
                <span>Filter</span>
            </button>

            {{-- Page header --}}
            <div class="page-header">
                <div>
                    <h1 class="page-title">Data Logs</h1>
                    <div class="page-sub">
                        <span>Data historis dari tabel <code style="font-family:var(--f-mono);color:var(--brand)">sensor_logs</code></span>
                        <span class="clock-dot"></span>
                        <span>{{ $totalCount }} record · {{ $fmtDateFrom }} — {{ $fmtDateTo }}</span>
                    </div>
                </div>
                <div class="status-cluster">
                    <button class="refresh-btn" wire:click="exportCsv"
                        style="background:var(--brand);color:#fff;border-color:var(--brand);display:flex;align-items:center;gap:6px">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:13px;height:13px">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="M7 10l5 5 5-5"/><path d="M12 15V3"/>
                        </svg>
                        Export CSV
                    </button>
                </div>
            </div>

            {{-- Toolbar: active filter + search --}}
            <div class="logs-toolbar">
                <div class="filter-group">
                    <span class="filter-lbl">Filter Aktif</span>
                    <div class="active-filter-pill">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:12px;height:12px">
                            <path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3z"/>
                        </svg>
                        <strong>{{ $groupLabels[$groupFilter] }}</strong>
                        <span class="active-filter-sep">·</span>
                        <span>{{ count($cols) - 1 }} kolom</span>
                    </div>
                </div>
                <div class="filter-group" style="margin-left:auto;flex:0 0 auto">
                    <div class="search-wrap">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px">
                            <circle cx="11" cy="11" r="7"/><path d="m20 20-3-3"/>
                        </svg>
                        <input type="text" wire:model.live.debounce.300ms="search"
                            placeholder="Cari nilai / waktu...">
                    </div>
                </div>
            </div>

            {{-- Stats strip --}}
            @if (!empty($stats))
                <div class="stats-strip">
                    @foreach ($stats as $s)
                        <div class="stat-card">
                            <div class="stat-label">{{ $s['label'] }}</div>
                            <div class="stat-row">
                                <div><span>min</span><strong>{{ $s['min'] }}</strong></div>
                                <div><span>avg</span><strong>{{ $s['avg'] }}</strong></div>
                                <div><span>max</span><strong>{{ $s['max'] }}</strong></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Trend charts — ECharts, protected dengan wire:ignore --}}
            @if (!empty($chartData))
                <section class="section logs-charts" style="margin-bottom:18px">
                    <div class="section-head">
                        <h2 class="section-title">Tren Historis</h2>
                        <span class="section-meta">{{ $totalCount }} titik · sesuai filter aktif</span>
                    </div>

                    {{-- Data payload dibaca JS, di luar wire:ignore agar update saat re-render --}}
                    <div id="logs-chart-payload"
                        data-charts="{{ base64_encode(json_encode($chartData)) }}"
                        data-count="{{ $totalCount }}"></div>

                    <div class="logs-chart-stack" wire:ignore id="logs-chart-stack">
                        @foreach ($chartData as $i => $set)
                            <div class="chart-card">
                                <div class="chart-head">
                                    <h4>
                                        <span class="ic" style="background:{{ $set['series'][0]['color'] }}"></span>
                                        {{ $set['title'] }}
                                    </h4>
                                    <div class="chart-legend">
                                        @foreach ($set['series'] as $s)
                                            <span class="legend-item">
                                                <span class="sw" style="background:{{ $s['color'] }}"></span>
                                                {{ $s['name'] }}
                                            </span>
                                        @endforeach
                                        @if ($set['unit'])
                                            <span style="color:var(--text-3)">{{ $set['unit'] }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="chart-wrap">
                                    <div id="logs-chart-{{ $i }}" style="width:100%;height:170px"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- Data table --}}
            <div class="log-card">
                <div class="log-head" style="display:flex;align-items:center;justify-content:space-between;gap:12px">
                    <h3>Sensor Logs · {{ $totalCount }} record</h3>
                    <span class="section-meta">Halaman {{ $page + 1 }} / {{ $pageCount }}</span>
                </div>
                <div class="log-table-wrap scroll-hide">
                    <table class="log-table">
                        <thead>
                            <tr>
                                @foreach ($cols as [$colField, $colLabel, $colUnit])
                                    <th class="{{ $colField === 'timestamp' ? 't-stamp' : '' }}">{{ $colLabel }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @if ($pageRows->isEmpty())
                                <tr>
                                    <td colspan="{{ count($cols) }}"
                                        style="text-align:center;padding:28px;color:var(--text-3)">
                                        Tidak ada data untuk filter ini.
                                    </td>
                                </tr>
                            @else
                                @foreach ($pageRows as $row)
                                    <tr>
                                        @foreach ($cols as [$colField, $colLabel])
                                            @php
                                                if ($colField === 'timestamp') {
                                                    $val = \Carbon\Carbon::parse($row->timestamp)->format('d/m H:i:s');
                                                    $cls = 't-stamp';
                                                    $display = $val;
                                                } else {
                                                    $raw = $row->$colField;
                                                    $cls = '';
                                                    if ($colField === 'turbidity_baku' && $raw > 22) $cls = 'crit';
                                                    elseif (str_starts_with($colField, 'turbidity') && $raw > 5) $cls = 'warn';
                                                    elseif ($colField === 'free_chlorine' && $raw < 0.5) $cls = 'warn';
                                                    elseif ($colField === 'pressure_distribusi' && $raw > 6.5) $cls = 'crit';

                                                    if ($raw === null) {
                                                        $display = '—';
                                                    } elseif (str_starts_with($colField, 'flow') || str_starts_with($colField, 'scm')) {
                                                        $display = number_format((float)$raw, 1);
                                                    } else {
                                                        $display = number_format((float)$raw, 2);
                                                    }
                                                }
                                            @endphp
                                            <td class="{{ $cls }}">{{ $display }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>

                <div class="log-pagination">
                    <button wire:click="prevPage" @disabled($page === 0)>← Sebelumnya</button>
                    <div class="page-info">
                        Menampilkan <strong>{{ $totalCount > 0 ? $page * 20 + 1 : 0 }}</strong>–<strong>{{ min(($page + 1) * 20, $totalCount) }}</strong>
                        dari <strong>{{ $totalCount }}</strong>
                    </div>
                    <button wire:click="nextPage({{ $pageCount }})" @disabled($page >= $pageCount - 1)>Berikutnya →</button>
                </div>
            </div>

        </div>{{-- /logs-result-shell --}}

    @endif

</div>{{-- /data-logs-page --}}

@once
<script>
// ============================================================
// Data Logs — ECharts init
// Dipanggil setiap kali Livewire selesai update DOM
// ============================================================
(function () {
    let _logsCharts = {};

    function buildLogsChartOption(series, labels) {
        var isDark = document.documentElement.getAttribute('data-theme') === 'dark';
        var gridColor    = isDark ? '#262b36' : '#eef0f5';
        var textColor    = isDark ? '#8b8f9b' : '#6b7280';
        var tooltipBg    = isDark ? 'rgba(22,26,34,.98)' : 'rgba(255,255,255,.98)';
        var tooltipBorder= isDark ? '#262b36' : '#e5e7eb';
        var tooltipText  = isDark ? '#e6e9ef' : '#1f2937';
        var tooltipMeta  = isDark ? '#8b8f9b' : '#6b7280';

        // Format tooltip header dari ISO string ke "dd Mon yyyy · HH:mm:ss"
        var months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        function fmtTooltipHeader(labelVal) {
            var dt = new Date(labelVal);
            if (isNaN(dt.getTime())) return String(labelVal);
            var pad = function(n) { return String(n).padStart(2,'0'); };
            return pad(dt.getDate()) + ' ' + months[dt.getMonth()] + ' ' + dt.getFullYear()
                + ' &middot; ' + pad(dt.getHours()) + ':' + pad(dt.getMinutes()) + ':' + pad(dt.getSeconds());
        }

        return {
            animation: true,
            animationDuration: 400,
            grid: { left: 38, right: 14, top: 10, bottom: 18 },
            tooltip: {
                trigger: 'axis',
                backgroundColor: tooltipBg,
                borderColor: tooltipBorder,
                borderWidth: 1,
                padding: [8, 12],
                textStyle: { fontFamily: 'IBM Plex Mono, monospace', fontSize: 11, color: tooltipText },
                axisPointer: { lineStyle: { color: gridColor, type: 'dashed' } },
                formatter: function(params) {
                    var idx = params[0] && params[0].dataIndex;
                    var header = '';
                    if (labels && idx != null && labels[idx] != null) {
                        header = '<div style="color:' + tooltipMeta + ';font-size:10.5px;font-weight:500;'
                            + 'letter-spacing:.3px;margin-bottom:5px;padding-bottom:5px;'
                            + 'border-bottom:1px dashed ' + gridColor + ';">'
                            + fmtTooltipHeader(labels[idx]) + '</div>';
                    }
                    var body = params.map(function(p) {
                        return '<div style="display:flex;justify-content:space-between;gap:14px;line-height:1.5;">'
                            + '<span><span style="display:inline-block;width:8px;height:8px;border-radius:50%;'
                            + 'background:' + p.color + ';margin-right:6px;vertical-align:middle;"></span>'
                            + p.seriesName + '</span>'
                            + '<b style="font-variant-numeric:tabular-nums;">'
                            + (p.value != null ? (+p.value).toFixed(2) : '—') + '</b></div>';
                    }).join('');
                    return header + body;
                }
            },
            legend: { show: false },
            xAxis: {
                type: 'category',
                data: labels,
                boundaryGap: false,
                axisLine: { show: false },
                axisTick: { show: false },
                axisLabel: { show: false }
            },
            yAxis: {
                type: 'value',
                scale: true,
                splitNumber: 4,
                axisLine: { show: false },
                axisTick: { show: false },
                axisLabel: {
                    color: textColor,
                    fontFamily: 'IBM Plex Mono, monospace',
                    fontSize: 9.5,
                    formatter: function(v) { return (+v).toFixed(1); },
                    margin: 8
                },
                splitLine: { lineStyle: { color: gridColor, type: 'dashed', width: 0.8 } }
            },
            series: series.map(function (s) {
                var hexColor = s.color;
                var lastIdx  = s.data.length - 1;
                // Cari index valid terakhir (non-null)
                var lastValidIdx = lastIdx;
                for (var k = lastIdx; k >= 0; k--) {
                    if (s.data[k] != null) { lastValidIdx = k; break; }
                }
                return {
                    name: s.name,
                    type: 'line',
                    data: s.data,
                    smooth: true,
                    smoothMonotone: 'x',
                    symbol: 'circle',
                    symbolSize: 0,
                    showSymbol: false,
                    lineStyle: { color: hexColor, width: 1.6 },
                    itemStyle: { color: hexColor, borderColor: hexColor, borderWidth: 1 },
                    areaStyle: {
                        color: {
                            type: 'linear', x: 0, y: 0, x2: 0, y2: 1,
                            colorStops: [
                                { offset: 0, color: hexColor + '40' },
                                { offset: 1, color: hexColor + '00' }
                            ]
                        }
                    },
                    markPoint: {
                        symbol: 'circle',
                        symbolSize: 8,
                        animation: false,
                        itemStyle: {
                            color: hexColor,
                            borderColor: '#fff',
                            borderWidth: 2,
                            shadowColor: hexColor,
                            shadowBlur: 6
                        },
                        data: s.data[lastValidIdx] != null
                            ? [{ coord: [lastValidIdx, s.data[lastValidIdx]] }]
                            : []
                    }
                };
            })
        };
    }

    function initLogsCharts() {
        var payload = document.getElementById('logs-chart-payload');
        if (!payload) return;

        var encoded = payload.getAttribute('data-charts');
        if (!encoded) return;

        var chartData;
        try {
            chartData = JSON.parse(atob(encoded));
        } catch (e) {
            console.error('[DataLogs] chart JSON parse error:', e);
            return;
        }

        // Bersihkan chart lama yang container-nya tidak ada lagi
        Object.keys(_logsCharts).forEach(function (k) {
            if (!document.getElementById('logs-chart-' + k)) {
                _logsCharts[k].dispose();
                delete _logsCharts[k];
            }
        });

        chartData.forEach(function (set, i) {
            var el = document.getElementById('logs-chart-' + i);
            if (!el) return;

            if (_logsCharts[i]) {
                _logsCharts[i].dispose();
            }
            _logsCharts[i] = echarts.init(el, null, { renderer: 'svg' });
            _logsCharts[i].setOption(buildLogsChartOption(set.series, set.labels));
            _logsCharts[i].resize();
        });
    }

    // Expose ke window agar bisa dipanggil dari $this->js() PHP
    window.initLogsCharts = initLogsCharts;

    // Init saat navigasi wire:navigate
    document.addEventListener('livewire:navigated', initLogsCharts);

    // Init via Livewire hook — fires setelah setiap commit/DOM update
    document.addEventListener('livewire:initialized', function () {
        Livewire.hook('commit', function (_ref) {
            var succeed = _ref.succeed;
            succeed(function () {
                requestAnimationFrame(initLogsCharts);
            });
        });
    });

    // Fallback: init saat halaman pertama kali load (bukan via wire:navigate)
    document.addEventListener('DOMContentLoaded', function () {
        requestAnimationFrame(initLogsCharts);
    });

    // Resize handler
    window.addEventListener('resize', function () {
        Object.values(_logsCharts).forEach(function (c) { c.resize(); });
    });
})();
</script>
@endonce

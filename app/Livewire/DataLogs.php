<?php

namespace App\Livewire;

use App\Models\SensorLog;
use Carbon\Carbon;
use Livewire\Component;

class DataLogs extends Component
{
    public string $dateFrom    = '';
    public string $dateTo      = '';
    public string $groupFilter = 'all';
    public array  $selectedFields = [];
    public bool   $submitted   = false;
    public string $search      = '';
    public int    $page        = 0;

    private const PAGE_SIZE = 20;

    // [field, label, group, unit]
    public const ALL_FIELDS = [
        ['flow_intake',         'Flow Intake',        'flow',     'L/s'],
        ['flow_yos_sudarso',    'Flow Yos Sudarso',   'flow',     'L/s'],
        ['flow_veteran',        'Flow Veteran',        'flow',     'L/s'],
        ['flow_bypass_yoss',    'Flow Bypass Yos',     'flow',     'L/s'],
        ['flow_bypass_vet',     'Flow Bypass Veteran', 'flow',     'L/s'],
        ['flow_backwash',       'Flow Backwash',       'flow',     'L/s'],
        ['pressure_intake',     'Pressure Intake',     'pressure', 'bar'],
        ['pressure_distribusi', 'Pressure Distribusi', 'pressure', 'bar'],
        ['pressure_service2',   'Pressure Service',    'pressure', 'bar'],
        ['pressure_backwash',   'Pressure Backwash',   'pressure', 'bar'],
        ['pressure_kompressor', 'Pressure Kompressor', 'pressure', 'bar'],
        ['level_reservoir_a',   'Level Reservoir A',   'pressure', 'm'],
        ['level_reservoir_b',   'Level Reservoir B',   'pressure', 'm'],
        ['turbidity_baku',      'Turbidity Baku',      'turb',     'NTU'],
        ['turbidity_reservoir', 'Turbidity Reservoir', 'turb',     'NTU'],
        ['turbidity_filter',    'Turbidity Filter',    'turb',     'NTU'],
        ['ph_baku',             'pH Baku',             'qual',     ''],
        ['ph_reservoir',        'pH Reservoir',        'qual',     ''],
        ['free_chlorine',       'Free Chlorine',       'qual',     'mg/L'],
        ['scm',                 'SCM',                 'qual',     ''],
    ];

    public function mount(): void
    {
        $now       = Carbon::now('Asia/Jakarta');
        $yesterday = Carbon::now('Asia/Jakarta')->subDay();
        $this->dateTo   = $now->format('Y-m-d\TH:i');
        $this->dateFrom = $yesterday->format('Y-m-d\TH:i');

        // Default: pilih semua field
        $this->selectedFields = array_column(self::ALL_FIELDS, 0);
    }

    public function setQuickRange(string $label): void
    {
        $now = Carbon::now('Asia/Jakarta');
        $this->dateTo = $now->format('Y-m-d\TH:i');
        $this->dateFrom = match ($label) {
            'today' => Carbon::now('Asia/Jakarta')->startOfDay()->format('Y-m-d\TH:i'),
            '24h'   => $now->copy()->subHours(24)->format('Y-m-d\TH:i'),
            '7d'    => $now->copy()->subDays(7)->format('Y-m-d\TH:i'),
            '30d'   => $now->copy()->subDays(30)->format('Y-m-d\TH:i'),
            '90d'   => $now->copy()->subDays(90)->format('Y-m-d\TH:i'),
            default => $now->copy()->subDay()->format('Y-m-d\TH:i'),
        };
    }

    public function setGroup(string $group): void
    {
        $this->groupFilter = $group;
        // Auto-pilih semua field dalam group baru
        $fields = array_filter(self::ALL_FIELDS, fn($f) => $group === 'all' || $f[2] === $group);
        $this->selectedFields = array_column(array_values($fields), 0);
    }

    public function selectAll(): void
    {
        $fields = array_filter(self::ALL_FIELDS, fn($f) => $this->groupFilter === 'all' || $f[2] === $this->groupFilter);
        $this->selectedFields = array_column(array_values($fields), 0);
    }

    public function clearAll(): void
    {
        $this->selectedFields = [];
    }

    public function submit(): void
    {
        $this->validate([
            'dateFrom' => 'required|date',
            'dateTo'   => 'required|date|after_or_equal:dateFrom',
        ], [
            'dateFrom.required'          => 'Tanggal mulai wajib diisi.',
            'dateTo.required'            => 'Tanggal akhir wajib diisi.',
            'dateTo.after_or_equal'      => 'Tanggal akhir harus setelah tanggal mulai.',
        ]);

        if (empty($this->selectedFields)) {
            $this->addError('selectedFields', 'Pilih minimal satu sensor.');
            return;
        }

        $this->submitted = true;
        $this->page      = 0;
        // Panggil JS setelah DOM di-update Livewire
        $this->js('requestAnimationFrame(function(){ window.initLogsCharts && window.initLogsCharts(); })');
    }

    public function resetForm(): void
    {
        $this->submitted = false;
        $this->search    = '';
        $this->page      = 0;
        $this->resetValidation();
    }

    public function updatedSearch(): void
    {
        $this->page = 0;
        $this->js('requestAnimationFrame(function(){ window.initLogsCharts && window.initLogsCharts(); })');
    }

    public function prevPage(): void
    {
        $this->page = max(0, $this->page - 1);
    }

    public function nextPage(int $pageCount): void
    {
        $this->page = min($pageCount - 1, $this->page + 1);
    }

    private function getGroupFields(?string $group = null): array
    {
        $g = $group ?? $this->groupFilter;
        return array_values(array_filter(self::ALL_FIELDS, fn($f) => $g === 'all' || $f[2] === $g));
    }

    private function fmtDate(string $dt): string
    {
        if (!$dt) return '—';
        try {
            $months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
            $d = Carbon::parse($dt);
            return $d->day . ' ' . $months[$d->month - 1] . ' ' . $d->year . ', ' . $d->format('H:i');
        } catch (\Throwable) {
            return '—';
        }
    }

    public function exportCsv()
    {
        if (!$this->submitted) return;

        $from = Carbon::parse($this->dateFrom, 'Asia/Jakarta');
        $to   = Carbon::parse($this->dateTo, 'Asia/Jakarta');

        $selectFields = array_values(array_filter(self::ALL_FIELDS, fn($f) => in_array($f[0], $this->selectedFields)));
        $selectCols   = ['timestamp', ...array_column($selectFields, 0)];

        $logs = SensorLog::whereBetween('timestamp', [$from, $to])
            ->orderByDesc('timestamp')
            ->get($selectCols);

        $search = trim($this->search);
        if ($search) {
            $logs = $logs->filter(function ($row) use ($search, $selectFields) {
                if (str_contains(Carbon::parse($row->timestamp)->format('d/m H:i:s'), $search)) return true;
                foreach ($selectFields as [$field]) {
                    if (str_contains((string) ($row->$field ?? ''), $search)) return true;
                }
                return false;
            });
        }

        $header = ['timestamp', ...array_column($selectFields, 1)];
        $lines  = [implode(',', $header)];
        foreach ($logs as $row) {
            $cells = [Carbon::parse($row->timestamp)->format('Y-m-d H:i:s')];
            foreach ($selectFields as [$field]) {
                $cells[] = $row->$field ?? '';
            }
            $lines[] = implode(',', $cells);
        }

        $filename = 'sensor_logs_' . $this->groupFilter . '_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($lines) {
            echo implode("\n", $lines);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    public function render()
    {
        $allFields   = self::ALL_FIELDS;
        $groupFields = $this->getGroupFields();
        $groupLabels = [
            'all'      => 'Semua Data',
            'flow'     => 'Flow',
            'pressure' => 'Pressure & Level',
            'turb'     => 'Turbidity',
            'qual'     => 'Kualitas',
        ];
        $groupItems = [
            ['all',      'Semua Data',       'Tampilkan semua sensor',                  '#8E8E93'],
            ['flow',     'Flow',             'Intake, Yos, Veteran, Bypass, Backwash',  '#0A84FF'],
            ['pressure', 'Pressure & Level', 'Tekanan & level reservoir',               '#FF453A'],
            ['turb',     'Turbidity',        'Air Baku, Reservoir, Filter',             '#FF9F0A'],
            ['qual',     'Kualitas',         'pH, Free Chlorine, SCM',                  '#30D158'],
        ];
        $quickRanges = [
            ['today', 'Hari Ini'],
            ['24h',   '24 Jam'],
            ['7d',    '7 Hari'],
            ['30d',   '30 Hari'],
            ['90d',   '90 Hari'],
        ];

        $filtered   = collect();
        $cols       = [];
        $stats      = [];
        $pageRows   = collect();
        $pageCount  = 1;
        $totalCount = 0;
        $chartData  = [];

        if ($this->submitted) {
            $from = Carbon::parse($this->dateFrom, 'Asia/Jakarta');
            $to   = Carbon::parse($this->dateTo, 'Asia/Jakarta');

            // Kolom yang dipilih user (filter dari ALL_FIELDS agar urutan konsisten)
            $selectFields = array_values(array_filter(self::ALL_FIELDS, fn($f) => in_array($f[0], $this->selectedFields)));
            $selectCols   = ['timestamp', ...array_column($selectFields, 0)];

            $logs = SensorLog::whereBetween('timestamp', [$from, $to])
                ->orderByDesc('timestamp')
                ->get($selectCols);

            // Search filter
            $search   = trim($this->search);
            $filtered = $search ? $logs->filter(function ($row) use ($search, $selectFields) {
                if (str_contains(Carbon::parse($row->timestamp)->format('d/m H:i:s'), $search)) return true;
                foreach ($selectFields as [$field]) {
                    if (str_contains((string) ($row->$field ?? ''), $search)) return true;
                }
                return false;
            })->values() : $logs;

            // Kolom header tabel: [field, label, unit]
            $cols = [['timestamp', 'Timestamp', ''], ...$selectFields];

            // Stats strip: min/avg/max untuk 6 field pertama
            $statsFields = array_slice($selectFields, 0, 6);
            foreach ($statsFields as [$field, $label]) {
                $vals = $filtered->pluck($field)->filter(fn($v) => $v !== null)->map(fn($v) => (float) $v);
                if ($vals->isEmpty()) continue;
                $stats[] = [
                    'field' => $field,
                    'label' => $label,
                    'min'   => number_format($vals->min(), 2),
                    'avg'   => number_format($vals->avg(), 2),
                    'max'   => number_format($vals->max(), 2),
                ];
            }

            // Pagination
            $totalCount = $filtered->count();
            $pageCount  = max(1, (int) ceil($totalCount / self::PAGE_SIZE));
            $this->page = min($this->page, $pageCount - 1);
            $pageRows   = $filtered->slice($this->page * self::PAGE_SIZE, self::PAGE_SIZE);

            // Chart data (chronological)
            $chrono = $filtered->reverse()->values();
            if ($chrono->isNotEmpty()) {
                // ISO string agar tooltip JS bisa parse jadi Date object
                $labels           = $chrono->map(fn($r) => Carbon::parse($r->timestamp)->format('Y-m-d\TH:i:s'))->toArray();
                $activeFieldNames = array_column($selectFields, 0);

                $chartSetsConfig = [
                    'all' => [
                        ['title' => 'Flow Rate', 'unit' => 'L/s', 'series' => [
                            ['name' => 'Flow Intake',  'color' => '#52B0ED', 'field' => 'flow_intake'],
                            ['name' => 'Yos+Veteran',  'color' => '#E4509A', 'combo' => ['flow_yos_sudarso', 'flow_veteran']],
                        ]],
                        ['title' => 'Turbidity', 'unit' => 'NTU', 'series' => [
                            ['name' => 'Reservoir', 'color' => '#52B0ED', 'field' => 'turbidity_reservoir'],
                            ['name' => 'Filter',    'color' => '#5FDA5A', 'field' => 'turbidity_filter'],
                        ]],
                        ['title' => 'pH', 'unit' => '', 'series' => [
                            ['name' => 'pH Baku',      'color' => '#52B0ED', 'field' => 'ph_baku'],
                            ['name' => 'pH Reservoir', 'color' => '#E4509A', 'field' => 'ph_reservoir'],
                        ]],
                    ],
                    'flow' => [
                        ['title' => 'Flow Sensor', 'unit' => 'L/s', 'series' => [
                            ['name' => 'Intake',     'color' => '#4e73df', 'field' => 'flow_intake'],
                            ['name' => 'Yos',        'color' => '#1cc88a', 'field' => 'flow_yos_sudarso'],
                            ['name' => 'Veteran',    'color' => '#36b9cc', 'field' => 'flow_veteran'],
                            ['name' => 'Bypass Yos', 'color' => '#F4C133', 'field' => 'flow_bypass_yoss'],
                            ['name' => 'Bypass Vet', 'color' => '#E4509A', 'field' => 'flow_bypass_vet'],
                            ['name' => 'Backwash',   'color' => '#AF8CB1', 'field' => 'flow_backwash'],
                        ]],
                    ],
                    'pressure' => [
                        ['title' => 'Pressure', 'unit' => 'bar', 'series' => [
                            ['name' => 'Intake',     'color' => '#4e73df', 'field' => 'pressure_intake'],
                            ['name' => 'Distribusi', 'color' => '#e74a3b', 'field' => 'pressure_distribusi'],
                            ['name' => 'Service',    'color' => '#36b9cc', 'field' => 'pressure_service2'],
                            ['name' => 'Backwash',   'color' => '#AF8CB1', 'field' => 'pressure_backwash'],
                            ['name' => 'Kompressor', 'color' => '#f6c23e', 'field' => 'pressure_kompressor'],
                        ]],
                        ['title' => 'Level Reservoir', 'unit' => 'm', 'series' => [
                            ['name' => 'Reservoir A', 'color' => '#52B0ED', 'field' => 'level_reservoir_a'],
                            ['name' => 'Reservoir B', 'color' => '#E4509A', 'field' => 'level_reservoir_b'],
                        ]],
                    ],
                    'turb' => [
                        ['title' => 'Turbidity', 'unit' => 'NTU', 'series' => [
                            ['name' => 'Air Baku',  'color' => '#f6c23e', 'field' => 'turbidity_baku'],
                            ['name' => 'Reservoir', 'color' => '#36b9cc', 'field' => 'turbidity_reservoir'],
                            ['name' => 'Filter',    'color' => '#5FDA5A', 'field' => 'turbidity_filter'],
                        ]],
                    ],
                    'qual' => [
                        ['title' => 'pH', 'unit' => '', 'series' => [
                            ['name' => 'pH Baku',      'color' => '#4e73df', 'field' => 'ph_baku'],
                            ['name' => 'pH Reservoir', 'color' => '#E4509A', 'field' => 'ph_reservoir'],
                        ]],
                        ['title' => 'Free Chlorine & SCM', 'unit' => '', 'series' => [
                            ['name' => 'Free Cl₂', 'color' => '#1cc88a', 'field' => 'free_chlorine'],
                            ['name' => 'SCM',      'color' => '#AF8CB1', 'field' => 'scm'],
                        ]],
                    ],
                ];

                $activeSets = $chartSetsConfig[$this->groupFilter] ?? $chartSetsConfig['all'];

                foreach ($activeSets as $set) {
                    $seriesData = [];
                    foreach ($set['series'] as $s) {
                        // Combo series (sum dua field) selalu ditampilkan
                        if (isset($s['combo'])) {
                            $data = $chrono->map(fn($r) =>
                                round((float) ($r->{$s['combo'][0]} ?? 0) + (float) ($r->{$s['combo'][1]} ?? 0), 1)
                            )->toArray();
                        } else {
                            // Skip jika field tidak dipilih user
                            if (!in_array($s['field'], $activeFieldNames)) continue;
                            $data = $chrono->pluck($s['field'])->map(fn($v) => $v !== null ? (float) $v : null)->toArray();
                        }
                        $seriesData[] = ['name' => $s['name'], 'color' => $s['color'], 'data' => $data];
                    }
                    if (!empty($seriesData)) {
                        $chartData[] = [
                            'title'  => $set['title'],
                            'unit'   => $set['unit'],
                            'series' => $seriesData,
                            'labels' => $labels,
                        ];
                    }
                }
            }
        }

        $fmtDateFrom = $this->fmtDate($this->dateFrom);
        $fmtDateTo   = $this->fmtDate($this->dateTo);

        return view('livewire.data-logs', compact(
            'allFields', 'groupFields', 'groupLabels', 'groupItems', 'quickRanges',
            'filtered', 'cols', 'stats', 'pageRows', 'pageCount', 'totalCount',
            'fmtDateFrom', 'fmtDateTo', 'chartData'
        ))->layout('layouts.app');
    }
}

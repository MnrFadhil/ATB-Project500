<?php

namespace App\Livewire;

use App\Services\WmaEvaluationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Livewire\Component;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Contracts\View\View;

class WmaEvaluationExport implements FromView, WithTitle
{
    public function __construct(
        protected array $rows,
        protected array $metrics,
        protected string $startDate,
        protected string $endDate
    ) {}

    public function view(): View
    {
        return view('download.wma-evaluation-excel', [
            'rows'      => $this->rows,
            'metrics'   => $this->metrics,
            'startDate' => $this->startDate,
            'endDate'   => $this->endDate,
        ]);
    }

    public function title(): string
    {
        return 'Evaluasi WMA';
    }
}

class WmaEvaluation extends Component
{
    // Tanggal di form input (belum di-apply)
    public string $startDate = '';
    public string $endDate   = '';

    // Tanggal yang sudah di-apply (dipakai untuk query)
    public string $filteredStart = '';
    public string $filteredEnd   = '';

    public function mount()
    {
        // Default: 30 hari terakhir (tapi JANGAN langsung apply)
        $this->endDate   = Carbon::now()->format('Y-m-d');
        $this->startDate = Carbon::now()->subDays(1)->format('Y-m-d');

        // Kosongkan filtered date → tabel kosong sampai user klik filter
        $this->filteredStart = '';
        $this->filteredEnd   = '';
    }

    // Dipanggil ketika tombol "Tampilkan Data" diklik
    public function applyFilter(string $start, string $end)
    {
        // Validasi tidak boleh kosong
        if (!$start || !$end) return;

        // Validasi tanggal mulai tidak boleh lebih besar dari akhir
        if ($start > $end) {
            $temp  = $start;
            $start = $end;
            $end   = $temp;
        }

        // Simpan ke filtered → ini yang dipakai query di buildData()
        $this->filteredStart = $start;
        $this->filteredEnd   = $end;

        // Update juga startDate & endDate supaya input form tetap sinkron
        $this->startDate = $start;
        $this->endDate   = $end;
    }
    private function buildData(): array
    {
        $service = new WmaEvaluationService();

        // Pakai filteredStart & filteredEnd, BUKAN startDate & endDate
        $rows = $service->buildEvaluationData($this->filteredStart, $this->filteredEnd);

        $metrics = [
            'turb' => $service->calculateMetrics($rows, 'turb'),
            'ph'   => $service->calculateMetrics($rows, 'ph'),
            'tds'  => $service->calculateMetrics($rows, 'tds'),
        ];

        // Tambahkan label untuk setiap metrik
        $metricLabels = [
            'turb' => 'Turbidity Air Baku',
            'ph'   => 'pH Air Baku',
            'tds'  => 'TDS Air Baku',
        ];
        foreach ($metrics as $k => $m) {
            $metrics[$k]['interpretasi'] = $service->interpretMape($m['mape']);
        }

        // Tambahkan bobot info
        $weightInfo = $service->getWeights();

        return [$rows, $metrics, $metricLabels, $weightInfo];
    }

    public function exportExcel()
    {
        [$rows, $metrics] = $this->buildData();

        return Excel::download(
            new WmaEvaluationExport($rows, $metrics, $this->filteredStart, $this->filteredEnd),
            "Evaluasi-WMA_{$this->filteredStart}_sd_{$this->filteredEnd}.xlsx"
        );
    }

    public function exportPdf()
    {
        [$rows, $metrics] = $this->buildData();

        $pdf = Pdf::loadView('download.wma-evaluation-pdf', [
            'rows'      => $rows,
            'metrics'   => $metrics,
            'startDate' => $this->filteredStart,
            'endDate'   => $this->filteredEnd,
        ])->setPaper('a4', 'landscape');

        return response()->streamDownload(
            fn() => print($pdf->stream()),
            "Evaluasi-WMA_{$this->filteredStart}_sd_{$this->filteredEnd}.pdf"
        );
    }

    public function render()
    {
        [$rows, $metrics, $metricLabels,$weightInfo] = $this->buildData();

    return view('livewire.wma-evaluation', [
        'rows'           => $rows,
        'metrics'        => $metrics,
        'metricLabels'   => $metricLabels,
        'weightInfo'     => $weightInfo,
    ])->layout('layouts.app');
    }
}
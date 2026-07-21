<?php

namespace App\Livewire;

use App\Services\FuzzyEvaluationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Livewire\Component;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Contracts\View\View;

class FuzzyEvaluationExport implements FromView, WithTitle
{
    public function __construct(
        protected array $rows,
        protected array $metrics,
        protected string $startDate,
        protected string $endDate
    ) {}

    public function view(): View
    {
        return view('download.fuzzy-evaluation-excel', [
            'rows'      => $this->rows,
            'metrics'   => $this->metrics,
            'startDate' => $this->startDate,
            'endDate'   => $this->endDate,
        ]);
    }

    public function title(): string
    {
        return 'Evaluasi Fuzzy';
    }
}

class FuzzyEvaluation extends Component
{
    public string $startDate     = '';
    public string $endDate       = '';
    public string $filteredStart = '';
    public string $filteredEnd   = '';

    public function mount()
    {
        $this->endDate   = Carbon::now()->format('Y-m-d');
        $this->startDate = Carbon::now()->subDays(1)->format('Y-m-d');

        $this->filteredStart = '';
        $this->filteredEnd   = '';
    }

    public function applyFilter(string $start, string $end)
    {
        if (!$start || !$end) return;

        if ($start > $end) {
            [$start, $end] = [$end, $start];
        }

        $this->filteredStart = $start;
        $this->filteredEnd   = $end;
        $this->startDate     = $start;
        $this->endDate       = $end;
    }

    private function buildData(): array
    {
        $service = new FuzzyEvaluationService();
        $rows    = $service->buildEvaluationData($this->filteredStart, $this->filteredEnd);

        $metrics = [
            'pac'     => $service->calculateMetrics($rows, 'pac'),
            'klorin'  => $service->calculateMetrics($rows, 'klorin'),
            'sodaash' => $service->calculateMetrics($rows, 'sodaash'),
        ];

        foreach ($metrics as $k => $m) {
            $metrics[$k]['interpretasi'] = $service->interpretMape($m['mape']);
        }

        $metricLabels = [
            'pac'     => 'PAC (ppm)',
            'klorin'  => 'Klorin/Kaporit (ppm)',
            'sodaash' => 'Soda Ash (ppm)',
        ];

        return [$rows, $metrics, $metricLabels];
    }

    public function exportExcel()
    {
        [$rows, $metrics] = $this->buildData();

        return Excel::download(
            new FuzzyEvaluationExport($rows, $metrics, $this->filteredStart, $this->filteredEnd),
            "Evaluasi-Fuzzy_{$this->filteredStart}_sd_{$this->filteredEnd}.xlsx"
        );
    }

    public function exportPdf()
    {
        [$rows, $metrics] = $this->buildData();

        $pdf = Pdf::loadView('download.fuzzy-evaluation-pdf', [
            'rows'      => $rows,
            'metrics'   => $metrics,
            'startDate' => $this->filteredStart,
            'endDate'   => $this->filteredEnd,
        ])->setPaper('a4', 'landscape');

        return response()->streamDownload(
            fn() => print($pdf->stream()),
            "Evaluasi-Fuzzy_{$this->filteredStart}_sd_{$this->filteredEnd}.pdf"
        );
    }

    public function render()
    {
        [$rows, $metrics, $metricLabels] = $this->buildData();

        $service = new FuzzyEvaluationService();

        return view('livewire.fuzzy-evaluation', [
            'rows'                 => $rows,
            'metrics'              => $metrics,
            'metricLabels'         => $metricLabels,
            'membershipDefinitions' => $service->getMembershipDefinitions(),
        ])->layout('layouts.app');
    }
}

<?php

namespace App\Livewire;

use App\Models\WmaSetting;
use App\Services\WmaDosisService;
use Carbon\Carbon;
use Livewire\Component;

class WmaDosisPrediksi extends Component
{
    public string $selectedMonth = '';
    public string $filteredMonth = '';

    // Bobot WMA (form input)
    public int $inputW1 = 1;
    public int $inputW2 = 1;
    public int $inputW3 = 10;

    public function mount()
    {
        $this->selectedMonth = Carbon::now()->format('Y-m');

        [$this->inputW1, $this->inputW2, $this->inputW3] =
            WmaSetting::getWeights('dosis_kimia', [1, 1, 10]);
    }

    public function saveWeights(): void
    {
        if (auth()->user()->role !== 'admin') return;

        $this->validate([
            'inputW1' => 'required|integer|min:1|max:100',
            'inputW2' => 'required|integer|min:1|max:100',
            'inputW3' => 'required|integer|min:1|max:100',
        ]);

        WmaSetting::updateOrCreate(
            ['key' => 'dosis_kimia'],
            ['w1' => $this->inputW1, 'w2' => $this->inputW2, 'w3' => $this->inputW3]
        );
    }

    public function applyFilter(string $month)
    {
        $this->filteredMonth = $month ?: $this->selectedMonth;
        $this->selectedMonth = $this->filteredMonth;
         // Build data
    [$weekly, $predictions, $weightInfo, $chemLabels, $evaluation] = $this->buildData();
    
    // Prepare chart data
    $jsChartsData = [];
    foreach (['pac', 'chlorine', 'soda_ash'] as $chemKey) {
        if (isset($weekly[$chemKey]) && count($weekly[$chemKey]) > 0) {
            $jsChartsData[$chemKey] = [
                'labels' => array_map(function($w) { return 'Mg ' . $w['week_num']; }, $weekly[$chemKey]),
                'hist' => array_column($weekly[$chemKey], 'avg_dosage'),
                'pred' => array_column($predictions[$chemKey] ?? [], 'avg_dosage'),
            ];
        }
    }
    
    // Dispatch event jika ada data
    if (!empty($jsChartsData)) {
        $this->dispatch('charts-ready', chartsData: $jsChartsData);
    }
    }

    private function buildData(): array
    {
        if (!$this->filteredMonth) return [[], [], [], [], []];

        $service = new WmaDosisService();

        // 3 bulan sebelum bulan yang dipilih
        $target = Carbon::parse($this->filteredMonth . '-01');
        $start = $target->copy()->subMonths(3)->startOfMonth()->format('Y-m-d');
        $end = $target->copy()->subDay()->format('Y-m-d'); // akhir bulan sebelumnya

        $weekly      = $service->getWeeklyAverages($start, $end);
        $predictions = $service->predictNextMonth($weekly);
        $weightInfo  = $service->getWeights();
        $chemLabels  = $service->getChemLabels();
        $evaluation  = $service->evaluatePredictions($predictions, $this->filteredMonth);

        return [$weekly, $predictions, $weightInfo, $chemLabels, $evaluation];
    }

public function getMonthOptions(): array
{
    $options = [];
    $now = Carbon::now();
    
    for ($i = -6; $i <= 6; $i++) {
        $date = $now->copy()->addMonths($i);
        $key = $date->format('Y-m');
        $options[$key] = $date->translatedFormat('F Y');
    }
    
    return collect($options)->sortKeys()->toArray();
}

public function render()
{
    [$weekly, $predictions, $weightInfo, $chemLabels, $evaluation] = $this->buildData();

    // Hitung periode historis untuk display
    $historisLabel = '';
    if ($this->filteredMonth) {
        $target = Carbon::parse($this->filteredMonth . '-01');
        $s = $target->copy()->subMonths(3);
        $e = $target->copy()->subDay();
        $historisLabel = $s->format('d M Y') . ' - ' . $e->format('d M Y') . ' (3 bulan)';
    }

    // Prepare data untuk chart
    if ($this->filteredMonth && $chemLabels) {
        $jsChartsData = [];
        foreach (['pac', 'chlorine', 'soda_ash'] as $chemKey) {
            $jsChartsData[$chemKey] = [
                'labels' => array_map(function($w) { return 'Mg ' . $w['week_num']; }, $weekly[$chemKey] ?? []),
                'hist' => array_column($weekly[$chemKey] ?? [], 'avg_dosage'),
                'pred' => array_column($predictions[$chemKey] ?? [], 'avg_dosage'),
            ];
        }
        

    }

    return view('livewire.wma-dosis-prediksi', compact(
        'weekly', 'predictions', 'weightInfo', 'chemLabels', 'historisLabel', 'evaluation'
    ))->layout('layouts.app');
}
}
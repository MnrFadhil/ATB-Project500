<?php

namespace App\Livewire;

use App\Models\Shift;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Component;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Validate;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Facades\Excel;



class MultipleSheets implements WithMultipleSheets
{

    protected $shifts;

    public function __construct($shifts)
    {
        $this->shifts = $shifts;
    }

    public function sheets(): array
    {
        return [
            'Worksheet 1' => new ShiftExportExcel($this->shifts, "download.monitoring-sheet1"),
            'Worksheet 2' => new ShiftExportExcel($this->shifts, "download.monitoring-sheet2"),
            'Worksheet 3' => new ShiftExportExcel($this->shifts, "download.monitoring-sheet3"),
            'Worksheet 4' => new ShiftExportExcel($this->shifts, "download.monitoring-sheet4"),
            'Worksheet 5' => new ShiftExportExcel($this->shifts, "download.monitoring-sheet5"),
        ];
    }
}

class ShiftExportExcel implements FromView, WithTitle
{

    protected $shifts, $view;

    public function __construct($shifts, $view)
    {

        $this->shifts = $shifts;
        $this->view = $view;
    }

    public function view(): View
    {
        return view($this->view,  ['shifts' => $this->shifts]);
    }

    public function title(): string
    {
        return 'Worksheet';
    }
}


class MonitoringIndex extends Component
{
    /* -------------------------------------------------------------------------- */
    /*                             Member Of Variabel                             */
    /* -------------------------------------------------------------------------- */
    public $shiftDetail;
    public $isAdmin;

    #[Validate('date:Y-m-d')]
    public string $downloadDate = '';
    #[Validate('string')]
    public string $downloadType = 'pdf';


    /* -------------------------------------------------------------------------- */
    /*                                Logic Methods                               */
    /* -------------------------------------------------------------------------- */
    public function showConfirmDelete($shift)
    {
        $this->shiftDetail = $shift;
        $this->dispatch('show-modal-detail');
    }

    public function deleteShift()
    {
        $isSuccess = Shift::findOrFail($this->shiftDetail['id'])->delete();
        if ($isSuccess) Session::flash('success', 'Success Delete Shift');
        else Session::flash('eror', 'Eror Delete Shift');

        return redirect('/monitoring-index');
    }

    public function downloadReport()
    {
        $shifts =  Shift::with([
            'shiftOperators',
            'waterQualities',
            'flowMeters',
            'reservoirLevels',
            'mdpPanels',
            'pumpProccess',
            'pumpChemicals',
            'unitOperation',
            'wtps',
            'pressureStaticMixer'
        ])->where('date', $this->downloadDate)->orderBy('shift', 'asc')->orderBy('date', 'asc')->orderBy('start_time', 'asc')->get()->toArray();

        if ($this->downloadType == 'pdf') {
            $pdf = PDF::loadView('download.monitoring', ['shifts' => $shifts])->setPaper('legal', 'landscape');
            return response()->streamDownload(function () use ($pdf) {
                echo $pdf->stream();
            }, "Report $this->downloadDate.pdf");
        } else return Excel::download(new MultipleSheets($shifts), "Report $this->downloadDate.xlsx");
    }

    /* -------------------------------------------------------------------------- */
    /*                               Lifecycle Hooks                              */
    /* -------------------------------------------------------------------------- */
    public function mount()
    {
        $this->isAdmin = Auth::user()->role == 'admin';
    }


    public function render()
    {
        return view('livewire.monitoring-index', [
            'shifts' => Shift::orderBy('shift', 'asc')->orderBy('date', 'asc')->orderBy('start_time', 'asc')->paginate(15)
        ])->layout('layouts.app');
    }
}

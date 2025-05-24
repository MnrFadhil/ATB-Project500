<?php

namespace App\Livewire;

use App\Models\Shift;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Component;
use Barryvdh\DomPDF\Facade\Pdf;

class MonitoringIndex extends Component
{
    /* -------------------------------------------------------------------------- */
    /*                             Member Of Variabel                             */
    /* -------------------------------------------------------------------------- */
    public $shiftDetail;
    public $isAdmin;


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
        ])->orderBy('shift', 'asc')->orderBy('date', 'asc')->orderBy('start_time', 'asc')->get()->toArray();


        $pdf = PDF::loadView('download.monitoring', ['shifts' => $shifts])->setPaper('legal', 'landscape');
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'report.pdf');
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

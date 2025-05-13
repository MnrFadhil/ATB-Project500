<?php

namespace App\Livewire;

use App\Models\Shift;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class MonitoringIndex extends Component
{
    /* -------------------------------------------------------------------------- */
    /*                             Member Of Variabel                             */
    /* -------------------------------------------------------------------------- */
    public $shiftDetail;

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

    /* -------------------------------------------------------------------------- */
    /*                               Lifecycle Hooks                              */
    /* -------------------------------------------------------------------------- */
    public function render()
    {
        return view('livewire.monitoring-index', [
            'shifts' => Shift::paginate(15)
        ])->layout('layouts.app');
    }
}

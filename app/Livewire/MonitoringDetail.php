<?php

namespace App\Livewire;

use App\Models\Shift;
use Livewire\Component;

class MonitoringDetail extends Component
{
    /* -------------------------------------------------------------------------- */
    /*                             Member Of Variabel                             */
    /* -------------------------------------------------------------------------- */
    public $id;

    /* -------------------------------------------------------------------------- */
    /*                               Lifecycle Hooks                              */
    /* -------------------------------------------------------------------------- */
    public function mount($id)
    {
        $this->id = $id;
    }

    public function render()
    {
        $shift =  Shift::with([
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
        ])->find($this->id);

        // dd($shift);

        return view('livewire.monitoring-detail', [
            'shifts' => $shift
        ])->layout('layouts.app');;
    }
}

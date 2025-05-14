<?php

namespace App\Livewire;

use App\Models\Shift;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class MonitoringDetail extends Component
{
    /* -------------------------------------------------------------------------- */
    /*                             Member Of Variabel                             */
    /* -------------------------------------------------------------------------- */
    public $id;
    public $isAdmin;

    /* -------------------------------------------------------------------------- */
    /*                               Lifecycle Hooks                              */
    /* -------------------------------------------------------------------------- */
    public function mount($id)
    {
        $this->id = $id;
        $this->isAdmin = Auth::user()->role == 'admin';
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

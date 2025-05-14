<?php

namespace App\Livewire;

use App\Models\Shift;
use Livewire\Component;
use Carbon\Carbon;

class Dashboard extends Component
{

    /* -------------------------------------------------------------------------- */
    /*                             Member Of Variabel                             */
    /* -------------------------------------------------------------------------- */
    public $date;

    /* -------------------------------------------------------------------------- */
    /*                               Lifecycle Hooks                              */
    /* -------------------------------------------------------------------------- */
    public function mount()
    {
        $this->date = Carbon::now()->format('Y-m-d');
    }


    public function render()
    {
        $shiftChart = Shift::whereDate('date', $this->date)->orderBy('start_time', 'desc')->with(['flowMeters', 'reservoirLevels'])->get()->toArray();
        return view('livewire.dashboard', [
            'shiftChart' => $shiftChart
        ])->layout('layouts.app');
    }
}

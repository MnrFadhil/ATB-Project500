<?php

namespace App\Livewire;

use App\Models\Shift;
use Livewire\Component;
use Carbon\Carbon;
use Livewire\Attributes\Validate;


class Dashboard extends Component
{

    /* -------------------------------------------------------------------------- */
    /*                             Member Of Variabel                             */
    /* -------------------------------------------------------------------------- */
    public string $date = '';

    /* -------------------------------------------------------------------------- */
    /*                               Lifecycle Hooks                              */
    /* -------------------------------------------------------------------------- */
    public function mount()
    {
        $this->date = Carbon::now()->format('Y-m-d');
    }

    public function updatedDate($value)
    {
        $shiftChart = Shift::whereDate('date', $this->date)->orderBy('end_time', 'asc')->with(['flowMeters', 'reservoirLevels', 'waterQualities'])->get()->toArray();
        $this->dispatch('post-created', shiftChartData: $shiftChart);
    }


    public function render()
    {
        $this->dispatch('post-created');

        $shiftChart = Shift::whereDate('date', $this->date)->orderBy('end_time', 'asc')->with(['flowMeters', 'reservoirLevels', 'waterQualities'])->get()->toArray();
        return view('livewire.dashboard', [
            'shiftChart' => $shiftChart
        ])->layout('layouts.app');
    }
}

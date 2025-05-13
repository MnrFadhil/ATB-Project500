<?php

namespace App\Livewire;

use App\Models\Shift;
use Livewire\Component;

class MonitoringIndex extends Component
{
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

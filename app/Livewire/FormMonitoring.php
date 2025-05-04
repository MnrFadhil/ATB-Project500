<?php

namespace App\Livewire;

use Livewire\Component;

class FormMonitoring extends Component
{
    /* -------------------------------------------------------------------------- */
    /*                               Lifecycle Hooks                              */
    /* -------------------------------------------------------------------------- */
    public function render()
    {
        return view('livewire.form-monitoring')->layout('layouts.app');
    }
}

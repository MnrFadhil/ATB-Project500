<?php

namespace App\Livewire;

use App\Livewire\Forms\MonitoringForm;
use App\Models\User;
use Livewire\Component;

class FormMonitoring extends Component
{
    public MonitoringForm $form;
    /* -------------------------------------------------------------------------- */
    /*                             Member Of Variabel                             */
    /* -------------------------------------------------------------------------- */


    /* -------------------------------------------------------------------------- */
    /*                               Lifecycle Hooks                              */
    /* -------------------------------------------------------------------------- */
    public function render()
    {
        return view('livewire.form-monitoring', [
            'users' => User::all()
        ])->layout('layouts.app');
    }
}

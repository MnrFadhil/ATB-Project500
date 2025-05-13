<?php

namespace App\Livewire;

use App\Livewire\Forms\MonitoringForm;
use App\Models\Shift;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class FormMonitoring extends Component
{
    public MonitoringForm $form;
    /* -------------------------------------------------------------------------- */
    /*                             Member Of Variabel                             */
    /* -------------------------------------------------------------------------- */
    public function submit()
    {
        // validate form
        $form = $this->validate();


        DB::transaction(function () use ($form) {
            // Format waktu ke 'HH:MM:SS'
            $form['shift']['start_time'] =  trim($form['shift']['start_time'], "'") . ':00';
            $form['shift']['end_time']   = trim($form['shift']['end_time'], "'") . ':00';


            // Shift model
            $shift = new Shift();
            $shift = $shift->create($form['shift']);
            $shift->shiftOperators()->createMany(
                [
                    ['user_id' => $form['shift']['operator_1']],
                    ['user_id' => $form['shift']['operator_2']]
                ]
            );
            $shift->shiftOperators()->createMany(
                [
                    ['user_id' => $form['shift']['operator_1']],
                    ['user_id' => $form['shift']['operator_2']]
                ]
            );
            $shift->waterQualities()->createMany([
                ['type' => 'air baku', ...$form['airBaku']],
                ['type' => 'sedimentation', ...$form['sedimentation']],
                ['type' => 'reservoir', ...$form['reservoir']]
            ]);
            $shift->flowMeters()->createMany([
                $form['flowAirBaku'],
                $form['flowSudarso'],
                $form['flowVeteran']
            ]);
            $shift->reservoirLevels()->create($form['reservoirLevel']);
            $shift->mdpPanels()->create($form['mdpPanel']);
            $shift->pressureStaticMixer()->create($form['pressStatic']);
            $shift->pumpProccess()->createMany([
                ['type' => 'intake a', ...$form['pumpIntakeA']],
                ['type' => 'intake b', ...$form['pumpIntakeB']],
                ['type' => 'intake c', ...$form['pumpIntakeC']],
                ['type' => 'distribusi a', ...$form['pumpDistriA']],
                ['type' => 'distribusi b', ...$form['pumpDistriB']],
                ['type' => 'distribusi c', ...$form['pumpDistriC']],
                ['type' => 'distribusi d', ...$form['pumpDistriD']],
            ]);
            $shift->pumpChemicals()->createMany([
                $form['pumpPac'],
                $form['pumpChlor']
            ]);
            $shift->unitOperation()->createMany([
                $form['unitOper'],
            ]);
            $shift->wtps()->createMany([
                $form['wtp'],
            ]);

            if ($shift) Session::flash('success', 'Success Create Monitoring Data');
            else Session::flash('eror', 'Eror Create Monitoring Data');
            return redirect('/data');
        });
    }

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

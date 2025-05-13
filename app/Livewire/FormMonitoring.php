<?php

namespace App\Livewire;

use App\Livewire\Forms\MonitoringForm;
use App\Models\Shift;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Livewire\Component;
use PhpParser\Node\Expr\FuncCall;

class FormMonitoring extends Component
{
    /* -------------------------------------------------------------------------- */
    /*                             Member Of Variabel                             */
    /* -------------------------------------------------------------------------- */
    public MonitoringForm $form;
    public $id;

    /* -------------------------------------------------------------------------- */
    /*                                 Logic Form                                 */
    /* -------------------------------------------------------------------------- */
    public function submit()
    {
        // validate form
        $form = $this->validate();


        if ($this->id) $this->updateRecord($form);
        else $this->createRecord($form);
    }

    public function assignValue()
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

        $shift = $shift->toArray();

        $this->form->shift = $shift;

        $this->form->shift = $shift;
        $this->form->shift['start_time'] = Carbon::createFromFormat('H:i:s', $shift['start_time'])->format('H:i');
        $this->form->shift['end_time'] = Carbon::createFromFormat('H:i:s', $shift['end_time'])->format('H:i');

        $this->form->shift['operator_1'] = $shift['shift_operators'][0]['id'];
        if (count($shift['shift_operators']) > 1) {
            $this->form->shift['operator_2'] = $shift['shift_operators'][1]['id'];
        }

        foreach ($shift['water_qualities'] as $waterQuality) {
            if ($waterQuality['type'] == 'air baku')  $this->form->airBaku = $waterQuality;
            if ($waterQuality['type'] == 'sedimentation')  $this->form->sedimentation = $waterQuality;
            if ($waterQuality['type'] == 'reservoir')  $this->form->reservoir = $waterQuality;
        };

        foreach ($shift['flow_meters'] as $flowMeter) {
            if ($flowMeter['location'] == null)  $this->form->flowAirBaku = $flowMeter;
            if ($flowMeter['location'] == 'yos sudarso')  $this->form->flowSudarso = $flowMeter;
            if ($flowMeter['location'] == 'veteran')  $this->form->flowVeteran = $flowMeter;
        };

        $this->form->reservoirLevel = $shift['reservoir_levels'];

        $this->form->mdpPanel = $shift['mdp_panels'];

        $this->form->pressStatic = $shift['pressure_static_mixer'];

        foreach ($shift['pump_proccess'] as $pumpProccess) {
            if ($pumpProccess['type'] == 'intake a')  $this->form->pumpIntakeA = $pumpProccess;
            if ($pumpProccess['type'] == 'intake b')  $this->form->pumpIntakeB = $pumpProccess;
            if ($pumpProccess['type'] == 'intake c')  $this->form->pumpIntakeC = $pumpProccess;
            if ($pumpProccess['type'] == 'distribusi a')  $this->form->pumpDistriA = $pumpProccess;
            if ($pumpProccess['type'] == 'distribusi b')  $this->form->pumpDistriB = $pumpProccess;
            if ($pumpProccess['type'] == 'distribusi c')  $this->form->pumpDistriC = $pumpProccess;
            if ($pumpProccess['type'] == 'distribusi d')  $this->form->pumpDistriD = $pumpProccess;
        };

        foreach ($shift['pump_chemicals'] as $pumpChemic) {
            if ($pumpChemic['type'] == 'pac')  $this->form->pumpPac = $pumpChemic;
            if ($pumpChemic['type'] == 'chlorine/kaporit')  $this->form->pumpChlor = $pumpChemic;
        };

        $this->form->unitOper = $shift['unit_operation'];

        $this->form->wtp = $shift['wtps'];
    }

    public function createRecord($form)
    {
        DB::transaction(function () use ($form) {
            // Format waktu ke 'HH:MM:SS'
            $form['shift']['start_time'] =  trim($form['shift']['start_time'], "'") . ':00';
            $form['shift']['end_time']   = trim($form['shift']['end_time'], "'") . ':00';


            // Shift model
            $shift = new Shift();
            $shift = $shift->create($form['shift']);
            $shift->shiftOperators()->attach(
                ['user_id' => $form['shift']['operator_1']],

            );
            if ($form['shift']['operator_2'] !== '') {

                $shift->shiftOperators()->attach(
                    ['user_id' => $form['shift']['operator_2']],
                );
            }
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
                ['type' => 'pac', ...$form['pumpPac']],
                ['type' => 'chlorine/kaporit', ...$form['pumpChlor']]
            ]);
            $shift->unitOperation()->createMany([
                $form['unitOper'],
            ]);

            $shift->wtps()->createMany([
                $form['wtp'],
            ]);

            if ($shift) Session::flash('success', 'Success Create Monitoring Data');
            else Session::flash('eror', 'Eror Create Monitoring Data');
            return redirect('/monitoring-index');
        });
    }

    public function updateRecord($form)
    {
        DB::transaction(function () use ($form) {
            // Format waktu ke 'HH:MM:SS'
            if (strlen($form['shift']['start_time']) !== 8) $form['shift']['start_time'] =  trim($form['shift']['start_time'], "'") . ':00';
            if (strlen($form['shift']['end_time']) !== 8) $form['shift']['end_time']   = trim($form['shift']['end_time'], "'") . ':00';

            // Shift model
            $shift = Shift::find($this->id);
            $shift->update($form['shift']);

            $shift->shiftOperators()->sync(
                ['user_id' => $form['shift']['operator_1']],
            );

            if (count($form['shift']) == 8) {
                $shift->shiftOperators()->sync(
                    ['user_id' => $form['shift']['operator_2']],
                );
            }

            $shift->waterQualities->where('type', 'air baku')->first()->update($form['airBaku']);
            $shift->waterQualities->where('type', 'sedimentation')->first()->update($form['sedimentation']);
            $shift->waterQualities->where('type', 'reservoir')->first()->update($form['reservoir']);

            $shift->flowMeters->where('location', null)->first()->update($form['flowAirBaku']);
            $shift->flowMeters->where('location', 'yos sudarso')->first()->update($form['flowSudarso']);
            $shift->flowMeters->where('location', 'veteran')->first()->update($form['flowVeteran']);


            $shift->reservoirLevels->first()->update(($form['reservoirLevel']));

            $shift->mdpPanels->first()->update($form['mdpPanel']);

            $shift->pressureStaticMixer()->first()->update($form['pressStatic']);

            $shift->pumpProccess->where('type', 'intake a')->first()->update($form['pumpIntakeA']);
            $shift->pumpProccess->where('type', 'intake b')->first()->update($form['pumpIntakeB']);
            $shift->pumpProccess->where('type', 'intake c')->first()->update($form['pumpIntakeC']);
            $shift->pumpProccess->where('type', 'distribusi a')->first()->update($form['pumpDistriA']);
            $shift->pumpProccess->where('type', 'distribusi b')->first()->update($form['pumpDistriB']);
            $shift->pumpProccess->where('type', 'distribusi c')->first()->update($form['pumpDistriC']);
            $shift->pumpProccess->where('type', 'distribusi d')->first()->update($form['pumpDistriD']);

            $shift->pumpChemicals->where('type', 'pac')->first()->update($form['pumpPac']);
            $shift->pumpChemicals->where('type', 'chlorine/kaporit')->first()->update($form['pumpChlor']);

            $shift->unitOperation->first()->update($form['unitOper']);

            $shift->wtps->first()->update($form['wtp']);

            if ($shift) Session::flash('success', 'Success Update Monitoring Data');
            else Session::flash('eror', 'Eror Update Monitoring Data');
            return redirect("/monitoring/$shift->id");
        });
    }

    /* -------------------------------------------------------------------------- */
    /*                               Lifecycle Hooks                              */
    /* -------------------------------------------------------------------------- */
    public function mount($id = null)
    {
        $this->id = $id;

        if ($this->id) {
            $this->assignValue();
        }
    }

    public function render()
    {
        return view('livewire.form-monitoring', [
            'users' => User::all()
        ])->layout('layouts.app');
    }
}

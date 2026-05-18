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
        $form = $this->setDefaultValueForStatus($form);

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
            // PAC
            if ($pumpChemic['type'] == 'pac' && $pumpChemic['pump_unit'] == 'A') {
                $this->form->pumpPacA = $pumpChemic;
            }
            if ($pumpChemic['type'] == 'pac' && $pumpChemic['pump_unit'] == 'B') {
                $this->form->pumpPacB = $pumpChemic;
            }
            
            // Chlorine
            if ($pumpChemic['type'] == 'chlorine/kaporit' && $pumpChemic['pump_unit'] == 'A') {
                $this->form->pumpChlorA = $pumpChemic;
            }
            if ($pumpChemic['type'] == 'chlorine/kaporit' && $pumpChemic['pump_unit'] == 'B') {
                $this->form->pumpChlorB = $pumpChemic;
            }
            
            // Soda Ash
            if ($pumpChemic['type'] == 'soda ash' && $pumpChemic['pump_unit'] == 'A') {
                $this->form->pumpSodaA = $pumpChemic;
            }
            if ($pumpChemic['type'] == 'soda ash' && $pumpChemic['pump_unit'] == 'B') {
                $this->form->pumpSodaB = $pumpChemic;
            }
            
            // Polymer
            if ($pumpChemic['type'] == 'polymer' && $pumpChemic['pump_unit'] == 'A') {
                $this->form->pumpPolymerA = $pumpChemic;
            }
            if ($pumpChemic['type'] == 'polymer' && $pumpChemic['pump_unit'] == 'B') {
                $this->form->pumpPolymerB = $pumpChemic;
            }
        }
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
                ['type' => 'pac', 'pump_unit' => 'A', ...$form['pumpPacA']],
                ['type' => 'pac', 'pump_unit' => 'B', ...$form['pumpPacB']],
                ['type' => 'chlorine/kaporit', 'pump_unit' => 'A', ...$form['pumpChlorA']],
                ['type' => 'chlorine/kaporit', 'pump_unit' => 'B', ...$form['pumpChlorB']],
                ['type' => 'soda ash', 'pump_unit' => 'A', ...$form['pumpSodaA']],
                ['type' => 'soda ash', 'pump_unit' => 'B', ...$form['pumpSodaB']],
                ['type' => 'polymer', 'pump_unit' => 'A', ...$form['pumpPolymerA']],
                ['type' => 'polymer', 'pump_unit' => 'B', ...$form['pumpPolymerB']],
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

            $userIds = [$form['shift']['operator_1']];

            if (isset($form['shift']['operator_2'])) {
                $userIds[] = $form['shift']['operator_2'];
            }

            // belum tau cara terbaik nya
            $shift->shiftOperators()->sync([]);
            $shift->shiftOperators()->attach($userIds);

            $shift->waterQualities->where('type', 'air baku')->first()->update($form['airBaku']);
            $shift->waterQualities->where('type', 'sedimentation')->first()->update($form['sedimentation']);
            $shift->waterQualities->where('type', 'reservoir')->first()->update($form['reservoir']);

            $shift->flowMeters->where('location', null)->first()->update($form['flowAirBaku']);
            $shift->flowMeters->where('location', 'yos sudarso')->first()->update($form['flowSudarso']);
            $shift->flowMeters->where('location', 'veteran')->first()->update($form['flowVeteran']);

            $shift->reservoirLevels->update(($form['reservoirLevel']));

            $shift->mdpPanels->update($form['mdpPanel']);

            $shift->pressureStaticMixer()->first()->update($form['pressStatic']);

            $shift->pumpProccess->where('type', 'intake a')->first()->update($form['pumpIntakeA']);
            $shift->pumpProccess->where('type', 'intake b')->first()->update($form['pumpIntakeB']);
            $shift->pumpProccess->where('type', 'intake c')->first()->update($form['pumpIntakeC']);
            $shift->pumpProccess->where('type', 'distribusi a')->first()->update($form['pumpDistriA']);
            $shift->pumpProccess->where('type', 'distribusi b')->first()->update($form['pumpDistriB']);
            $shift->pumpProccess->where('type', 'distribusi c')->first()->update($form['pumpDistriC']);
            $shift->pumpProccess->where('type', 'distribusi d')->first()->update($form['pumpDistriD']);

            // Update atau Create Pump Chemicals A & B
            $this->updateOrCreatePumpChemical($shift, 'pac', 'A', $form['pumpPacA']);
            $this->updateOrCreatePumpChemical($shift, 'pac', 'B', $form['pumpPacB']);
            $this->updateOrCreatePumpChemical($shift, 'chlorine/kaporit', 'A', $form['pumpChlorA']);
            $this->updateOrCreatePumpChemical($shift, 'chlorine/kaporit', 'B', $form['pumpChlorB']);
            $this->updateOrCreatePumpChemical($shift, 'soda ash', 'A', $form['pumpSodaA']);
            $this->updateOrCreatePumpChemical($shift, 'soda ash', 'B', $form['pumpSodaB']);
            $this->updateOrCreatePumpChemical($shift, 'polymer', 'A', $form['pumpPolymerA']);
            $this->updateOrCreatePumpChemical($shift, 'polymer', 'B', $form['pumpPolymerB']);


            $shift->unitOperation->update($form['unitOper']);

            $shift->wtps->first()->update($form['wtp']);

            if ($shift) Session::flash('success', 'Success Update Monitoring Data');
            else Session::flash('eror', 'Eror Update Monitoring Data');
            return redirect("/monitoring/$shift->id");
        });
    }

    private function updateOrCreatePumpChemical($shift, $type, $pumpUnit, $data)
    {
        $existing = $shift->pumpChemicals()
            ->where('type', $type)
            ->where('pump_unit', $pumpUnit)
            ->first();

        if ($existing) {
            $existing->update($data);
        } else {
            $shift->pumpChemicals()->create([
                'type' => $type,
                'pump_unit' => $pumpUnit,
                ...$data
            ]);
        }
    }

    public function setDefaultValueForStatus($form)
    {
        if ($form['pumpIntakeA']['status'] !== 'running') {
            $form['pumpIntakeA']['ampere'] = 0;
            $form['pumpIntakeA']['frequency'] = 0;
            $form['pumpIntakeA']['pressure'] = 0;
        };
        if ($form['pumpIntakeB']['status'] !== 'running') {
            $form['pumpIntakeB']['ampere'] = 0;
            $form['pumpIntakeB']['frequency'] = 0;
            $form['pumpIntakeB']['pressure'] = 0;
        };
        if ($form['pumpIntakeC']['status'] !== 'running') {
            $form['pumpIntakeC']['ampere'] = 0;
            $form['pumpIntakeC']['frequency'] = 0;
            $form['pumpIntakeC']['pressure'] = 0;
        };
        if ($form['pumpDistriA']['status'] !== 'running') {
            $form['pumpDistriA']['ampere'] = 0;
            $form['pumpDistriA']['frequency'] = 0;
            $form['pumpDistriA']['pressure'] = 0;
        };
        if ($form['pumpDistriB']['status'] !== 'running') {
            $form['pumpDistriB']['ampere'] = 0;
            $form['pumpDistriB']['frequency'] = 0;
            $form['pumpDistriB']['pressure'] = 0;
        };
        if ($form['pumpDistriC']['status'] !== 'running') {
            $form['pumpDistriC']['ampere'] = 0;
            $form['pumpDistriC']['frequency'] = 0;
            $form['pumpDistriC']['pressure'] = 0;
        };
        if ($form['pumpDistriD']['status'] !== 'running') {
            $form['pumpDistriD']['ampere'] = 0;
            $form['pumpDistriD']['frequency'] = 0;
            $form['pumpDistriD']['pressure'] = 0;
        };
        if ($form['wtp']['gravity_filter_a_status'] !== 'running') $form['wtp']['gravity_filter_a'] = 0;
        if ($form['wtp']['gravity_filter_b_status'] !== 'running') $form['wtp']['gravity_filter_b'] = 0;
        if ($form['wtp']['gravity_filter_c_status'] !== 'running') $form['wtp']['gravity_filter_c'] = 0;
        if ($form['wtp']['gravity_filter_d_status'] !== 'running') $form['wtp']['gravity_filter_d'] = 0;
        if ($form['wtp']['gravity_filter_e_status'] !== 'running') $form['wtp']['gravity_filter_e'] = 0;
        if ($form['wtp']['gravity_filter_f_status'] !== 'running') $form['wtp']['gravity_filter_f'] = 0;

        // PAC A
        if ($form['pumpPacA']['status'] !== 'running') {
            $form['pumpPacA']['frequency'] = 0;
            $form['pumpPacA']['dosage'] = 0;
            $form['pumpPacA']['concentration'] = 0;
            $form['pumpPacA']['stirring'] = 0;
            $form['pumpPacA']['tank_level'] = 0;
        }

        // PAC B
        if ($form['pumpPacB']['status'] !== 'running') {
            $form['pumpPacB']['frequency'] = 0;
            $form['pumpPacB']['dosage'] = 0;
            $form['pumpPacB']['concentration'] = 0;
            $form['pumpPacB']['stirring'] = 0;
            $form['pumpPacB']['tank_level'] = 0;
        }

        // Chlorine A
        if ($form['pumpChlorA']['status'] !== 'running') {
            $form['pumpChlorA']['flow_rate'] = 0;
            $form['pumpChlorA']['dosage'] = 0;
            $form['pumpChlorA']['concentration'] = 0;
            $form['pumpChlorA']['stirring'] = 0;
            $form['pumpChlorA']['tank_level'] = 0;
        }

        // Chlorine B
        if ($form['pumpChlorB']['status'] !== 'running') {
            $form['pumpChlorB']['flow_rate'] = 0;
            $form['pumpChlorB']['dosage'] = 0;
            $form['pumpChlorB']['concentration'] = 0;
            $form['pumpChlorB']['stirring'] = 0;
            $form['pumpChlorB']['tank_level'] = 0;
        }

        // Soda Ash A
        if ($form['pumpSodaA']['status'] !== 'running') {
            $form['pumpSodaA']['flow_rate'] = 0;
            $form['pumpSodaA']['dosage'] = 0;
            $form['pumpSodaA']['concentration'] = 0;
            $form['pumpSodaA']['stirring'] = 0;
            $form['pumpSodaA']['tank_level'] = 0;
        }

        // Soda Ash B
        if ($form['pumpSodaB']['status'] !== 'running') {
            $form['pumpSodaB']['flow_rate'] = 0;
            $form['pumpSodaB']['dosage'] = 0;
            $form['pumpSodaB']['concentration'] = 0;
            $form['pumpSodaB']['stirring'] = 0;
            $form['pumpSodaB']['tank_level'] = 0;
        }

        // Polymer A
        if ($form['pumpPolymerA']['status'] !== 'running') {
            $form['pumpPolymerA']['flow_rate'] = 0;
            $form['pumpPolymerA']['dosage'] = 0;
            $form['pumpPolymerA']['concentration'] = 0;
            $form['pumpPolymerA']['stirring'] = 0;
            $form['pumpPolymerA']['tank_level'] = 0;
        }

        // Polymer B
        if ($form['pumpPolymerB']['status'] !== 'running') {
            $form['pumpPolymerB']['flow_rate'] = 0;
            $form['pumpPolymerB']['dosage'] = 0;
            $form['pumpPolymerB']['concentration'] = 0;
            $form['pumpPolymerB']['stirring'] = 0;
            $form['pumpPolymerB']['tank_level'] = 0;
        }



        return $form;
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
            'users' => User::all()->where('role', 'user')
        ])->layout('layouts.app');
    }
}

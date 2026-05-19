<?php

namespace App\Livewire;

use App\Livewire\Forms\MonitoringForm;
use App\Models\Shift;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class HideFormMonitoring extends Component
{
    /* -------------------------------------------------------------------------- */
    /*                             Member Variables                               */
    /* -------------------------------------------------------------------------- */
    public MonitoringForm $form;
    public $id;
    
    // Section visibility
    public $showMonitoringData = false;  // 1 group besar untuk semua
    public $showShiftInfo = true;
    public $monitoringDataLoaded = false;

    // Stores previous shift totalizer values for change detection
    public array $prevTotalizers = [];

    /* -------------------------------------------------------------------------- */
    /*                           Toggle Sections                                  */
    /* -------------------------------------------------------------------------- */
    
    public function updatedFormShiftEndTime($value)
    {
        if (!empty($value)) {
            $endHour = (int) explode(':', $value)[0];
            $startHour = $endHour - 2;
            if ($startHour < 0) $startHour += 24;
            $this->form->shift['start_time'] = str_pad($startHour, 2, '0', STR_PAD_LEFT) . ':00';
        }
    }

    public function toggleSection($section)
    {
        $section = str_replace('-', '_', $section);
        $property = "show" . ucfirst($section);
        if (property_exists($this, $property)) {
            $this->{$property} = !$this->{$property};
        }
    }

    public function expandAll()
    {
        $this->showShiftInfo = true;
        $this->showMonitoringData = true;  // Hanya ini saja
    }

    public function collapseAll()
    {
        $this->showShiftInfo = true;
        $this->showMonitoringData = false;  // Hanya ini saja
    }

    /* -------------------------------------------------------------------------- */
    /*                          Form Submit & CRUD                                */
    /* -------------------------------------------------------------------------- */

    public function submit()
    {
        $form = $this->validate();
        $form = $this->setDefaultValueForStatus($form);

        if ($this->id) {
            $this->updateRecord($form);
        } else {
            $this->createRecord($form);
        }
    }

    public function assignValue()
    {
        $shift = Shift::with([
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
        }

        foreach ($shift['flow_meters'] as $flowMeter) {
            if ($flowMeter['location'] == null)  $this->form->flowAirBaku = $flowMeter;
            if ($flowMeter['location'] == 'yos sudarso')  $this->form->flowSudarso = $flowMeter;
            if ($flowMeter['location'] == 'veteran')  $this->form->flowVeteran = $flowMeter;
        }

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
        }

        foreach ($shift['pump_chemicals'] as $pumpChemic) {
            if ($pumpChemic['type'] == 'pac' && $pumpChemic['pump_unit'] == 'A') {
                $this->form->pumpPacA = $pumpChemic;
            }
            if ($pumpChemic['type'] == 'pac' && $pumpChemic['pump_unit'] == 'B') {
                $this->form->pumpPacB = $pumpChemic;
            }
            if ($pumpChemic['type'] == 'chlorine/kaporit' && $pumpChemic['pump_unit'] == 'A') {
                $this->form->pumpChlorA = $pumpChemic;
            }
            if ($pumpChemic['type'] == 'chlorine/kaporit' && $pumpChemic['pump_unit'] == 'B') {
                $this->form->pumpChlorB = $pumpChemic;
            }
            if ($pumpChemic['type'] == 'soda ash' && $pumpChemic['pump_unit'] == 'A') {
                $this->form->pumpSodaA = $pumpChemic;
            }
            if ($pumpChemic['type'] == 'soda ash' && $pumpChemic['pump_unit'] == 'B') {
                $this->form->pumpSodaB = $pumpChemic;
            }
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
            $form['shift']['start_time'] = trim($form['shift']['start_time'], "'") . ':00';
            $form['shift']['end_time'] = trim($form['shift']['end_time'], "'") . ':00';

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
            else Session::flash('error', 'Error Create Monitoring Data');
            return redirect('/monitoring-index');
        });
    }

    public function updateRecord($form)
    {
        DB::transaction(function () use ($form) {
            if (strlen($form['shift']['start_time']) !== 8) $form['shift']['start_time'] = trim($form['shift']['start_time'], "'") . ':00';
            if (strlen($form['shift']['end_time']) !== 8) $form['shift']['end_time'] = trim($form['shift']['end_time'], "'") . ':00';

            $shift = Shift::find($this->id);
            $shift->update($form['shift']);

            $userIds = [$form['shift']['operator_1']];
            if (isset($form['shift']['operator_2'])) {
                $userIds[] = $form['shift']['operator_2'];
            }

            $shift->shiftOperators()->sync([]);
            $shift->shiftOperators()->attach($userIds);

            $shift->waterQualities->where('type', 'air baku')->first()->update($form['airBaku']);
            $shift->waterQualities->where('type', 'sedimentation')->first()->update($form['sedimentation']);
            $shift->waterQualities->where('type', 'reservoir')->first()->update($form['reservoir']);

            $shift->flowMeters->where('location', null)->first()->update($form['flowAirBaku']);
            $shift->flowMeters->where('location', 'yos sudarso')->first()->update($form['flowSudarso']);
            $shift->flowMeters->where('location', 'veteran')->first()->update($form['flowVeteran']);

            $shift->reservoirLevels->update(($form['reservoirLevel']));
            $shift->mdpPanels->first()->update($form['mdpPanel']);
            $shift->pressureStaticMixer()->first()->update($form['pressStatic']);

            $shift->pumpProccess->where('type', 'intake a')->first()->update($form['pumpIntakeA']);
            $shift->pumpProccess->where('type', 'intake b')->first()->update($form['pumpIntakeB']);
            $shift->pumpProccess->where('type', 'intake c')->first()->update($form['pumpIntakeC']);
            $shift->pumpProccess->where('type', 'distribusi a')->first()->update($form['pumpDistriA']);
            $shift->pumpProccess->where('type', 'distribusi b')->first()->update($form['pumpDistriB']);
            $shift->pumpProccess->where('type', 'distribusi c')->first()->update($form['pumpDistriC']);
            $shift->pumpProccess->where('type', 'distribusi d')->first()->update($form['pumpDistriD']);

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
            else Session::flash('error', 'Error Update Monitoring Data');
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
        $pumps = [
            'pumpIntakeA', 'pumpIntakeB', 'pumpIntakeC',
            'pumpDistriA', 'pumpDistriB', 'pumpDistriC', 'pumpDistriD',
            'pumpPacA', 'pumpPacB', 'pumpChlorA', 'pumpChlorB',
            'pumpSodaA', 'pumpSodaB', 'pumpPolymerA', 'pumpPolymerB'
        ];

        foreach ($pumps as $pump) {
            if ($form[$pump]['status'] !== 'running') {
                foreach ($form[$pump] as $key => $value) {
                    if ($key !== 'status') {
                        $form[$pump][$key] = 0;
                    }
                }
            }
        }

        $filters = ['gravity_filter_a', 'gravity_filter_b', 'gravity_filter_c', 'gravity_filter_d', 'gravity_filter_e', 'gravity_filter_f'];
        foreach ($filters as $filter) {
            if ($form['wtp']["{$filter}_status"] !== 'running') {
                $form['wtp'][$filter] = 0;
            }
        }

        return $form;
    }

    public function loadPreviousShiftData()
    {
        try {
            $date = $this->form->shift['date'] ?? null;
            $startTime = $this->form->shift['start_time'] ?? null;

            if (empty($date) || empty($startTime)) {
                $this->showMonitoringData = true;
                $this->monitoringDataLoaded = true;
                return;
            }

            try {
                $dateFormatted = \Carbon\Carbon::parse($date)->format('Y-m-d');
            } catch (\Exception $e) {
                $this->showMonitoringData = true;
                $this->monitoringDataLoaded = true;
                return;
            }

            // Logic untuk jam 23:00 → cari hari sebelumnya
            if ($startTime === '23:00') {
                $searchDate = \Carbon\Carbon::parse($dateFormatted)->subDay()->format('Y-m-d');
                $searchEndTime = '23:00:00';
            } else {
                $searchDate = $dateFormatted;
                $searchEndTime = $startTime . ':00';
            }

            \Log::info('Search params', [
                'searchDate' => $searchDate,
                'searchEndTime' => $searchEndTime,
            ]);

            $previousShift = Shift::with([
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
            ])
            ->where('date', $searchDate)
            ->where('end_time', $searchEndTime)
            ->first();

            if (!$previousShift) {
                \Log::warning('No shift found');
                $this->showMonitoringData = true;
                $this->monitoringDataLoaded = true;
                return;
            }

            \Log::info('Found shift ID: ' . $previousShift->id);
            $shift = $previousShift->toArray();

            // ========== LOAD NOTES ==========
            if (!empty($shift['notes'])) {
                $this->form->shift['notes'] = $shift['notes'];
            }

            // ========== LOAD COLLECTION TANK ==========
            if (!empty($shift['collection_tank'])) {
                $this->form->shift['collection_tank'] = $shift['collection_tank'];
            }

            // ========== LOAD WATER QUALITIES ==========
            foreach ($shift['water_qualities'] as $wq) {
                $data = collect($wq)->except(['id', 'shift_id', 'type', 'created_at', 'updated_at'])->toArray();
                if ($wq['type'] == 'air baku') $this->form->airBaku = $data;
                if ($wq['type'] == 'sedimentation') $this->form->sedimentation = $data;
                if ($wq['type'] == 'reservoir') $this->form->reservoir = $data;
            }

            // ========== LOAD FLOW METERS ==========
            foreach ($shift['flow_meters'] as $fm) {
                $data = collect($fm)->except(['id', 'shift_id', 'created_at', 'updated_at'])->toArray();
                if ($fm['location'] == null) $this->form->flowAirBaku = $data;
                if ($fm['location'] == 'yos sudarso') $this->form->flowSudarso = $data;
                if ($fm['location'] == 'veteran') $this->form->flowVeteran = $data;
            }

            $this->prevTotalizers = [
                'air_baku' => (string) ($this->form->flowAirBaku['totalizer'] ?? ''),
                'sudarso'  => (string) ($this->form->flowSudarso['totalizer'] ?? ''),
                'veteran'  => (string) ($this->form->flowVeteran['totalizer'] ?? ''),
            ];

            // ========== LOAD RESERVOIR LEVELS ==========
            if (!empty($shift['reservoir_levels'])) {
                $rl = is_array($shift['reservoir_levels']) && isset($shift['reservoir_levels'][0])
                    ? $shift['reservoir_levels'][0]
                    : $shift['reservoir_levels'];
                $this->form->reservoirLevel = collect($rl)
                    ->except(['id', 'shift_id', 'created_at', 'updated_at'])->toArray();
            }

            // ========== LOAD MDP PANELS ==========
            if (!empty($shift['mdp_panels'])) {
                $mdp = is_array($shift['mdp_panels']) && isset($shift['mdp_panels'][0])
                    ? $shift['mdp_panels'][0]
                    : $shift['mdp_panels'];
                $this->form->mdpPanel = collect($mdp)
                    ->except(['id', 'shift_id', 'created_at', 'updated_at'])->toArray();
            }

            // ========== LOAD PRESSURE STATIC MIXER ==========
            if (!empty($shift['pressure_static_mixer'])) {
                $ps = is_array($shift['pressure_static_mixer']) && isset($shift['pressure_static_mixer'][0])
                    ? $shift['pressure_static_mixer'][0]
                    : $shift['pressure_static_mixer'];
                $this->form->pressStatic = collect($ps)
                    ->except(['id', 'shift_id', 'created_at', 'updated_at'])->toArray();
            }

            // ========== LOAD PUMP PROCCESS ==========
            foreach ($shift['pump_proccess'] as $pp) {
                $data = collect($pp)->except(['id', 'shift_id', 'type', 'created_at', 'updated_at'])->toArray();
                if ($pp['type'] == 'intake a') $this->form->pumpIntakeA = $data;
                if ($pp['type'] == 'intake b') $this->form->pumpIntakeB = $data;
                if ($pp['type'] == 'intake c') $this->form->pumpIntakeC = $data;
                if ($pp['type'] == 'distribusi a') $this->form->pumpDistriA = $data;
                if ($pp['type'] == 'distribusi b') $this->form->pumpDistriB = $data;
                if ($pp['type'] == 'distribusi c') $this->form->pumpDistriC = $data;
                if ($pp['type'] == 'distribusi d') $this->form->pumpDistriD = $data;
            }

            // ========== LOAD PUMP CHEMICALS ==========
            foreach ($shift['pump_chemicals'] as $pc) {
                $data = collect($pc)->except(['id', 'shift_id', 'type', 'pump_unit', 'created_at', 'updated_at'])->toArray();
                if ($pc['type'] == 'pac' && $pc['pump_unit'] == 'A') $this->form->pumpPacA = $data;
                if ($pc['type'] == 'pac' && $pc['pump_unit'] == 'B') $this->form->pumpPacB = $data;
                if ($pc['type'] == 'chlorine/kaporit' && $pc['pump_unit'] == 'A') $this->form->pumpChlorA = $data;
                if ($pc['type'] == 'chlorine/kaporit' && $pc['pump_unit'] == 'B') $this->form->pumpChlorB = $data;
                if ($pc['type'] == 'soda ash' && $pc['pump_unit'] == 'A') $this->form->pumpSodaA = $data;
                if ($pc['type'] == 'soda ash' && $pc['pump_unit'] == 'B') $this->form->pumpSodaB = $data;
                if ($pc['type'] == 'polymer' && $pc['pump_unit'] == 'A') $this->form->pumpPolymerA = $data;
                if ($pc['type'] == 'polymer' && $pc['pump_unit'] == 'B') $this->form->pumpPolymerB = $data;
            }

            // ========== LOAD UNIT OPERATION (SAFE) ==========
            if (!empty($shift['unit_operation'])) {
                $uo = is_array($shift['unit_operation']) && isset($shift['unit_operation'][0])
                    ? $shift['unit_operation'][0]
                    : $shift['unit_operation'];
                $this->form->unitOper = collect($uo)
                    ->except(['id', 'shift_id', 'created_at', 'updated_at'])->toArray();
            }

            // ========== LOAD WTP (SAFE) ==========
            if (!empty($shift['wtps'])) {
                $wtp = is_array($shift['wtps']) && isset($shift['wtps'][0])
                    ? $shift['wtps'][0]
                    : $shift['wtps'];
                $this->form->wtp = collect($wtp)
                    ->except(['id', 'shift_id', 'created_at', 'updated_at'])->toArray();
            }

            // Auto-expand Monitoring Data
            $this->showMonitoringData = true;
            $this->monitoringDataLoaded = true;

            \Log::info('✓ All data loaded successfully');

        } catch (\Exception $e) {
            \Log::error('Error: ' . $e->getMessage());
            $this->showMonitoringData = true;
            $this->monitoringDataLoaded = true;
        }
    }

    public function isShiftInfoComplete()
    {
        $shift = $this->form->shift;
        
        return !empty($shift['date']) 
            && !empty($shift['shift']) && $shift['shift'] !== ''
            && !empty($shift['start_time']) && $shift['start_time'] !== ''
            && !empty($shift['end_time']) && $shift['end_time'] !== ''
            && !empty($shift['operator_1']) && $shift['operator_1'] !== ''
            && !empty($shift['operator_2']) && $shift['operator_2'] !== '';
    }

    public function mount($id = null)
    {
        $this->id = $id;

        if ($this->id) {
            $this->assignValue();
        }
    }

    public function render()
    {
        return view('livewire.hide-form-monitoring', [
            'users' => User::all()->where('role', 'user')
        ])->layout('layouts.app');
    }
}
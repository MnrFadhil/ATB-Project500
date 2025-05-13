<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class MonitoringForm extends Form
{
    public $shift = [
        'date'            => '',
        'shift'           => '',
        'start_time'      => '00:00',
        'end_time'        => '00:00',
        'notes'           => '',
        'collection_tank' => 0,
        'operator_1'      => '',
        'operator_2'      => '',
    ];

    public $airBaku = [
        'ph'            => 0,
        'turbidity'     => 0,
        'color'         => 0,
        'tds'           => 0,
    ];

    public $sedimentation = [
        'ph'            => 0,
        'turbidity'     => 0,
        'color'         => 0,
        'tds'           => 0,
    ];

    public $reservoir = [
        'ph'            => 0,
        'turbidity'     => 0,
        'color'         => 0,
        'tds'           => 0,
        'free_chlor'    => 0,
        'orp'           => 0,
    ];

    public $flowAirBaku = [
        'flow'          => 0,
        'totalizer'     => 0,
    ];

    public $flowSudarso = [
        'location'      => 'yos sudarso',
        'flow'          => 0,
        'totalizer'     => 0,
    ];

    public $flowVeteran = [
        'location'      => 'veteran',
        'flow'          => 0,
        'totalizer'     => 0,
    ];

    public $reservoirLevel = [
        'level_a'       => 0,
        'level_b'       => 0,
    ];

    public $mdpPanel = [
        'kwh_total'       => 0,
        'wdp'             => 0,
        'lwbp'            => 0,
        'kvar'            => 0,
    ];

    public $pressStatic = [
        'inlet'       => 0,
        'outlet'      => 0,
    ];

    public $pumpIntakeA = [
        'ampere'       => 0,
        'frequency'    => 0,
        'pressure'     => 0,
        'status'       => 'normal'
    ];
    public $pumpIntakeB = [
        'ampere'       => 0,
        'frequency'    => 0,
        'pressure'      => 0,
        'status'       => 'normal'
    ];
    public $pumpIntakeC = [
        'ampere'       => 0,
        'frequency'    => 0,
        'pressure'      => 0,
        'status'       => 'normal'
    ];

    public $pumpDistriA = [
        'ampere'       => 0,
        'frequency'    => 0,
        'pressure'      => 0,
        'status'       => 'normal'
    ];
    public $pumpDistriB = [
        'ampere'       => 0,
        'frequency'    => 0,
        'pressure'      => 0,
        'status'       => 'normal'
    ];
    public $pumpDistriC = [
        'ampere'       => 0,
        'frequency'    => 0,
        'pressure'      => 0,
        'status'       => 'normal'
    ];
    public $pumpDistriD = [
        'ampere'       => 0,
        'frequency'    => 0,
        'pressure'      => 0,
        'status'       => 'normal'
    ];

    public $pumpPac = [
        'frequency'     => 0,
        'dosage'        => 0,
        'concentration' => 0,
        'stirring'      => 0,
        'tank_level'    => 0
    ];

    public $pumpChlor = [
        'flow_rate'     => 0,
        'dosage'        => 0,
        'concentration' => 0,
        'stirring'      => 0,
        'tank_level'    => 0
    ];
    public $unitOper = [
        'barscreen'        => 'normal',
        'finescreen_a'     => 'normal',
        'finescreen_b'     => 'normal',
        'compressor_a'     => 'normal',
        'compressor_b'     => 'normal',
        'air_drayer'       => 'normal',
    ];

    public $wtp = [
        'flokulator_a'     => 'off',
        'flokulator_b'     => 'off',
        'clarifier_a'      => 'off',
        'clarifier_b'      => 'off',
        'filtration'       => 'off',
        'gravity_filter_a' => 0,
        'gravity_filter_b' => 0,
        'gravity_filter_c' => 0,
        'gravity_filter_d' => 0,
        'gravity_filter_e' => 0,
        'gravity_filter_f' => 0,
    ];



    protected $validationAttributes = [
        // shift
        'shift.date'            => 'Date',
        'shift.shift'           => 'Shift',
        'shift.start_time'      => 'Start Time',
        'shift.end_time'        => 'End Time',
        'shift.notes'           => 'Notes',
        'shift.collection_tank' => 'Collection Tank',
        'shift.operator_1'      => 'Operator 1',
        'shift.operator_2'      => 'Operator 2',

        // airBaku
        'airBaku.ph'        => 'Air Baku pH',
        'airBaku.turbidity' => 'Air Baku Turbidity',
        'airBaku.color'     => 'Air Baku Color',
        'airBaku.tds'       => 'Air Baku TDS',

        // sedimentation
        'sedimentation.ph'        => 'Sedimentation pH',
        'sedimentation.turbidity' => 'Sedimentation Turbidity',
        'sedimentation.color'     => 'Sedimentation Color',
        'sedimentation.tds'       => 'Sedimentation TDS',

        // reservoir
        'reservoir.ph'         => 'Reservoir pH',
        'reservoir.turbidity'  => 'Reservoir Turbidity',
        'reservoir.color'      => 'Reservoir Color',
        'reservoir.tds'        => 'Reservoir TDS',
        'reservoir.free_chlor' => 'Reservoir Free Chlorine',
        'reservoir.orp'        => 'Reservoir ORP',

        // flowAirBaku
        'flowAirBaku.flow'      => 'Flow Air Baku',
        'flowAirBaku.totalizer' => 'Flow Air Baku Totalizer',

        // flowSudarso
        'flowSudarso.location'  => 'Flow Sudarso Location',
        'flowSudarso.flow'      => 'Flow Sudarso',
        'flowSudarso.totalizer' => 'Flow Sudarso Totalizer',

        // flowVeteran
        'flowVeteran.location'  => 'Flow Veteran Location',
        'flowVeteran.flow'      => 'Flow Veteran',
        'flowVeteran.totalizer' => 'Flow Veteran Totalizer',

        // reservoirLevel
        'reservoirLevel.level_a' => 'Reservoir Level A',
        'reservoirLevel.level_b' => 'Reservoir Level B',

        // mdpPanel
        'mdpPanel.kwh_total' => 'MDP Panel kWh Total',
        'mdpPanel.wdp'       => 'MDP Panel WDP',
        'mdpPanel.lwbp'      => 'MDP Panel LWBP',
        'mdpPanel.kvar'      => 'MDP Panel KVAR',

        // pressStatic
        'pressStatic.inlet'  => 'Press Static Inlet',
        'pressStatic.outlet' => 'Press Static Outlet',

        // pumpIntake
        'pumpIntakeA.ampere'    => 'Pump Intake A Ampere',
        'pumpIntakeA.frequency' => 'Pump Intake A Frequency',
        'pumpIntakeA.pressure'  => 'Pump Intake A Pressure',
        'pumpIntakeA.status'    => 'Pump Intake A Status',

        'pumpIntakeB.ampere'    => 'Pump Intake B Ampere',
        'pumpIntakeB.frequency' => 'Pump Intake B Frequency',
        'pumpIntakeB.pressure'  => 'Pump Intake B Pressure',
        'pumpIntakeB.status'    => 'Pump Intake B Status',

        'pumpIntakeC.ampere'    => 'Pump Intake C Ampere',
        'pumpIntakeC.frequency' => 'Pump Intake C Frequency',
        'pumpIntakeC.pressure'  => 'Pump Intake C Pressure',
        'pumpIntakeC.status'    => 'Pump Intake C Status',

        // pumpDistri
        'pumpDistriA.ampere'    => 'Pump Distribusi A Ampere',
        'pumpDistriA.frequency' => 'Pump Distribusi A Frequency',
        'pumpDistriA.pressure'  => 'Pump Distribusi A Pressure',
        'pumpDistriA.status'    => 'Pump Distribusi A Status',

        'pumpDistriB.ampere'    => 'Pump Distribusi B Ampere',
        'pumpDistriB.frequency' => 'Pump Distribusi B Frequency',
        'pumpDistriB.pressure'  => 'Pump Distribusi B Pressure',
        'pumpDistriB.status'    => 'Pump Distribusi B Status',

        'pumpDistriC.ampere'    => 'Pump Distribusi C Ampere',
        'pumpDistriC.frequency' => 'Pump Distribusi C Frequency',
        'pumpDistriC.pressure'  => 'Pump Distribusi C Pressure',
        'pumpDistriC.status'    => 'Pump Distribusi C Status',

        'pumpDistriD.ampere'    => 'Pump Distribusi D Ampere',
        'pumpDistriD.frequency' => 'Pump Distribusi D Frequency',
        'pumpDistriD.pressure'  => 'Pump Distribusi D Pressure',
        'pumpDistriD.status'    => 'Pump Distribusi D Status',

        // pumpPac
        'pumpPac.frequency'     => 'Pump PAC Frequency',
        'pumpPac.dosage'        => 'Pump PAC Dosage',
        'pumpPac.concentration' => 'Pump PAC Concentration',
        'pumpPac.stirring'      => 'Pump PAC Stirring',
        'pumpPac.tank_level'    => 'Pump PAC Tank Level',

        // pumpChlor
        'pumpChlor.flow_rate'    => 'Pump Chlorine Flow Rate',
        'pumpChlor.dosage'       => 'Pump Chlorine Dosage',
        'pumpChlor.concentration' => 'Pump Chlorine Concentration',
        'pumpChlor.stirring'     => 'Pump Chlorine Stirring',
        'pumpChlor.tank_level'   => 'Pump Chlorine Tank Level',

        // unitOper
        'unitOper.barscreen'    => 'Barscreen',
        'unitOper.finescreen_a' => 'Fine Screen A',
        'unitOper.finescreen_b' => 'Fine Screen B',
        'unitOper.compressor_a' => 'Compressor A',
        'unitOper.compressor_b' => 'Compressor B',
        'unitOper.air_drayer'   => 'Air Dryer',

        // wtp
        'wtp.flokulator_a'     => 'Flokulator A',
        'wtp.flokulator_b'     => 'Flokulator B',
        'wtp.clarifier_a'      => 'Clarifier A',
        'wtp.clarifier_b'      => 'Clarifier B',
        'wtp.filtration'       => 'Filtration',
        'wtp.gravity_filter_a' => 'Gravity Filter A',
        'wtp.gravity_filter_b' => 'Gravity Filter B',
        'wtp.gravity_filter_c' => 'Gravity Filter C',
        'wtp.gravity_filter_d' => 'Gravity Filter D',
        'wtp.gravity_filter_e' => 'Gravity Filter E',
        'wtp.gravity_filter_f' => 'Gravity Filter F',
    ];


    /* -------------------------------------------------------------------------- */
    /*                                 Rules Form                                 */
    /* -------------------------------------------------------------------------- */
    protected function rules()
    {
        return [
            // shift
            'shift.date'            => 'required|string',
            'shift.shift'           => 'required|string',
            'shift.start_time'      => 'required|string',
            'shift.end_time'        => 'required|string',
            'shift.notes'           => 'nullable|string',
            'shift.collection_tank' => 'required|numeric',
            'shift.operator_1'      => 'required|string',
            'shift.operator_2'      => 'string',

            // airBaku
            'airBaku.ph'        => 'required|numeric',
            'airBaku.turbidity' => 'required|numeric',
            'airBaku.color'     => 'required|integer',
            'airBaku.tds'       => 'required|integer',

            // sedimentation
            'sedimentation.ph'        => 'required|numeric',
            'sedimentation.turbidity' => 'required|numeric',
            'sedimentation.color'     => 'required|integer',
            'sedimentation.tds'       => 'required|integer',

            // reservoir
            'reservoir.ph'         => 'required|numeric',
            'reservoir.turbidity'  => 'required|numeric',
            'reservoir.color'      => 'required|integer',
            'reservoir.tds'        => 'required|integer',
            'reservoir.free_chlor' => 'required|numeric',
            'reservoir.orp'        => 'required|numeric',

            // flowAirBaku
            'flowAirBaku.flow'      => 'required|integer',
            'flowAirBaku.totalizer' => 'required|integer',

            // flowSudarso
            'flowSudarso.location'  => 'required|string',
            'flowSudarso.flow'      => 'required|integer',
            'flowSudarso.totalizer' => 'required|integer',

            // flowVeteran
            'flowVeteran.location'  => 'required|string',
            'flowVeteran.flow'      => 'required|integer',
            'flowVeteran.totalizer' => 'required|integer',

            // reservoirLevel
            'reservoirLevel.level_a' => 'required|numeric',
            'reservoirLevel.level_b' => 'required|numeric',

            // mdpPanel
            'mdpPanel.kwh_total' => 'required|numeric',
            'mdpPanel.wdp'       => 'required|numeric',
            'mdpPanel.lwbp'      => 'required|numeric',
            'mdpPanel.kvar'      => 'required|numeric',

            // pressStatic
            'pressStatic.inlet'  => 'required|numeric',
            'pressStatic.outlet' => 'required|numeric',

            // pumpIntake
            'pumpIntakeA.ampere'    => 'required|numeric',
            'pumpIntakeA.frequency' => 'required|numeric',
            'pumpIntakeA.pressure'  => 'required|numeric',
            'pumpIntakeA.status'    => 'required|string',

            'pumpIntakeB.ampere'    => 'required|numeric',
            'pumpIntakeB.frequency' => 'required|numeric',
            'pumpIntakeB.pressure'  => 'required|numeric',
            'pumpIntakeB.status'    => 'required|string',

            'pumpIntakeC.ampere'    => 'required|numeric',
            'pumpIntakeC.frequency' => 'required|numeric',
            'pumpIntakeC.pressure'  => 'required|numeric',
            'pumpIntakeC.status'    => 'required|string',

            // pumpDistri
            'pumpDistriA.ampere'    => 'required|numeric',
            'pumpDistriA.frequency' => 'required|numeric',
            'pumpDistriA.pressure'  => 'required|numeric',
            'pumpDistriA.status'    => 'required|string',

            'pumpDistriB.ampere'    => 'required|numeric',
            'pumpDistriB.frequency' => 'required|numeric',
            'pumpDistriB.pressure'  => 'required|numeric',
            'pumpDistriB.status'    => 'required|string',

            'pumpDistriC.ampere'    => 'required|numeric',
            'pumpDistriC.frequency' => 'required|numeric',
            'pumpDistriC.pressure'  => 'required|numeric',
            'pumpDistriC.status'    => 'required|string',

            'pumpDistriD.ampere'    => 'required|numeric',
            'pumpDistriD.frequency' => 'required|numeric',
            'pumpDistriD.pressure'  => 'required|numeric',
            'pumpDistriD.status'    => 'required|string',

            // pumpPac
            'pumpPac.frequency'     => 'required|numeric',
            'pumpPac.dosage'        => 'required|numeric',
            'pumpPac.concentration' => 'required|numeric',
            'pumpPac.stirring'      => 'required|numeric',
            'pumpPac.tank_level'    => 'required|numeric',

            // pumpChlor
            'pumpChlor.flow_rate'   => 'required|numeric',
            'pumpChlor.dosage'      => 'required|numeric',
            'pumpChlor.concentration' => 'required|numeric',
            'pumpChlor.stirring'    => 'required|numeric',
            'pumpChlor.tank_level'  => 'required|numeric',

            // unitOper
            'unitOper.barscreen'    => 'required|string',
            'unitOper.finescreen_a' => 'required|string',
            'unitOper.finescreen_b' => 'required|string',
            'unitOper.compressor_a' => 'required|string',
            'unitOper.compressor_b' => 'required|string',
            'unitOper.air_drayer'   => 'required|string',

            // wtp
            'wtp.flokulator_a'     => 'required|string',
            'wtp.flokulator_b'     => 'required|string',
            'wtp.clarifier_a'      => 'required|string',
            'wtp.clarifier_b'      => 'required|string',
            'wtp.filtration'       => 'required|string',
            'wtp.gravity_filter_a' => 'required|numeric',
            'wtp.gravity_filter_b' => 'required|numeric',
            'wtp.gravity_filter_c' => 'required|numeric',
            'wtp.gravity_filter_d' => 'required|numeric',
            'wtp.gravity_filter_e' => 'required|numeric',
            'wtp.gravity_filter_f' => 'required|numeric',
        ];
    }
}

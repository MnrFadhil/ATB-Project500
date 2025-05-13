<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class MonitoringForm extends Form
{
    public $shift = [
        'date'            => '',
        'shift'           => 'shift i',
        'start_time'      => '00:00',
        'end_time'        => '00:00',
        'notes'           => '',
        'collection_tank' => 0,
        'operator_1'      => '',
        'operator_2'      => '',
    ];

    public $airBaku = [
        'ph'            => 1,
        'turbidity'     => 2,
        'color'         => 3,
        'tds'           => 4,
    ];

    public $sedimentation = [
        'ph'            => 5,
        'turbidity'     => 6,
        'color'         => 7,
        'tds'           => 8,
    ];

    public $reservoir = [
        'ph'            => 9,
        'turbidity'     => 10,
        'color'         => 11,
        'tds'           => 12,
        'free_chlor'    => 13,
        'orp'           => 14,
    ];

    public $flowAirBaku = [
        'flow'          => 15,
        'totalizer'     => 16,
    ];

    public $flowSudarso = [
        'location'      => 'yos sudarso',
        'flow'          => 17,
        'totalizer'     => 18,
    ];

    public $flowVeteran = [
        'location'      => 'veteran',
        'flow'          => 19,
        'totalizer'     => 20,
    ];

    public $reservoirLevel = [
        'level_a'       => 21,
        'level_b'       => 22,
    ];

    public $mdpPanel = [
        'kwh_total'       => 23,
        'wdp'             => 24,
        'lwbp'            => 25,
        'kvar'            => 26,
    ];

    public $pressStatic = [
        'inlet'       => 27,
        'outlet'      => 28,
    ];

    public $pumpIntakeA = [
        'ampere'       => 29,
        'frequency'    => 30,
        'pressure'     => 31,
        'status'       => 'standby'
    ];
    public $pumpIntakeB = [
        'ampere'       => 32,
        'frequency'    => 33,
        'pressure'      => 34,
        'status'       => 'standby'
    ];
    public $pumpIntakeC = [
        'ampere'       => 35,
        'frequency'    => 36,
        'pressure'      => 37,
        'status'       => 'standby'
    ];

    public $pumpDistriA = [
        'ampere'       => 38,
        'frequency'    => 39,
        'pressure'      => 40,
        'status'       => 'standby'
    ];
    public $pumpDistriB = [
        'ampere'       => 41,
        'frequency'    => 42,
        'pressure'      => 43,
        'status'       => 'standby'
    ];
    public $pumpDistriC = [
        'ampere'       => 44,
        'frequency'    => 45,
        'pressure'      => 46,
        'status'       => 'standby'
    ];
    public $pumpDistriD = [
        'ampere'       => 47,
        'frequency'    => 48,
        'pressure'      => 49,
        'status'       => 'standby'
    ];

    public $pumpPac = [
        'frequency'     => 50,
        'dosage'        => 51,
        'concentration' => 52,
        'stirring'      => 53,
        'tank_level'    => 54
    ];

    public $pumpChlor = [
        'flow_rate'     => 55,
        'dosage'        => 56,
        'concentration' => 57,
        'stirring'      => 58,
        'tank_level'    => 59
    ];
    public $unitOper = [
        'barscreen'        => 'standby',
        'finescreen_a'     => 'standby',
        'finescreen_b'     => 'standby',
        'compressor_a'     => 'standby',
        'compressor_b'     => 'standby',
        'air_drayer'       => 'standby',
    ];

    public $wtp = [
        'flokulator_a'     => 'on',
        'flokulator_b'     => 'on',
        'clarifier_a'      => 'on',
        'clarifier_b'      => 'on',
        'filtration'       => 'on',
        'gravity_filter_a' => 60,
        'gravity_filter_b' => 61,
        'gravity_filter_c' => 62,
        'gravity_filter_d' => 63,
        'gravity_filter_e' => 64,
        'gravity_filter_f' => 65,
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
            'shift.operator_2'      => 'required|string',

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

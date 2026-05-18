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
        'collection_tank' => '',
        'operator_1'      => '',
        'operator_2'      => '',
    ];

    public $airBaku = [
        'ph'            => '',
        'turbidity'     => '',
        'color'         => '',
        'tds'           => '',
    ];

    public $sedimentation = [
        'ph'            => '',
        'turbidity'     => '',
        'color'         => '',
        'tds'           => '',
    ];

    public $reservoir = [
        'ph'            => '',
        'turbidity'     => '',
        'color'         => '',
        'tds'           => '',
        'free_chlor'    => '',
        'orp'           => '',
    ];

    public $flowAirBaku = [
        'flow'          => '',
        'totalizer'     => '',
    ];

    public $flowSudarso = [
        'location'      => 'yos sudarso',
        'flow'          => '',
        'totalizer'     => '',
    ];

    public $flowVeteran = [
        'location'      => 'veteran',
        'flow'          => '',
        'totalizer'     => '',
    ];

    public $reservoirLevel = [
        'level_a'       => '',
        'level_b'       => '',
    ];

    public $mdpPanel = [
        'kwh_total'       => '',
        'wdp'             => '',
        'lwbp'            => '',
        'kvar'            => '',
    ];

    public $pressStatic = [
        'inlet'       => '',
        'outlet'      => '',
    ];

    public $pumpIntakeA = [
        'ampere'       => '',
        'frequency'    => '',
        'pressure'     => '',
        'status'       => 'standby'
    ];
    public $pumpIntakeB = [
        'ampere'       => '',
        'frequency'    => '',
        'pressure'     => '',
        'status'       => 'standby'
    ];
    public $pumpIntakeC = [
        'ampere'       => '',
        'frequency'    => '',
        'pressure'     => '',
        'status'       => 'standby'
    ];

    public $pumpDistriA = [
        'ampere'       => '',
        'frequency'    => '',
        'pressure'     => '',
        'status'       => 'standby'
    ];
    public $pumpDistriB = [
        'ampere'       => '',
        'frequency'    => '',
        'pressure'     => '',
        'status'       => 'standby'
    ];
    public $pumpDistriC = [
        'ampere'       => '',
        'frequency'    => '',
        'pressure'     => '',
        'status'       => 'standby'
    ];
    public $pumpDistriD = [
        'ampere'       => '',
        'frequency'    => '',
        'pressure'     => '',
        'status'       => 'standby'
    ];

    // ========== POMPA PAC A & B ==========
    public $pumpPacA = [
        'type'          => 'pac',
        'pump_unit'     => 'A',
        'status'        => 'standby',
        'frequency'     => '',
        'dosage'        => '',
        'concentration' => '',
        'stirring'      => 0,
        'tank_level'    => 0
    ];

    public $pumpPacB = [
        'type'          => 'pac',
        'pump_unit'     => 'B',
        'status'        => 'standby',
        'frequency'     => '',
        'dosage'        => '',
        'concentration' => '',
        'stirring'      => 0,
        'tank_level'    => 0
    ];

    // ========== POMPA CHLORINE A & B ==========
    public $pumpChlorA = [
        'type'          => 'chlorine/kaporit',
        'pump_unit'     => 'A',
        'status'        => 'standby',
        'flow_rate'     => '',
        'dosage'        => '',
        'concentration' => '',
        'stirring'      => 0,
        'tank_level'    => 0
    ];

    public $pumpChlorB = [
        'type'          => 'chlorine/kaporit',
        'pump_unit'     => 'B',
        'status'        => 'standby',
        'flow_rate'     => '',
        'dosage'        => '',
        'concentration' => '',
        'stirring'      => 0,
        'tank_level'    => 0
    ];

    // ========== POMPA SODA ASH A & B ==========
    public $pumpSodaA = [
        'type'          => 'soda ash',
        'pump_unit'     => 'A',
        'status'        => 'standby',
        'flow_rate'     => '',
        'dosage'        => '',
        'concentration' => '',
        'stirring'      => 0,
        'tank_level'    => 0
    ];

    public $pumpSodaB = [
        'type'          => 'soda ash',
        'pump_unit'     => 'B',
        'status'        => 'standby',
        'flow_rate'     => '',
        'dosage'        => '',
        'concentration' => '',
        'stirring'      => 0,
        'tank_level'    => 0
    ];

    // ========== POMPA POLYMER A & B ==========
    public $pumpPolymerA = [
        'type'          => 'polymer',
        'pump_unit'     => 'A',
        'status'        => 'standby',
        'flow_rate'     => '',
        'dosage'        => '',
        'concentration' => '',
        'stirring'      => 0,
        'tank_level'    => 0
    ];

    public $pumpPolymerB = [
        'type'          => 'polymer',
        'pump_unit'     => 'B',
        'status'        => 'standby',
        'flow_rate'     => '',
        'dosage'        => '',
        'concentration' => '',
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
        'gravity_filter_a' => '',
        'gravity_filter_b' => '',
        'gravity_filter_c' => '',
        'gravity_filter_d' => '',
        'gravity_filter_e' => '',
        'gravity_filter_f' => '',
        'gravity_filter_a_status' => 'standby',
        'gravity_filter_b_status' => 'standby',
        'gravity_filter_c_status' => 'standby',
        'gravity_filter_d_status' => 'standby',
        'gravity_filter_e_status' => 'standby',
        'gravity_filter_f_status' => 'standby',
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

        // pumpPac A
        'pumpPacA.status'        => 'Pump PAC A Status',
        'pumpPacA.frequency'     => 'Pump PAC A Frequency',
        'pumpPacA.dosage'        => 'Pump PAC A Dosage',
        'pumpPacA.concentration' => 'Pump PAC A Concentration',
        'pumpPacA.stirring'      => 'Pump PAC A Stirring',
        'pumpPacA.tank_level'    => 'Pump PAC A Tank Level',

        // pumpPac B
        'pumpPacB.status'        => 'Pump PAC B Status',
        'pumpPacB.frequency'     => 'Pump PAC B Frequency',
        'pumpPacB.dosage'        => 'Pump PAC B Dosage',
        'pumpPacB.concentration' => 'Pump PAC B Concentration',
        'pumpPacB.stirring'      => 'Pump PAC B Stirring',
        'pumpPacB.tank_level'    => 'Pump PAC B Tank Level',

        // pumpChlor A
        'pumpChlorA.status'        => 'Pump Chlorine A Status',
        'pumpChlorA.flow_rate'     => 'Pump Chlorine A Flow Rate',
        'pumpChlorA.dosage'        => 'Pump Chlorine A Dosage',
        'pumpChlorA.concentration' => 'Pump Chlorine A Concentration',
        'pumpChlorA.stirring'      => 'Pump Chlorine A Stirring',
        'pumpChlorA.tank_level'    => 'Pump Chlorine A Tank Level',

        // pumpChlor B
        'pumpChlorB.status'        => 'Pump Chlorine B Status',
        'pumpChlorB.flow_rate'     => 'Pump Chlorine B Flow Rate',
        'pumpChlorB.dosage'        => 'Pump Chlorine B Dosage',
        'pumpChlorB.concentration' => 'Pump Chlorine B Concentration',
        'pumpChlorB.stirring'      => 'Pump Chlorine B Stirring',
        'pumpChlorB.tank_level'    => 'Pump Chlorine B Tank Level',

        // pumpSoda A
        'pumpSodaA.status'        => 'Pump Soda Ash A Status',
        'pumpSodaA.flow_rate'     => 'Pump Soda Ash A Flow Rate',
        'pumpSodaA.dosage'        => 'Pump Soda Ash A Dosage',
        'pumpSodaA.concentration' => 'Pump Soda Ash A Concentration',
        'pumpSodaA.stirring'      => 'Pump Soda Ash A Stirring',
        'pumpSodaA.tank_level'    => 'Pump Soda Ash A Tank Level',

        // pumpSoda B
        'pumpSodaB.status'        => 'Pump Soda Ash B Status',
        'pumpSodaB.flow_rate'     => 'Pump Soda Ash B Flow Rate',
        'pumpSodaB.dosage'        => 'Pump Soda Ash B Dosage',
        'pumpSodaB.concentration' => 'Pump Soda Ash B Concentration',
        'pumpSodaB.stirring'      => 'Pump Soda Ash B Stirring',
        'pumpSodaB.tank_level'    => 'Pump Soda Ash B Tank Level',

        // pumpPolymer A
        'pumpPolymerA.status'        => 'Pump Polymer A Status',
        'pumpPolymerA.flow_rate'     => 'Pump Polymer A Flow Rate',
        'pumpPolymerA.dosage'        => 'Pump Polymer A Dosage',
        'pumpPolymerA.concentration' => 'Pump Polymer A Concentration',
        'pumpPolymerA.stirring'      => 'Pump Polymer A Stirring',
        'pumpPolymerA.tank_level'    => 'Pump Polymer A Tank Level',

        // pumpPolymer B
        'pumpPolymerB.status'        => 'Pump Polymer B Status',
        'pumpPolymerB.flow_rate'     => 'Pump Polymer B Flow Rate',
        'pumpPolymerB.dosage'        => 'Pump Polymer B Dosage',
        'pumpPolymerB.concentration' => 'Pump Polymer B Concentration',
        'pumpPolymerB.stirring'      => 'Pump Polymer B Stirring',
        'pumpPolymerB.tank_level'    => 'Pump Polymer B Tank Level',

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
        'wtp.gravity_filter_a_status' => 'Gravity Filter A Status',
        'wtp.gravity_filter_b_status' => 'Gravity Filter B Status',
        'wtp.gravity_filter_c_status' => 'Gravity Filter C Status',
        'wtp.gravity_filter_d_status' => 'Gravity Filter D Status',
        'wtp.gravity_filter_e_status' => 'Gravity Filter E Status',
        'wtp.gravity_filter_f_status' => 'Gravity Filter F Status',
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
            'airBaku.color'     => 'required|numeric',
            'airBaku.tds'       => 'required|numeric',

            // sedimentation
            'sedimentation.ph'        => 'required|numeric',
            'sedimentation.turbidity' => 'required|numeric',
            'sedimentation.color'     => 'required|numeric',
            'sedimentation.tds'       => 'required|numeric',

            // reservoir
            'reservoir.ph'         => 'required|numeric',
            'reservoir.turbidity'  => 'required|numeric',
            'reservoir.color'      => 'required|numeric',
            'reservoir.tds'        => 'required|numeric',
            'reservoir.free_chlor' => 'required|numeric',
            'reservoir.orp'        => 'required|numeric',

            // flowAirBaku
            'flowAirBaku.flow'      => 'required|numeric',
            'flowAirBaku.totalizer' => 'required|numeric',

            // flowSudarso
            'flowSudarso.location'  => 'required|string',
            'flowSudarso.flow'      => 'required|numeric',
            'flowSudarso.totalizer' => 'required|numeric',

            // flowVeteran
            'flowVeteran.location'  => 'required|string',
            'flowVeteran.flow'      => 'required|numeric',
            'flowVeteran.totalizer' => 'required|numeric',

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
            'pumpIntakeA.ampere'    => 'required_if:pumpIntakeA.status,running|numeric',
            'pumpIntakeA.frequency' => 'required_if:pumpIntakeA.status,running|numeric',
            'pumpIntakeA.pressure'  => 'required_if:pumpIntakeA.status,running|numeric',
            'pumpIntakeA.status'    => 'required|string',

            'pumpIntakeB.ampere'    => 'required_if:pumpIntakeB.status,running|numeric',
            'pumpIntakeB.frequency' => 'required_if:pumpIntakeB.status,running|numeric',
            'pumpIntakeB.pressure'  => 'required_if:pumpIntakeB.status,running|numeric',
            'pumpIntakeB.status'    => 'required|string',

            'pumpIntakeC.ampere'    => 'required_if:pumpIntakeC.status,running|numeric',
            'pumpIntakeC.frequency' => 'required_if:pumpIntakeC.status,running|numeric',
            'pumpIntakeC.pressure'  => 'required_if:pumpIntakeC.status,running|numeric',
            'pumpIntakeC.status'    => 'required|string',

            // pumpDistri
            'pumpDistriA.ampere'    => 'required_if:pumpDistriA.status,running|numeric',
            'pumpDistriA.frequency' => 'required_if:pumpDistriA.status,running|numeric',
            'pumpDistriA.pressure'  => 'required_if:pumpDistriA.status,running|numeric',
            'pumpDistriA.status'    => 'required|string',

            'pumpDistriB.ampere'    => 'required_if:pumpDistriB.status,running|numeric',
            'pumpDistriB.frequency' => 'required_if:pumpDistriB.status,running|numeric',
            'pumpDistriB.pressure'  => 'required_if:pumpDistriB.status,running|numeric',
            'pumpDistriB.status'    => 'required|string',

            'pumpDistriC.ampere'    => 'required_if:pumpDistriC.status,running|numeric',
            'pumpDistriC.frequency' => 'required_if:pumpDistriC.status,running|numeric',
            'pumpDistriC.pressure'  => 'required_if:pumpDistriC.status,running|numeric',
            'pumpDistriC.status'    => 'required|string',

            'pumpDistriD.ampere'    => 'required_if:pumpDistriD.status,running|numeric',
            'pumpDistriD.frequency' => 'required_if:pumpDistriD.status,running|numeric',
            'pumpDistriD.pressure'  => 'required_if:pumpDistriD.status,running|numeric',
            'pumpDistriD.status'    => 'required|string',

            // pumpPac A
            'pumpPacA.status'        => 'required|string',
            'pumpPacA.frequency'     => 'required_if:pumpPacA.status,running|numeric',
            'pumpPacA.dosage'        => 'required_if:pumpPacA.status,running|numeric',
            'pumpPacA.concentration' => 'required_if:pumpPacA.status,running|numeric',
            'pumpPacA.stirring'      => 'required_if:pumpPacA.status,running|numeric',
            'pumpPacA.tank_level'    => 'required_if:pumpPacA.status,running|numeric',

            // pumpPac B
            'pumpPacB.status'        => 'required|string',
            'pumpPacB.frequency'     => 'required_if:pumpPacB.status,running|numeric',
            'pumpPacB.dosage'        => 'required_if:pumpPacB.status,running|numeric',
            'pumpPacB.concentration' => 'required_if:pumpPacB.status,running|numeric',
            'pumpPacB.stirring'      => 'required_if:pumpPacB.status,running|numeric',
            'pumpPacB.tank_level'    => 'required_if:pumpPacB.status,running|numeric',

            // pumpChlor A
            'pumpChlorA.status'        => 'required|string',
            'pumpChlorA.flow_rate'     => 'required_if:pumpChlorA.status,running|numeric',
            'pumpChlorA.dosage'        => 'required_if:pumpChlorA.status,running|numeric',
            'pumpChlorA.concentration' => 'required_if:pumpChlorA.status,running|numeric',
            'pumpChlorA.stirring'      => 'required_if:pumpChlorA.status,running|numeric',
            'pumpChlorA.tank_level'    => 'required_if:pumpChlorA.status,running|numeric',

            // pumpChlor B
            'pumpChlorB.status'        => 'required|string',
            'pumpChlorB.flow_rate'     => 'required_if:pumpChlorB.status,running|numeric',
            'pumpChlorB.dosage'        => 'required_if:pumpChlorB.status,running|numeric',
            'pumpChlorB.concentration' => 'required_if:pumpChlorB.status,running|numeric',
            'pumpChlorB.stirring'      => 'required_if:pumpChlorB.status,running|numeric',
            'pumpChlorB.tank_level'    => 'required_if:pumpChlorB.status,running|numeric',

            // pumpSoda A
            'pumpSodaA.status'        => 'required|string',
            'pumpSodaA.flow_rate'     => 'required_if:pumpSodaA.status,running|numeric',
            'pumpSodaA.dosage'        => 'required_if:pumpSodaA.status,running|numeric',
            'pumpSodaA.concentration' => 'required_if:pumpSodaA.status,running|numeric',
            'pumpSodaA.stirring'      => 'required_if:pumpSodaA.status,running|numeric',
            'pumpSodaA.tank_level'    => 'required_if:pumpSodaA.status,running|numeric',

            // pumpSoda B
            'pumpSodaB.status'        => 'required|string',
            'pumpSodaB.flow_rate'     => 'required_if:pumpSodaB.status,running|numeric',
            'pumpSodaB.dosage'        => 'required_if:pumpSodaB.status,running|numeric',
            'pumpSodaB.concentration' => 'required_if:pumpSodaB.status,running|numeric',
            'pumpSodaB.stirring'      => 'required_if:pumpSodaB.status,running|numeric',
            'pumpSodaB.tank_level'    => 'required_if:pumpSodaB.status,running|numeric',

            // pumpPolymer A
            'pumpPolymerA.status'        => 'required|string',
            'pumpPolymerA.flow_rate'     => 'required_if:pumpPolymerA.status,running|numeric',
            'pumpPolymerA.dosage'        => 'required_if:pumpPolymerA.status,running|numeric',
            'pumpPolymerA.concentration' => 'required_if:pumpPolymerA.status,running|numeric',
            'pumpPolymerA.stirring'      => 'required_if:pumpPolymerA.status,running|numeric',
            'pumpPolymerA.tank_level'    => 'required_if:pumpPolymerA.status,running|numeric',

            // pumpPolymer B
            'pumpPolymerB.status'        => 'required|string',
            'pumpPolymerB.flow_rate'     => 'required_if:pumpPolymerB.status,running|numeric',
            'pumpPolymerB.dosage'        => 'required_if:pumpPolymerB.status,running|numeric',
            'pumpPolymerB.concentration' => 'required_if:pumpPolymerB.status,running|numeric',
            'pumpPolymerB.stirring'      => 'required_if:pumpPolymerB.status,running|numeric',
            'pumpPolymerB.tank_level'    => 'required_if:pumpPolymerB.status,running|numeric',

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
            'wtp.gravity_filter_a' => 'required_if:wtp.gravity_filter_a_status,running|numeric',
            'wtp.gravity_filter_b' => 'required_if:wtp.gravity_filter_b_status,running|numeric',
            'wtp.gravity_filter_c' => 'required_if:wtp.gravity_filter_c_status,running|numeric',
            'wtp.gravity_filter_d' => 'required_if:wtp.gravity_filter_d_status,running|numeric',
            'wtp.gravity_filter_e' => 'required_if:wtp.gravity_filter_e_status,running|numeric',
            'wtp.gravity_filter_f' => 'required_if:wtp.gravity_filter_f_status,running|numeric',
            'wtp.gravity_filter_a_status' => 'required|string',
            'wtp.gravity_filter_b_status' => 'required|string',
            'wtp.gravity_filter_c_status' => 'required|string',
            'wtp.gravity_filter_d_status' => 'required|string',
            'wtp.gravity_filter_e_status' => 'required|string',
            'wtp.gravity_filter_f_status' => 'required|string',
        ];
    }
}

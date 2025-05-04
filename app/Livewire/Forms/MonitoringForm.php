<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class MonitoringForm extends Form
{
    public $shift = [
        'date'            => '',
        'shift'           => 'shift i',
        'start_time'      => '',
        'end_time'        => '',
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
        'status'       => 'standby'
    ];
    public $pumpIntakeB = [
        'ampere'       => 0,
        'frequency'    => 0,
        'pressure'      => 0,
        'status'       => 'standby'
    ];
    public $pumpIntakeC = [
        'ampere'       => 0,
        'frequency'    => 0,
        'pressure'      => 0,
        'status'       => 'standby'
    ];

    public $pumpDistriA = [
        'ampere'       => 0,
        'frequency'    => 0,
        'pressure'      => 0,
        'status'       => 'standby'
    ];
    public $pumpDistriB = [
        'ampere'       => 0,
        'frequency'    => 0,
        'pressure'      => 0,
        'status'       => 'standby'
    ];
    public $pumpDistriC = [
        'ampere'       => 0,
        'frequency'    => 0,
        'pressure'      => 0,
        'status'       => 'standby'
    ];
    public $pumpDistriD = [
        'ampere'       => 0,
        'frequency'    => 0,
        'pressure'      => 0,
        'status'       => 'standby'
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
        'gravity_filter_a' => 0,
        'gravity_filter_b' => 0,
        'gravity_filter_c' => 0,
        'gravity_filter_d' => 0,
        'gravity_filter_e' => 0,
        'gravity_filter_f' => 0,
    ];

    protected function rules()
    {
        return [
            'shift' => [
                'date'            => 'required|string',
                'shift'           => 'required|string',
                'start_time'      => 'required|string',
                'end_time'        => 'required|string',
                'notes'           => 'nullable|string',
                'collection_tank' => 'required|decimal',
                'operator_1'      => 'required|string',
                'operator_2'      => 'required|string',
            ],
            'airBaku' => [
                'ph'              => 'required|decimal',
                'turbidity'       => 'required|decimal',
                'color'           => 'required|integer',
                'tds'             => 'required|integer',
            ],
            'sedimentation' => [
                'ph'              => 'required|decimal',
                'turbidity'       => 'required|decimal',
                'color'           => 'required|integer',
                'tds'             => 'required|integer',
            ],
            'reservoir' => [
                'ph'              => 'required|decimal',
                'turbidity'       => 'required|decimal',
                'color'           => 'required|integer',
                'tds'             => 'required|integer',
                'free_chlor'      => 'required|decimal',
                'orp'             => 'required|decimal',
            ],
            'flowAirBaku' => [
                'flow'            => 'required|integer',
                'totalizer'       => 'required|integer',
            ],
            'flowSudarso' => [
                'location'        => 'required|string',
                'flow'            => 'required|integer',
                'totalizer'       => 'required|integer',
            ],
            'flowVeteran' => [
                'location'        => 'required|string',
                'flow'            => 'required|integer',
                'totalizer'       => 'required|integer',
            ],
            'reservoirLevel' => [
                'level_a'         => 'required|decimal',
                'level_b'         => 'required|decimal',
            ],
            'mdpPanel' => [
                'kwh_total'       => 'required|decimal',
                'wdp'             => 'required|decimal',
                'lwbp'            => 'required|decimal',
                'kvar'            => 'required|decimal',
            ],
            'pressStatic' => [
                'inlet'           => 'required|decimal',
                'outlet'          => 'required|decimal',
            ],
            'pumpIntakeA' => [
                'ampere'          => 'required|decimal',
                'frequency'       => 'required|decimal',
                'pressure'        => 'required|decimal',
                'status'          => 'required|string',
            ],
            'pumpIntakeB' => [
                'ampere'          => 'required|decimal',
                'frequency'       => 'required|decimal',
                'pressure'        => 'required|decimal',
                'status'          => 'required|string',
            ],
            'pumpIntakeC' => [
                'ampere'          => 'required|decimal',
                'frequency'       => 'required|decimal',
                'pressure'        => 'required|decimal',
                'status'          => 'required|string',
            ],
            'pumpDistriA' => [
                'ampere'          => 'required|decimal',
                'frequency'       => 'required|decimal',
                'pressure'        => 'required|decimal',
                'status'          => 'required|string',
            ],
            'pumpDistriB' => [
                'ampere'          => 'required|decimal',
                'frequency'       => 'required|decimal',
                'pressure'        => 'required|decimal',
                'status'          => 'required|string',
            ],
            'pumpDistriC' => [
                'ampere'          => 'required|decimal',
                'frequency'       => 'required|decimal',
                'pressure'        => 'required|decimal',
                'status'          => 'required|string',
            ],
            'pumpDistriD' => [
                'ampere'          => 'required|decimal',
                'frequency'       => 'required|decimal',
                'pressure'        => 'required|decimal',
                'status'          => 'required|string',
            ],
            'pumpPac' => [
                'frequency'       => 'required|decimal',
                'dosage'          => 'required|decimal',
                'concentration'   => 'required|decimal',
                'stirring'        => 'required|decimal',
                'tank_level'      => 'required|decimal',
            ],
            'pumpChlor' => [
                'flow_rate'       => 'required|decimal',
                'dosage'          => 'required|decimal',
                'concentration'   => 'required|decimal',
                'stirring'        => 'required|decimal',
                'tank_level'      => 'required|decimal',
            ],
            'unitOper' => [
                'barscreen'       => 'required|string',
                'finescreen_a'    => 'required|string',
                'finescreen_b'    => 'required|string',
                'compressor_a'    => 'required|string',
                'compressor_b'    => 'required|string',
                'air_drayer'      => 'required|string',
            ],
            'unitOper' => [
                'flokulator_a'     => 'required|string',
                'flokulator_b'     => 'required|string',
                'clarifier_a'      => 'required|string',
                'clarifier_b'      => 'required|string',
                'gravity_filter_a' => 'reuired|decimal',
                'gravity_filter_b' => 'reuired|decimal',
                'gravity_filter_c' => 'reuired|decimal',
                'gravity_filter_d' => 'reuired|decimal',
                'gravity_filter_e' => 'reuired|decimal',
                'gravity_filter_f' => 'reuired|decimal',
            ],
        ];
    }
}

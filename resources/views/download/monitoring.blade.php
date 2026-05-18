<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <style>
        body {
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table,
        th,
        td {
            border: 1px solid black;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
            white-space: nowrap;
        }

        .text-center {
            text-align: center;
        }

        .page-break {
            page-break-before: always;
        }
    </style>

    <title>Report</title>

    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/sb-admin-2.js'])
</head>

<body>
    <table>
        <thead style="background-color: rgb(193, 193, 193)">
            <tr>
                <th scope="col" rowspan="3" class="text-center">Tanggal</th>
                <th scope="col" rowspan="3" class="text-center">Shift</th>
                <th scope="col" rowspan="3" class="text-center">Jam</th>
                <th scope="col" colspan="2" class="text-center">Operator</th>
                <th scope="col" colspan="4" class="text-center">Air Baku</th>
                <th scope="col" colspan="4" class="text-center">Sedimentation</th>
                <th scope="col" colspan="6" class="text-center">Reservoir</th>
            </tr>
            <tr>
                {{-- Operator --}}
                <th rowspan="2">Operator 1</th>
                <th rowspan="2">Operator 2</th>

                {{-- Air Baku --}}
                <th>pH</th>
                <th>Turbidity</th>
                <th>Warna</th>
                <th>TDS</th>

                {{-- Sedimentation --}}
                <th>pH</th>
                <th>Turbidity</th>
                <th>Warna</th>
                <th>TDS</th>

                {{-- Reservoir --}}
                <th>pH</th>
                <th>Turbidity</th>
                <th>Warna</th>
                <th>TDS</th>
                <th>Free Chlor</th>
                <th>ORP</th>
            </tr>

            <tr>
                {{-- Air Baku Stuan --}}
                <th>-</th>
                <th>NTU</th>
                <th>PCU</th>
                <th>ppm</th>

                {{-- Sedimentation Stuan --}}
                <th>-</th>
                <th>NTU</th>
                <th>PCU</th>
                <th>ppm</th>

                {{-- Reservoir --}}
                <th>-</th>
                <th>NTU</th>
                <th>PCU</th>
                <th>ppm</th>
                <th>mg/L</th>
                <th>mV</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($shifts as $shift)
                <tr>
                    <td>{{ $shift['date'] }}</td>
                    <td style="text-transform: uppercase;">{{ $shift['shift'] }}</td>
                    <td>{{ substr($shift['start_time'], 0, 5) }} -
                        {{ substr($shift['end_time'], 0, 5) }}</td>

                    <td>{{ $shift['shift_operators'][0]['name'] }}</td>
                    <td>{{ $shift['shift_operators'][1]['name'] ?? '-' }}</td>


                    @foreach ($shift['water_qualities'] as $waterQuality)
                        @if ($waterQuality['type'] == 'air baku')
                            <td>{{ $waterQuality['ph'] }}</td>
                            <td>{{ $waterQuality['turbidity'] }}</td>
                            <td>{{ $waterQuality['color'] }}</td>
                            <td>{{ $waterQuality['tds'] }}</td>
                        @endif

                        @if ($waterQuality['type'] == 'sedimentation')
                            <td>{{ $waterQuality['ph'] }}</td>
                            <td>{{ $waterQuality['turbidity'] }}</td>
                            <td>{{ $waterQuality['color'] }}</td>
                            <td>{{ $waterQuality['tds'] }}</td>
                        @endif

                        @if ($waterQuality['type'] == 'reservoir')
                            <td>{{ $waterQuality['ph'] }}</td>
                            <td>{{ $waterQuality['turbidity'] }}</td>
                            <td>{{ $waterQuality['color'] }}</td>
                            <td>{{ $waterQuality['tds'] }}</td>
                            <td>{{ $waterQuality['free_chlor'] }}</td>
                            <td>{{ $waterQuality['orp'] }}</td>
                        @endif
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="page-break"></div>

    <table>
        <thead style="background-color: rgb(193, 193, 193)">
            <tr>
                <th scope="col" colspan="2" class="text-center">Flow Air Baku</th>
                <th scope="col" colspan="2" class="text-center">Pressure Static Mixer</th>
                <th scope="col" colspan="2" class="text-center">
                    <div>Flow Meter Distribusi</div>
                    <div>Yos Sudarso</div>
                </th>
                <th scope="col" colspan="2" class="text-center">
                    <div>Flow Meter Distribusi</div>
                    <div>Veteran</div>
                </th>
                <th scope="col" colspan="2" class="text-center">Reservoir</th>
                <th scope="col" colspan="4" class="text-center">In Comer MDP Panel </th>
            </tr>
            <tr>
                {{-- Flow Meter Distribusi --}}
                <th>Flow</th>
                <th>Totalizer</th>

                {{-- Pressure Static Mixer --}}
                <th>Inlet</th>
                <th>Outler</th>

                {{-- Flow Meter Sudarso --}}
                <th>Flow</th>
                <th>Totalizer</th>

                {{-- Flow Meter Veteran --}}
                <th>Flow</th>
                <th>Totalizer</th>

                {{-- Reservoir --}}
                <th>Reservoir A</th>
                <th>Reservoir B</th>

                {{-- In Comer MDP Panel --}}
                <th rowspan="2">KWH Total</th>
                <th rowspan="2">WBP</th>
                <th rowspan="2">LWBP</th>
                <th rowspan="2">KVARH</th>
            </tr>

            <tr>
                {{-- Flow Meter Distribusi --}}
                <th>L/s</th>
                <th>m³</th>

                {{-- Pressure Static Mixer --}}
                <th>Bar</th>
                <th>Bar</th>

                {{-- Flow Meter Sudarso --}}
                <th>L/s</th>
                <th>m³</th>

                {{-- Flow Meter Veteran --}}
                <th>L/s</th>
                <th>m³</th>


                {{-- Reservoir --}}
                <th>m</th>
                <th>m</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($shifts as $shift)
                <tr>
                    @foreach ($shift['flow_meters'] as $flowMeter)
                        @if (!$flowMeter['location'])
                            <td>{{ $flowMeter['flow'] }}</td>
                            <td>{{ $flowMeter['totalizer'] }}</td>
                        @endif
                    @endforeach

                    <td>{{ $shift['pressure_static_mixer']['inlet'] }}</td>
                    <td>{{ $shift['pressure_static_mixer']['outlet'] }}</td>

                    @foreach ($shift['flow_meters'] as $flowMeter)
                        @if ($flowMeter['location'] == 'yos sudarso')
                            <td>{{ $flowMeter['flow'] }}</td>
                            <td>{{ $flowMeter['totalizer'] }}</td>
                        @endif

                        @if ($flowMeter['location'] == 'veteran')
                            <td>{{ $flowMeter['flow'] }}</td>
                            <td>{{ $flowMeter['totalizer'] }}</td>
                        @endif
                    @endforeach

                    <td>{{ $shift['reservoir_levels']['level_a'] }}</td>
                    <td>{{ $shift['reservoir_levels']['level_b'] }}</td>


                    <td>{{ $shift['mdp_panels']['kwh_total'] }}</td>
                    <td>{{ $shift['mdp_panels']['wdp'] }}</td>
                    <td>{{ $shift['mdp_panels']['lwbp'] }}</td>
                    <td>{{ $shift['mdp_panels']['kvar'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="page-break"></div>

    <table>
        <thead style="background-color: rgb(193, 193, 193)">
            <tr>
                <th scope="col" colspan="12" class="text-center">Pump Intake</th>
                <th scope="col" colspan="16" class="text-center">Pump Distribusi</th>
            </tr>
            <tr>
                {{-- Pump Intake --}}
                <th colspan="4">Intake A</th>
                <th colspan="4">Intake B</th>
                <th colspan="4">Intake C</th>

                {{-- Pump Distribusi --}}
                <th colspan="4">Distribusi A</th>
                <th colspan="4">Distribusi B</th>
                <th colspan="4">Distribusi C</th>
                <th colspan="4">Distribusi D</th>

            </tr>

            <tr>
                {{-- Pump Intake --}}
                @foreach ([1, 2, 3] as $loop)
                    <th>A</th>
                    <th>Hz</th>
                    <th>Bar</th>
                    <th>-</th>
                @endforeach

                {{-- Pump Distribusi --}}
                @foreach ([1, 2, 3, 4] as $loop)
                    <th>A</th>
                    <th>Hz</th>
                    <th>Bar</th>
                    <th>-</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($shifts as $shift)
                <tr>
                    @foreach ($shift['pump_proccess'] as $pumpProccess)
                        <td>{{ $pumpProccess['ampere'] }}</td>
                        <td>{{ $pumpProccess['frequency'] }}</td>
                        <td>{{ $pumpProccess['pressure'] }}</td>
                        <td style="text-transform: capitalize;">{{ $pumpProccess['status'] }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="page-break"></div>

    <table>
        <thead style="background-color: rgb(193, 193, 193)">
            <tr>
                <th scope="col" colspan="8" class="text-center">Pump Dosing PAC</th>
                <th scope="col" colspan="8" class="text-center">Pump Dosing Chlorine/Kaporit</th>
            </tr>
            <tr>
                <th colspan="4" class="text-center">Pompa A</th>
                <th colspan="4" class="text-center">Pompa B</th>
                <th colspan="4" class="text-center">Pompa A</th>
                <th colspan="4" class="text-center">Pompa B</th>
            </tr>
            <tr>
                <th>Frekuensi</th><th>Dosis</th><th>Pengadukan</th><th>Level Tangki</th>
                <th>Frekuensi</th><th>Dosis</th><th>Pengadukan</th><th>Level Tangki</th>
                <th>Flow Rate</th><th>Dosis</th><th>Pengadukan</th><th>Level Tangki</th>
                <th>Flow Rate</th><th>Dosis</th><th>Pengadukan</th><th>Level Tangki</th>
            </tr>
            <tr>
                <th>Hz</th><th>ppm</th><th>Kg</th><th>cm</th>
                <th>Hz</th><th>ppm</th><th>Kg</th><th>cm</th>
                <th>l/h</th><th>ppm</th><th>Kg</th><th>cm</th>
                <th>l/h</th><th>ppm</th><th>Kg</th><th>cm</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($shifts as $shift)
                <tr>
                    @php
                        $grouped = collect($shift['pump_chemicals'])->groupBy(function($p) {
                            return $p['type'] . '|' . $p['pump_unit'];
                        });
                    @endphp
                    @foreach ([['pac','A'],['pac','B']] as [$type,$unit])
                        @php $pump = $grouped->get("$type|$unit")?->first(); @endphp
                        <td>{{ $pump['frequency'] ?? '-' }}</td>
                        <td>{{ $pump['dosage'] ?? '-' }}</td>
                        <td>{{ $pump['stirring'] ?? '-' }}</td>
                        <td>{{ $pump['tank_level'] ?? '-' }}</td>
                    @endforeach
                    @foreach ([['chlorine/kaporit','A'],['chlorine/kaporit','B']] as [$type,$unit])
                        @php $pump = $grouped->get("$type|$unit")?->first(); @endphp
                        <td>{{ $pump['flow_rate'] ?? '-' }}</td>
                        <td>{{ $pump['dosage'] ?? '-' }}</td>
                        <td>{{ $pump['stirring'] ?? '-' }}</td>
                        <td>{{ $pump['tank_level'] ?? '-' }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="page-break"></div>

    <table>
        <thead style="background-color: rgb(193, 193, 193)">
            <tr>
                <th scope="col" colspan="8" class="text-center">Pump Dosing Soda Ash</th>
                <th scope="col" colspan="8" class="text-center">Pump Dosing Polymer</th>
            </tr>
            <tr>
                <th colspan="4" class="text-center">Pompa A</th>
                <th colspan="4" class="text-center">Pompa B</th>
                <th colspan="4" class="text-center">Pompa A</th>
                <th colspan="4" class="text-center">Pompa B</th>
            </tr>
            <tr>
                <th>Flow Rate</th><th>Dosis</th><th>Pengadukan</th><th>Level Tangki</th>
                <th>Flow Rate</th><th>Dosis</th><th>Pengadukan</th><th>Level Tangki</th>
                <th>Flow Rate</th><th>Dosis</th><th>Pengadukan</th><th>Level Tangki</th>
                <th>Flow Rate</th><th>Dosis</th><th>Pengadukan</th><th>Level Tangki</th>
            </tr>
            <tr>
                <th>l/h</th><th>ppm</th><th>Kg</th><th>cm</th>
                <th>l/h</th><th>ppm</th><th>Kg</th><th>cm</th>
                <th>l/h</th><th>ppm</th><th>Kg</th><th>cm</th>
                <th>l/h</th><th>ppm</th><th>Kg</th><th>cm</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($shifts as $shift)
                <tr>
                    @php
                        $grouped = collect($shift['pump_chemicals'])->groupBy(function($p) {
                            return $p['type'] . '|' . $p['pump_unit'];
                        });
                    @endphp
                    @foreach ([['soda ash','A'],['soda ash','B'],['polymer','A'],['polymer','B']] as [$type,$unit])
                        @php $pump = $grouped->get("$type|$unit")?->first(); @endphp
                        <td>{{ $pump['flow_rate'] ?? '-' }}</td>
                        <td>{{ $pump['dosage'] ?? '-' }}</td>
                        <td>{{ $pump['stirring'] ?? '-' }}</td>
                        <td>{{ $pump['tank_level'] ?? '-' }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>


</body>

</html>

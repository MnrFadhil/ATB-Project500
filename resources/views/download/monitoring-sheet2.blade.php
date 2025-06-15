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

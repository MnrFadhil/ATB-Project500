@php
    $thBase   = "background-color: rgb(193,193,193); border: 1.5px solid #333; text-align: center; font-weight: bold; padding: 4px 6px;";
    $thNarrow = $thBase . " width: 60px;";
    $thMedium = $thBase . " width: 90px;";
    $thWide   = $thBase . " width: 110px;";
    $tdStyle  = "border: 1px solid #555; text-align: center; padding: 3px 5px;";
@endphp
<table style="border-collapse: collapse; width: 100%;">
    <thead>
        <tr>
            <th colspan="2" style="{{ $thBase }}">Flow Air Baku</th>
            <th colspan="2" style="{{ $thBase }}">Pressure Static Mixer</th>
            <th colspan="2" style="{{ $thBase }}">Flow Meter Distribusi Yos Sudarso</th>
            <th colspan="2" style="{{ $thBase }}">Flow Meter Distribusi Veteran</th>
            <th colspan="2" style="{{ $thBase }}">Reservoir</th>
            <th colspan="4" style="{{ $thBase }}">In Comer MDP Panel</th>
        </tr>
        <tr>
            <th style="{{ $thMedium }}">Flow</th>
            <th style="{{ $thMedium }}">Totalizer</th>
            <th style="{{ $thNarrow }}">Inlet</th>
            <th style="{{ $thNarrow }}">Outlet</th>
            <th style="{{ $thMedium }}">Flow</th>
            <th style="{{ $thMedium }}">Totalizer</th>
            <th style="{{ $thMedium }}">Flow</th>
            <th style="{{ $thMedium }}">Totalizer</th>
            <th style="{{ $thMedium }}">Reservoir A</th>
            <th style="{{ $thMedium }}">Reservoir B</th>
            <th rowspan="2" style="{{ $thMedium }}">KWH Total</th>
            <th rowspan="2" style="{{ $thNarrow }}">WBP</th>
            <th rowspan="2" style="{{ $thNarrow }}">LWBP</th>
            <th rowspan="2" style="{{ $thNarrow }}">KVARH</th>
        </tr>
        <tr>
            <th style="{{ $thNarrow }}">L/s</th>
            <th style="{{ $thNarrow }}">m³</th>
            <th style="{{ $thNarrow }}">Bar</th>
            <th style="{{ $thNarrow }}">Bar</th>
            <th style="{{ $thNarrow }}">L/s</th>
            <th style="{{ $thNarrow }}">m³</th>
            <th style="{{ $thNarrow }}">L/s</th>
            <th style="{{ $thNarrow }}">m³</th>
            <th style="{{ $thNarrow }}">m</th>
            <th style="{{ $thNarrow }}">m</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($shifts as $shift)
            <tr>
                @foreach ($shift['flow_meters'] as $flowMeter)
                    @if (!$flowMeter['location'])
                        <td style="{{ $tdStyle }}">{{ $flowMeter['flow'] }}</td>
                        <td style="{{ $tdStyle }}">{{ $flowMeter['totalizer'] }}</td>
                    @endif
                @endforeach
                <td style="{{ $tdStyle }}">{{ $shift['pressure_static_mixer']['inlet'] }}</td>
                <td style="{{ $tdStyle }}">{{ $shift['pressure_static_mixer']['outlet'] }}</td>
                @foreach ($shift['flow_meters'] as $flowMeter)
                    @if ($flowMeter['location'] == 'yos sudarso')
                        <td style="{{ $tdStyle }}">{{ $flowMeter['flow'] }}</td>
                        <td style="{{ $tdStyle }}">{{ $flowMeter['totalizer'] }}</td>
                    @endif
                    @if ($flowMeter['location'] == 'veteran')
                        <td style="{{ $tdStyle }}">{{ $flowMeter['flow'] }}</td>
                        <td style="{{ $tdStyle }}">{{ $flowMeter['totalizer'] }}</td>
                    @endif
                @endforeach
                <td style="{{ $tdStyle }}">{{ $shift['reservoir_levels']['level_a'] }}</td>
                <td style="{{ $tdStyle }}">{{ $shift['reservoir_levels']['level_b'] }}</td>
                <td style="{{ $tdStyle }}">{{ $shift['mdp_panels']['kwh_total'] }}</td>
                <td style="{{ $tdStyle }}">{{ $shift['mdp_panels']['wdp'] }}</td>
                <td style="{{ $tdStyle }}">{{ $shift['mdp_panels']['lwbp'] }}</td>
                <td style="{{ $tdStyle }}">{{ $shift['mdp_panels']['kvar'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

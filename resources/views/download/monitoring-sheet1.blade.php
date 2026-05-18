@php
    $thBase   = "background-color: rgb(193,193,193); border: 1.5px solid #333; text-align: center; font-weight: bold; padding: 4px 6px;";
    $thNarrow = $thBase . " width: 55px;";
    $thMedium = $thBase . " width: 80px;";
    $thWide   = $thBase . " width: 110px;";
    $tdStyle  = "border: 1px solid #555; text-align: center; padding: 3px 5px;";
@endphp
<table style="border-collapse: collapse; width: 100%;">
    <thead>
        <tr>
            <th rowspan="3" style="{{ $thMedium }}">Tanggal</th>
            <th rowspan="3" style="{{ $thNarrow }}">Shift</th>
            <th rowspan="3" style="{{ $thMedium }}">Jam</th>
            <th colspan="2" style="{{ $thBase }}">Operator</th>
            <th colspan="4" style="{{ $thBase }}">Air Baku</th>
            <th colspan="4" style="{{ $thBase }}">Sedimentation</th>
            <th colspan="6" style="{{ $thBase }}">Reservoir</th>
        </tr>
        <tr>
            <th rowspan="2" style="{{ $thWide }}">Operator 1</th>
            <th rowspan="2" style="{{ $thWide }}">Operator 2</th>
            <th style="{{ $thNarrow }}">pH</th>
            <th style="{{ $thMedium }}">Turbidity</th>
            <th style="{{ $thNarrow }}">Warna</th>
            <th style="{{ $thNarrow }}">TDS</th>
            <th style="{{ $thNarrow }}">pH</th>
            <th style="{{ $thMedium }}">Turbidity</th>
            <th style="{{ $thNarrow }}">Warna</th>
            <th style="{{ $thNarrow }}">TDS</th>
            <th style="{{ $thNarrow }}">pH</th>
            <th style="{{ $thMedium }}">Turbidity</th>
            <th style="{{ $thNarrow }}">Warna</th>
            <th style="{{ $thNarrow }}">TDS</th>
            <th style="{{ $thMedium }}">Free Chlor</th>
            <th style="{{ $thNarrow }}">ORP</th>
        </tr>
        <tr>
            <th style="{{ $thNarrow }}">-</th>
            <th style="{{ $thNarrow }}">NTU</th>
            <th style="{{ $thNarrow }}">PCU</th>
            <th style="{{ $thNarrow }}">ppm</th>
            <th style="{{ $thNarrow }}">-</th>
            <th style="{{ $thNarrow }}">NTU</th>
            <th style="{{ $thNarrow }}">PCU</th>
            <th style="{{ $thNarrow }}">ppm</th>
            <th style="{{ $thNarrow }}">-</th>
            <th style="{{ $thNarrow }}">NTU</th>
            <th style="{{ $thNarrow }}">PCU</th>
            <th style="{{ $thNarrow }}">ppm</th>
            <th style="{{ $thNarrow }}">mg/L</th>
            <th style="{{ $thNarrow }}">mV</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($shifts as $shift)
            <tr>
                <td style="{{ $tdStyle }}">{{ $shift['date'] }}</td>
                <td style="{{ $tdStyle }}; text-transform: uppercase;">{{ $shift['shift'] }}</td>
                <td style="{{ $tdStyle }}">{{ substr($shift['start_time'], 0, 5) }} - {{ substr($shift['end_time'], 0, 5) }}</td>
                <td style="{{ $tdStyle }}">{{ $shift['shift_operators'][0]['name'] }}</td>
                <td style="{{ $tdStyle }}">{{ $shift['shift_operators'][1]['name'] ?? '-' }}</td>
                @foreach ($shift['water_qualities'] as $waterQuality)
                    @if ($waterQuality['type'] == 'air baku')
                        <td style="{{ $tdStyle }}">{{ $waterQuality['ph'] }}</td>
                        <td style="{{ $tdStyle }}">{{ $waterQuality['turbidity'] }}</td>
                        <td style="{{ $tdStyle }}">{{ $waterQuality['color'] }}</td>
                        <td style="{{ $tdStyle }}">{{ $waterQuality['tds'] }}</td>
                    @endif
                    @if ($waterQuality['type'] == 'sedimentation')
                        <td style="{{ $tdStyle }}">{{ $waterQuality['ph'] }}</td>
                        <td style="{{ $tdStyle }}">{{ $waterQuality['turbidity'] }}</td>
                        <td style="{{ $tdStyle }}">{{ $waterQuality['color'] }}</td>
                        <td style="{{ $tdStyle }}">{{ $waterQuality['tds'] }}</td>
                    @endif
                    @if ($waterQuality['type'] == 'reservoir')
                        <td style="{{ $tdStyle }}">{{ $waterQuality['ph'] }}</td>
                        <td style="{{ $tdStyle }}">{{ $waterQuality['turbidity'] }}</td>
                        <td style="{{ $tdStyle }}">{{ $waterQuality['color'] }}</td>
                        <td style="{{ $tdStyle }}">{{ $waterQuality['tds'] }}</td>
                        <td style="{{ $tdStyle }}">{{ $waterQuality['free_chlor'] }}</td>
                        <td style="{{ $tdStyle }}">{{ $waterQuality['orp'] }}</td>
                    @endif
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>

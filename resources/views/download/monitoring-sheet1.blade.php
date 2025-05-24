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

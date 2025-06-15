<table>
    <thead style="background-color: rgb(193, 193, 193)">
        <tr>
            <th scope="col" colspan="4" class="text-center">Pump Dosing PAC</th>
            <th scope="col" colspan="10" class="text-center">Pump Dosing Clorine/Kaporit</th>
        </tr>
        <tr>
            {{-- Pump PAC --}}
            <th>Frekuensi</th>
            <th>Dosis</th>
            <th>Pengadukan</th>
            <th>Level Tangki</th>

            {{-- Clorine/Kaporit --}}
            <th>Flow Rate</th>
            <th>Dosis</th>
            <th>Pengadukan</th>
            <th>Level Tangki</th>
            <th>Air Drayer</th>
            <th>Bar Screen</th>
            <th>Finescreen A</th>
            <th>Finescreen B</th>
            <th>Compressor A</th>
            <th>Compressor B</th>
        </tr>

        <tr>
            {{-- Pump PAC --}}
            <th>Hz</th>
            <th>ppm</th>
            <th>Kg</th>
            <th>cm</th>

            {{-- Pump PAC --}}
            <th>l/h</th>
            <th>ppm</th>
            <th>Kg</th>
            <th>cm</th>
            <th>-</th>
            <th>-</th>
            <th>-</th>
            <th>-</th>
            <th>-</th>
            <th>-</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($shifts as $shift)
            <tr>
                @foreach ($shift['pump_chemicals'] as $pumpChemical)
                    @if ($pumpChemical['type'] == 'pac')
                        <td>{{ $pumpChemical['frequency'] }}</td>
                        <td>{{ $pumpChemical['dosage'] }}</td>
                        <td>{{ $pumpChemical['stirring'] }}</td>
                        <td>{{ $pumpChemical['tank_level'] }}</td>
                    @endif
                    @if ($pumpChemical['type'] !== 'pac')
                        <td>{{ $pumpChemical['flow_rate'] }}</td>
                        <td>{{ $pumpChemical['dosage'] }}</td>
                        <td>{{ $pumpChemical['stirring'] }}</td>
                        <td>{{ $pumpChemical['tank_level'] }}</td>
                    @endif
                @endforeach
                <td style="text-transform: capitalize;">{{ $shift['unit_operation']['air_drayer'] }}</td>
                <td style="text-transform: capitalize;">{{ $shift['unit_operation']['barscreen'] }}</td>
                <td style="text-transform: capitalize;">{{ $shift['unit_operation']['finescreen_a'] }}</td>
                <td style="text-transform: capitalize;">{{ $shift['unit_operation']['finescreen_b'] }}</td>
                <td style="text-transform: capitalize;">{{ $shift['unit_operation']['compressor_a'] }}</td>
                <td style="text-transform: capitalize;">{{ $shift['unit_operation']['compressor_b'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

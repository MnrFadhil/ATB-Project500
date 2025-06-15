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

@php
    $thBase   = "background-color: rgb(193,193,193); border: 1.5px solid #333; text-align: center; font-weight: bold; padding: 4px 6px;";
    $thNarrow = $thBase . " width: 50px;";
    $thMedium = $thBase . " width: 75px;";
    $tdStyle  = "border: 1px solid #555; text-align: center; padding: 3px 5px;";
@endphp
<table style="border-collapse: collapse; width: 100%;">
    <thead>
        <tr>
            <th colspan="12" style="{{ $thBase }}">Pump Intake</th>
            <th colspan="16" style="{{ $thBase }}">Pump Distribusi</th>
        </tr>
        <tr>
            <th colspan="4" style="{{ $thBase }}">Intake A</th>
            <th colspan="4" style="{{ $thBase }}">Intake B</th>
            <th colspan="4" style="{{ $thBase }}">Intake C</th>
            <th colspan="4" style="{{ $thBase }}">Distribusi A</th>
            <th colspan="4" style="{{ $thBase }}">Distribusi B</th>
            <th colspan="4" style="{{ $thBase }}">Distribusi C</th>
            <th colspan="4" style="{{ $thBase }}">Distribusi D</th>
        </tr>
        <tr>
            @foreach ([1,2,3] as $i)
                <th style="{{ $thNarrow }}">A</th>
                <th style="{{ $thNarrow }}">Hz</th>
                <th style="{{ $thNarrow }}">Bar</th>
                <th style="{{ $thNarrow }}">-</th>
            @endforeach
            @foreach ([1,2,3,4] as $i)
                <th style="{{ $thNarrow }}">A</th>
                <th style="{{ $thNarrow }}">Hz</th>
                <th style="{{ $thNarrow }}">Bar</th>
                <th style="{{ $thNarrow }}">-</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach ($shifts as $shift)
            <tr>
                @foreach ($shift['pump_proccess'] as $pumpProccess)
                    <td style="{{ $tdStyle }}">{{ $pumpProccess['ampere'] }}</td>
                    <td style="{{ $tdStyle }}">{{ $pumpProccess['frequency'] }}</td>
                    <td style="{{ $tdStyle }}">{{ $pumpProccess['pressure'] }}</td>
                    <td style="{{ $tdStyle }}; text-transform: capitalize;">{{ $pumpProccess['status'] }}</td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>


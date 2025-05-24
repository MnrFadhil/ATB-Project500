<table>
    <thead style="background-color: rgb(193, 193, 193)">
        <tr>
            <th scope="col" colspan="5" class="text-center">Water Treatment Plant</th>
            <th scope="col" colspan="6" class="text-center">Level Grafity Filtrasi</th>
        </tr>

        <tr>
            {{-- Water Treament Plant --}}
            <th>Flocullation A</th>
            <th>Flocullation B</th>
            <th>Clarifier A</th>
            <th>Clarifier B</th>
            <th>Filtrasi</th>

            {{-- Level Grafity Filtrasi --}}
            <th>A</th>
            <th>B</th>
            <th>C</th>
            <th>D</th>
            <th>E</th>
            <th>F</th>
        </tr>

        <tr>
            {{-- Water Treament Plant --}}
            <th>-</th>
            <th>-</th>
            <th>-</th>
            <th>-</th>
            <th>-</th>

            {{-- Level Grafity Filtrasi --}}
            <th>m</th>
            <th>m</th>
            <th>m</th>
            <th>m</th>
            <th>m</th>
            <th>m</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($shifts as $shift)
            <tr>
                <td style="text-transform: capitalize;">{{ $shift['wtps']['flokulator_a'] }}</td>
                <td style="text-transform: capitalize;">{{ $shift['wtps']['flokulator_b'] }}</td>
                <td style="text-transform: capitalize;">{{ $shift['wtps']['clarifier_a'] }}</td>
                <td style="text-transform: capitalize;">{{ $shift['wtps']['clarifier_b'] }}</td>
                <td style="text-transform: capitalize;">{{ $shift['wtps']['filtration'] }}</td>
                <td>{{ $shift['wtps']['gravity_filter_a'] }}</td>
                <td>{{ $shift['wtps']['gravity_filter_b'] }}</td>
                <td>{{ $shift['wtps']['gravity_filter_c'] }}</td>
                <td>{{ $shift['wtps']['gravity_filter_d'] }}</td>
                <td>{{ $shift['wtps']['gravity_filter_e'] }}</td>
                <td>{{ $shift['wtps']['gravity_filter_f'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

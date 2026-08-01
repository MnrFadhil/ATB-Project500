<table>
    <thead>
        <tr><th colspan="12">EVALUASI AKURASI PREDIKSI WEIGHTED MOVING AVERAGE (WMA)</th></tr>
        <tr><th colspan="12">PT Adaro Tirta Brayan</th></tr>
        <tr><th colspan="12">Periode: {{ $startDate }} s/d {{ $endDate }}</th></tr>
        <tr><th colspan="12">Bobot WMA: W1=8 (terbaru), W2=2, W3=1 (terlama)</th></tr>
        <tr><th colspan="12"></th></tr>

        <tr>
            <th rowspan="2">No</th>
            <th rowspan="2">Tanggal</th>
            <th rowspan="2">Shift</th>
            <th colspan="3">Turbidity (NTU)</th>
            <th colspan="3">pH</th>
            <th colspan="3">TDS (mg/L)</th>
        </tr>
        <tr>
            <th>Aktual</th><th>Prediksi</th><th>Error</th>
            <th>Aktual</th><th>Prediksi</th><th>Error</th>
            <th>Aktual</th><th>Prediksi</th><th>Error</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($rows as $r)
            <tr>
                <td>{{ $r['no'] }}</td>
                <td>{{ $r['date'] }}</td>
                <td>{{ $r['shift'] }}</td>
                <td>{{ $r['aktual_turb'] }}</td>
                <td>{{ $r['prediksi_turb'] }}</td>
                <td>{{ $r['error_turb'] }}</td>
                <td>{{ $r['aktual_ph'] }}</td>
                <td>{{ $r['prediksi_ph'] }}</td>
                <td>{{ $r['error_ph'] }}</td>
                <td>{{ $r['aktual_tds'] }}</td>
                <td>{{ $r['prediksi_tds'] }}</td>
                <td>{{ $r['error_tds'] }}</td>
            </tr>
        @endforeach

        <tr><td colspan="12"></td></tr>
        <tr><th colspan="12">RINGKASAN METRIK EVALUASI</th></tr>
        <tr>
            <th>Parameter</th>
            <th colspan="2">MAPE (%)</th>
            <th colspan="3">Interpretasi</th>
            <th colspan="2">Jumlah Data</th>
        </tr>
        @foreach (['turb' => 'Turbidity AB', 'ph' => 'pH AB', 'tds' => 'TDS AB'] as $k => $label)
            <tr>
                <td>{{ $label }}</td>
                <td colspan="2">{{ $metrics[$k]['mape'] }}</td>
                <td colspan="3">{{ $metrics[$k]['interpretasi'] }}</td>
                <td colspan="2">{{ $metrics[$k]['n'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
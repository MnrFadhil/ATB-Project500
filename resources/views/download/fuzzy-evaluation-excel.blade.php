<table>
    <thead>
        <tr><th colspan="17">EVALUASI AKURASI REKOMENDASI FUZZY MAMDANI</th></tr>
        <tr><th colspan="17">PT Adaro Tirta Brayan</th></tr>
        <tr><th colspan="17">Periode: {{ $startDate }} s/d {{ $endDate }}</th></tr>
        <tr><th colspan="17">Error = Rekomendasi Fuzzy (shift T) − Dosis Aktual Operator (shift T+1)</th></tr>
        <tr><th colspan="17"></th></tr>

        <tr>
            <th rowspan="2">No</th>
            <th rowspan="2">Tanggal</th>
            <th rowspan="2">Shift</th>
            <th rowspan="2">Jam Rekomen (T)</th>
            <th rowspan="2">Jam Aktual (T+1)</th>
            <th rowspan="2">Turb Sedimentation (NTU)</th>
            <th rowspan="2">Free Chlorine Reservoir (mg/L)</th>
            <th rowspan="2">pH Reservoir</th>
            <th colspan="3">PAC (ppm)</th>
            <th colspan="3">Klorin/Kaporit (ppm)</th>
            <th colspan="3">Soda Ash (ppm)</th>
        </tr>
        <tr>
            <th>Rekomendasi</th><th>Aktual</th><th>Error</th>
            <th>Rekomendasi</th><th>Aktual</th><th>Error</th>
            <th>Rekomendasi</th><th>Aktual</th><th>Error</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($rows as $r)
            <tr>
                <td>{{ $r['no'] }}</td>
                <td>{{ $r['date'] }}</td>
                <td>{{ strtoupper($r['shift']) }}</td>
                <td>{{ $r['end_time'] }}</td>
                <td>{{ $r['next_end_time'] }}</td>
                <td>{{ $r['turb_sed'] }}</td>
                <td>{{ $r['free_chlor'] }}</td>
                <td>{{ $r['ph_res'] }}</td>
                <td>{{ $r['rekomen_pac'] }}</td>
                <td>{{ $r['aktual_pac'] }}</td>
                <td>{{ $r['error_pac'] }}</td>
                <td>{{ $r['rekomen_klorin'] }}</td>
                <td>{{ $r['aktual_klorin'] }}</td>
                <td>{{ $r['error_klorin'] }}</td>
                <td>{{ $r['rekomen_sodaash'] }}</td>
                <td>{{ $r['aktual_sodaash'] }}</td>
                <td>{{ $r['error_sodaash'] }}</td>
            </tr>
        @endforeach

        <tr><td colspan="17"></td></tr>
        <tr><th colspan="17">RINGKASAN METRIK EVALUASI</th></tr>
        <tr>
            <th>Parameter</th>
            <th colspan="2">RMSE (ppm)</th>
            <th colspan="2">MAE (ppm)</th>
            <th colspan="3">MAPE (%)</th>
            <th colspan="5">Interpretasi</th>
            <th colspan="4">Jumlah Data</th>
        </tr>
        @foreach (['pac' => 'PAC', 'klorin' => 'Klorin/Kaporit', 'sodaash' => 'Soda Ash'] as $k => $label)
            <tr>
                <td>{{ $label }}</td>
                <td colspan="2">{{ $metrics[$k]['rmse'] }}</td>
                <td colspan="2">{{ $metrics[$k]['mae'] }}</td>
                <td colspan="3">{{ $metrics[$k]['mape'] }}%</td>
                <td colspan="5">{{ $metrics[$k]['interpretasi'] }}</td>
                <td colspan="4">{{ $metrics[$k]['n'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

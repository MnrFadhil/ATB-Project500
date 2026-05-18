<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Evaluasi Fuzzy Mamdani</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 9px; }
        h2, h3, h4 { margin: 4px 0; text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #333; padding: 3px 5px; }
        th { background: #f0f0f0; text-align: center; }
        td { text-align: center; }
        .summary th { background: #d9edf7; }
        .err-bad { color: #c0392b; font-weight: bold; }
        .footer { margin-top: 15px; font-size: 8px; }
    </style>
</head>
<body>
    <h2>EVALUASI AKURASI REKOMENDASI FUZZY MAMDANI</h2>
    <h3>PT Adaro Tirta Brayan</h3>
    <h4>Periode: {{ $startDate }} s/d {{ $endDate }}</h4>

    <table>
        <thead>
            <tr>
                <th rowspan="2">No</th>
                <th rowspan="2">Tanggal</th>
                <th rowspan="2">Shift</th>
                <th rowspan="2">Jam Rekomen (T)</th>
                <th rowspan="2">Jam Aktual (T+1)</th>
                <th rowspan="2">Turb Sed (NTU)</th>
                <th rowspan="2">Free Chlor (mg/L)</th>
                <th rowspan="2">pH Res</th>
                <th colspan="3">PAC (ppm)</th>
                <th colspan="3">Klorin (ppm)</th>
                <th colspan="3">Soda Ash (ppm)</th>
            </tr>
            <tr>
                <th>Rekomen</th><th>Aktual</th><th>Error</th>
                <th>Rekomen</th><th>Aktual</th><th>Error</th>
                <th>Rekomen</th><th>Aktual</th><th>Error</th>
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
                    <td class="{{ abs($r['error_pac']) > 3 ? 'err-bad' : '' }}">{{ $r['error_pac'] }}</td>
                    <td>{{ $r['rekomen_klorin'] }}</td>
                    <td>{{ $r['aktual_klorin'] }}</td>
                    <td class="{{ abs($r['error_klorin']) > 0.5 ? 'err-bad' : '' }}">{{ $r['error_klorin'] }}</td>
                    <td>{{ $r['rekomen_sodaash'] }}</td>
                    <td>{{ $r['aktual_sodaash'] }}</td>
                    <td class="{{ abs($r['error_sodaash']) > 1.5 ? 'err-bad' : '' }}">{{ $r['error_sodaash'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h3 style="margin-top: 15px;">Ringkasan Metrik Evaluasi</h3>
    <table class="summary">
        <thead>
            <tr>
                <th>Parameter</th>
                <th>RMSE (ppm)</th>
                <th>MAE (ppm)</th>
                <th>MAPE (%)</th>
                <th>Interpretasi</th>
                <th>Jumlah Data</th>
            </tr>
        </thead>
        <tbody>
            @foreach (['pac' => 'PAC', 'klorin' => 'Klorin/Kaporit', 'sodaash' => 'Soda Ash'] as $k => $label)
                <tr>
                    <td>{{ $label }}</td>
                    <td>{{ $metrics[$k]['rmse'] }}</td>
                    <td>{{ $metrics[$k]['mae'] }}</td>
                    <td>{{ $metrics[$k]['mape'] }}%</td>
                    <td>{{ $metrics[$k]['interpretasi'] }}</td>
                    <td>{{ $metrics[$k]['n'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak: {{ date('d-m-Y H:i') }} | Metode: Fuzzy Mamdani | Interpretasi MAPE: Lewis (1982) | Error = Rekomendasi Fuzzy − Dosis Aktual Operator
    </div>
</body>
</html>

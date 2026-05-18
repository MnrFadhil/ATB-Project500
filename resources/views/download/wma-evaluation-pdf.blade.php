<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Evaluasi WMA</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; }
        h2, h3, h4 { margin: 4px 0; text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #333; padding: 4px 6px; }
        th { background: #f0f0f0; text-align: center; }
        td { text-align: center; }
        .summary th { background: #d9edf7; }
        .footer { margin-top: 15px; font-size: 9px; }
        .err-bad { color: #c0392b; font-weight: bold; }
    </style>
</head>
<body>
    <h2>EVALUASI AKURASI PREDIKSI WEIGHTED MOVING AVERAGE (WMA)</h2>
    <h3>PT Adaro Tirta Brayan</h3>
    <h4>Periode: {{ $startDate }} s/d {{ $endDate }} | Bobot: W1=8, W2=2, W3=1</h4>

    <table>
        <thead>
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
                    <td class="{{ abs($r['error_turb']) > 5 ? 'err-bad' : '' }}">{{ $r['error_turb'] }}</td>
                    <td>{{ $r['aktual_ph'] }}</td>
                    <td>{{ $r['prediksi_ph'] }}</td>
                    <td>{{ $r['error_ph'] }}</td>
                    <td>{{ $r['aktual_tds'] }}</td>
                    <td>{{ $r['prediksi_tds'] }}</td>
                    <td>{{ $r['error_tds'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h3 style="margin-top: 15px;">Ringkasan Metrik Evaluasi</h3>
    <table class="summary">
        <thead>
            <tr>
                <th>Parameter</th>
                <th>RMSE</th>
                <th>MAE</th>
                <th>MAPE (%)</th>
                <th>Interpretasi MAPE</th>
                <th>Jumlah Data (n)</th>
            </tr>
        </thead>
        <tbody>
            @foreach (['turb' => 'Turbidity AB', 'ph' => 'pH AB', 'tds' => 'TDS AB'] as $k => $label)
                <tr>
                    <td>{{ $label }}</td>
                    <td>{{ $metrics[$k]['rmse'] }}</td>
                    <td>{{ $metrics[$k]['mae'] }}</td>
                    <td>{{ $metrics[$k]['mape'] }}</td>
                    <td>{{ $metrics[$k]['interpretasi'] }}</td>
                    <td>{{ $metrics[$k]['n'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p><strong>Keterangan:</strong></p>
        <p>RMSE = Root Mean Square Error | MAE = Mean Absolute Error | MAPE = Mean Absolute Percentage Error</p>
        <p>Interpretasi MAPE (Lewis, 1982): &lt;10% Sangat Akurat | 10-20% Akurat | 20-50% Cukup | &gt;50% Tidak Akurat</p>
    </div>
</body>
</html>
<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <style>
        body {
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table,
        th,
        td {
            border: 1px solid black;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
            white-space: nowrap;
        }

        .text-center {
            text-align: center;
        }

        .page-break {
            page-break-before: always;
        }

        .report-header {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0;
            margin-bottom: 0;
        }

        .report-header td {
            border: 1.5px solid black;
            padding: 4px 8px;
            white-space: nowrap;
        }

        .p2-table td, .p2-table th {
            white-space: normal;
        }

        .p3-table td, .p3-table th {
            white-space: normal;
        }

        .header-company {
            text-align: center;
            font-weight: bold;
            font-size: 13px;
            vertical-align: middle;
            width: 160px;
        }

        .header-title {
            text-align: center;
            font-weight: bold;
            font-size: 14px;
            letter-spacing: 1px;
        }

        .header-logo {
            text-align: center;
            vertical-align: middle;
            width: 120px;
        }
    </style>

    <title>Report</title>

    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/sb-admin-2.js'])
</head>

<body>
    {{-- Header Laporan --}}
    <table class="report-header">
        <tr>
            <td class="header-company" rowspan="3">PT. ADARO TIRTA<br>BRAYAN</td>
            <td class="header-title" colspan="2">LAPORAN HARIAN PRODUKSI</td>
            <td class="header-logo" rowspan="3">
                <img src="{{ public_path('assets/logoadaro-water.jpeg') }}" style="height:40px; filter: grayscale(100%);">
            </td>
        </tr>
        <tr>
            <td style="width:200px;">No. Form &nbsp;&nbsp;: KUALITAS AIR</td>
            <td>Halaman &nbsp;&nbsp;: &nbsp; 1/5</td>
        </tr>
        <tr>
            @php
                \Carbon\Carbon::setLocale('id');
                $hariTanggal = \Carbon\Carbon::parse($shifts[0]['date'] ?? '')->translatedFormat('l, d F Y');
            @endphp
            <td colspan="2">Hari/Tanggal &nbsp;: {{ $hariTanggal }}</td>
        </tr>
    </table>

    @php
        $shiftI   = array_values(array_filter($shifts, fn($s) => $s['shift'] === 'shift i'));
        $shiftII  = array_values(array_filter($shifts, fn($s) => $s['shift'] === 'shift ii'));
        $shiftIII = array_values(array_filter($shifts, fn($s) => $s['shift'] === 'shift iii'));

        $opsI   = $shiftI[0]['shift_operators']   ?? [];
        $opsII  = $shiftII[0]['shift_operators']  ?? [];
        $opsIII = $shiftIII[0]['shift_operators'] ?? [];

        $fmtJam = fn($t) => str_replace(':', '.', substr($t ?? '', 0, 5));

        $getWQ = function($rec, $type) {
            foreach (($rec['water_qualities'] ?? []) as $wq) {
                if ($wq['type'] === $type) return $wq;
            }
            return null;
        };

        // Rowspan total: 5 header + data rows + 3 spacer + 1 rata-rata
        $totalBodyRows = count($shiftI) + count($shiftII) + count($shiftIII) + 3 + 1;
        $allRowspan    = 5 + $totalBodyRows;

        $bd  = "border:0.8px solid #777;";
        $bdH = "border:1px solid #333;";

        // Header cells
        $thBase = $bdH . " font-weight:bold; text-align:center; vertical-align:middle; font-size:10px; padding:5px 2px;";
        $thGn   = $thBase . " background-color:#E2EFDA;";
        $thB1   = $thBase . " background-color:#BDD7EE;";
        $thB2   = $thBase . " background-color:#DDEBF7;";
        $thB3   = $thBase . " background-color:#9DC3E6;";
        $thSub  = $thBase . " background-color:#F2F2F2; font-size:9px;";
        $thCol  = $thBase . " background-color:#F2F2F2; font-size:9px;";

        // Vertikal header (SHIFT SCHEDULE, TIME WIB)
        $thVt = $bdH . " background-color:#F2F2F2; text-align:center; vertical-align:middle; overflow:hidden; padding:0;";

        // Data cells
        $td  = $bd  . " text-align:center; padding:9px 2px; font-size:10.5px;";
        $tdT = $bdH . " text-align:center; padding:9px 2px; font-size:10.5px; font-weight:bold;";

        // Shift label cells
        $slBase = $bdH . " text-align:center; vertical-align:middle; overflow:hidden; color:white; font-weight:bold; padding:0;";
        $slI    = $slBase . " background-color:#70AD47;";
        $slII   = $slBase . " background-color:#4472C4;";
        $slIII  = $slBase . " background-color:#ED7D31;";

        // Rata-rata
        $rdY = $bd . " background-color:#FFF2CC; padding:9px 2px; font-size:10.5px;";
        $rdG = $bd . " background-color:#D9D9D9; padding:9px 2px; font-size:10.5px;";
        $rdB = $bd . " background-color:#DAE8FC; padding:9px 2px; font-size:10.5px;";

        $spc = "border:0; padding:0; height:6px;";
    @endphp

    {{--
        Struktur kolom (17 total):
        Col 1 : SHIFT SCHEDULE (rowspan=semua, lebar 14px)
        Col 2 : Header=TIME WIB colspan=2 rowspan=5 | Body=label shift (rowspan=4 per shift)
        Col 3 : Body=time values
        Col 4-17 : 14 kolom data
    --}}
    <table style="border-collapse:collapse; width:100%; margin-top:4px; table-layout:fixed; font-size:7px;">
        <colgroup>
            <col style="width:30px;">{{-- SHIFT SCHEDULE header / shift label body --}}
            <col style="width:32px;">{{-- TIME WIB header / time values body --}}
            <col style="width:auto;">{{-- pH ab --}}
            <col style="width:auto;">{{-- Turb ab --}}
            <col style="width:auto;">{{-- Warna ab --}}
            <col style="width:auto;">{{-- TDS ab --}}
            <col style="width:auto;">{{-- pH sd --}}
            <col style="width:auto;">{{-- Turb sd --}}
            <col style="width:auto;">{{-- Warna sd --}}
            <col style="width:auto;">{{-- TDS sd --}}
            <col style="width:auto;">{{-- pH rs --}}
            <col style="width:auto;">{{-- Turb rs --}}
            <col style="width:auto;">{{-- Warna rs --}}
            <col style="width:auto;">{{-- TDS rs --}}
            <col style="width:auto;">{{-- Free Chlor --}}
            <col style="width:auto;">{{-- ORP --}}
        </colgroup>
        <tbody>
            {{-- ===== HEADER ROWS (5 baris) ===== --}}
            <tr>
                {{-- SHIFT SCHEDULE: header saja (rowspan=5), body=shift labels --}}
                <td rowspan="5" style="{{ $thVt }} width:30px;">
                    <div style="transform:rotate(-90deg); white-space:nowrap; font-size:6px; font-weight:bold; letter-spacing:0.3px;">SHIFT SCHEDULE</div>
                </td>
                {{-- TIME (WIB): header saja (rowspan=5), body=time values --}}
                <td rowspan="5" style="{{ $thVt }} width:32px;">
                    <div style="transform:rotate(-90deg); white-space:nowrap; font-size:6px; font-weight:bold; letter-spacing:0.3px;">TIME (WIB)</div>
                </td>
                <td colspan="14" style="{{ $thGn }}">PENGUJIAN KUALITAS AIR CHEMICAL LABORATORIUM</td>
            </tr>
            <tr>
                <td colspan="4" style="{{ $thB1 }}">AIR BAKU (RAW WATER)</td>
                <td colspan="4" style="{{ $thB2 }}">SEDIMENTATION</td>
                <td colspan="6" style="{{ $thB3 }}">AIR CURAH &amp; RESERVOIR (TREATED WATER)</td>
            </tr>
            <tr>
                <td colspan="4" style="{{ $thSub }}">Quality/Characteristic</td>
                <td colspan="4" style="{{ $thSub }}">Clarified Quality</td>
                <td colspan="6" style="{{ $thSub }}">Treated Water Quality</td>
            </tr>
            <tr>
                <td style="{{ $thCol }}">pH</td><td style="{{ $thCol }}">Turbidity</td><td style="{{ $thCol }}">Warna</td><td style="{{ $thCol }}">TDS</td>
                <td style="{{ $thCol }}">pH</td><td style="{{ $thCol }}">Turbidity</td><td style="{{ $thCol }}">Warna</td><td style="{{ $thCol }}">TDS</td>
                <td style="{{ $thCol }}">pH</td><td style="{{ $thCol }}">Turbidity</td><td style="{{ $thCol }}">Warna</td><td style="{{ $thCol }}">TDS</td>
                <td style="{{ $thCol }}">Free Chlor</td><td style="{{ $thCol }}">ORP</td>
            </tr>
            <tr>
                <td style="{{ $thCol }}">-</td><td style="{{ $thCol }}">(NTU)</td><td style="{{ $thCol }}">(PCU)</td><td style="{{ $thCol }}">(ppm)</td>
                <td style="{{ $thCol }}">-</td><td style="{{ $thCol }}">(NTU)</td><td style="{{ $thCol }}">(TCU)</td><td style="{{ $thCol }}">(ppm)</td>
                <td style="{{ $thCol }}">-</td><td style="{{ $thCol }}">(NTU)</td><td style="{{ $thCol }}">(TCU)</td><td style="{{ $thCol }}">(ppm)</td>
                <td style="{{ $thCol }}">(mg/L)</td><td style="{{ $thCol }}">(mV)</td>
            </tr>

            {{-- ===== SHIFT I (PAGI) ===== --}}
            @foreach ($shiftI as $idx => $rec)
                @php $ab = $getWQ($rec,'air baku'); $sd = $getWQ($rec,'sedimentation'); $rs = $getWQ($rec,'reservoir'); @endphp
                <tr>
                    @if ($idx === 0)
                        <td rowspan="{{ count($shiftI) }}" style="{{ $slI }}">
                            <div style="transform:rotate(-90deg); white-space:nowrap; font-size:6px; font-weight:bold;">SHIFT - 1 (Pagi)</div>
                        </td>
                    @endif
                    <td style="{{ $tdT }}">{{ $fmtJam($rec['end_time']) }}</td>
                    <td style="{{ $td }}">{{ $ab['ph'] ?? '' }}</td><td style="{{ $td }}">{{ $ab['turbidity'] ?? '' }}</td><td style="{{ $td }}">{{ $ab['color'] ?? '' }}</td><td style="{{ $td }}">{{ $ab['tds'] ?? '' }}</td>
                    <td style="{{ $td }}">{{ $sd['ph'] ?? '' }}</td><td style="{{ $td }}">{{ $sd['turbidity'] ?? '' }}</td><td style="{{ $td }}">{{ $sd['color'] ?? '' }}</td><td style="{{ $td }}">{{ $sd['tds'] ?? '' }}</td>
                    <td style="{{ $td }}">{{ $rs['ph'] ?? '' }}</td><td style="{{ $td }}">{{ $rs['turbidity'] ?? '' }}</td><td style="{{ $td }}">{{ $rs['color'] ?? '' }}</td><td style="{{ $td }}">{{ $rs['tds'] ?? '' }}</td><td style="{{ $td }}">{{ $rs['free_chlor'] ?? '' }}</td><td style="{{ $td }}">{{ $rs['orp'] ?? '' }}</td>
                </tr>
            @endforeach
            <tr><td colspan="16" style="{{ $spc }}"></td></tr>

            {{-- ===== SHIFT II (SIANG) ===== --}}
            @foreach ($shiftII as $idx => $rec)
                @php $ab = $getWQ($rec,'air baku'); $sd = $getWQ($rec,'sedimentation'); $rs = $getWQ($rec,'reservoir'); @endphp
                <tr>
                    @if ($idx === 0)
                        <td rowspan="{{ count($shiftII) }}" style="{{ $slII }}">
                            <div style="transform:rotate(-90deg); white-space:nowrap; font-size:6px;">SHIFT - 2 (SIANG)</div>
                        </td>
                    @endif
                    <td style="{{ $tdT }}">{{ $fmtJam($rec['end_time']) }}</td>
                    <td style="{{ $td }}">{{ $ab['ph'] ?? '' }}</td><td style="{{ $td }}">{{ $ab['turbidity'] ?? '' }}</td><td style="{{ $td }}">{{ $ab['color'] ?? '' }}</td><td style="{{ $td }}">{{ $ab['tds'] ?? '' }}</td>
                    <td style="{{ $td }}">{{ $sd['ph'] ?? '' }}</td><td style="{{ $td }}">{{ $sd['turbidity'] ?? '' }}</td><td style="{{ $td }}">{{ $sd['color'] ?? '' }}</td><td style="{{ $td }}">{{ $sd['tds'] ?? '' }}</td>
                    <td style="{{ $td }}">{{ $rs['ph'] ?? '' }}</td><td style="{{ $td }}">{{ $rs['turbidity'] ?? '' }}</td><td style="{{ $td }}">{{ $rs['color'] ?? '' }}</td><td style="{{ $td }}">{{ $rs['tds'] ?? '' }}</td><td style="{{ $td }}">{{ $rs['free_chlor'] ?? '' }}</td><td style="{{ $td }}">{{ $rs['orp'] ?? '' }}</td>
                </tr>
            @endforeach
            <tr><td colspan="16" style="{{ $spc }}"></td></tr>

            {{-- ===== SHIFT III (MALAM) ===== --}}
            @foreach ($shiftIII as $idx => $rec)
                @php $ab = $getWQ($rec,'air baku'); $sd = $getWQ($rec,'sedimentation'); $rs = $getWQ($rec,'reservoir'); @endphp
                <tr>
                    @if ($idx === 0)
                        <td rowspan="{{ count($shiftIII) }}" style="{{ $slIII }}">
                            <div style="transform:rotate(-90deg); white-space:nowrap; font-size:6px;">SHIFT - 3 (MALAM)</div>
                        </td>
                    @endif
                    <td style="{{ $tdT }}">{{ $fmtJam($rec['end_time']) }}</td>
                    <td style="{{ $td }}">{{ $ab['ph'] ?? '' }}</td><td style="{{ $td }}">{{ $ab['turbidity'] ?? '' }}</td><td style="{{ $td }}">{{ $ab['color'] ?? '' }}</td><td style="{{ $td }}">{{ $ab['tds'] ?? '' }}</td>
                    <td style="{{ $td }}">{{ $sd['ph'] ?? '' }}</td><td style="{{ $td }}">{{ $sd['turbidity'] ?? '' }}</td><td style="{{ $td }}">{{ $sd['color'] ?? '' }}</td><td style="{{ $td }}">{{ $sd['tds'] ?? '' }}</td>
                    <td style="{{ $td }}">{{ $rs['ph'] ?? '' }}</td><td style="{{ $td }}">{{ $rs['turbidity'] ?? '' }}</td><td style="{{ $td }}">{{ $rs['color'] ?? '' }}</td><td style="{{ $td }}">{{ $rs['tds'] ?? '' }}</td><td style="{{ $td }}">{{ $rs['free_chlor'] ?? '' }}</td><td style="{{ $td }}">{{ $rs['orp'] ?? '' }}</td>
                </tr>
            @endforeach
            <tr><td colspan="16" style="{{ $spc }}"></td></tr>

            {{-- RATA-RATA --}}
            <tr>
                <td colspan="2" style="{{ $thBase }} background-color:#FFC000;">RATA -RATA</td>
                <td style="{{ $rdY }}"></td><td style="{{ $rdY }}"></td><td style="{{ $rdY }}"></td><td style="{{ $rdY }}"></td>
                <td style="{{ $rdG }}"></td><td style="{{ $rdG }}"></td><td style="{{ $rdG }}"></td><td style="{{ $rdG }}"></td>
                <td style="{{ $rdB }}"></td><td style="{{ $rdB }}"></td><td style="{{ $rdB }}"></td><td style="{{ $rdB }}"></td><td style="{{ $rdB }}"></td><td style="{{ $rdB }}"></td>
            </tr>
        </tbody>
    </table>

    {{-- CATATAN --}}
    <table style="border-collapse:collapse; width:100%; margin-top:10px; font-size:10px;">
        <tr>
            <td rowspan="6" style="{{ $bdH }} text-align:center; font-weight:bold; font-size:13px; width:70px; vertical-align:middle; padding:6px;">CATATAN</td>
            <td rowspan="2" style="{{ $bdH }} text-align:center; vertical-align:middle; background-color:#70AD47; color:white; overflow:hidden; width:18px; padding:0;">
                <div style="transform:rotate(-90deg); white-space:nowrap; font-size:7px; font-weight:bold;">SHIFT 1</div>
            </td>
            <td style="{{ $bdH }} padding:11px 8px; border-bottom:1px dotted #aaa;">&nbsp;</td>
        </tr>
        <tr><td style="{{ $bdH }} padding:11px 8px; border-top:1px dotted #aaa;">&nbsp;</td></tr>
        <tr>
            <td rowspan="2" style="{{ $bdH }} text-align:center; vertical-align:middle; background-color:#4472C4; color:white; overflow:hidden; width:18px; padding:0;">
                <div style="transform:rotate(-90deg); white-space:nowrap; font-size:7px; font-weight:bold;">SHIFT 2</div>
            </td>
            <td style="{{ $bdH }} padding:11px 8px; border-bottom:1px dotted #aaa;">&nbsp;</td>
        </tr>
        <tr><td style="{{ $bdH }} padding:11px 8px; border-top:1px dotted #aaa;">&nbsp;</td></tr>
        <tr>
            <td rowspan="2" style="{{ $bdH }} text-align:center; vertical-align:middle; background-color:#ED7D31; color:white; overflow:hidden; width:18px; padding:0;">
                <div style="transform:rotate(-90deg); white-space:nowrap; font-size:7px; font-weight:bold;">SHIFT 3</div>
            </td>
            <td style="{{ $bdH }} padding:11px 8px; border-bottom:1px dotted #aaa;">&nbsp;</td>
        </tr>
        <tr><td style="{{ $bdH }} padding:11px 8px; border-top:1px dotted #aaa;">&nbsp;</td></tr>
    </table>

    {{-- OPERATOR --}}
    <table style="border-collapse:collapse; width:100%; margin-top:10px; font-size:11px;">
        <tr>
            <td style="{{ $bdH }} font-weight:bold; background-color:#70AD47; color:white; padding:9px 10px; width:33%;">SHIFT -1 PAGI ( 7:00 - 15:00 )</td>
            <td style="{{ $bdH }} font-weight:bold; background-color:#4472C4; color:white; padding:9px 10px; width:33%;">SHIFT -2 SIANG ( 15:00 - 23:00 )</td>
            <td style="{{ $bdH }} font-weight:bold; background-color:#ED7D31; color:white; padding:9px 10px; width:34%;">SHIFT -3 MALAM ( 23:00 - 7:00 )</td>
        </tr>
        @for ($i = 0; $i < 3; $i++)
        <tr>
            <td style="{{ $bdH }} padding:10px 10px;">{{ ($i+1) }}. Operator : {{ $opsI[$i]['name'] ?? '' }}</td>
            <td style="{{ $bdH }} padding:10px 10px;">{{ ($i+1) }}. Operator : {{ $opsII[$i]['name'] ?? '' }}</td>
            <td style="{{ $bdH }} padding:10px 10px;">{{ ($i+1) }}. Operator : {{ $opsIII[$i]['name'] ?? '' }}</td>
        </tr>
        @endfor
    </table>

    @php
        // Styles dipakai halaman 3-5 (lama, format compact)
        $thP = "background-color:rgb(193,193,193); border:1px solid #333; font-weight:bold; text-align:center; padding:2px 3px; font-size:7px; white-space:nowrap;";
        $tdP = "border:1px solid #555; text-align:center; padding:2px 3px; font-size:7.5px; white-space:nowrap;";

        $fmtJam2 = fn($t) => str_replace(':', '.', substr($t ?? '', 0, 5));
        $pgHdr = function($no, $formName = 'KUALITAS AIR') use ($hariTanggal) {
            return '
            <table style="width:100%; border-collapse:collapse; margin-bottom:4px;">
                <tr>
                    <td style="border:1.5px solid #333; text-align:center; font-weight:bold; font-size:11px; width:140px; padding:3px;" rowspan="3">PT. ADARO TIRTA<br>BRAYAN</td>
                    <td style="border:1.5px solid #333; text-align:center; font-weight:bold; font-size:13px; letter-spacing:1px;" colspan="2">LAPORAN HARIAN PRODUKSI</td>
                    <td style="border:1.5px solid #333; text-align:center; width:100px;" rowspan="3">
                        <img src="'.public_path('assets/logoadaro-water.jpeg').'" style="height:35px; filter:grayscale(100%);">
                    </td>
                </tr>
                <tr>
                    <td style="border:1.5px solid #333; padding:2px 6px; font-size:9px;">No. Form &nbsp;: '.$formName.'</td>
                    <td style="border:1.5px solid #333; padding:2px 6px; font-size:9px;">Halaman &nbsp;: '.$no.'/5</td>
                </tr>
                <tr>
                    <td colspan="2" style="border:1.5px solid #333; padding:2px 6px; font-size:9px;">Hari/Tanggal &nbsp;: '.$hariTanggal.'</td>
                </tr>
            </table>';
        };
    @endphp

    {{-- ======================================================== --}}
    {{-- HALAMAN 2: Flow Meter + Pressure + Reservoir + MDP Panel --}}
    {{-- ======================================================== --}}
    <div class="page-break"></div>
    {!! $pgHdr(2, 'KUANTITAS AIR') !!}

    @php
        // Page 2 styles
        $p2bdH    = "border:1px solid #333;";
        $p2thVt   = $p2bdH . " background-color:#F2F2F2; text-align:center; vertical-align:middle; overflow:hidden; padding:0; white-space:normal;";
        $p2thBase = $p2bdH . " font-weight:bold; text-align:center; vertical-align:middle; font-size:7px; padding:6px 1px; white-space:normal;";
        $p2thGray  = $p2thBase . " background-color:#F2F2F2;";
        $p2thBlue  = $p2thBase . " background-color:#BDD7EE;";
        $p2thGreen = $p2thBase . " background-color:#E2EFDA;";
        $p2thOr    = $p2thBase . " background-color:#FCE4D6;";
        $p2thPurp  = $p2thBase . " background-color:#E2D0F0;";
        $p2thTeal  = $p2thBase . " background-color:#DDEBF7;";
        $p2td      = $p2bdH . " text-align:center; padding:5px 1px; font-size:9px; white-space:normal;";
        $p2tdT     = $p2bdH . " text-align:center; padding:5px 1px; font-size:9px; font-weight:bold; white-space:normal;";
        $p2spc     = "border:0; padding:0; height:4px;";
        $p2rdTotal = $p2bdH . " background-color:#D9D9D9; padding:5px 1px; font-size:9px; white-space:normal;";

        $p2shiftI   = array_values(array_filter($shifts, fn($s) => $s['shift'] === 'shift i'));
        $p2shiftII  = array_values(array_filter($shifts, fn($s) => $s['shift'] === 'shift ii'));
        $p2shiftIII = array_values(array_filter($shifts, fn($s) => $s['shift'] === 'shift iii'));

        $p2slBase = $p2bdH . " text-align:center; vertical-align:middle; overflow:hidden; color:white; font-weight:bold; padding:0; white-space:normal;";
        $p2slI    = $p2slBase . " background-color:#70AD47;";
        $p2slII   = $p2slBase . " background-color:#4472C4;";
        $p2slIII  = $p2slBase . " background-color:#ED7D31;";
    @endphp

    {{--
        17 columns:
        col1=SHIFT SCHEDULE header (body=shift labels), col2=TIME WIB, col3-17=data
        Total: 30+45+28+44+22+22+22+28+44+28+44+22+22+32+20+20+20 = 493px
    --}}
    <table class="p2-table" style="border-collapse:collapse; width:100%; table-layout:fixed;">
        <colgroup>
            <col style="width:30px;">{{-- SHIFT SCHEDULE / shift label --}}
            <col style="width:45px;">{{-- TIME WIB --}}
            <col style="width:28px;">{{-- FLOW baku --}}
            <col style="width:44px;">{{-- TOTALIZER baku --}}
            <col style="width:22px;">{{-- Level Muka Air --}}
            <col style="width:22px;">{{-- Inlet --}}
            <col style="width:22px;">{{-- Outlet --}}
            <col style="width:28px;">{{-- FLOW Yos --}}
            <col style="width:44px;">{{-- TOTALIZER Yos --}}
            <col style="width:28px;">{{-- FLOW Veteran --}}
            <col style="width:44px;">{{-- TOTALIZER Veteran --}}
            <col style="width:22px;">{{-- Res A --}}
            <col style="width:22px;">{{-- Res B --}}
            <col style="width:32px;">{{-- KWH Total --}}
            <col style="width:20px;">{{-- WBP --}}
            <col style="width:20px;">{{-- LWBP --}}
            <col style="width:20px;">{{-- KVARH --}}
        </colgroup>
        <thead>
            {{-- Anchor row: 17 cells to force fixed widths --}}
            <tr style="height:1px; font-size:0; line-height:0;">
                <td style="width:30px; padding:0; border:0;"></td>
                <td style="width:45px; padding:0; border:0;"></td>
                <td style="width:28px; padding:0; border:0;"></td>
                <td style="width:44px; padding:0; border:0;"></td>
                <td style="width:22px; padding:0; border:0;"></td>
                <td style="width:22px; padding:0; border:0;"></td>
                <td style="width:22px; padding:0; border:0;"></td>
                <td style="width:28px; padding:0; border:0;"></td>
                <td style="width:44px; padding:0; border:0;"></td>
                <td style="width:28px; padding:0; border:0;"></td>
                <td style="width:44px; padding:0; border:0;"></td>
                <td style="width:22px; padding:0; border:0;"></td>
                <td style="width:22px; padding:0; border:0;"></td>
                <td style="width:32px; padding:0; border:0;"></td>
                <td style="width:20px; padding:0; border:0;"></td>
                <td style="width:20px; padding:0; border:0;"></td>
                <td style="width:20px; padding:0; border:0;"></td>
            </tr>
            {{-- Row 1: group headers --}}
            <tr>
                <td rowspan="3" style="{{ $p2thVt }} width:20px;">
                    <div style="transform:rotate(-90deg); font-size:6.5px; font-weight:bold; white-space:nowrap;">SHIFT SCHEDULE</div>
                </td>
                <td rowspan="3" style="{{ $p2thVt }} width:45px;">
                    <div style="transform:rotate(-90deg); font-size:6.5px; font-weight:bold;">TIME (WIB)</div>
                </td>
                <td colspan="3" style="{{ $p2thBlue }} font-size:6.5px; padding:6px 1px;">WATER METER AIR BAKU</td>
                <td colspan="2" style="{{ $p2thGray }} font-size:6px; padding:6px 1px;">Pressure Static Mixer</td>
                <td colspan="2" style="{{ $p2thGreen }} font-size:6.5px; padding:6px 1px;">YOS SUDARSO</td>
                <td colspan="2" style="{{ $p2thOr }} font-size:6.5px; padding:6px 1px;">VETERAN 1</td>
                <td colspan="2" style="{{ $p2thPurp }} font-size:6.5px; padding:6px 1px;">RESERVOIR</td>
                <td colspan="4" style="{{ $p2thTeal }} font-size:6.5px; padding:6px 1px;">IN COMER MDP PANEL</td>
            </tr>
            {{-- Row 2: sub-headers --}}
            <tr>
                <td style="{{ $p2thBase }} background-color:#DDEBF7; font-size:6.5px;">FLOW</td>
                <td style="{{ $p2thBase }} background-color:#DDEBF7; font-size:6.5px;">TOTALIZER</td>
                <td style="{{ $p2thBase }} background-color:#DDEBF7; font-size:6px;">Level Muka Air (m)</td>
                <td style="{{ $p2thGray }} font-size:6.5px;">Inlet</td>
                <td style="{{ $p2thGray }} font-size:6.5px;">Outlet</td>
                <td style="{{ $p2thBase }} background-color:#E2EFDA; font-size:6.5px;">FLOW</td>
                <td style="{{ $p2thBase }} background-color:#E2EFDA; font-size:6.5px;">TOTALIZER   </td>
                <td style="{{ $p2thBase }} background-color:#FCE4D6; font-size:6.5px;">FLOW</td>
                <td style="{{ $p2thBase }} background-color:#FCE4D6; font-size:6.5px;">TOTALIZER</td>
                <td style="{{ $p2thBase }} background-color:#E2D0F0; font-size:6px;">Res A (m)</td>
                <td style="{{ $p2thBase }} background-color:#E2D0F0; font-size:6px;">Res B (m)</td>
                <td style="{{ $p2thBase }} background-color:#DDEBF7; font-size:6px;">KWH TOTAL</td>
                <td style="{{ $p2thBase }} background-color:#DDEBF7; font-size:6.5px;">WBP</td>
                <td style="{{ $p2thBase }} background-color:#DDEBF7; font-size:6.5px;">LWBP</td>
                <td style="{{ $p2thBase }} background-color:#DDEBF7; font-size:6.5px;">KVARH</td>
            </tr>
            {{-- Row 3: units --}}
            <tr>
                <td style="{{ $p2thGray }} font-size:7px;">(lps)</td>
                <td style="{{ $p2thGray }} font-size:7px;">(m3)</td>
                <td style="{{ $p2thGray }} font-size:7px;">(m)</td>
                <td style="{{ $p2thGray }} font-size:7px;">(bar)</td>
                <td style="{{ $p2thGray }} font-size:7px;">(bar)</td>
                <td style="{{ $p2thGray }} font-size:7px;">(lps)</td>
                <td style="{{ $p2thGray }} font-size:7px;">(m3)</td>
                <td style="{{ $p2thGray }} font-size:7px;">(lps)</td>
                <td style="{{ $p2thGray }} font-size:7px;">(m3)</td>
                <td style="{{ $p2thGray }} font-size:7px;">(m)</td>
                <td style="{{ $p2thGray }} font-size:7px;">(m)</td>
                <td style="{{ $p2thGray }} font-size:7px;">-</td>
                <td style="{{ $p2thGray }} font-size:7px;">-</td>
                <td style="{{ $p2thGray }} font-size:7px;">-</td>
                <td style="{{ $p2thGray }} font-size:7px;">-</td>
            </tr>
        </thead>
        <tbody>
            {{-- SHIFT I (PAGI) --}}
            @foreach ($p2shiftI as $idx => $rec)
                @php
                    $fmBaku = collect($rec['flow_meters'] ?? [])->first(fn($f) => !$f['location']);
                    $fmYos  = collect($rec['flow_meters'] ?? [])->first(fn($f) => $f['location'] === 'yos sudarso');
                    $fmVet  = collect($rec['flow_meters'] ?? [])->first(fn($f) => $f['location'] === 'veteran');
                @endphp
                <tr>
                    @if ($idx === 0)
                        <td rowspan="{{ count($p2shiftI) }}" style="{{ $p2slI }} width:30px;">
                            <div style="transform:rotate(-90deg); font-size:7px; white-space:nowrap;">SHIFT - 1 (Pagi)</div>
                        </td>
                    @endif
                    <td style="{{ $p2tdT }}">{{ $fmtJam2($rec['end_time']) }}</td>
                    <td style="{{ $p2td }}">{{ $fmBaku['flow'] ?? '' }}</td>
                    <td style="{{ $p2td }}">{{ $fmBaku['totalizer'] ?? '' }}</td>
                    <td style="{{ $p2td }}">{{ $rec['collection_tank'] ?? '' }}</td>
                    <td style="{{ $p2td }}">{{ $rec['pressure_static_mixer']['inlet'] ?? '' }}</td>
                    <td style="{{ $p2td }}">{{ $rec['pressure_static_mixer']['outlet'] ?? '' }}</td>
                    <td style="{{ $p2td }}">{{ $fmYos['flow'] ?? '' }}</td>
                    <td style="{{ $p2td }}">{{ $fmYos['totalizer'] ?? '' }}</td>
                    <td style="{{ $p2td }}">{{ $fmVet['flow'] ?? '' }}</td>
                    <td style="{{ $p2td }}">{{ $fmVet['totalizer'] ?? '' }}</td>
                    <td style="{{ $p2td }}">{{ $rec['reservoir_levels']['level_a'] ?? '' }}</td>
                    <td style="{{ $p2td }}">{{ $rec['reservoir_levels']['level_b'] ?? '' }}</td>
                    <td style="{{ $p2td }}">{{ $rec['mdp_panels']['kwh_total'] ?? '' }}</td>
                    <td style="{{ $p2td }}">{{ $rec['mdp_panels']['wdp'] ?? '' }}</td>
                    <td style="{{ $p2td }}">{{ $rec['mdp_panels']['lwbp'] ?? '' }}</td>
                    <td style="{{ $p2td }}">{{ $rec['mdp_panels']['kvar'] ?? '' }}</td>
                </tr>
            @endforeach
            <tr><td colspan="17" style="{{ $p2spc }}"></td></tr>

            {{-- SHIFT II (SIANG) --}}
            @foreach ($p2shiftII as $idx => $rec)
                @php
                    $fmBaku = collect($rec['flow_meters'] ?? [])->first(fn($f) => !$f['location']);
                    $fmYos  = collect($rec['flow_meters'] ?? [])->first(fn($f) => $f['location'] === 'yos sudarso');
                    $fmVet  = collect($rec['flow_meters'] ?? [])->first(fn($f) => $f['location'] === 'veteran');
                @endphp
                <tr>
                    @if ($idx === 0)
                        <td rowspan="{{ count($p2shiftII) }}" style="{{ $p2slII }} width:30px;">
                            <div style="transform:rotate(-90deg); font-size:7px; white-space:nowrap;">SHIFT - 2 (Siang)</div>
                        </td>
                    @endif
                    <td style="{{ $p2tdT }}">{{ $fmtJam2($rec['end_time']) }}</td>
                    <td style="{{ $p2td }}">{{ $fmBaku['flow'] ?? '' }}</td>
                    <td style="{{ $p2td }}">{{ $fmBaku['totalizer'] ?? '' }}</td>
                    <td style="{{ $p2td }}">{{ $rec['collection_tank'] ?? '' }}</td>
                    <td style="{{ $p2td }}">{{ $rec['pressure_static_mixer']['inlet'] ?? '' }}</td>
                    <td style="{{ $p2td }}">{{ $rec['pressure_static_mixer']['outlet'] ?? '' }}</td>
                    <td style="{{ $p2td }}">{{ $fmYos['flow'] ?? '' }}</td>
                    <td style="{{ $p2td }}">{{ $fmYos['totalizer'] ?? '' }}</td>
                    <td style="{{ $p2td }}">{{ $fmVet['flow'] ?? '' }}</td>
                    <td style="{{ $p2td }}">{{ $fmVet['totalizer'] ?? '' }}</td>
                    <td style="{{ $p2td }}">{{ $rec['reservoir_levels']['level_a'] ?? '' }}</td>
                    <td style="{{ $p2td }}">{{ $rec['reservoir_levels']['level_b'] ?? '' }}</td>
                    <td style="{{ $p2td }}">{{ $rec['mdp_panels']['kwh_total'] ?? '' }}</td>
                    <td style="{{ $p2td }}">{{ $rec['mdp_panels']['wdp'] ?? '' }}</td>
                    <td style="{{ $p2td }}">{{ $rec['mdp_panels']['lwbp'] ?? '' }}</td>
                    <td style="{{ $p2td }}">{{ $rec['mdp_panels']['kvar'] ?? '' }}</td>
                </tr>
            @endforeach
            <tr><td colspan="17" style="{{ $p2spc }}"></td></tr>

            {{-- SHIFT III (MALAM) --}}
            @foreach ($p2shiftIII as $idx => $rec)
                @php
                    $fmBaku = collect($rec['flow_meters'] ?? [])->first(fn($f) => !$f['location']);
                    $fmYos  = collect($rec['flow_meters'] ?? [])->first(fn($f) => $f['location'] === 'yos sudarso');
                    $fmVet  = collect($rec['flow_meters'] ?? [])->first(fn($f) => $f['location'] === 'veteran');
                @endphp
                <tr>
                    @if ($idx === 0)
                        <td rowspan="{{ count($p2shiftIII) }}" style="{{ $p2slIII }} width:30px;">
                            <div style="transform:rotate(-90deg); font-size:7px; white-space:nowrap;">SHIFT - 3 (Malam)</div>
                        </td>
                    @endif
                    <td style="{{ $p2tdT }}">{{ $fmtJam2($rec['end_time']) }}</td>
                    <td style="{{ $p2td }}">{{ $fmBaku['flow'] ?? '' }}</td>
                    <td style="{{ $p2td }}">{{ $fmBaku['totalizer'] ?? '' }}</td>
                    <td style="{{ $p2td }}">{{ $rec['collection_tank'] ?? '' }}</td>
                    <td style="{{ $p2td }}">{{ $rec['pressure_static_mixer']['inlet'] ?? '' }}</td>
                    <td style="{{ $p2td }}">{{ $rec['pressure_static_mixer']['outlet'] ?? '' }}</td>
                    <td style="{{ $p2td }}">{{ $fmYos['flow'] ?? '' }}</td>
                    <td style="{{ $p2td }}">{{ $fmYos['totalizer'] ?? '' }}</td>
                    <td style="{{ $p2td }}">{{ $fmVet['flow'] ?? '' }}</td>
                    <td style="{{ $p2td }}">{{ $fmVet['totalizer'] ?? '' }}</td>
                    <td style="{{ $p2td }}">{{ $rec['reservoir_levels']['level_a'] ?? '' }}</td>
                    <td style="{{ $p2td }}">{{ $rec['reservoir_levels']['level_b'] ?? '' }}</td>
                    <td style="{{ $p2td }}">{{ $rec['mdp_panels']['kwh_total'] ?? '' }}</td>
                    <td style="{{ $p2td }}">{{ $rec['mdp_panels']['wdp'] ?? '' }}</td>
                    <td style="{{ $p2td }}">{{ $rec['mdp_panels']['lwbp'] ?? '' }}</td>
                    <td style="{{ $p2td }}">{{ $rec['mdp_panels']['kvar'] ?? '' }}</td>
                </tr>
            @endforeach
            <tr><td colspan="17" style="{{ $p2spc }}"></td></tr>

            {{-- TOTAL --}}
            <tr>
                <td colspan="2" style="{{ $p2rdTotal }} font-weight:bold; text-align:center; background-color:#FFF2CC;">TOTAL</td>
                <td style="{{ $p2rdTotal }}"></td><td style="{{ $p2rdTotal }}"></td>
                <td style="{{ $p2rdTotal }}"></td><td style="{{ $p2rdTotal }}"></td>
                <td style="{{ $p2rdTotal }}"></td><td style="{{ $p2rdTotal }}"></td>
                <td style="{{ $p2rdTotal }}"></td><td style="{{ $p2rdTotal }}"></td>
                <td style="{{ $p2rdTotal }}"></td><td style="{{ $p2rdTotal }}"></td>
                <td style="{{ $p2rdTotal }}"></td><td style="{{ $p2rdTotal }}"></td>
                <td style="{{ $p2rdTotal }}"></td><td style="{{ $p2rdTotal }}"></td>
                <td style="{{ $p2rdTotal }}"></td>
            </tr>
        </tbody>
    </table>

    {{-- CATATAN page 2 --}}
    <table style="border-collapse:collapse; width:100%; margin-top:10px; font-size:10px;">
        <tr>
            <td rowspan="3" style="{{ $p2bdH }} text-align:center; vertical-align:middle; font-weight:bold; font-size:13px; width:70px; padding:8px;">CATATAN</td>
            <td style="{{ $p2bdH }} text-align:center; vertical-align:middle; background-color:#70AD47; color:white; overflow:hidden; width:22px; padding:0;">
                <div style="transform:rotate(-90deg); white-space:nowrap; font-size:7px; font-weight:bold;">SHIFT 1</div>
            </td>
            <td style="{{ $p2bdH }} padding:20px 8px;">&nbsp;</td>
        </tr>
        <tr>
            <td style="{{ $p2bdH }} text-align:center; vertical-align:middle; background-color:#4472C4; color:white; overflow:hidden; width:22px; padding:0;">
                <div style="transform:rotate(-90deg); white-space:nowrap; font-size:7px; font-weight:bold;">SHIFT 2</div>
            </td>
            <td style="{{ $p2bdH }} padding:20px 8px;">&nbsp;</td>
        </tr>
        <tr>
            <td style="{{ $p2bdH }} text-align:center; vertical-align:middle; background-color:#ED7D31; color:white; overflow:hidden; width:22px; padding:0;">
                <div style="transform:rotate(-90deg); white-space:nowrap; font-size:7px; font-weight:bold;">SHIFT 3</div>
            </td>
            <td style="{{ $p2bdH }} padding:20px 8px;">&nbsp;</td>
        </tr>
    </table>

    {{-- OPERATOR page 2 --}}
    @php
        $p2opsI   = $p2shiftI[0]['shift_operators']   ?? [];
        $p2opsII  = $p2shiftII[0]['shift_operators']  ?? [];
        $p2opsIII = $p2shiftIII[0]['shift_operators'] ?? [];
    @endphp
    <table style="border-collapse:collapse; width:100%; margin-top:10px; font-size:11px;">
        <tr>
            <td style="{{ $p2bdH }} font-weight:bold; background-color:#70AD47; color:white; padding:9px 10px; width:33%;">SHIFT -1 PAGI ( 7:00 - 15:00 )</td>
            <td style="{{ $p2bdH }} font-weight:bold; background-color:#4472C4; color:white; padding:9px 10px; width:33%;">SHIFT -2 SIANG ( 15:00 - 23:00 )</td>
            <td style="{{ $p2bdH }} font-weight:bold; background-color:#ED7D31; color:white; padding:9px 10px; width:34%;">SHIFT -3 MALAM ( 23:00 - 7:00 )</td>
        </tr>
        @for ($i = 0; $i < 3; $i++)
        <tr>
            <td style="{{ $p2bdH }} padding:10px 10px;">{{ ($i+1) }}. Operator : {{ $p2opsI[$i]['name'] ?? '' }}</td>
            <td style="{{ $p2bdH }} padding:10px 10px;">{{ ($i+1) }}. Operator : {{ $p2opsII[$i]['name'] ?? '' }}</td>
            <td style="{{ $p2bdH }} padding:10px 10px;">{{ ($i+1) }}. Operator : {{ $p2opsIII[$i]['name'] ?? '' }}</td>
        </tr>
        @endfor
    </table>

    {{-- ======================================================== --}}
    {{-- HALAMAN 3: Pump Intake + Pump Distribusi (split 2 tabel) --}}
    {{-- ======================================================== --}}
    {{-- ======================================================== --}}
    {{-- HALAMAN 3: MEKANIKAL (Pump Distribusi + Pump Intake)     --}}
    {{-- ======================================================== --}}
    <div class="page-break"></div>
    {!! $pgHdr(3, 'MEKANIKAL') !!}

    @php
        $p3bdH    = "border:1px solid #333;";
        $p3thVt   = $p3bdH . " background-color:#F2F2F2; text-align:center; vertical-align:middle; overflow:hidden; padding:0; white-space:normal;";
        $p3thBase = $p3bdH . " font-weight:bold; text-align:center; vertical-align:middle; font-size:7px; padding:6px 1px; white-space:normal;";
        $p3thDist = $p3thBase . " background-color:#BDD7EE;";
        $p3thInt  = $p3thBase . " background-color:#E2EFDA;";
        $p3thGray = $p3thBase . " background-color:#F2F2F2;";
        $p3td     = $p3bdH . " text-align:center; padding:5px 1px; font-size:9px; white-space:normal;";
        $p3tdT    = $p3bdH . " text-align:center; padding:5px 1px; font-size:9px; font-weight:bold; white-space:normal;";
        $p3spc    = "border:0; padding:0; height:4px;";
        $p3rdTotal = $p3bdH . " background-color:#D9D9D9; padding:5px 1px; font-size:9px; white-space:normal;";
        $p3slBase = $p3bdH . " text-align:center; vertical-align:middle; overflow:hidden; color:white; font-weight:bold; padding:0; white-space:normal;";
        $p3slI    = $p3slBase . " background-color:#70AD47;";
        $p3slII   = $p3slBase . " background-color:#4472C4;";
        $p3slIII  = $p3slBase . " background-color:#ED7D31;";

        $p3shiftI   = $p2shiftI;
        $p3shiftII  = $p2shiftII;
        $p3shiftIII = $p2shiftIII;
    @endphp

    {{-- 23 cols: col1(SHIFT SCHEDULE/label) + col2(TIME/jam) + 12(dist) + 9(intake) --}}
    <table class="p3-table" style="border-collapse:collapse; width:100%; table-layout:fixed;">
        <colgroup>
            <col style="width:25px;">
            <col style="width:30px;">
            @for ($i=0; $i<21; $i++)<col style="width:auto;">@endfor
        </colgroup>
        <thead>
            <tr style="height:1px; font-size:0; line-height:0;">
                <td style="width:25px; padding:0; border:0;"></td>
                <td style="width:30px; padding:0; border:0;"></td>
                @for ($i=0; $i<21; $i++)<td style="padding:0; border:0;"></td>@endfor
            </tr>
            <tr>
                <td rowspan="3" style="{{ $p3thVt }} width:25px;">
                    <div style="transform:rotate(-90deg); font-size:6px; font-weight:bold; white-space:nowrap;">SHIFT SCHEDULE</div>
                </td>
                <td rowspan="3" style="{{ $p3thVt }} width:30px;">
                    <div style="transform:rotate(-90deg); font-size:6px; font-weight:bold; white-space:nowrap;">TIME (WIB)</div>
                </td>
                <td colspan="12" style="{{ $p3thDist }}">PUMP DISTRIBUSI</td>
                <td colspan="9"  style="{{ $p3thInt }}">PUMP INTAKE</td>
            </tr>
            <tr>
                <td colspan="3" style="{{ $p3thDist }} font-size:6.5px;">PUMP A</td>
                <td colspan="3" style="{{ $p3thDist }} font-size:6.5px;">PUMP B</td>
                <td colspan="3" style="{{ $p3thDist }} font-size:6.5px;">PUMP C</td>
                <td colspan="3" style="{{ $p3thDist }} font-size:6.5px;">PUMP D</td>
                <td colspan="3" style="{{ $p3thInt }} font-size:6.5px;">PUMP A</td>
                <td colspan="3" style="{{ $p3thInt }} font-size:6.5px;">PUMP B</td>
                <td colspan="3" style="{{ $p3thInt }} font-size:6.5px;">PUMP C</td>
            </tr>
            <tr>
                @for ($i=0; $i<7; $i++)
                    <td style="{{ $p3thGray }} font-size:7px;">(A)</td>
                    <td style="{{ $p3thGray }} font-size:7px;">(Hz)</td>
                    <td style="{{ $p3thGray }} font-size:7px;">(Bar)</td>
                @endfor
            </tr>
        </thead>
        <tbody>
            {{-- SHIFT I --}}
            @foreach ($p3shiftI as $idx => $rec)
                @php
                    $pp3   = collect($rec['pump_proccess'] ?? []);
                    $dA = $pp3->firstWhere('type', 'distribusi a');
                    $dB = $pp3->firstWhere('type', 'distribusi b');
                    $dC = $pp3->firstWhere('type', 'distribusi c');
                    $dD = $pp3->firstWhere('type', 'distribusi d');
                    $iA = $pp3->firstWhere('type', 'intake a');
                    $iB = $pp3->firstWhere('type', 'intake b');
                    $iC = $pp3->firstWhere('type', 'intake c');
                @endphp
                <tr>
                    @if ($idx === 0)
                        <td rowspan="{{ count($p3shiftI) }}" style="{{ $p3slI }} width:25px;">
                            <div style="transform:rotate(-90deg); font-size:7px; white-space:nowrap;">SHIFT - 1 (Pagi)</div>
                        </td>
                    @endif
                    <td style="{{ $p3tdT }}">{{ $fmtJam2($rec['end_time']) }}</td>
                    @foreach ([$dA,$dB,$dC,$dD,$iA,$iB,$iC] as $pump)
                        @php $isStandby = ($pump['status'] ?? '') === 'standby'; @endphp
                        <td style="{{ $p3td }}">{{ $isStandby ? '' : ($pump['ampere'] ?? '') }}</td>
                        <td style="{{ $p3td }}">{{ $isStandby ? '' : ($pump['frequency'] ?? '') }}</td>
                        <td style="{{ $p3td }}">{{ $isStandby ? '' : ($pump['pressure'] ?? '') }}</td>
                    @endforeach
                </tr>
            @endforeach
            <tr><td colspan="23" style="{{ $p3spc }}"></td></tr>

            {{-- SHIFT II --}}
            @foreach ($p3shiftII as $idx => $rec)
                @php
                    $pp3   = collect($rec['pump_proccess'] ?? []);
                    $dA = $pp3->firstWhere('type', 'distribusi a');
                    $dB = $pp3->firstWhere('type', 'distribusi b');
                    $dC = $pp3->firstWhere('type', 'distribusi c');
                    $dD = $pp3->firstWhere('type', 'distribusi d');
                    $iA = $pp3->firstWhere('type', 'intake a');
                    $iB = $pp3->firstWhere('type', 'intake b');
                    $iC = $pp3->firstWhere('type', 'intake c');
                @endphp
                <tr>
                    @if ($idx === 0)
                        <td rowspan="{{ count($p3shiftII) }}" style="{{ $p3slII }} width:25px;">
                            <div style="transform:rotate(-90deg); font-size:7px; white-space:nowrap;">SHIFT - 2 (Siang)</div>
                        </td>
                    @endif
                    <td style="{{ $p3tdT }}">{{ $fmtJam2($rec['end_time']) }}</td>
                    @foreach ([$dA,$dB,$dC,$dD,$iA,$iB,$iC] as $pump)
                        @php $isStandby = ($pump['status'] ?? '') === 'standby'; @endphp
                        <td style="{{ $p3td }}">{{ $isStandby ? '' : ($pump['ampere'] ?? '') }}</td>
                        <td style="{{ $p3td }}">{{ $isStandby ? '' : ($pump['frequency'] ?? '') }}</td>
                        <td style="{{ $p3td }}">{{ $isStandby ? '' : ($pump['pressure'] ?? '') }}</td>
                    @endforeach
                </tr>
            @endforeach
            <tr><td colspan="23" style="{{ $p3spc }}"></td></tr>

            {{-- SHIFT III --}}
            @foreach ($p3shiftIII as $idx => $rec)
                @php
                    $pp3   = collect($rec['pump_proccess'] ?? []);
                    $dA = $pp3->firstWhere('type', 'distribusi a');
                    $dB = $pp3->firstWhere('type', 'distribusi b');
                    $dC = $pp3->firstWhere('type', 'distribusi c');
                    $dD = $pp3->firstWhere('type', 'distribusi d');
                    $iA = $pp3->firstWhere('type', 'intake a');
                    $iB = $pp3->firstWhere('type', 'intake b');
                    $iC = $pp3->firstWhere('type', 'intake c');
                @endphp
                <tr>
                    @if ($idx === 0)
                        <td rowspan="{{ count($p3shiftIII) }}" style="{{ $p3slIII }} width:25px;">
                            <div style="transform:rotate(-90deg); font-size:7px; white-space:nowrap;">SHIFT - 3 (Malam)</div>
                        </td>
                    @endif
                    <td style="{{ $p3tdT }}">{{ $fmtJam2($rec['end_time']) }}</td>
                    @foreach ([$dA,$dB,$dC,$dD,$iA,$iB,$iC] as $pump)
                        @php $isStandby = ($pump['status'] ?? '') === 'standby'; @endphp
                        <td style="{{ $p3td }}">{{ $isStandby ? '' : ($pump['ampere'] ?? '') }}</td>
                        <td style="{{ $p3td }}">{{ $isStandby ? '' : ($pump['frequency'] ?? '') }}</td>
                        <td style="{{ $p3td }}">{{ $isStandby ? '' : ($pump['pressure'] ?? '') }}</td>
                    @endforeach
                </tr>
            @endforeach
            <tr><td colspan="23" style="{{ $p3spc }}"></td></tr>

            {{-- TOTAL --}}
            <tr>
                <td colspan="2" style="{{ $p3rdTotal }} font-weight:bold; text-align:center; background-color:#FFF2CC;">TOTAL</td>
                @for ($i=0; $i<21; $i++)<td style="{{ $p3rdTotal }}"></td>@endfor
            </tr>
        </tbody>
    </table>

    {{-- CATATAN page 3 --}}
    <table style="border-collapse:collapse; width:100%; margin-top:10px; font-size:10px;">
        <tr>
            <td rowspan="3" style="{{ $p3bdH }} text-align:center; vertical-align:middle; font-weight:bold; font-size:13px; width:70px; padding:8px;">CATATAN</td>
            <td style="{{ $p3bdH }} text-align:center; vertical-align:middle; background-color:#70AD47; color:white; overflow:hidden; width:22px; padding:0;">
                <div style="transform:rotate(-90deg); white-space:nowrap; font-size:7px; font-weight:bold;">SHIFT 1</div>
            </td>
            <td style="{{ $p3bdH }} padding:20px 8px;">&nbsp;</td>
        </tr>
        <tr>
            <td style="{{ $p3bdH }} text-align:center; vertical-align:middle; background-color:#4472C4; color:white; overflow:hidden; width:22px; padding:0;">
                <div style="transform:rotate(-90deg); white-space:nowrap; font-size:7px; font-weight:bold;">SHIFT 2</div>
            </td>
            <td style="{{ $p3bdH }} padding:20px 8px;">&nbsp;</td>
        </tr>
        <tr>
            <td style="{{ $p3bdH }} text-align:center; vertical-align:middle; background-color:#ED7D31; color:white; overflow:hidden; width:22px; padding:0;">
                <div style="transform:rotate(-90deg); white-space:nowrap; font-size:7px; font-weight:bold;">SHIFT 3</div>
            </td>
            <td style="{{ $p3bdH }} padding:20px 8px;">&nbsp;</td>
        </tr>
    </table>

    {{-- OPERATOR page 3 --}}
    @php
        $p3opsI   = $p3shiftI[0]['shift_operators']   ?? [];
        $p3opsII  = $p3shiftII[0]['shift_operators']  ?? [];
        $p3opsIII = $p3shiftIII[0]['shift_operators'] ?? [];
    @endphp
    <table style="border-collapse:collapse; width:100%; margin-top:10px; font-size:11px;">
        <tr>
            <td style="{{ $p3bdH }} font-weight:bold; background-color:#70AD47; color:white; padding:9px 10px; width:33%;">SHIFT -1 PAGI ( 7:00 - 15:00 )</td>
            <td style="{{ $p3bdH }} font-weight:bold; background-color:#4472C4; color:white; padding:9px 10px; width:33%;">SHIFT -2 SIANG ( 15:00 - 23:00 )</td>
            <td style="{{ $p3bdH }} font-weight:bold; background-color:#ED7D31; color:white; padding:9px 10px; width:34%;">SHIFT -3 MALAM ( 23:00 - 7:00 )</td>
        </tr>
        @for ($i = 0; $i < 3; $i++)
        <tr>
            <td style="{{ $p3bdH }} padding:10px 10px;">{{ ($i+1) }}. Operator : {{ $p3opsI[$i]['name'] ?? '' }}</td>
            <td style="{{ $p3bdH }} padding:10px 10px;">{{ ($i+1) }}. Operator : {{ $p3opsII[$i]['name'] ?? '' }}</td>
            <td style="{{ $p3bdH }} padding:10px 10px;">{{ ($i+1) }}. Operator : {{ $p3opsIII[$i]['name'] ?? '' }}</td>
        </tr>
        @endfor
    </table>

    {{-- ======================================================== --}}
    {{-- HALAMAN 4: Pump Dosing PAC + Chlorine/Kaporit           --}}
    {{-- ======================================================== --}}
    <div class="page-break"></div>
    {!! $pgHdr(4) !!}

    <table style="border-collapse:collapse; width:100%; table-layout:fixed; font-size:7.5px;">
        <thead>
            <tr>
                <th style="{{ $thP }}" width="32">Jam</th>
                <th style="{{ $thP }}" colspan="4">Pump Dosing PAC — Pompa A</th>
                <th style="{{ $thP }}" colspan="4">Pump Dosing PAC — Pompa B</th>
                <th style="{{ $thP }}" colspan="4">Pump Dosing Chlorine — Pompa A</th>
                <th style="{{ $thP }}" colspan="4">Pump Dosing Chlorine — Pompa B</th>
            </tr>
            <tr>
                <th style="{{ $thP }}"></th>
                @foreach ([1,2,3,4] as $_)
                    <th style="{{ $thP }}">Frekuensi</th>
                    <th style="{{ $thP }}">Dosis</th>
                    <th style="{{ $thP }}">Pengadukan</th>
                    <th style="{{ $thP }}">Level Tangki</th>
                @endforeach
            </tr>
            <tr>
                <th style="{{ $thP }}"></th>
                <th style="{{ $thP }}">Hz</th><th style="{{ $thP }}">ppm</th><th style="{{ $thP }}">Kg</th><th style="{{ $thP }}">cm</th>
                <th style="{{ $thP }}">Hz</th><th style="{{ $thP }}">ppm</th><th style="{{ $thP }}">Kg</th><th style="{{ $thP }}">cm</th>
                <th style="{{ $thP }}">l/h</th><th style="{{ $thP }}">ppm</th><th style="{{ $thP }}">Kg</th><th style="{{ $thP }}">cm</th>
                <th style="{{ $thP }}">l/h</th><th style="{{ $thP }}">ppm</th><th style="{{ $thP }}">Kg</th><th style="{{ $thP }}">cm</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($shifts as $shift)
                @php
                    $grp = collect($shift['pump_chemicals'] ?? [])->groupBy(fn($p) => $p['type'].'|'.$p['pump_unit']);
                @endphp
                <tr>
                    <td style="{{ $tdP }} font-weight:bold;">{{ $fmtJam2($shift['end_time']) }}</td>
                    @foreach ([['pac','A'],['pac','B']] as [$t,$u])
                        @php $p = $grp->get("$t|$u")?->first(); @endphp
                        <td style="{{ $tdP }}">{{ $p['frequency'] ?? '' }}</td>
                        <td style="{{ $tdP }}">{{ $p['dosage'] ?? '' }}</td>
                        <td style="{{ $tdP }}">{{ $p['stirring'] ?? '' }}</td>
                        <td style="{{ $tdP }}">{{ $p['tank_level'] ?? '' }}</td>
                    @endforeach
                    @foreach ([['chlorine/kaporit','A'],['chlorine/kaporit','B']] as [$t,$u])
                        @php $p = $grp->get("$t|$u")?->first(); @endphp
                        <td style="{{ $tdP }}">{{ $p['flow_rate'] ?? '' }}</td>
                        <td style="{{ $tdP }}">{{ $p['dosage'] ?? '' }}</td>
                        <td style="{{ $tdP }}">{{ $p['stirring'] ?? '' }}</td>
                        <td style="{{ $tdP }}">{{ $p['tank_level'] ?? '' }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- ======================================================== --}}
    {{-- HALAMAN 5: Pump Dosing Soda Ash + Polymer               --}}
    {{-- ======================================================== --}}
    <div class="page-break"></div>
    {!! $pgHdr(5) !!}

    <table style="border-collapse:collapse; width:100%; table-layout:fixed; font-size:7.5px;">
        <thead>
            <tr>
                <th style="{{ $thP }}" width="32">Jam</th>
                <th style="{{ $thP }}" colspan="4">Pump Dosing Soda Ash — Pompa A</th>
                <th style="{{ $thP }}" colspan="4">Pump Dosing Soda Ash — Pompa B</th>
                <th style="{{ $thP }}" colspan="4">Pump Dosing Polymer — Pompa A</th>
                <th style="{{ $thP }}" colspan="4">Pump Dosing Polymer — Pompa B</th>
            </tr>
            <tr>
                <th style="{{ $thP }}"></th>
                @foreach ([1,2,3,4] as $_)
                    <th style="{{ $thP }}">Flow Rate</th>
                    <th style="{{ $thP }}">Dosis</th>
                    <th style="{{ $thP }}">Pengadukan</th>
                    <th style="{{ $thP }}">Level Tangki</th>
                @endforeach
            </tr>
            <tr>
                <th style="{{ $thP }}"></th>
                @foreach ([1,2,3,4] as $_)
                    <th style="{{ $thP }}">l/h</th><th style="{{ $thP }}">ppm</th><th style="{{ $thP }}">Kg</th><th style="{{ $thP }}">cm</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($shifts as $shift)
                @php
                    $grp = collect($shift['pump_chemicals'] ?? [])->groupBy(fn($p) => $p['type'].'|'.$p['pump_unit']);
                @endphp
                <tr>
                    <td style="{{ $tdP }} font-weight:bold;">{{ $fmtJam2($shift['end_time']) }}</td>
                    @foreach ([['soda ash','A'],['soda ash','B'],['polymer','A'],['polymer','B']] as [$t,$u])
                        @php $p = $grp->get("$t|$u")?->first(); @endphp
                        <td style="{{ $tdP }}">{{ $p['flow_rate'] ?? '' }}</td>
                        <td style="{{ $tdP }}">{{ $p['dosage'] ?? '' }}</td>
                        <td style="{{ $tdP }}">{{ $p['stirring'] ?? '' }}</td>
                        <td style="{{ $tdP }}">{{ $p['tank_level'] ?? '' }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

</body>

</html>

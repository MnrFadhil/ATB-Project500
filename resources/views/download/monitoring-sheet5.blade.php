@php
    \Carbon\Carbon::setLocale('id');
    $thBase   = "background-color: rgb(193,193,193); border: 1.5px solid #333; text-align: center; font-weight: bold; padding: 4px 6px;";
    $thNarrow = $thBase . " width: 60px;";
    $thMedium = $thBase . " width: 80px;";
    $thWide   = $thBase . " width: 100px;";
    $tdStyle  = "border: 1px solid #555; text-align: center; padding: 3px 5px;";
    $hariTanggal = \Carbon\Carbon::parse($shifts[0]['date'] ?? '')->translatedFormat('l, d F Y');
@endphp

<table style="border-collapse: collapse; width: 100%;">
    <thead>
        <tr>
            <th colspan="8" style="{{ $thBase }}">Pump Dosing Soda Ash</th>
            <th colspan="8" style="{{ $thBase }}">Pump Dosing Polymer</th>
        </tr>
        <tr>
            <th colspan="4" style="{{ $thBase }}">Pompa A</th>
            <th colspan="4" style="{{ $thBase }}">Pompa B</th>
            <th colspan="4" style="{{ $thBase }}">Pompa A</th>
            <th colspan="4" style="{{ $thBase }}">Pompa B</th>
        </tr>
        <tr>
            <th style="{{ $thMedium }}">Flow Rate</th><th style="{{ $thNarrow }}">Dosis</th><th style="{{ $thMedium }}">Pengadukan</th><th style="{{ $thWide }}">Level Tangki</th>
            <th style="{{ $thMedium }}">Flow Rate</th><th style="{{ $thNarrow }}">Dosis</th><th style="{{ $thMedium }}">Pengadukan</th><th style="{{ $thWide }}">Level Tangki</th>
            <th style="{{ $thMedium }}">Flow Rate</th><th style="{{ $thNarrow }}">Dosis</th><th style="{{ $thMedium }}">Pengadukan</th><th style="{{ $thWide }}">Level Tangki</th>
            <th style="{{ $thMedium }}">Flow Rate</th><th style="{{ $thNarrow }}">Dosis</th><th style="{{ $thMedium }}">Pengadukan</th><th style="{{ $thWide }}">Level Tangki</th>
        </tr>
        <tr>
            <th style="{{ $thNarrow }}">l/h</th><th style="{{ $thNarrow }}">ppm</th><th style="{{ $thNarrow }}">Kg</th><th style="{{ $thNarrow }}">cm</th>
            <th style="{{ $thNarrow }}">l/h</th><th style="{{ $thNarrow }}">ppm</th><th style="{{ $thNarrow }}">Kg</th><th style="{{ $thNarrow }}">cm</th>
            <th style="{{ $thNarrow }}">l/h</th><th style="{{ $thNarrow }}">ppm</th><th style="{{ $thNarrow }}">Kg</th><th style="{{ $thNarrow }}">cm</th>
            <th style="{{ $thNarrow }}">l/h</th><th style="{{ $thNarrow }}">ppm</th><th style="{{ $thNarrow }}">Kg</th><th style="{{ $thNarrow }}">cm</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($shifts as $shift)
            <tr>
                @php
                    $grouped = collect($shift['pump_chemicals'])->groupBy(function($p) {
                        return $p['type'] . '|' . $p['pump_unit'];
                    });
                @endphp
                @foreach ([['soda ash','A'],['soda ash','B'],['polymer','A'],['polymer','B']] as [$type,$unit])
                    @php $pump = $grouped->get("$type|$unit")?->first(); @endphp
                    <td style="{{ $tdStyle }}">{{ $pump['flow_rate'] ?? '-' }}</td>
                    <td style="{{ $tdStyle }}">{{ $pump['dosage'] ?? '-' }}</td>
                    <td style="{{ $tdStyle }}">{{ $pump['stirring'] ?? '-' }}</td>
                    <td style="{{ $tdStyle }}">{{ $pump['tank_level'] ?? '-' }}</td>
                @endforeach
            </tr>
            {{-- Baris kosong jam genap (16 kolom) --}}
            <tr>
                @for ($i = 0; $i < 16; $i++)<td style="{{ $tdStyle }}"></td>@endfor
            </tr>
        @endforeach
    </tbody>
</table>

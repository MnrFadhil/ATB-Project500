@once
<style>
@keyframes scada-wave-a {
    0%   { transform: translateX(0); }
    100% { transform: translateX(-50%); }
}
</style>
@endonce

@props([
    'value'       => 0,
    'max'         => 100,
    'color'       => '#48AC90',
    'width'       => '60px',
    'height'      => '150px',
    'label'       => '',
    'unit'        => '',
    'showWarning' => false,
    'showScale'   => false,
    'scaleSteps'  => 5,
])

@php
    $filler = $max > 0 ? min(($value / $max) * 100, 100) : 0;

    $ticks = [];
    if ($showScale && $scaleSteps > 0) {
        for ($i = $scaleSteps; $i >= 0; $i--) {
            $ticks[] = round($max / $scaleSteps * $i, 1);
        }
    }
@endphp

<div style="display:flex; flex-direction:column; align-items:center; width:100%; padding:0 16px; box-sizing:border-box;">
    <div style="display:flex; align-items:flex-end; width:100%; gap:6px; margin-top:10px;">

        {{-- Skala kiri --}}
        @if($showScale)
        <div style="display:flex; flex-direction:column; justify-content:space-between; height:{{ $height }}; align-items:flex-end; flex-shrink:0; min-width:28px;">
            @foreach($ticks as $tick)
            <div style="display:flex; align-items:center; gap:4px;">
                <span style="font-size:10px; color:#999; line-height:1;">{{ $tick }}</span>
                <span style="width:6px; height:1px; background:#ccc; display:inline-block;"></span>
            </div>
            @endforeach
        </div>
        @endif

        {{-- Bar --}}
        <div style="
            flex:1;
            height:{{ $height }};
            background:#d1d5db;
            display:flex;
            align-items:flex-end;
            border-radius:4px;
            overflow:hidden;
        ">
            <div style="
                position:relative;
                width:100%;
                height:{{ $filler }}%;
                background:{{ $color }};
                transition:height 1s ease;
                overflow:hidden;
            ">
                {{-- Wave layer 1 --}}
                <div style="
                    position:absolute;top:0;left:0;
                    width:200%;height:20px;
                    animation:scada-wave-a 2.2s linear infinite;
                ">
                    <svg viewBox="0 0 200 20" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg" style="width:100%;height:100%;display:block;">
                        <path d="M0,10 C12.5,2 25,18 37.5,10 C50,2 62.5,18 75,10 C87.5,2 100,10 100,10 C112.5,2 125,18 137.5,10 C150,2 162.5,18 175,10 C187.5,2 200,10 200,10 L200,20 L0,20 Z"
                              fill="rgba(255,255,255,0.35)"/>
                    </svg>
                </div>
                {{-- Wave layer 2 (opposite direction, slower) --}}
                <div style="
                    position:absolute;top:4px;left:0;
                    width:200%;height:16px;
                    animation:scada-wave-a 3.4s linear infinite reverse;
                ">
                    <svg viewBox="0 0 200 16" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg" style="width:100%;height:100%;display:block;">
                        <path d="M0,8 C12.5,1 25,15 37.5,8 C50,1 62.5,15 75,8 C87.5,1 100,8 100,8 C112.5,1 125,15 137.5,8 C150,1 162.5,15 175,8 C187.5,1 200,8 200,8 L200,16 L0,16 Z"
                              fill="rgba(255,255,255,0.18)"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Spacer kanan (balance skala kiri) --}}
        @if($showScale)
        <div style="min-width:28px; flex-shrink:0;"></div>
        @endif
    </div>

    <div style="text-align:center; margin-top:8px;">
        <div style="display:flex; align-items:center; justify-content:center; gap:4px; font-weight:600; font-size:18px;">
            @if($showWarning)
                <span style="color:#f97316;" title="Warning">⚠</span>
            @endif
            <span>{{ $value }}</span>
            <span>{!! $unit !!}</span>
        </div>
        @if($label)
            <div style="font-size:13px; color:#6b7280; padding:4px 0;">{{ $label }}</div>
        @endif
    </div>
</div>

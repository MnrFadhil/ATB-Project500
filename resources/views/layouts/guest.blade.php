<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Masuk · Adaro Tirta Brayan</title>

    {{-- Favicon --}}
    <link rel="icon" type="image/svg+xml" href="{{ asset('assets/adaro-login-logo.svg') }}">

    {{-- Fonts: Nunito --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400;1,600&display=swap" rel="stylesheet">

    {{-- App + Login CSS --}}
    @vite(['resources/css/app.css', 'resources/css/atb-login.css', 'resources/js/app.js'])
</head>
<body>

<div class="atb-page">

    {{-- ── Brand panel (left) ───────────────────────────────────────── --}}
    <aside class="atb-brand">

        {{-- Decorative ripple / topographic pattern --}}
        <svg class="atb-brand-pattern" viewBox="0 0 600 800"
             preserveAspectRatio="xMidYMid slice" aria-hidden="true">
            <defs>
                <radialGradient id="atb-vign" cx="50%" cy="35%" r="75%">
                    <stop offset="0%"   stop-color="#fff" stop-opacity="0.10"/>
                    <stop offset="100%" stop-color="#000" stop-opacity="0.35"/>
                </radialGradient>
            </defs>
            <g stroke="#ffffff" stroke-opacity="0.07" fill="none" stroke-width="1">
                @for ($i = 0; $i < 22; $i++)
                    @php $r = 60 + $i * 38; $ry = round($r * 0.62, 2); @endphp
                    <ellipse cx="120" cy="640"
                             rx="{{ $r }}" ry="{{ $ry }}"
                             transform="rotate(-12 120 640)"/>
                @endfor
            </g>
            <rect width="600" height="800" fill="url(#atb-vign)"/>
        </svg>

        <div class="atb-brand-inner">

            {{-- Adaro mark --}}
            <div class="atb-brand-top">
                <div class="atb-adaro-mark">
                    <img src="{{ asset('assets/adaro-login-logo.svg') }}" alt="Adaro Tirta Brayan">
                    <span class="atb-mark-divider"></span>
                    <div class="atb-mark-text">
                        <span class="atb-mark-line-1">Adaro Tirta Brayan</span>
                        <span class="atb-mark-line-2">Sistem Monitoring Produksi</span>
                    </div>
                </div>
            </div>

            {{-- Tagline --}}
            <div class="atb-brand-mid">
                <h2>Mengalir bersama,<br>tumbuh berkelanjutan.</h2>
                <p>Portal terpadu untuk monitoring produksi Instalasi
                   Pengolahan Air Adaro Tirta Brayan.</p>
            </div>

            {{-- Version --}}
            <div class="atb-brand-bottom">
                <span class="atb-version">v 1 &middot; &copy; {{ date('Y') }} ATB</span>
            </div>

        </div>
    </aside>

    {{-- ── Form panel (right) ──────────────────────────────────────── --}}
    <main class="atb-panel">
        <div class="atb-panel-frame">

            {{-- Mobile-only Adaro mark (hidden ≥620 px) --}}
            <div class="atb-mobile-mark">
                <img src="{{ asset('assets/adaro-login-logo.svg') }}" alt="Adaro Tirta Brayan">
                <span class="atb-mark-divider"></span>
                <span>Adaro Tirta Brayan</span>
            </div>

            {{-- Livewire component slot --}}
            {{ $slot }}

        </div>
    </main>

</div>

</body>
</html>

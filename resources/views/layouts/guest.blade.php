<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="icon" type="image/x-icon" href="{{ asset('assets/sidebar-close.svg') }}">

    {{-- Fonts --}}
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body
    style="background-image: url('{{ asset('assets/bg-login.svg') }}');  background-size: cover; background-position: center; background-repeat: no-repeat;">
    <div class="container">
        <!-- Outer Row -->
        <div class="row justify-content-center align-items-center" style="height: 100vh;">
            <div class="col-xl-6 col-lg-12 col-md-9">
                <div style="border: solid 5px #00664A; border-radius: 10px; background:rgba(255,255,255, 0.7);">
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row">
                            {{-- <div class="col-lg-6 d-none d-lg-block bg-login-image"></div> --}}
                            <div class="col-12">
                                {{ $slot }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>

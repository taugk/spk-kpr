@php
    $hideMenu = in_array(Route::currentRouteName(), ['login', 'register']);
@endphp

<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">
            <i class="bi bi-building"></i> Citra Pasada Property
        </a>

        @if(!$hideMenu)
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item"><a class="nav-link" href="#beranda">Beranda</a></li>
                <li class="nav-item"><a class="nav-link" href="#promo">Promo</a></li>
                <li class="nav-item"><a class="nav-link" href="#fasilitas">Fasilitas</a></li>
                <li class="nav-item"><a class="nav-link" href="#daftar-perum">Perumahan</a></li>
                <li class="nav-item"><a class="nav-link" href="#kenapa-kami">Keunggulan</a></li>
                <li class="nav-item"><a class="nav-link" href="#lokasi">Lokasi</a></li>
                <li class="nav-item"><a class="nav-link" href="#feedback">Testimoni</a></li>
            </ul>
        </div>
        @endif

        <div class="d-flex gap-2">
            @if(Auth::check())
                {{-- <a href="{{ route('dashboard') }}" class="btn-login-nav">Dashboard</a> --}}
                <form action="{{ route('debitur.logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn-register-nav">Logout</button>
                </form>
            @else
                <a href="{{ route('debitur.login') }}" class="btn-login-nav">Login</a>
                <a href="{{ route('debitur.register') }}" class="btn-register-nav">Daftar</a>
            @endif
        </div>
    </div>
</nav>
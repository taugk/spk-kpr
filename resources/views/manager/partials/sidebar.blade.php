{{-- resources/views/manager/partials/sidebar.blade.php --}}
<div class="left-side-bar">
    {{-- Logo --}}
    <div class="brand-logo">
        <a href="{{ route('manajer.dashboard') }}">
            <img src="{{ asset('deskapp/vendors/images/deskapp-logo.svg') }}"
                alt="Logo" class="dark-logo">
            <img src="{{ asset('deskapp/vendors/images/deskapp-logo-white.svg') }}"
                alt="Logo" class="light-logo">
        </a>
        <div class="close-sidebar" data-toggle="left-sidebar-close">
            <i class="ion-close-round"></i>
        </div>
    </div>

    {{-- Sidebar Menu --}}
    <div class="menu-block customscroll">
        <div class="sidebar-menu">
            <ul id="accordion-menu">

                {{-- DASHBOARD MONITORING --}}
                <li>
                    <a href="{{ route('manajer.dashboard') }}"
                        class="dropdown-toggle no-arrow
                            {{ request()->routeIs('manajer.dashboard') ? 'active' : '' }}">
                        <span class="micon dw dw-house-1"></span>
                        <span class="mtext">Dashboard</span>
                    </a>
                </li>

                {{-- MONITORING PENGAJUAN --}}
                <li>
                    <div class="sidebar-small-cap">Monitoring Pengajuan</div>
                </li>

                <li>
                    <a href="{{ route('manajer.pengajuan.semua') }}"
                        class="dropdown-toggle no-arrow
                            {{ request()->routeIs('manajer.pengajuan.semua') ? 'active' : '' }}">
                        <span class="micon dw dw-file"></span>
                        <span class="mtext">Semua Pengajuan</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('manajer.pengajuan.proses') }}"
                        class="dropdown-toggle no-arrow
                            {{ request()->routeIs('manajer.pengajuan.proses') ? 'active' : '' }}">
                        <span class="micon dw dw-sync"></span>
                        <span class="mtext">Sedang Diproses</span>
                        @php
                            $sedangDiproses = \App\Models\Pengajuan::whereIn('status', [
                                'diajukan', 'verifikasi_dokumen', 'verifikasi_lapangan', 'diteruskan_admin', 'penilaian'
                            ])->count();
                        @endphp
                        @if($sedangDiproses > 0)
                            <span class="badge badge-warning badge-pill float-right">
                                {{ $sedangDiproses }}
                            </span>
                        @endif
                    </a>
                </li>

                <li>
                    <a href="{{ route('manajer.pengajuan.selesai') }}"
                        class="dropdown-toggle no-arrow
                            {{ request()->routeIs('manajer.pengajuan.selesai') ? 'active' : '' }}">
                        <span class="micon dw dw-check"></span>
                        <span class="mtext">Selesai Dinilai</span>
                    </a>
                </li>

                {{-- MONITORING KINERJA --}}
                <li>
                    <div class="sidebar-small-cap">Monitoring Kinerja</div>
                </li>

                <li>
                    <a href="{{ route('manajer.kinerja.marketing') }}"
                        class="dropdown-toggle no-arrow
                            {{ request()->routeIs('manajer.kinerja.marketing') ? 'active' : '' }}">
                        <span class="micon dw dw-user"></span>
                        <span class="mtext">Kinerja Marketing</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('manajer.kinerja.admin') }}"
                        class="dropdown-toggle no-arrow
                            {{ request()->routeIs('manajer.kinerja.admin') ? 'active' : '' }}">
                        <span class="micon dw dw-settings"></span>
                        <span class="mtext">Kinerja Admin</span>
                    </a>
                </li>

                {{-- LAPORAN & STATISTIK --}}
                <li>
                    <div class="sidebar-small-cap">Laporan & Statistik</div>
                </li>

                <li>
                    <a href="{{ route('manajer.laporan.bulanan') }}"
                        class="dropdown-toggle no-arrow
                            {{ request()->routeIs('manajer.laporan.bulanan') ? 'active' : '' }}">
                        <span class="micon dw dw-calendar"></span>
                        <span class="mtext">Laporan Bulanan</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('manajer.laporan.tahunan') }}"
                        class="dropdown-toggle no-arrow
                            {{ request()->routeIs('manajer.laporan.tahunan') ? 'active' : '' }}">
                        <span class="micon dw dw-pie-chart"></span>
                        <span class="mtext">Laporan Tahunan</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('manajer.laporan.export') }}"
                        class="dropdown-toggle no-arrow
                            {{ request()->routeIs('manajer.laporan.export') ? 'active' : '' }}">
                        <span class="micon dw dw-download"></span>
                        <span class="mtext">Export Data</span>
                    </a>
                </li>

                {{-- ANALISIS DATA --}}
                <li>
                    <div class="sidebar-small-cap">Analisis</div>
                </li>

                <li>
                    <a href="{{ route('manajer.analisis.statistik') }}"
                        class="dropdown-toggle no-arrow
                            {{ request()->routeIs('manajer.analisis.statistik') ? 'active' : '' }}">
                        <span class="micon dw dw-analytics-21"></span>
                        <span class="mtext">Statistik Penilaian</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('manajer.analisis.tren') }}"
                        class="dropdown-toggle no-arrow
                            {{ request()->routeIs('manajer.analisis.tren') ? 'active' : '' }}">
                        <span class="micon dw dw-line-chart"></span>
                        <span class="mtext">Tren Pengajuan</span>
                    </a>
                </li>

            </ul>
        </div>
    </div>
</div>

<div class="mobile-menu-overlay"></div>

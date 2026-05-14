<div class="left-side-bar">
    <!-- Logo -->
    <div class="brand-logo">
        <a href="{{ route('admin.dashboard') }}">
            <img src="{{ asset('deskapp/vendors/images/deskapp-logo.svg') }}"
                alt="Logo"
                class="dark-logo">

            <img src="{{ asset('deskapp/vendors/images/deskapp-logo-white.svg') }}"
                alt="Logo"
                class="light-logo">
        </a>

        <div class="close-sidebar" data-toggle="left-sidebar-close">
            <i class="ion-close-round"></i>
        </div>
    </div>

    <!-- Sidebar Menu -->
    <div class="menu-block customscroll">
        <div class="sidebar-menu">
            <ul id="accordion-menu">

                {{-- DASHBOARD --}}
                <li>
                    <a href="{{ route('admin.dashboard') }}"
                        class="dropdown-toggle no-arrow {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <span class="micon dw dw-house-1"></span>
                        <span class="mtext">Dashboard</span>
                    </a>
                </li>

                {{-- SMART SYSTEM --}}
                <li>
                    <div class="sidebar-small-cap">
                        Sistem SMART
                    </div>
                </li>

                <li>
                    <a href="{{ route('admin.pengajuan.index') }}"
                        class="dropdown-toggle no-arrow {{ request()->routeIs('admin.pengajuan.*') ? 'active' : '' }}">
                        <span class="micon dw dw-file"></span>
                        <span class="mtext">Pengajuan KPR</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.kriteria.index') }}"
                        class="dropdown-toggle no-arrow {{ request()->routeIs('admin.kriteria.*') ? 'active' : '' }}">
                        <span class="micon dw dw-list3"></span>
                        <span class="mtext">Data Kriteria</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.subkriteria.index') }}"
                        class="dropdown-toggle no-arrow {{ request()->routeIs('admin.subkriteria.*') ? 'active' : '' }}">
                        <span class="micon dw dw-folder-11"></span>
                        <span class="mtext">Subkriteria</span>
                    </a>
                </li>

                {{-- PENILAIAN --}}
                <li>
                    <div class="sidebar-small-cap">
                        Penilaian
                    </div>
                </li>

                <li>
                    <a href="{{ route('admin.penilaian.index') }}"
                        class="dropdown-toggle no-arrow {{ request()->routeIs('admin.penilaian.index') ? 'active' : '' }}">
                        <span class="micon dw dw-edit-2"></span>
                        <span class="mtext">Input Penilaian</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.hasil-penilaian') }}"
                        class="dropdown-toggle no-arrow {{ request()->routeIs('admin.penilaian.hasil') ? 'active' : '' }}">
                        <span class="micon dw dw-analytics-21"></span>
                        <span class="mtext">Hasil Perankingan</span>
                    </a>
                </li>

                {{-- DATA MASTER --}}
                <li>
                    <div class="sidebar-small-cap">
                        Data Master
                    </div>
                </li>

                <li>
                    <a href="{{ route('admin.properti.index') }}"
                        class="dropdown-toggle no-arrow {{ request()->routeIs('admin.properti.*') ? 'active' : '' }}">
                        <span class="micon dw dw-building"></span>
                        <span class="mtext">Manajemen Properti</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.users.index') }}"
                        class="dropdown-toggle no-arrow {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <span class="micon dw dw-user"></span>
                        <span class="mtext">Data Pengguna</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.laporan.index') }}"
                        class="dropdown-toggle no-arrow {{ request()->routeIs('admin.laporan.*') ? 'active' : '' }}">
                        <span class="micon dw dw-print"></span>
                        <span class="mtext">Laporan Rekap</span>
                    </a>
                </li>

                {{-- PENGATURAN --}}
                <li>
                    <div class="sidebar-small-cap">
                        Konfigurasi
                    </div>
                </li>

                <li>
                    <a href="{{ route('admin.pengaturan.index') }}"
                        class="dropdown-toggle no-arrow {{ request()->routeIs('admin.pengaturan.*') ? 'active' : '' }}">
                        <span class="micon dw dw-settings2"></span>
                        <span class="mtext">Pengaturan Sistem</span>
                    </a>
                </li>

            </ul>
        </div>
    </div>
</div>

<div class="mobile-menu-overlay"></div>
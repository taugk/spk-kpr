{{-- resources/views/marketing/partials/sidebar.blade.php --}}
<div class="left-side-bar">
    {{-- Logo --}}
    <div class="brand-logo">
        <a href="{{ route('marketing.dashboard') }}">
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

                {{-- DASHBOARD --}}
                <li>
                    <a href="{{ route('marketing.dashboard') }}"
                        class="dropdown-toggle no-arrow
                            {{ request()->routeIs('marketing.dashboard') ? 'active' : '' }}">
                        <span class="micon dw dw-house-1"></span>
                        <span class="mtext">Dashboard</span>
                    </a>
                </li>

                {{-- PENGAJUAN MASUK --}}
                <li>
                    <div class="sidebar-small-cap">Pengajuan Masuk</div>
                </li>

                <li>
                    <a href="{{ route('marketing.pengajuan.masuk') }}"
                        class="dropdown-toggle no-arrow
                            {{ request()->routeIs('marketing.pengajuan.masuk') ? 'active' : '' }}">
                        <span class="micon dw dw-inbox"></span>
                        <span class="mtext">
                            Antrian Pengajuan
                            @php
                                $antrian = \App\Models\Pengajuan::where('status', 'diajukan')
                                    ->where('marketing_id', auth()->id())
                                    ->count();
                            @endphp
                            @if($antrian > 0)
                                <span class="badge badge-danger badge-pill float-right">
                                    {{ $antrian }}
                                </span>
                            @endif
                        </span>
                    </a>
                </li>

                {{-- VERIFIKASI DATA --}}
                <li>
                    <div class="sidebar-small-cap">Verifikasi</div>
                </li>

                {{-- Verifikasi Kelengkapan Dokumen --}}
                <li>
                    <a href="{{ route('marketing.verifikasi.dokumen') }}"
                        class="dropdown-toggle no-arrow
                            {{ request()->routeIs('marketing.verifikasi.dokumen') ? 'active' : '' }}">
                        <span class="micon dw dw-file-31"></span>
                        <span class="mtext">Verifikasi Dokumen</span>
                        @php
                            $perluVerifikasiDokumen = \App\Models\Pengajuan::where('status', 'verifikasi_dokumen')
                                ->where('marketing_id', auth()->id())
                                ->count();
                        @endphp
                        @if($perluVerifikasiDokumen > 0)
                            <span class="badge badge-warning badge-pill float-right">
                                {{ $perluVerifikasiDokumen }}
                            </span>
                        @endif
                    </a>
                </li>

                {{-- Verifikasi Lapangan
                <li>
                    <a href="{{ route('marketing.verifikasi.lapangan') }}"
                        class="dropdown-toggle no-arrow
                            {{ request()->routeIs('marketing.verifikasi.lapangan') ? 'active' : '' }}">
                        <span class="micon ion-location"></span>
                        <span class="mtext">Verifikasi Lapangan</span>
                        @php
                            $perluVerifikasiLapangan = \App\Models\Pengajuan::where('status', 'verifikasi_lapangan')
                                ->where('marketing_id', auth()->id())
                                ->count();
                        @endphp
                        @if($perluVerifikasiLapangan > 0)
                            <span class="badge badge-warning badge-pill float-right">
                                {{ $perluVerifikasiLapangan }}
                            </span>
                        @endif
                    </a>
                </li> --}}

                {{-- STATUS PENGAJUAN --}}
                <li>
                    <div class="sidebar-small-cap">Status Pengajuan</div>
                </li>

                {{-- Menunggu Persetujuan Admin --}}
                <li>
                    <a href="{{ route('marketing.pengajuan.antrian.admin') }}"
                        class="dropdown-toggle no-arrow
                            {{ request()->routeIs('marketing.pengajuan.antrian.admin') ? 'active' : '' }}">
                        <span class="micon dw dw-hourglass"></span>
                        <span class="mtext">Menunggu Admin</span>
                    </a>
                </li>

                {{-- Revisi --}}
                <li>
                    <a href="{{ route('marketing.pengajuan.data.revisi') }}"
                        class="dropdown-toggle no-arrow
                            {{ request()->routeIs('marketing.pengajuan.data.revisi') ? 'active' : '' }}">
                        <span class="micon dw dw-refresh"></span>
                        <span class="mtext">Perlu Revisi</span>
                        @php
                            $revisi = \App\Models\Pengajuan::where('status', 'revisi')
                                ->where('marketing_id', auth()->id())
                                ->count();
                        @endphp
                        @if($revisi > 0)
                            <span class="badge badge-danger badge-pill float-right">
                                {{ $revisi }}
                            </span>
                        @endif
                    </a>
                </li>

                {{-- Ditolak --}}
                <li>
                    <a href="{{ route('marketing.pengajuan.ditolak') }}"
                        class="dropdown-toggle no-arrow
                            {{ request()->routeIs('marketing.pengajuan.ditolak') ? 'active' : '' }}">
                        <span class="micon dw dw-cancel"></span>
                        <span class="mtext">Ditolak</span>
                    </a>
                </li>

                {{-- RIWAYAT LENGKAP --}}
                <li>
                    <div class="sidebar-small-cap">Riwayat</div>
                </li>

                <li>
                    <a href="{{ route('marketing.pengajuan.semua') }}"
                        class="dropdown-toggle no-arrow
                            {{ request()->routeIs('marketing.pengajuan.semua') ? 'active' : '' }}">
                        <span class="micon dw dw-file1"></span>
                        <span class="mtext">Semua Pengajuan</span>
                    </a>
                </li>

                {{-- LAPORAN MARKETING --}}
                <li>
                    <div class="sidebar-small-cap">Laporan</div>
                </li>

                <li>
                    <a href="{{ route('marketing.laporan.kinerja') }}"
                        class="dropdown-toggle no-arrow
                            {{ request()->routeIs('marketing.laporan.kinerja') ? 'active' : '' }}">
                        <span class="micon dw dw-bar-chart"></span>
                        <span class="mtext">Laporan Kinerja</span>
                    </a>
                </li>

            </ul>
        </div>
    </div>
</div>

<div class="mobile-menu-overlay"></div>

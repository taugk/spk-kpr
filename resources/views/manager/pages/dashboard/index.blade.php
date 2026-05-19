@extends('manager.layouts.app')

@section('title', 'Dashboard Manajer')

@section('breadcrumb')
<li class="breadcrumb-item active"><a href="#"><i class="fa fa-home"></i> Dashboard</a></li>
@endsection

@section('content')

<div class="row clearfix">
    <div class="col-12">
        <div class="card-box mb-30 pd-20 bg-primary text-white">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h4 class="text-white mb-2">
                        Selamat Datang, {{ Auth::user()->nama_lengkap ?? 'Manajer' }}!
                    </h4>
                    <p class="mb-0 text-white">
                        Berikut adalah ringkasan kinerja sistem KPR hari ini.
                    </p>
                </div>
                <div class="col-md-4 text-right d-none d-md-block">
                    <i class="dw dw-analytics-21" style="font-size: 70px; opacity: .35;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row clearfix">
   <div class="col-xl-3 col-lg-6 col-md-6 mb-30">
    <div class="card-box height-100-p pd-20">
        <div class="d-flex justify-content-between align-items-start">

            <div class="pr-3">
                <h3 class="font-weight-bold text-dark mb-2">
                    {{ number_format($stats['total_pengajuan']) }}
                </h3>

                <div class="text-secondary mb-2">
                    Total Pengajuan
                </div>


            </div>

            <div class="bg-light rounded-lg p-3">
                <i class="dw dw-file text-primary" style="font-size: 24px;"></i>
            </div>

        </div>
    </div>
</div>

    <div class="col-xl-3 col-lg-6 col-md-6 mb-30">
    <div class="card-box height-100-p pd-20">
        <div class="d-flex justify-content-between align-items-start">

            <div class="pr-3">
                <h3 class="font-weight-bold text-dark mb-2">
                    {{ number_format($stats['pengajuan_bulan_ini']) }}
                </h3>

                <div class="text-secondary mb-2">
                    Pengajuan Bulan Ini
                </div>


            </div>

            <div class="bg-light rounded-lg p-3">
                <i class="fa fa-calendar-check-o text-success" style="font-size: 24px;"></i>
            </div>

        </div>
    </div>
</div>

<div class="col-xl-3 col-lg-6 col-md-6 mb-30">
    <div class="card-box height-100-p pd-20">
        <div class="d-flex justify-content-between align-items-start">

            <div class="pr-3">
                <h3 class="font-weight-bold text-dark mb-2">
                    {{ number_format($stats['approval_rate'], 2) }}%
                </h3>

                <div class="text-secondary mb-2">
                    Approval Rate
                </div>


            </div>

            <div class="bg-light rounded-lg p-3">
                <i class="fa fa-line-chart text-info" style="font-size: 24px;"></i>
            </div>

        </div>
    </div>
</div>

<div class="col-xl-3 col-lg-6 col-md-6 mb-30">
    <div class="card-box height-100-p pd-20">
        <div class="d-flex justify-content-between align-items-start">

            <div class="pr-3">
                <h3 class="font-weight-bold text-dark mb-2">
                    {{ number_format($stats['rata_rata_waktu_proses'], 1) }} hari
                </h3>

                <div class="text-secondary mb-2">
                    Rata-rata Proses
                </div>


            </div>

            <div class="bg-light rounded-lg p-3">
                <i class="dw dw-wall-clock text-warning" style="font-size: 24px;"></i>
            </div>

        </div>
    </div>
</div>
</div>

<div class="row clearfix">
    <div class="col-xl-8 mb-30">
        <div class="card-box height-100-p pd-20">
            <div class="d-flex justify-content-between align-items-center mb-20">
                <h5 class="h5 mb-0">
                    <i class="dw dw-line-chart mr-2"></i> Tren Pengajuan 7 Hari Terakhir
                </h5>
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-toggle="dropdown">
                        <i class="fa fa-download"></i> Export
                    </button>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item" href="#" onclick="exportChart('excel')">
                            <i class="fa fa-file-excel-o text-success"></i> Excel
                        </a>
                        <a class="dropdown-item" href="#" onclick="exportChart('pdf')">
                            <i class="fa fa-file-pdf-o text-danger"></i> PDF
                        </a>
                        <a class="dropdown-item" href="#" onclick="exportChart('png')">
                            <i class="fa fa-image text-primary"></i> PNG
                        </a>
                    </div>
                </div>
            </div>
            <div id="trendChart" style="height: 350px;"></div>
        </div>
    </div>

    <div class="col-xl-4 mb-30">
        <div class="card-box height-100-p pd-20">
            <h5 class="h5 mb-20">
                <i class="dw dw-trophy mr-2"></i> Top Performance Marketing
            </h5>

            <div class="table-responsive">
                <table class="table table-hover nowrap">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Marketing</th>
                            <th>Total</th>
                            <th>Rate</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($marketingStats as $index => $marketing)
                        <tr>
                           <td>
                                @if($index == 0)
                                    <!-- Juara 1: Piala Emas -->
                                    <i class="fa fa-trophy text-warning" style="font-size: 18px;"></i>
                                @elseif($index == 1)
                                    <!-- Juara 2: Bintang Perak -->
                                    <i class="fa fa-star text-secondary" style="font-size: 16px;"></i>
                                @elseif($index == 2)
                                    <!-- Juara 3: Bintang Perunggu -->
                                    <i class="fa fa-star" style="color: #cd7f32; font-size: 16px;"></i>
                                @else
                                    <!-- Peringkat 4 ke bawah: Angka Biasa -->
                                    <span class="text-muted pl-1">{{ $index + 1 }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="user-info-dropdown">
                                    <span class="user-icon">
                                        <i class="dw dw-user1"></i>
                                    </span>
                                    <span class="user-name">
                                        <strong>{{ $marketing->nama_lengkap ?? $marketing->name }}</strong><br>
                                        <small class="text-muted">{{ $marketing->email }}</small>
                                    </span>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-primary">{{ $marketing->total ?? 0 }}</span>
                                <small class="d-block text-muted">{{ $marketing->disetujui ?? 0 }} disetujui</small>
                            </td>
                            <td>
                                <div class="progress mb-1" style="height: 6px;">
                                    <div class="progress-bar bg-success" style="width: {{ $marketing->rate }}%"></div>
                                </div>
                                <small>{{ number_format($marketing->rate, 1) }}%</small>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
                                <i class="dw dw-user-13 d-block mb-2" style="font-size: 40px;"></i>
                                Belum ada data marketing
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row clearfix">
    <div class="col-xl-4 mb-30">
        <div class="card-box height-100-p pd-20">
            <h5 class="h5 mb-20">
                <i class="dw dw-notification mr-2"></i> Notifikasi Penting
            </h5>

            <div class="list-group list-group-flush">
                @if($notifications['pengajuan_baru'] > 0)
                <div class="list-group-item px-0">
                    <div class="media">
                        <div class="mr-3">
                            <span class="badge badge-warning badge-pill p-3">
                                <i class="fa fa-file-import text-white"></i>
                            </span>
                        </div>
                        <div class="media-body">
                            <h6>Pengajuan Baru</h6>
                            <p class="mb-1">{{ $notifications['pengajuan_baru'] }} pengajuan baru menunggu verifikasi</p>
                            <small class="text-muted">{{ now()->diffForHumans() }}</small>
                        </div>
                    </div>
                </div>
                @endif

                @if($notifications['verifikasi_tertunda'] > 0)
                <div class="list-group-item px-0">
                    <div class="media">
                        <div class="mr-3">
                            <span class="badge badge-danger badge-pill p-3">
                                <i class="fa fa-hourglass-start text-white"></i>
                            </span>
                        </div>
                        <div class="media-body">
                            <h6>Verifikasi Tertunda</h6>
                            <p class="mb-1">{{ $notifications['verifikasi_tertunda'] }} pengajuan verifikasi tertunda > 3 hari</p>
                            <small class="text-danger">Perlu tindakan segera</small>
                        </div>
                    </div>
                </div>
                @endif

                @if($notifications['antrian_admin'] > 0)
                <div class="list-group-item px-0">
                    <div class="media">
                        <div class="mr-3">
                            <span class="badge badge-info badge-pill p-3">
                                <i class="fa fa-hourglass-half text-white"></i>
                            </span>
                        </div>
                        <div class="media-body">
                            <h6>Antrian Admin</h6>
                            <p class="mb-1">{{ $notifications['antrian_admin'] }} pengajuan menunggu penilaian admin</p>
                            <small class="text-muted">Antrian perlu diproses</small>
                        </div>
                    </div>
                </div>
                @endif

                @if($notifications['penilaian_tertunda'] > 0)
                <div class="list-group-item px-0">
                    <div class="media">
                        <div class="mr-3">
                            <span class="badge badge-warning badge-pill p-3">
                                <i class="fa fa-line-chart text-white"></i>
                            </span>
                        </div>
                        <div class="media-body">
                            <h6>Penilaian Tertunda</h6>
                            <p class="mb-1">{{ $notifications['penilaian_tertunda'] }} penilaian admin belum selesai > 5 hari</p>
                            <small class="text-danger">Segera selesaikan penilaian</small>
                        </div>
                    </div>
                </div>
                @endif

                @if($notifications['pengajuan_baru'] == 0 && $notifications['verifikasi_tertunda'] == 0 && $notifications['antrian_admin'] == 0 && $notifications['penilaian_tertunda'] == 0)
                <div class="text-center py-4">
                    <i class="dw dw-checked d-block text-success mb-2" style="font-size: 45px;"></i>
                    <p class="mb-0">Semua sistem berjalan normal</p>
                    <small class="text-muted">Tidak ada notifikasi penting</small>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-xl-8 mb-30">
        <div class="card-box height-100-p pd-20">
            <div class="d-flex justify-content-between align-items-center mb-20">
                <h5 class="h5 mb-0">
                    <i class="dw dw-time-management mr-2"></i> Pengajuan Terbaru
                </h5>
                <a href="{{ route('manager.pengajuan.semua') }}" class="btn btn-sm btn-primary">
                    Lihat Semua <i class="fa fa-arrow-right ml-1"></i>
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-hover nowrap" id="recentTable">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Debitur</th>
                            <th>Jumlah Pinjaman</th>
                            <th>Marketing</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pengajuanTerbaru as $pengajuan)
                        <tr>
                            <td>
                                <strong>{{ $pengajuan->kode_pengajuan }}</strong><br>
                                <small class="text-muted">#{{ $pengajuan->id }}</small>
                            </td>
                            <td>
                                <strong>{{ $pengajuan->user->nama_lengkap ?? '-' }}</strong><br>
                                <small class="text-muted">{{ $pengajuan->user->email ?? '-' }}</small>
                            </td>
                            <td>Rp {{ number_format($pengajuan->jumlah_pinjaman ?? 0, 0, ',', '.') }}</td>
                            <td>{{ $pengajuan->marketing->nama_lengkap ?? '-' }}</td>
                            <td>{!! $pengajuan->status_badge !!}</td>
                            <td>
                                {{ optional($pengajuan->created_at)->format('d/m/Y') ?? '-' }}<br>
                                <small class="text-muted">{{ optional($pengajuan->created_at)->format('H:i') ?? '-' }}</small>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" data-toggle="dropdown">
                                        Aksi
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item" href="{{ route('manager.pengajuan.show', $pengajuan->id) }}">
                                            <i class="fa fa-eye"></i> Detail
                                        </a>
                                        @if($pengajuan->penilaian)
                                        <a class="dropdown-item" href="{{ route('manager.penilaian.show', $pengajuan->penilaian->id) }}">
                                            <i class="fa fa-star"></i> Lihat Penilaian
                                        </a>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="dw dw-inbox d-block mb-2" style="font-size: 45px;"></i>
                                Belum ada pengajuan
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row clearfix">
    <div class="col-xl-3 col-md-6 mb-30">
        <div class="card-box height-100-p pd-20">
            <div class="d-flex justify-content-between">
                <div>
                    <p class="text-muted mb-1">Pengajuan Minggu Ini</p>
                    <h4>{{ number_format($stats['pengajuan_minggu_ini']) }}</h4>
                </div>
                <div class="h1 text-primary">
                    <i class="dw dw-calendar-7"></i>
                </div>
            </div>
            <div class="progress mt-3" style="height: 6px;">
                <div class="progress-bar bg-primary" style="width: {{ ($stats['pengajuan_minggu_ini'] / max($stats['pengajuan_bulan_ini'], 1)) * 100 }}%"></div>
            </div>
            <small class="text-muted">{{ number_format(($stats['pengajuan_minggu_ini'] / max($stats['pengajuan_bulan_ini'], 1)) * 100, 1) }}% dari total bulan ini</small>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-30">
        <div class="card-box height-100-p pd-20">
            <div class="d-flex justify-content-between">
                <div>
                    <p class="text-muted mb-1">Rata-rata Skor</p>
                    <h4>{{ number_format($stats['rata_rata_skor'] ?? 0, 2) }}%</h4>
                </div>
                <div class="h1 text-success">
                    <i class="dw dw-star"></i>
                </div>
            </div>
            <div class="progress mt-3" style="height: 6px;">
                <div class="progress-bar bg-success" style="width: {{ $stats['rata_rata_skor'] ?? 0 }}%"></div>
            </div>
            <small class="text-muted">Target kelulusan: 75%</small>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-30">
        <div class="card-box height-100-p pd-20">
            <div class="d-flex justify-content-between">
                <div>
                    <p class="text-muted mb-1">Total Marketing Aktif</p>
                    <h4>{{ $marketingStats->count() }}</h4>
                    <a href="{{ route('manager.kinerja.marketing') }}" class="text-info">
                        Lihat detail <i class="fa fa-arrow-right"></i>
                    </a>
                </div>
                <div class="h1 text-info">
                    <i class="dw dw-group"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-30">
        <div class="card-box height-100-p pd-20">
            <div class="d-flex justify-content-between">
                <div>
                    <p class="text-muted mb-1">Total Admin Aktif</p>
                    <h4>{{ $adminStats->count() ?? 0 }}</h4>
                    <a href="{{ route('manager.kinerja.admin') }}" class="text-warning">
                        Lihat detail <i class="fa fa-arrow-right"></i>
                    </a>
                </div>
                <div class="h1 text-warning">
                    <i class="dw dw-user-12"></i>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .card-box,
    .card {
        border: 0;
        border-radius: 10px;
        box-shadow: 0 0 28px rgba(0,0,0,.08);
    }

    .welcome-card {
        border-radius: 12px;
        padding: 28px;
        background: linear-gradient(135deg, #1b00ff 0%, #00e0ff 100%);
        box-shadow: 0 8px 25px rgba(27,0,255,.25);
    }

    .card-stats {
        transition: .25s ease;
        margin-bottom: 30px;
    }

    .card-stats:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 32px rgba(0,0,0,.12);
    }

    .card-stats .card-body,
    .card .card-body {
        padding: 22px 26px;
    }

    .card-category {
        font-size: 13px;
        color: #6c757d;
        margin-bottom: 8px;
        font-weight: 500;
    }

    .card-title {
        font-weight: 700;
        color: #131e22;
        margin-bottom: 4px;
        padding-left: 10px;
    }

    .icon-big {
        width: 58px;
        height: 58px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        background: #f4f6ff;
    }

    .icon-primary {
        color: #1b00ff;
        background: rgba(27,0,255,.1);
    }

    .icon-success {
        color: #28a745;
        background: rgba(40,167,69,.12);
    }

    .icon-info {
        color: #17a2b8;
        background: rgba(23,162,184,.12);
    }

    .icon-warning {
        color: #ffc107;
        background: rgba(255,193,7,.16);
    }

    .card .card-header {
        background: #fff;
        border-bottom: 1px solid #edf0f5;
        padding: 18px 22px;
        border-radius: 10px 10px 0 0;
    }

    .card .card-header .card-title {
        font-size: 16px;
        font-weight: 700;
        margin-bottom: 0;
        color: #131e22;
    }

    .table {
        margin-bottom: 0;
    }

    .table thead th {
        border-top: 0;
        border-bottom: 1px solid #edf0f5;
        background: #f8f9fc;
        color: #475569;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: .3px;
        font-weight: 700;
        white-space: nowrap;
    }

    .table tbody td {
        vertical-align: middle;
        border-top: 1px solid #f0f2f5;
        color: #334155;
    }

    .table-hover tbody tr:hover {
        background: #f8f9ff;
    }

    .avatar {
        width: 42px;
        height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .avatar-initial {
        width: 42px;
        height: 42px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: #f1f5f9;
    }

    .list-group-item {
        border-left: 0;
        border-right: 0;
        padding: 18px 22px;
    }

    .list-group-flush .list-group-item:first-child {
        border-top: 0;
    }

    .list-group-flush .list-group-item:last-child {
        border-bottom: 0;
    }

    .badge {
        padding: 6px 10px;
        border-radius: 20px;
        font-weight: 600;
    }

    .progress {
        border-radius: 20px;
        background: #edf2f7;
        overflow: hidden;
    }

    .progress-bar {
        border-radius: 20px;
    }

    .btn {
        border-radius: 7px;
        font-weight: 500;
    }

    .btn-sm {
        padding: 6px 12px;
        font-size: 12px;
    }

    .btn-icon {
        width: 34px;
        height: 34px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
    }

    .dropdown-menu {
        border: 0;
        border-radius: 10px;
        box-shadow: 0 10px 35px rgba(0,0,0,.12);
        padding: 8px;
    }

    .dropdown-item {
        border-radius: 7px;
        padding: 8px 12px;
        font-size: 13px;
    }

    .dropdown-item i {
        width: 18px;
        margin-right: 6px;
    }

    #trendChart {
        min-height: 350px;
    }

    .opacity-50 {
        opacity: .5;
    }

    .numbers small {
        font-size: 12px;
    }

    a {
        font-weight: 500;
    }

    @media (max-width: 768px) {
        .welcome-card {
            padding: 22px;
            text-align: center;
        }

        .welcome-card h3 {
            font-size: 22px;
        }

        .card-stats .card-body,
        .card .card-body {
            padding: 18px;
        }

        .icon-big {
            width: 48px;
            height: 48px;
            font-size: 20px;
        }

        .card-title {
            font-size: 18px;
        }

        .table-responsive {
            border: 0;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.42.0/dist/apexcharts.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        $('#recentTable').DataTable({
            pageLength: 5,
            order: [[5, 'desc']],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
            },
            responsive: true,
            autoWidth: false
        });

        loadTrendChart();
    });

    function loadTrendChart() {
        const chartData = @json($chartData);

        const options = {
            series: [
                {
                    name: 'Pengajuan',
                    type: 'column',
                    data: chartData.map(item => item.pengajuan)
                },
                {
                    name: 'Disetujui',
                    type: 'column',
                    data: chartData.map(item => item.disetujui)
                }
            ],
            chart: {
                height: 350,
                type: 'line',
                toolbar: {
                    show: true,
                    tools: {
                        download: true,
                        selection: true,
                        zoom: true,
                        zoomin: true,
                        zoomout: true,
                        pan: true,
                        reset: true
                    }
                },
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 800
                }
            },
            colors: ['#0d6efd', '#198754'],
            stroke: {
                width: [0, 0, 3],
                curve: 'smooth'
            },
            plotOptions: {
                bar: {
                    columnWidth: '50%',
                    borderRadius: 5,
                    distributed: false,
                    dataLabels: {
                        position: 'top'
                    }
                }
            },
            dataLabels: {
                enabled: true,
                offsetY: -20,
                style: {
                    fontSize: '11px',
                    colors: ["#304758"]
                },
                formatter: function(val) {
                    return val.toLocaleString();
                }
            },
            xaxis: {
                categories: chartData.map(item => item.tanggal),
                title: {
                    text: 'Tanggal',
                    style: {
                        fontSize: '12px',
                        fontWeight: 500
                    }
                },
                labels: {
                    rotate: -45,
                    rotateAlways: false
                }
            },
            yaxis: {
                title: {
                    text: 'Jumlah Pengajuan',
                    style: {
                        fontSize: '12px',
                        fontWeight: 500
                    }
                },
                labels: {
                    formatter: function(val) {
                        return val.toLocaleString();
                    }
                },
                min: 0
            },
            tooltip: {
                shared: true,
                intersect: false,
                y: {
                    formatter: function(val) {
                        return val.toLocaleString() + ' pengajuan';
                    }
                }
            },
            legend: {
                position: 'top',
                horizontalAlign: 'center',
                offsetY: 0
            },
            grid: {
                borderColor: '#f1f1f1',
                row: {
                    colors: ['transparent', 'transparent'],
                    opacity: 0.5
                }
            },
            responsive: [{
                breakpoint: 768,
                options: {
                    plotOptions: {
                        bar: {
                            columnWidth: '70%'
                        }
                    },
                    dataLabels: {
                        enabled: false
                    }
                }
            }]
        };

        const chart = new ApexCharts(document.querySelector("#trendChart"), options);
        chart.render();
    }

    function exportChart(format) {
        const chart = ApexCharts.getChartByID("trendChart");

        if (format === 'excel') {
            const data = @json($chartData);
            let csv = 'Tanggal,Pengajuan,Disetujui\n';
            data.forEach(item => {
                csv += `${item.tanggal},${item.pengajuan},${item.disetujui}\n`;
            });
            const blob = new Blob([csv], { type: 'text/csv' });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = `trend_pengajuan_${new Date().toISOString().split('T')[0]}.csv`;
            link.click();
            URL.revokeObjectURL(link.href);

            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Data berhasil diekspor ke Excel',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
        } else {
            chart.dataURI().then(({ imgURI }) => {
                const link = document.createElement('a');
                link.download = `trend_chart_${new Date().toISOString().split('T')[0]}.png`;
                link.href = imgURI;
                link.click();

                if (format === 'pdf') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Chart berhasil diekspor',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                    });
                }
            });
        }
    }
</script>
@endpush

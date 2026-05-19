@extends('manager.layouts.app')

@section('title', 'Kinerja Admin Penilai')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('manager.dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="#">Kinerja</a></li>
<li class="breadcrumb-item active">Kinerja Admin</li>
@endsection

@section('content')
<!-- Header Section Banner Ala DeskApp -->
<div class="page-header mb-4">
    <div class="row align-items-center">
        <div class="col-md-6 col-sm-12">
            <div class="title">
                <h4>Kinerja Admin Penilai</h4>
            </div>
            <nav aria-label="breadcrumb" role="navigation">
                <ol class="breadcrumb mb-0">
                    @yield('breadcrumb')
                </ol>
            </nav>
        </div>
        <div class="col-md-6 col-sm-12 text-md-right d-none d-md-block">
            <span class="text-muted">Monitoring dan evaluasi kinerja admin dalam melakukan penilaian KPR</span>
        </div>
    </div>
</div>

<!-- Filter Section DeskApp Forms -->
<div class="card-box p-4 mb-4">
    <div class="h5 mb-3 font-16 text-blue"><i class="fa fa-filter mr-2"></i>Filter Periode & Aksi</div>
    <div class="row align-items-end">
        <div class="col-md-3 col-sm-12 form-group mb-md-0">
            <label class="weight-600">Periode</label>
            <select id="periodeFilter" class="custom-select form-control">
                <option value="harian" {{ $periode == 'harian' ? 'selected' : '' }}>Harian</option>
                <option value="mingguan" {{ $periode == 'mingguan' ? 'selected' : '' }}>Mingguan</option>
                <option value="bulanan" {{ $periode == 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                <option value="tahunan" {{ $periode == 'tahunan' ? 'selected' : '' }}>Tahunan</option>
            </select>
        </div>
        <div class="col-md-3 col-sm-6 form-group mb-md-0">
            <label class="weight-600">Tanggal Mulai</label>
            <input type="date" id="dateFrom" class="form-control">
        </div>
        <div class="col-md-3 col-sm-6 form-group mb-md-0">
            <label class="weight-600">Tanggal Selesai</label>
            <input type="date" id="dateTo" class="form-control">
        </div>
        <div class="col-md-3 col-sm-12 mb-0">
            <div class="row">
                <div class="col-6 pr-1">
                    <button class="btn btn-primary btn-block btn-sm pt-2 pb-2" onclick="applyFilter()">
                        <i class="fa fa-search mr-1"></i> Filter
                    </button>
                </div>
                <div class="col-6 pl-1">
                    <button class="btn btn-success btn-block btn-sm pt-2 pb-2" onclick="exportKinerja()">
                        <i class="fa fa-file-excel-o mr-1"></i> Export
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Summary Cards DeskApp Mini Widget Style -->
<div class="row mb-4">
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-3">
        <div class="card-box p-3 d-flex align-items-center justify-content-between" style="min-height: 90px;">
            <div class="w-75">
                <p class="text-muted weight-500 mb-1">Total Admin</p>
                <h4 class="mb-0 weight-700 font-22 text-dark" id="totalAdmin">{{ $admins->count() }}</h4>
            </div>
            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                <i class="fa fa-users text-primary font-20"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-3">
        <div class="card-box p-3 d-flex align-items-center justify-content-between" style="min-height: 90px;">
            <div class="w-75">
                <p class="text-muted weight-500 mb-1">Total Penilaian</p>
                <h4 class="mb-0 weight-700 font-22 text-success" id="totalPenilaian">{{ $admins->sum(fn($a) => $a->statistik['total_penilaian']) }}</h4>
            </div>
            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                <i class="fa fa-bar-chart text-success font-20"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-3">
        <div class="card-box p-3 d-flex align-items-center justify-content-between" style="min-height: 90px;">
            <div class="w-75">
                <p class="text-muted weight-500 mb-1">Rata-rata Skor</p>
                <h4 class="mb-0 weight-700 font-22 text-info" id="rataSkor">
                    {{ number_format($admins->avg(fn($a) => $a->statistik['rata_rata_skor']), 2) }}
                </h4>
            </div>
            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                <i class="fa fa-star text-info font-20"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-3">
        <div class="card-box p-3 d-flex align-items-center justify-content-between" style="min-height: 90px;">
            <div class="w-75">
                <p class="text-muted weight-500 mb-1">Rata-rata Performa</p>
                <h4 class="mb-0 weight-700 font-22 text-warning" id="rataPerforma">
                    {{ number_format($admins->avg(fn($a) => $a->statistik['total_penilaian'] > 0 ? ($a->statistik['penilaian_cepat'] / $a->statistik['total_penilaian']) * 100 : 0), 1) }}%
                </h4>
            </div>
            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                <i class="fa fa-tachometer text-warning font-20"></i>
            </div>
        </div>
    </div>
</div>

<!-- Leaderboard & Chart Section -->
<div class="row mb-4">
    <div class="col-xl-8 col-lg-12 mb-4">
        <div class="card-box p-4" style="min-height: 490px;">
            <h5 class="h4 text-blue mb-4"><i class="fa fa-bar-chart mr-2"></i>Perbandingan Kinerja Admin</h5>
            <div id="adminChart" style="height: 380px;"></div>
        </div>
    </div>
    <div class="col-xl-4 col-lg-12 mb-4">
        <div class="card-box p-4" style="min-height: 490px;">
            <h5 class="h4 text-blue mb-4"><i class="fa fa-trophy mr-2"></i>Leaderboard</h5>
            <div class="leaderboard-list">
                @foreach($ranking->take(5) as $index => $admin)
                <div class="d-flex align-items-center mb-3 p-2 border-dashed-bottom {{ $index == 0 ? 'bg-gold-light' : '' }}">
                    <div class="mr-3" style="width: 40px; text-center">
                        @if($index == 0)
                            <i class="fa fa-trophy text-warning font-24"></i>
                        @elseif($index == 1)
                            <i class="fa fa-star text-secondary font-22"></i>
                        @elseif($index == 2)
                            <i class="fa fa-star" style="color: #cd7f32; font-size: 1.3rem;"></i>
                        @else
                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                <span class="weight-700 font-14">{{ $index + 1 }}</span>
                            </div>
                        @endif
                    </div>
                    <div class="w-50 pr-2">
                        <h6 class="mb-0 font-14 weight-600 text-dark text-truncate">{{ $admin->nama_lengkap ?? $admin->name }}</h6>
                        <small class="text-muted d-block text-truncate">{{ $admin->email }}</small>
                    </div>
                    <div class="w-25 text-right ml-auto">
                        <div class="weight-700 text-dark">{{ number_format($admin->statistik['total_penilaian']) }}</div>
                        <small class="text-muted font-12">proses</small>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Tabel Kinerja Admin Detail -->
<div class="card-box mb-30">
    <div class="pd-20">
        <h5 class="text-blue h4"><i class="fa fa-table mr-2"></i>Detail Kinerja Admin</h5>
    </div>
    <div class="pb-20">
        <div class="table-responsive px-3">
            <table class="table table-hover nowrap" id="adminTable" width="100%">
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Nama Admin</th>
                        <th>Total Penilaian</th>
                        <th>Rata-rata Skor</th>
                        <th>Penilaian Cepat</th>
                        <th>Penilaian Lambat</th>
                        <th>Performa</th>
                        <th class="datatable-nosort">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ranking as $index => $admin)
                    <tr>
                        <td>
                            @if($index == 0)
                                <span class="badge badge-warning text-white font-12 px-2 py-1">🏆 1</span>
                            @elseif($index == 1)
                                <span class="badge badge-secondary font-12 px-2 py-1">🥈 2</span>
                            @elseif($index == 2)
                                <span class="badge text-white font-12 px-2 py-1" style="background-color: #cd7f32;">🥉 3</span>
                            @else
                                <span class="badge badge-light font-12 px-2 py-1 text-dark border">{{ $index + 1 }}</span>
                            @endif
                        </td>
                        <td>
                            <div class="user-info-dropdown">
                                <span class="user-icon bg-light rounded-circle d-inline-flex align-items-center justify-content-center mr-2" style="width: 32px; height: 32px;">
                                    <i class="ti-user text-primary"></i>
                                </span>
                                <span class="user-name d-inline-block vertical-align-middle">
                                    <div class="weight-600 text-dark">{{ $admin->nama_lengkap ?? $admin->name }}</div>
                                    <small class="text-muted">{{ $admin->email }}</small>
                                </span>
                            </div>
                        </td>
                        <td>
                            <span class="weight-700 text-dark">{{ number_format($admin->statistik['total_penilaian']) }}</span>
                            <div class="progress mt-1" style="height: 4px;">
                                <div class="progress-bar bg-primary" style="width: {{ min(100, ($admin->statistik['total_penilaian'] / max($ranking->first()->statistik['total_penilaian'], 1)) * 100) }}%"></div>
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-info font-12 px-2">{{ number_format($admin->statistik['rata_rata_skor'], 2) }}</span>
                            <div class="progress mt-1" style="height: 4px;">
                                <div class="progress-bar bg-info" style="width: {{ $admin->statistik['rata_rata_skor'] }}%"></div>
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-success font-12 px-2">{{ number_format($admin->statistik['penilaian_cepat']) }}</span>
                            <small class="text-muted d-block mt-1">(< 24 jam)</small>
                        </td>
                        <td>
                            <span class="badge badge-warning font-12 px-2">{{ number_format($admin->statistik['penilaian_lambat']) }}</span>
                            <small class="text-muted d-block mt-1">(> 7 hari)</small>
                        </td>
                        <td>
                            @php
                                $performance = $admin->statistik['total_penilaian'] > 0
                                    ? ($admin->statistik['penilaian_cepat'] / $admin->statistik['total_penilaian']) * 100
                                    : 0;
                                $performanceClass = $performance >= 70 ? 'success' : ($performance >= 50 ? 'warning' : 'danger');
                            @endphp
                            <div class="d-flex align-items-center" style="min-width: 120px;">
                                <div class="w-75 mr-2">
                                    <div class="progress mb-0" style="height: 6px;">
                                        <div class="progress-bar bg-{{ $performanceClass }}" style="width: {{ $performance }}%"></div>
                                    </div>
                                </div>
                                <div class="w-25 text-right">
                                    <span class="weight-700 text-dark font-13">{{ number_format($performance, 1) }}%</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <button onclick="viewDetail({{ $admin->id }})" class="btn btn-sm btn-outline-primary pt-1 pb-1 px-2">
                                <i class="fa fa-line-chart"></i> Detail
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .leaderboard-list {
        max-height: 400px;
        overflow-y: auto;
    }
    .bg-gold-light {
        background-color: rgba(255, 193, 7, 0.08) !important;
        border-radius: 6px;
    }
    .border-dashed-bottom {
        border-bottom: 1px dashed #e0e0e0;
        padding-bottom: 10px;
    }
    .border-dashed-bottom:last-child {
        border-bottom: none;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.42.0/dist/apexcharts.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let adminChart;

    $(document).ready(function() {
        $('#adminTable').DataTable({
            pageLength: 10,
            order: [[2, 'desc']],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
            },
            responsive: true,
            searching: true,
            paging: true,
            info: true,
            columnDefs: [{
                targets: "datatable-nosort",
                orderable: false,
            }]
        });

        loadAdminChart();
    });

    function loadAdminChart() {
        const adminData = @json($ranking);

        const options = {
            series: [
                {
                    name: 'Total Penilaian',
                    data: adminData.map(a => a.statistik.total_penilaian)
                },
                {
                    name: 'Penilaian Cepat',
                    data: adminData.map(a => a.statistik.penilaian_cepat)
                },
                {
                    name: 'Rata-rata Skor',
                    data: adminData.map(a => a.statistik.rata_rata_skor)
                }
            ],
            chart: {
                type: 'bar',
                height: 380,
                toolbar: { show: true },
                stacked: false
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '55%',
                    borderRadius: 5,
                    endingShape: 'rounded'
                }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            xaxis: {
                categories: adminData.map(a => a.nama_lengkap || a.name),
                title: {
                    text: 'Admin'
                },
                labels: {
                    rotate: -45,
                    style: {
                        fontSize: '11px'
                    }
                }
            },
            yaxis: [
                {
                    title: {
                        text: 'Jumlah Penilaian'
                    },
                    min: 0
                },
                {
                    opposite: true,
                    title: {
                        text: 'Rata-rata Skor (%)'
                    },
                    min: 0,
                    max: 100,
                    labels: {
                        formatter: function(val) {
                            return val + '%';
                        }
                    }
                }
            ],
            colors: ['#0d6efd', '#198754', '#ffc107'],
            fill: {
                opacity: 1
            },
            tooltip: {
                y: {
                    formatter: function(val, { seriesIndex }) {
                        if (seriesIndex === 2) return val.toFixed(2) + '%';
                        return val.toLocaleString();
                    }
                }
            },
            legend: {
                position: 'top',
                horizontalAlign: 'center'
            },
            responsive: [{
                breakpoint: 768,
                options: {
                    plotOptions: {
                        bar: {
                            columnWidth: '70%'
                        }
                    }
                }
            }]
        };

        adminChart = new ApexCharts(document.querySelector("#adminChart"), options);
        adminChart.render();
    }

    function applyFilter() {
        let periode = $('#periodeFilter').val();
        let dateFrom = $('#dateFrom').val();
        let dateTo = $('#dateTo').val();

        window.location.href = '{{ route("manager.kinerja.admin") }}?periode=' + periode +
            '&tanggal_mulai=' + dateFrom + '&tanggal_selesai=' + dateTo;
    }

    function exportKinerja() {
        let periode = $('#periodeFilter').val();
        let dateFrom = $('#dateFrom').val();
        let dateTo = $('#dateTo').val();

        window.location.href = '{{ route("manager.laporan.export") }}?jenis=kinerja_admin&format=excel' +
            '&periode=' + periode + '&tanggal_mulai=' + dateFrom + '&tanggal_selesai=' + dateTo;

        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: 'Export data sedang diproses',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        });
    }

    function viewDetail(adminId) {
        let bulan = new Date().getMonth() + 1;
        let tahun = new Date().getFullYear();

        Swal.fire({
            title: 'Detail Kinerja Admin',
            html: '<div class="text-center py-3"><div class="spinner-border text-primary"></div><br><span class="mt-2 d-block text-muted">Memuat data...</span></div>',
            showConfirmButton: false,
            width: '800px'
        });

        $.get('/manager/kinerja/admin/' + adminId + '/detail', { bulan: bulan, tahun: tahun }, function(response) {
            let html = `
                <div class="row">
                    <div class="col-md-12">
                        <div class="text-center mb-4">
                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 70px; height: 70px;">
                                <i class="fa fa-user fa-3x text-primary"></i>
                            </div>
                            <h5 class="mb-1">${response.admin.nama_lengkap || response.admin.name}</h5>
                            <p class="text-muted mb-0">${response.admin.email}</p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <div class="border rounded p-3 text-center bg-white shadow-sm">
                            <h6 class="text-muted font-14 mb-1">Total Penilaian</h6>
                            <h3 class="text-primary mb-0 weight-700">${response.total_penilaian.toLocaleString()}</h3>
                        </div>
                    </div>
                    <div class="col-md-4 mb-2">
                        <div class="border rounded p-3 text-center bg-white shadow-sm">
                            <h6 class="text-muted font-14 mb-1">Rata-rata Skor</h6>
                            <h3 class="text-info mb-0 weight-700">${response.rata_rata_skor}</h3>
                        </div>
                    </div>
                    <div class="col-md-4 mb-2">
                        <div class="border rounded p-3 text-center bg-white shadow-sm">
                            <h6 class="text-muted font-14 mb-1">Performa</h6>
                            <h3 class="${response.total_penilaian > 0 && (response.penilaian_cepat / response.total_penilaian) * 100 >= 70 ? 'text-success' : 'text-warning'} mb-0 weight-700">
                                ${response.total_penilaian > 0 ? ((response.penilaian_cepat / response.total_penilaian) * 100).toFixed(1) : 0}%
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="mt-4 text-left">
                    <h6 class="font-15 mb-2 weight-600 text-dark"><i class="fa fa-calendar mr-1"></i> Penilaian Per Hari (Bulan ${bulan}/${tahun})</h6>
                    <canvas id="dailyChart" height="200"></canvas>
                </div>
                <div class="mt-4 text-left">
                    <h6 class="font-15 mb-2 weight-600 text-dark"><i class="fa fa-pie-chart mr-1"></i> Distribusi Hasil Penilaian</h6>
                    <div id="distribusiChart" style="height: 200px;"></div>
                </div>
            `;

            Swal.fire({
                title: 'Detail Kinerja Admin',
                html: html,
                width: '900px',
                showConfirmButton: true,
                confirmButtonText: 'Tutup'
            });

            // Load daily chart after modal opens
            setTimeout(() => {
                if (response.penilaian_per_hari) {
                    const ctx = document.getElementById('dailyChart')?.getContext('2d');
                    if (ctx) {
                        new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: response.penilaian_per_hari.map(d => d.tanggal),
                                datasets: [{
                                    label: 'Jumlah Penilaian',
                                    data: response.penilaian_per_hari.map(d => d.jumlah),
                                    backgroundColor: '#0d6efd',
                                    borderRadius: 5
                                }]
                            },
                            options: {
                                responsive: true,
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        ticks: { stepSize: 1 }
                                    }
                                },
                                plugins: {
                                    legend: { position: 'top' }
                                }
                            }
                        });
                    }
                }

                // Load distribusi chart
                if (response.distribusi_status && document.querySelector('#distribusiChart')) {
                    const distribusiData = response.distribusi_status;
                    const options = {
                        series: distribusiData.map(d => d.total),
                        chart: { type: 'donut', height: 180 },
                        labels: distribusiData.map(d => d.hasil === 'layak' ? 'Layak' : 'Tidak Layak'),
                        colors: ['#198754', '#dc3545'],
                        legend: { position: 'bottom' },
                        responsive: [{
                            breakpoint: 480,
                            options: { chart: { width: '100%' }, legend: { position: 'bottom' } }
                        }]
                    };
                    const chart = new ApexCharts(document.querySelector("#distribusiChart"), options);
                    chart.render();
                }
            }, 100);
        }).fail(function() {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Gagal memuat detail kinerja admin'
            });
        });
    }
</script>
@endpush

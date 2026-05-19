@extends('manager.layouts.app')

@section('title', 'Kinerja Marketing')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('manager.dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="#">Kinerja</a></li>
<li class="breadcrumb-item active">Kinerja Marketing</li>
@endsection

@section('content')
<!-- Header Banner Ala DeskApp -->
<div class="page-header mb-4">
    <div class="row align-items-center">
        <div class="col-md-6 col-sm-12">
            <div class="title">
                <h4>Kinerja Marketing</h4>
            </div>
            <nav aria-label="breadcrumb" role="navigation">
                <ol class="breadcrumb mb-0">
                    @yield('breadcrumb')
                </ol>
            </nav>
        </div>
        <div class="col-md-6 col-sm-12 text-md-right d-none d-md-block">
            <span class="text-muted">Monitoring dan evaluasi kinerja marketing dalam memproses pengajuan KPR</span>
        </div>
    </div>
</div>

<!-- Filter Section Layout DeskApp -->
<div class="card-box pd-20 mb-30">
    <div class="row align-items-end">
        <div class="col-md-3 col-sm-12 mb-3 mb-md-0">
            <div class="form-group mb-0">
                <label class="weight-600">Periode</label>
                <select id="periodeFilter" class="custom-select form-control">
                    <option value="bulanan" {{ $periode == 'bulanan' ? 'selected' : '' }}>Bulan Ini</option>
                    <option value="triwulan" {{ $periode == 'triwulan' ? 'selected' : '' }}>Triwulan Ini</option>
                    <option value="tahunan" {{ $periode == 'tahunan' ? 'selected' : '' }}>Tahun Ini</option>
                </select>
            </div>
        </div>
        <div class="col-md-3 col-sm-12 mb-3 mb-md-0">
            <div class="form-group mb-0">
                <label class="weight-600">Tanggal Mulai</label>
                <input type="date" id="dateFrom" class="form-control">
            </div>
        </div>
        <div class="col-md-3 col-sm-12 mb-3 mb-md-0">
            <div class="form-group mb-0">
                <label class="weight-600">Tanggal Selesai</label>
                <input type="date" id="dateTo" class="form-control">
            </div>
        </div>
        <div class="col-md-3 col-sm-12">
            <div class="row mx-n1">
                <div class="col-6 px-1">
                    <button class="btn btn-primary btn-block" onclick="applyFilter()">
                        <i class="fa fa-search mr-1"></i> Filter
                    </button>
                </div>
                <div class="col-6 px-1">
                    <button class="btn btn-success btn-block" onclick="exportKinerja()">
                        <i class="fa fa-file-excel-o mr-1"></i> Export
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Leaderboard Cards DeskApp Style -->
<div class="row mb-4">
    <div class="col-md-4 mb-3">
        <div class="card-box pd-20 text-center height-100-p">
            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 70px; height: 70px;">
                <i class="fa fa-trophy text-warning font-30"></i>
            </div>
            <p class="text-muted weight-500 mb-1">Terbanyak Memproses</p>
            <h5 class="mb-2 weight-700 text-dark">{{ $leaderboard['terbanyak']->name ?? '-' }}</h5>
            <div>
                <span class="badge badge-primary px-2 py-1">
                    {{ number_format($leaderboard['terbanyak']->statistik['total_pengajuan'] ?? 0) }} Pengajuan
                </span>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card-box pd-20 text-center height-100-p">
            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 70px; height: 70px;">
                <i class="fa fa-line-chart text-success font-30"></i>
            </div>
            <p class="text-muted weight-500 mb-1">Approval Rate Tertinggi</p>
            <h5 class="mb-2 weight-700 text-dark">{{ $leaderboard['tertinggi_approval']->name ?? '-' }}</h5>
            <div>
                <span class="badge badge-success px-2 py-1">
                    {{ number_format($leaderboard['tertinggi_approval']->statistik['approval_rate'] ?? 0) }}% Persetujuan
                </span>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card-box pd-20 text-center height-100-p">
            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 70px; height: 70px;">
                <i class="fa fa-dashboard text-info font-30"></i>
            </div>
            <p class="text-muted weight-500 mb-1">Proses Tercepat</p>
            <h5 class="mb-2 weight-700 text-dark">{{ $leaderboard['tercepat']->name ?? '-' }}</h5>
            <div>
                <span class="badge badge-info px-2 py-1">
                    {{ number_format($leaderboard['tercepat']->statistik['rata_rata_waktu_proses'] ?? 0) }} Hari
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Performance Chart Section -->
<div class="card-box mb-30">
    <div class="pd-20">
        <h5 class="text-blue h4"><i class="fa fa-bar-chart mr-2"></i>Perbandingan Kinerja Marketing</h5>
    </div>
    <div class="pd-20 pt-0">
        <div id="marketingChart" style="height: 450px;"></div>
    </div>
</div>

<!-- Tabel Kinerja Marketing Layout DeskApp -->
<div class="card-box mb-30">
    <div class="pd-20">
        <h5 class="text-blue h4"><i class="fa fa-table mr-2"></i>Detail Kinerja Marketing</h5>
    </div>
    <div class="pb-20">
        <div class="table-responsive px-3">
            <table class="table table-hover nowrap" id="marketingTable" width="100%">
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Marketing</th>
                        <th>Total Pengajuan</th>
                        <th>Disetujui</th>
                        <th>Ditolak</th>
                        <th>Proses</th>
                        <th>Approval Rate</th>
                        <th>Rata-rata Proses</th>
                        <th class="datatable-nosort">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ranking as $index => $marketing)
                    <tr>
                        <td>
                            @if($index == 0)
                                <span class="badge badge-warning px-2">🏆 1</span>
                            @elseif($index == 1)
                                <span class="badge badge-secondary px-2">🥈 2</span>
                            @elseif($index == 2)
                                <span class="badge px-2 text-white" style="background-color: #cd7f32;">🥉 3</span>
                            @else
                                <span class="badge badge-light text-dark px-2">{{ $index + 1 }}</span>
                            @endif
                        </td>
                        <td>
                            <div class="user-info-dropdown">
                                <span class="user-icon bg-light rounded-circle d-inline-flex align-items-center justify-content-center mr-2" style="width: 32px; height: 32px;">
                                    <i class="ti-user text-success"></i>
                                </span>
                                <span class="user-name d-inline-block vertical-align-middle">
                                    <div class="weight-600 text-dark">{{ $marketing->nama_lengkap ?? $marketing->name }}</div>
                                    <small class="text-muted">{{ $marketing->email }}</small>
                                </span>
                            </div>
                        </td>
                        <td>
                            <span class="weight-700 text-dark">{{ number_format($marketing->statistik['total_pengajuan']) }}</span>
                            <div class="progress mt-1" style="height: 4px;">
                                <div class="progress-bar bg-primary" style="width: {{ min(100, ($marketing->statistik['total_pengajuan'] / max($ranking->first()->statistik['total_pengajuan'], 1)) * 100) }}%"></div>
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-success px-2">{{ number_format($marketing->statistik['disetujui']) }}</span>
                            <div class="progress mt-1" style="height: 4px;">
                                <div class="progress-bar bg-success" style="width: {{ min(100, ($marketing->statistik['disetujui'] / max($marketing->statistik['total_pengajuan'], 1)) * 100) }}%"></div>
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-danger px-2">{{ number_format($marketing->statistik['ditolak']) }}</span>
                        </td>
                        <td>
                            <span class="badge badge-warning px-2">{{ number_format($marketing->statistik['proses']) }}</span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center" style="min-width: 100px;">
                                <div class="flex-grow-1 mr-2">
                                    <div class="progress mb-0" style="height: 8px;">
                                        <div class="progress-bar bg-success" style="width: {{ $marketing->statistik['approval_rate'] }}%"></div>
                                    </div>
                                </div>
                                <div class="flex-shrink-0">
                                    <span class="weight-700 text-dark font-12">{{ number_format($marketing->statistik['approval_rate'], 1) }}%</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-info px-2">{{ number_format($marketing->statistik['rata_rata_waktu_proses'], 1) }} hari</span>
                        </td>
                        <td>
                            <button onclick="viewPengajuan({{ $marketing->id }})" class="btn btn-sm btn-info mr-1">
                                <i class="fa fa-list"></i> Detail
                            </button>
                            <button onclick="viewDetail({{ $marketing->id }})" class="btn btn-sm btn-secondary">
                                <i class="fa fa-line-chart"></i>
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
    /* Mengatasi bentrok dropdown style jika terdeteksi komponen global */
    .dropdown-toggle::after {
        display: none !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.42.0/dist/apexcharts.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let marketingChart;

    $(document).ready(function() {
        $('#marketingTable').DataTable({
            pageLength: 10,
            order: [[6, 'desc']],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
            },
            responsive: true,
            searching: true,
            columnDefs: [{
                targets: "datatable-nosort",
                orderable: false,
            }]
        });

        loadMarketingChart();
    });

    function loadMarketingChart() {
        const marketingData = @json($ranking);

        const options = {
            series: [
                {
                    name: 'Total Pengajuan',
                    data: marketingData.map(m => m.statistik.total_pengajuan)
                },
                {
                    name: 'Disetujui',
                    data: marketingData.map(m => m.statistik.disetujui)
                },
                {
                    name: 'Approval Rate',
                    data: marketingData.map(m => m.statistik.approval_rate)
                }
            ],
            chart: {
                type: 'bar',
                height: 400,
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
                categories: marketingData.map(m => m.nama_lengkap || m.name),
                title: { text: 'Marketing' },
                labels: {
                    rotate: -45,
                    style: { fontSize: '11px' }
                }
            },
            yaxis: [
                {
                    title: { text: 'Jumlah Pengajuan' },
                    min: 0
                },
                {
                    opposite: true,
                    title: { text: 'Approval Rate (%)' },
                    min: 0,
                    max: 100,
                    labels: {
                        formatter: function(val) {
                            return val + '%';
                        }
                    }
                }
            ],
            colors: ['#1b00ff', '#28a745', '#ffc107'],
            fill: { opacity: 1 },
            tooltip: {
                y: {
                    formatter: function(val, { seriesIndex }) {
                        if (seriesIndex === 2) return val.toFixed(1) + '%';
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

        marketingChart = new ApexCharts(document.querySelector("#marketingChart"), options);
        marketingChart.render();
    }

    function applyFilter() {
        let periode = $('#periodeFilter').val();
        let dateFrom = $('#dateFrom').val();
        let dateTo = $('#dateTo').val();

        window.location.href = '{{ route("manager.kinerja.marketing") }}?periode=' + periode +
            '&tanggal_mulai=' + dateFrom + '&tanggal_selesai=' + dateTo;
    }

    function exportKinerja() {
        let periode = $('#periodeFilter').val();
        let dateFrom = $('#dateFrom').val();
        let dateTo = $('#dateTo').val();

        window.location.href = '{{ route("manager.laporan.export") }}?jenis=kinerja_marketing&format=excel' +
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

    function viewPengajuan(marketingId) {
        window.location.href = '{{ route("manager.pengajuan.semua") }}?marketing_id=' + marketingId;
    }

    function viewDetail(marketingId) {
        Swal.fire({
            title: 'Detail Kinerja Marketing',
            html: '<div class="text-center"><div class="spinner-border text-primary" role="status"></div><br><span class="mt-2 d-inline-block">Memuat data...</span></div>',
            showConfirmButton: false,
            width: '800px'
        });

        $.get('/manager/kinerja/marketing/' + marketingId + '/detail', function(response) {
            let html = `
                <div class="row">
                    <div class="col-md-12">
                        <div class="text-center mb-3">
                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 70px; height: 70px;">
                                <i class="fa fa-user font-30 text-success"></i>
                            </div>
                            <h5 class="mb-1">${response.marketing.nama_lengkap || response.marketing.name}</h5>
                            <p class="text-muted mb-0">${response.marketing.email}</p>
                        </div>
                    </div>
                </div>
                <div class="row mx-n2">
                    <div class="col-md-3 px-2 mb-2">
                        <div class="border rounded p-2 text-center bg-light">
                            <small class="text-muted d-block mb-1">Total</small>
                            <h5 class="text-primary mb-0 weight-700">${response.statistik.total_pengajuan.toLocaleString()}</h5>
                        </div>
                    </div>
                    <div class="col-md-3 px-2 mb-2">
                        <div class="border rounded p-2 text-center bg-light">
                            <small class="text-muted d-block mb-1">Disetujui</small>
                            <h5 class="text-success mb-0 weight-700">${response.statistik.disetujui.toLocaleString()}</h5>
                        </div>
                    </div>
                    <div class="col-md-3 px-2 mb-2">
                        <div class="border rounded p-2 text-center bg-light">
                            <small class="text-muted d-block mb-1">Approval Rate</small>
                            <h5 class="text-info mb-0 weight-700">${response.statistik.approval_rate}%</h5>
                        </div>
                    </div>
                    <div class="col-md-3 px-2 mb-2">
                        <div class="border rounded p-2 text-center bg-light">
                            <small class="text-muted d-block mb-1">Rata-rata Proses</small>
                            <h5 class="text-warning mb-0 weight-700">${response.statistik.rata_rata_waktu_proses} hari</h5>
                        </div>
                    </div>
                </div>
                <div class="mt-4 text-left">
                    <h6 class="weight-600 mb-2"><i class="fa fa-line-chart mr-1"></i> Trend Pengajuan per Bulan</h6>
                    <div id="trendChart" style="height: 250px;"></div>
                </div>
            `;

            Swal.fire({
                title: 'Detail Kinerja Marketing',
                html: html,
                width: '900px',
                showConfirmButton: true,
                confirmButtonText: 'Tutup',
                customClass: {
                    confirmButton: 'btn btn-secondary'
                }
            });

            setTimeout(() => {
                if (response.trend_data && document.querySelector('#trendChart')) {
                    const options = {
                        series: [{
                            name: 'Pengajuan',
                            data: response.trend_data.map(t => t.jumlah)
                        }],
                        chart: { type: 'line', height: 250, toolbar: { show: false } },
                        xaxis: { categories: response.trend_data.map(t => t.bulan) },
                        colors: ['#0d6efd'],
                        stroke: { curve: 'smooth', width: 3 },
                        markers: { size: 5 }
                    };
                    const chart = new ApexCharts(document.querySelector("#trendChart"), options);
                    chart.render();
                }
            }, 150);
        }).fail(function() {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Gagal memuat detail kinerja marketing'
            });
        });
    }
</script>
@endpush

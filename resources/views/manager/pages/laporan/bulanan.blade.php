@extends('manager.layouts.app')

@section('title', 'Laporan Bulanan')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('manager.dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="#">Laporan</a></li>
<li class="breadcrumb-item active">Laporan Bulanan</li>
@endsection

@section('content')
<!-- Header Banner Ala DeskApp -->
<div class="page-header mb-4">
    <div class="row align-items-center">
        <div class="col-md-6 col-sm-12">
            <div class="title">
                <h4>Laporan Bulanan</h4>
            </div>
            <nav aria-label="breadcrumb" role="navigation">
                <ol class="breadcrumb mb-0">
                    @yield('breadcrumb')
                </ol>
            </nav>
        </div>
        <div class="col-md-6 col-sm-12 text-md-right d-none d-md-block">
            <span class="text-muted">Laporan statistik dan analisis pengajuan KPR per bulan</span>
        </div>
    </div>
</div>

<!-- Filter Periode DeskApp Card Style -->
<div class="card-box pd-20 mb-30">
    <div class="clearfix mb-3">
        <div class="pull-left">
            <h5 class="text-blue h5"><i class="fa fa-filter mr-2"></i>Filter Periode</h5>
        </div>
    </div>
    <form method="GET" class="row align-items-end">
        <div class="col-md-3 mb-3 mb-md-0">
            <label class="weight-600">Bulan</label>
            <select name="bulan" class="form-control selectpicker" data-style="btn-outline-primary">
                @for($i = 1; $i <= 12; $i++)
                <option value="{{ $i }}" {{ $bulan == $i ? 'selected' : '' }}>
                    {{ date('F', mktime(0,0,0,$i,1)) }}
                </option>
                @endfor
            </select>
        </div>
        <div class="col-md-3 mb-3 mb-md-0">
            <label class="weight-600">Tahun</label>
            <select name="tahun" class="form-control selectpicker" data-style="btn-outline-primary">
                @for($i = 2022; $i <= date('Y'); $i++)
                <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>
                    {{ $i }}
                </option>
                @endfor
            </select>
        </div>
        <div class="col-md-3 mb-3 mb-md-0">
            <button type="submit" class="btn btn-primary btn-block">
                <i class="fa fa-search mr-1"></i> Tampilkan
            </button>
        </div>
        <div class="col-md-3">
            <button type="button" onclick="exportLaporan('excel')" class="btn btn-success btn-block">
                <i class="fa fa-file-excel-o mr-1"></i> Export Excel
            </button>
        </div>
    </form>
</div>

<!-- Periode Info Alert -->
<div class="alert alert-info border-radius-8 mb-30" role="alert">
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <i class="fa fa-calendar mr-2"></i>
            <strong>Periode Laporan:</strong>
            {{ $laporan['periode']['nama_bulan'] }} {{ $laporan['periode']['tahun'] }}
            <span class="mx-2">|</span>
            <span class="weight-500">
                {{ $laporan['periode']['start_date']->format('d/m/Y') }} - {{ $laporan['periode']['end_date']->format('d/m/Y') }}
            </span>
        </div>
        <div class="mt-2 mt-md-0">
            <button onclick="printLaporan()" class="btn btn-sm btn-outline-info bg-white text-info">
                <i class="fa fa-print"></i> Cetak / Print
            </button>
        </div>
    </div>
</div>

<!-- Summary Cards (Widget DeskApp Style) -->
<div class="row mb-30">
    <div class="col-xl-3 col-lg-6 col-md-6 mb-20">
        <div class="card-box height-100-p pd-20 min-height-120 d-flex flex-column justify-content-between custom-card-animate">
            <div class="d-flex justify-content-between align-items-start">
                <div class="w-75">
                    <div class="text-muted weight-500 font-14">Total Pengajuan</div>
                    <div class="font-24 weight-700 text-secondary mt-1">{{ number_format($laporan['ringkasan']['total_pengajuan']) }}</div>
                </div>
                <div class="bg-light rounded p-2 text-center" style="width: 40px; height: 40px;">
                    <i class="fa fa-file-text-o text-blue font-20"></i>
                </div>
            </div>
            @if(isset($laporan['perbandingan']['total_pengajuan']))
            <div class="mt-2">
                <span class="weight-600 font-12 {{ $laporan['perbandingan']['total_pengajuan']['perubahan'] >= 0 ? 'text-success' : 'text-danger' }}">
                    <i class="fa {{ $laporan['perbandingan']['total_pengajuan']['perubahan'] >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' }} mr-1"></i>
                    {{ number_format(abs($laporan['perbandingan']['total_pengajuan']['perubahan'])) }}
                    ({{ $laporan['perbandingan']['total_pengajuan']['persen'] >= 0 ? '+' : '' }}{{ number_format($laporan['perbandingan']['total_pengajuan']['persen'], 1) }}%)
                </span>
            </div>
            @endif
        </div>
    </div>

    <div class="col-xl-3 col-lg-6 col-md-6 mb-20">
        <div class="card-box height-100-p pd-20 min-height-120 d-flex flex-column justify-content-between custom-card-animate">
            <div class="d-flex justify-content-between align-items-start">
                <div class="w-75">
                    <div class="text-muted weight-500 font-14">Disetujui</div>
                    <div class="font-24 weight-700 text-success mt-1">{{ number_format($laporan['ringkasan']['disetujui']) }}</div>
                </div>
                <div class="bg-light rounded p-2 text-center" style="width: 40px; height: 40px;">
                    <i class="fa fa-check-circle-o text-success font-20"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-lg-6 col-md-6 mb-20">
        <div class="card-box height-100-p pd-20 min-height-120 d-flex flex-column justify-content-between custom-card-animate">
            <div class="d-flex justify-content-between align-items-start">
                <div class="w-75">
                    <div class="text-muted weight-500 font-14">Approval Rate</div>
                    <div class="font-24 weight-700 text-info mt-1">{{ number_format($laporan['ringkasan']['approval_rate'], 1) }}%</div>
                </div>
                <div class="bg-light rounded p-2 text-center" style="width: 40px; height: 40px;">
                    <i class="fa fa-line-chart text-info font-20"></i>
                </div>
            </div>
            @if(isset($laporan['perbandingan']['approval_rate']))
            <div class="mt-2">
                <span class="weight-600 font-12 {{ $laporan['perbandingan']['approval_rate']['perubahan'] >= 0 ? 'text-success' : 'text-danger' }}">
                    <i class="fa {{ $laporan['perbandingan']['approval_rate']['perubahan'] >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' }} mr-1"></i>
                    {{ number_format(abs($laporan['perbandingan']['approval_rate']['perubahan']), 1) }}%
                </span>
            </div>
            @endif
        </div>
    </div>

    <div class="col-xl-3 col-lg-6 col-md-6 mb-20">
        <div class="card-box height-100-p pd-20 min-height-120 d-flex flex-column justify-content-between custom-card-animate">
            <div class="d-flex justify-content-between align-items-start">
                <div class="w-75">
                    <div class="text-muted weight-500 font-14">Total Pinjaman</div>
                    <div class="font-20 weight-700 text-warning mt-1">Rp {{ number_format($laporan['ringkasan']['total_nilai_pinjaman'], 0, ',', '.') }}</div>
                </div>
                <div class="bg-light rounded p-2 text-center" style="width: 40px; height: 40px;">
                    <i class="fa fa-money text-warning font-20"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistik Harian Chart -->
<div class="card-box mb-30">
    <div class="pd-20">
        <h5 class="text-blue h4"><i class="fa fa-area-chart mr-2"></i>Statistik Harian</h5>
    </div>
    <div class="pb-20 px-3">
        <div id="dailyChart" style="height: 350px;"></div>
    </div>
</div>

<!-- Komposisi Status & Kriteria Tertinggi -->
<div class="row">
    <div class="col-md-6 mb-30">
        <div class="card-box height-100-p">
            <div class="pd-20">
                <h5 class="text-blue h4"><i class="fa fa-pie-chart mr-2"></i>Komposisi Status Pengajuan</h5>
            </div>
            <div class="pb-20 d-flex justify-content-center align-items-center">
                <div id="statusChart" style="height: 300px; width: 100%;"></div>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-30">
        <div class="card-box height-100-p">
            <div class="pd-20">
                <h5 class="text-blue h4"><i class="fa fa-bar-chart mr-2"></i>Kriteria Tertinggi</h5>
            </div>
            <div class="pb-20 px-3">
                <div class="table-responsive">
                    <table class="table table-striped table-hover vertical-align-middle">
                        <thead>
                            <tr>
                                <th>Kriteria</th>
                                <th width="45%">Rata-rata Nilai</th>
                                <th>Kategori</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($laporan['kriteria_tertinggi'] as $kriteria)
                            <tr>
                                <td class="weight-600 text-dark">{{ $kriteria->nama }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1 mr-2">
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar bg-blue" style="width: {{ $kriteria->rata_rata }}%"></div>
                                            </div>
                                        </div>
                                        <div class="weight-600 text-secondary" style="font-size: 13px;">
                                            {{ number_format($kriteria->rata_rata, 2) }}%
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($kriteria->rata_rata >= 80)
                                        <span class="badge badge-success px-2 py-1">Sangat Baik</span>
                                    @elseif($kriteria->rata_rata >= 60)
                                        <span class="badge badge-info px-2 py-1">Baik</span>
                                    @elseif($kriteria->rata_rata >= 50)
                                        <span class="badge badge-warning px-2 py-1">Cukup</span>
                                    @else
                                        <span class="badge badge-danger px-2 py-1">Perlu Perbaikan</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Performansi Marketing -->
<div class="card-box mb-30">
    <div class="pd-20">
        <h5 class="text-blue h4"><i class="fa fa-users mr-2"></i>Performansi Marketing</h5>
    </div>
    <div class="pb-20">
        <div class="table-responsive px-3">
            <table class="table table-hover nowrap" id="marketingTable" width="100%">
                <thead>
                    <tr>
                        <th>Marketing</th>
                        <th>Total Pengajuan</th>
                        <th>Disetujui</th>
                        <th width="30%">Approval Rate</th>
                        <th class="datatable-nosort">Rating</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($laporan['performansi_marketing'] as $marketing)
                    <tr>
                        <td>
                            <div class="user-info-dropdown">
                                <span class="user-icon bg-light rounded-circle d-inline-flex align-items-center justify-content-center mr-2" style="width: 35px; height: 35px;">
                                    <i class="ti-user text-success"></i>
                                </span>
                                <span class="user-name d-inline-block vertical-align-middle">
                                    <div class="weight-600 text-dark">{{ $marketing->name }}</div>
                                    <small class="text-muted">{{ $marketing->email }}</small>
                                </span>
                            </div>
                        </td>
                        <td class="weight-500">{{ number_format($marketing->total) }}</td>
                        <td class="weight-500 text-success">{{ number_format($marketing->disetujui) }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1 mr-2">
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar bg-success" style="width: {{ $marketing->approval_rate }}%"></div>
                                    </div>
                                </div>
                                <div class="weight-600 text-secondary" style="font-size: 13px;">
                                    {{ number_format($marketing->approval_rate, 1) }}%
                                </div>
                            </div>
                        </td>
                        <td>
                            @php
                                $stars = $marketing->approval_rate >= 80 ? 5 : ($marketing->approval_rate >= 60 ? 4 : ($marketing->approval_rate >= 40 ? 3 : 2));
                            @endphp
                            @for($s = 1; $s <= 5; $s++)
                                @if($s <= $stars)
                                    <i class="fa fa-star text-warning"></i>
                                @else
                                    <i class="fa fa-star-o text-muted opacity-50"></i>
                                @endif
                            @endfor
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Daftar Pengajuan Terbaru -->
<div class="card-box mb-30">
    <div class="pd-20">
        <h5 class="text-blue h4"><i class="fa fa-list-alt mr-2"></i>Daftar Pengajuan Terbaru</h5>
    </div>
    <div class="pb-20">
        <div class="table-responsive px-3">
            <table class="table table-hover nowrap" id="pengajuanTable" width="100%">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Debitur</th>
                        <th>Jumlah Pinjaman</th>
                        <th>Status</th>
                        <th>Skor</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($laporan['pengajuan_terbaru'] as $pengajuan)
                    <tr>
                        <td>
                            <span class="weight-600 text-dark">{{ $pengajuan->kode_pengajuan }}</span>
                            <br>
                            <small class="text-muted">#{{ $pengajuan->id }}</small>
                        </td>
                        <td>
                            <div class="user-info-dropdown">
                                <span class="user-icon bg-light rounded-circle d-inline-flex align-items-center justify-content-center mr-2" style="width: 32px; height: 32px;">
                                    <i class="ti-user text-secondary"></i>
                                </span>
                                <span class="user-name d-inline-block vertical-align-middle">
                                    <div class="weight-500 text-dark">{{ $pengajuan->user->nama_lengkap ?? '-' }}</div>
                                    <small class="text-muted">{{ $pengajuan->user->email ?? '-' }}</small>
                                </span>
                            </div>
                        </td>
                        <td class="weight-600 text-dark">Rp {{ number_format($pengajuan->jumlah_pinjaman ?? 0, 0, ',', '.') }}</td>
                        <td>{!! $pengajuan->status_badge !!}</td>
                        <td>
                            @if($pengajuan->penilaian)
                                @php
                                    $score = $pengajuan->penilaian->skor_akhir;
                                    $badgeColor = $score >= 75 ? 'badge-success' : ($score >= 50 ? 'badge-warning' : 'badge-danger');
                                @endphp
                                <span class="badge {{ $badgeColor }} px-2 py-1">
                                    {{ number_format($score, 2) }}%
                                </span>
                            @else
                                <span class="badge badge-secondary px-2 py-1">-</span>
                            @endif
                        </td>
                        <td class="weight-500 text-muted">{{ $pengajuan->created_at->format('d/m/Y H:i') }}</td>
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
    .custom-card-animate {
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }
    .custom-card-animate:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 18px rgba(0,0,0,0.08);
    }
    .vertical-align-middle td {
        vertical-align: middle !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.42.0/dist/apexcharts.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let dailyChart, statusChart;

    $(document).ready(function() {
        $('#marketingTable, #pengajuanTable').DataTable({
            pageLength: 10,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
            },
            responsive: true,
            columnDefs: [{
                targets: "datatable-nosort",
                orderable: false,
            }]
        });

        loadDailyChart();
        loadStatusChart();
    });

    function loadDailyChart() {
        const dailyData = @json($laporan['statistik_harian']);

        const options = {
            series: [
                {
                    name: 'Pengajuan',
                    type: 'column',
                    data: dailyData.map(d => d.pengajuan)
                },
                {
                    name: 'Disetujui',
                    type: 'column',
                    data: dailyData.map(d => d.disetujui)
                },
                {
                    name: 'Approval Rate',
                    type: 'line',
                    data: dailyData.map(d => {
                        return d.pengajuan > 0 ? ((d.disetujui / d.pengajuan) * 100).toFixed(1) : 0;
                    })
                }
            ],
            chart: {
                height: 350,
                type: 'line',
                toolbar: { show: true },
                zoom: { enabled: true }
            },
            stroke: {
                width: [0, 0, 3],
                curve: 'smooth'
            },
            plotOptions: {
                bar: {
                    columnWidth: '50%',
                    borderRadius: 5
                }
            },
            xaxis: {
                categories: dailyData.map(d => d.tanggal),
                title: { text: 'Tanggal' }
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
            tooltip: {
                shared: true,
                intersect: false,
                y: {
                    formatter: function(val, { seriesIndex }) {
                        if (seriesIndex === 2) return val + '%';
                        return val.toLocaleString();
                    }
                }
            },
            legend: {
                position: 'top',
                horizontalAlign: 'center'
            }
        };

        dailyChart = new ApexCharts(document.querySelector("#dailyChart"), options);
        dailyChart.render();
    }

    function loadStatusChart() {
        const statusData = {
            labels: ['Disetujui', 'Ditolak', 'Diproses', 'Verifikasi'],
            series: [
                {{ $laporan['ringkasan']['disetujui'] }},
                {{ $laporan['ringkasan']['ditolak'] }},
                {{ $laporan['ringkasan']['proses'] }},
                {{ $laporan['ringkasan']['verifikasi'] }}
            ]
        };

        const options = {
            series: statusData.series,
            chart: {
                type: 'donut',
                height: 300,
                toolbar: { show: true }
            },
            labels: statusData.labels,
            colors: ['#28a745', '#dc3545', '#007bff', '#ffc107'],
            legend: {
                position: 'bottom',
                horizontalAlign: 'center'
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: '65%',
                        labels: {
                            show: true,
                            total: {
                                show: true,
                                label: 'Total',
                                formatter: function(w) {
                                    return w.globals.seriesTotals.reduce((a, b) => a + b, 0).toLocaleString();
                                }
                            }
                        }
                    }
                }
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        const total = statusData.series.reduce((a, b) => a + b, 0);
                        const percent = ((val / total) * 100).toFixed(1);
                        return val.toLocaleString() + ' (' + percent + '%)';
                    }
                }
            }
        };

        statusChart = new ApexCharts(document.querySelector("#statusChart"), options);
        statusChart.render();
    }

    function exportLaporan(format) {
        let bulan = $('select[name="bulan"]').val();
        let tahun = $('select[name="tahun"]').val();

        Swal.fire({
            title: 'Export Laporan',
            text: 'Sedang memproses export data...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        $.post('{{ route("manager.laporan.export.proses") }}', {
            jenis_laporan: 'bulanan',
            format: format,
            bulan: bulan,
            tahun: tahun,
            _token: $('meta[name="csrf-token"]').attr('content')
        }, function(response) {
            Swal.close();
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Laporan berhasil diekspor',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = '/manager/laporan/download/' + response.filename;
                });
            }
        }).fail(function() {
            Swal.close();
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: 'Terjadi kesalahan saat export'
            });
        });
    }

    function printLaporan() {
        window.print();

        Swal.fire({
            icon: 'success',
            title: 'Print',
            text: 'Dokumen sedang diproses',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000
        });
    }
</script>
@endpush

@extends('manager.layouts.app')

@section('title', 'Laporan Tahunan')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('manager.dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="#">Laporan</a></li>
<li class="breadcrumb-item active">Laporan Tahunan</li>
@endsection

@section('content')
<!-- Header Banner Ala DeskApp -->
<div class="page-header mb-4">
    <div class="row align-items-center">
        <div class="col-md-6 col-sm-12">
            <div class="title">
                <h4>Laporan Tahunan</h4>
            </div>
            <nav aria-label="breadcrumb" role="navigation">
                <ol class="breadcrumb mb-0">
                    @yield('breadcrumb')
                </ol>
            </nav>
        </div>
        <div class="col-md-6 col-sm-12 text-md-right mt-2 mt-md-0">
            <div class="form-group mb-0 d-inline-block text-left" style="min-width: 150px;">
                <select id="tahunFilter" class="selectpicker form-control" data-style="btn-outline-primary">
                    @for($i = 2022; $i <= date('Y'); $i++)
                    <option value="{{ $i }}" {{ $laporan['tahun'] == $i ? 'selected' : '' }}>
                        Tahun {{ $i }}
                    </option>
                    @endfor
                </select>
            </div>
        </div>
    </div>
</div>

<!-- YoY Growth Alert DeskApp Style -->
<div class="row mb-4">
    <div class="col-12">
        <div class="alert alert-dismissible fade show role="alert" style="border-left: 5px solid {{ $laporan['yoy_growth']['trend'] == 'positif' ? '#28a745' : '#dc3545' }}; background: #fff; box-shadow: 0px 0px 10px rgba(0,0,0,0.05);">
            <div class="d-flex align-items-center justify-content-between flex-wrap">
                <div class="d-flex align-items-center m-1">
                    <div class="p-3 mr-3 rounded" style="background: {{ $laporan['yoy_growth']['trend'] == 'positif' ? 'rgba(40, 167, 69, 0.1)' : 'rgba(220, 53, 69, 0.1)' }};">
                        <i class="fa fa-line-chart fa-2x {{ $laporan['yoy_growth']['trend'] == 'positif' ? 'text-success' : 'text-danger' }}"></i>
                    </div>
                    <div>
                        <h5 class="weight-600 mb-1 font-16 text-dark">Year-over-Year Growth</h5>
                        <p class="mb-0 text-secondary">
                            Pertumbuhan pengajuan dari tahun {{ $laporan['yoy_growth']['tahun_lalu'] }} ke {{ $laporan['yoy_growth']['tahun_sekarang'] }}:
                            <strong class="{{ $laporan['yoy_growth']['trend'] == 'positif' ? 'text-success' : 'text-danger' }}">{{ $laporan['yoy_growth']['pertumbuhan'] >= 0 ? '+' : '' }}{{ number_format($laporan['yoy_growth']['pertumbuhan'], 1) }}%</strong>
                            <br>
                            <span class="font-12 text-muted">
                                {{ number_format($laporan['yoy_growth']['total_lalu']) }} → {{ number_format($laporan['yoy_growth']['total_sekarang']) }} pengajuan
                            </span>
                        </p>
                    </div>
                </div>
                <div class="m-1">
                    <h2 class="weight-700 mb-0 {{ $laporan['yoy_growth']['trend'] == 'positif' ? 'text-success' : 'text-danger' }}">
                        {{ $laporan['yoy_growth']['pertumbuhan'] >= 0 ? '+' : '' }}{{ number_format($laporan['yoy_growth']['pertumbuhan'], 1) }}%
                    </h2>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Summary Cards DeskApp Widget Style -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card-box p-3 d-flex align-items-center justify-content-between card-box-animate" style="min-height: 100px;">
            <div class="w-75">
                <p class="text-muted weight-500 mb-1">Total Pengajuan</p>
                <h4 class="mb-0 weight-700 font-20 text-blue">{{ number_format($laporan['ringkasan_tahunan']['total_pengajuan']) }}</h4>
            </div>
            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center p-3" style="width: 48px; height: 48px;">
                <i class="fa fa-file-text-o text-blue font-20"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card-box p-3 d-flex align-items-center justify-content-between card-box-animate" style="min-height: 100px;">
            <div class="w-75">
                <p class="text-muted weight-500 mb-1">Disetujui</p>
                <h4 class="mb-0 weight-700 font-20 text-success">{{ number_format($laporan['ringkasan_tahunan']['disetujui']) }}</h4>
            </div>
            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center p-3" style="width: 48px; height: 48px;">
                <i class="fa fa-check-circle-o text-success font-20"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card-box p-3 d-flex align-items-center justify-content-between card-box-animate" style="min-height: 100px;">
            <div class="w-75">
                <p class="text-muted weight-500 mb-1">Rata-rata per Bulan</p>
                <h4 class="mb-0 weight-700 font-20 text-info">{{ number_format($laporan['ringkasan_tahunan']['rata_rata_per_bulan']) }}</h4>
            </div>
            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center p-3" style="width: 48px; height: 48px;">
                <i class="fa fa-bar-chart text-info font-20"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card-box p-3 d-flex align-items-center justify-content-between card-box-animate" style="min-height: 100px;">
            <div class="w-75">
                <p class="text-muted weight-500 mb-1">Total Pinjaman</p>
                <h5 class="mb-0 weight-700 font-16 text-warning">Rp {{ number_format($laporan['ringkasan_tahunan']['total_nilai_pinjaman'], 0, ',', '.') }}</h5>
            </div>
            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center p-3" style="width: 48px; height: 48px;">
                <i class="fa fa-money text-warning font-20"></i>
            </div>
        </div>
    </div>
</div>

<!-- Monthly Chart DeskApp Block -->
<div class="card-box mb-30">
    <div class="pd-20">
        <h5 class="text-blue h4"><i class="fa fa-columns mr-2"></i>Statistik Per Bulan</h5>
    </div>
    <div class="pd-20">
        <div id="monthlyChart" style="min-height: 380px;"></div>
    </div>
</div>

<!-- Kuartal & Top Marketing -->
<div class="row mb-30">
    <div class="col-lg-6 mb-4 mb-lg-0">
        <div class="card-box height-100-p">
            <div class="pd-20">
                <h5 class="text-blue h4"><i class="fa fa-pie-chart mr-2"></i>Trend Kuartal</h5>
            </div>
            <div class="pd-20">
                <div id="quarterChart" style="min-height: 300px;"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card-box height-100-p">
            <div class="pd-20">
                <h5 class="text-blue h4"><i class="fa fa-trophy mr-2"></i>Top 5 Marketing Terbaik</h5>
            </div>
            <div class="pb-20">
                <div class="table-responsive px-3">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Marketing</th>
                                <th>Total</th>
                                <th>Disetujui</th>
                                <th>Rate</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($laporan['top_marketing'] as $index => $marketing)
                            <tr>
                                <td class="weight-600 text-dark">
                                    @if($index == 0)
                                        <i class="fa fa-star text-warning font-16" title="Juara 1"></i>
                                    @elseif($index == 1)
                                        <i class="fa fa-bookmark text-secondary font-16"></i>
                                    @elseif($index == 2)
                                        <i class="fa fa-bookmark-o text-bronze font-16"></i>
                                    @else
                                        {{ $index + 1 }}
                                    @endif
                                </td>
                                <td>
                                    <div class="user-info-dropdown">
                                        <span class="user-icon bg-light rounded-circle d-inline-flex align-items-center justify-content-center mr-2" style="width: 32px; height: 32px;">
                                            <i class="ti-user text-success"></i>
                                        </span>
                                        <span class="user-name d-inline-block vertical-align-middle">
                                            <div class="weight-600 text-dark">{{ $marketing->name }}</div>
                                            <small class="text-muted">{{ $marketing->email }}</small>
                                        </span>
                                    </div>
                                </td>
                                <td class="weight-500 text-dark">{{ number_format($marketing->total) }}</td>
                                <td class="weight-500 text-success">{{ number_format($marketing->disetujui) }}</td>
                                <td>
                                    <div class="d-flex align-items-center" style="min-width: 100px;">
                                        <div class="flex-grow-1 mr-2">
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar bg-success" style="width: {{ ($marketing->disetujui / max($marketing->total, 1)) * 100 }}%"></div>
                                            </div>
                                        </div>
                                        <div class="flex-shrink-0 weight-600 text-dark font-12">
                                            {{ number_format(($marketing->disetujui / max($marketing->total, 1)) * 100, 1) }}%
                                        </div>
                                    </div>
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

<!-- Analisis Kriteria DeskApp Table Style -->
<div class="card-box mb-30">
    <div class="pd-20">
        <h5 class="text-blue h4"><i class="fa fa-tasks mr-2"></i>Analisis Kriteria Penilaian</h5>
    </div>
    <div class="pb-20">
        <div class="table-responsive px-3">
            <table class="table table-hover nowrap" id="kriteriaTable" width="100%">
                <thead>
                    <tr>
                        <th>Kriteria</th>
                        <th>Rata-rata Nilai</th>
                        <th>Minimal</th>
                        <th>Maksimal</th>
                        <th>Kategori</th>
                        <th>Rekomendasi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($laporan['kriteria_analysis'] as $kriteria)
                    <tr>
                        <td class="weight-600 text-dark">
                            {{ $kriteria->nama }}
                        </td>
                        <td>
                            <div class="d-flex align-items-center" style="min-width: 140px;">
                                <div class="flex-grow-1 mr-2">
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar {{ $kriteria->rata_rata >= 70 ? 'bg-success' : ($kriteria->rata_rata >= 50 ? 'bg-warning' : 'bg-danger') }}"
                                             style="width: {{ $kriteria->rata_rata }}%">
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-shrink-0 weight-600 text-dark font-12">
                                    {{ number_format($kriteria->rata_rata, 2) }}%
                                </div>
                            </div>
                        </td>
                        <td class="weight-500 text-dark">{{ number_format($kriteria->minimal, 2) }}</td>
                        <td class="weight-500 text-dark">{{ number_format($kriteria->maksimal, 2) }}</td>
                        <td>
                            @if($kriteria->rata_rata >= 70)
                                <span class="badge badge-success px-2 py-1">Sangat Baik</span>
                            @elseif($kriteria->rata_rata >= 50)
                                <span class="badge badge-warning px-2 py-1">Cukup</span>
                            @else
                                <span class="badge badge-danger px-2 py-1">Perlu Perbaikan</span>
                            @endif
                        </td>
                        <td class="weight-500 text-dark">
                            @if($kriteria->rata_rata < 50)
                                <i class="fa fa-exclamation-triangle text-danger mr-1"></i> Perlu peningkatan
                            @elseif($kriteria->rata_rata < 70)
                                <i class="fa fa-info-circle text-warning mr-1"></i> Pertahankan & tingkatkan
                            @else
                                <i class="fa fa-check-circle text-success mr-1"></i> Sudah baik
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Ringkasan Eksekutif DeskApp Panels Style -->
<div class="card-box mb-30">
    <div class="pd-20">
        <h5 class="text-blue h4"><i class="fa fa-file-text mr-2"></i>Ringkasan Eksekutif Tahunan</h5>
    </div>
    <div class="pd-20">
        <div class="row">
            <div class="col-md-6 mb-3 mb-md-0">
                <div class="p-3 rounded" style="background: rgba(23, 162, 184, 0.08); border-left: 4px solid #17a2b8;">
                    <h6 class="text-info weight-600 mb-2"><i class="fa fa-line-chart mr-2"></i>Analisis Kinerja</h6>
                    <ul class="mb-0 pl-3 style-list" style="list-style-type: square;">
                        @php
                            $bestMonth = collect($laporan['per_bulan'])->sortByDesc('jumlah')->first();
                            $worstMonth = collect($laporan['per_bulan'])->sortBy('jumlah')->first();
                        @endphp
                        <li class="mb-1 text-dark">Bulan dengan pengajuan tertinggi: <strong class="text-blue">{{ $bestMonth['bulan'] ?? '-' }}</strong> ({{ number_format($bestMonth['jumlah'] ?? 0) }} pengajuan)</li>
                        <li class="mb-1 text-dark">Bulan dengan pengajuan terendah: <strong class="text-danger">{{ $worstMonth['bulan'] ?? '-' }}</strong> ({{ number_format($worstMonth['jumlah'] ?? 0) }} pengajuan)</li>
                        <li class="text-dark">Rata-rata approval rate: <strong class="text-success">
                            {{ number_format(collect($laporan['per_bulan'])->avg(function($item) {
                                return $item['jumlah'] > 0 ? ($item['disetujui'] / $item['jumlah']) * 100 : 0;
                            }), 1) }}%
                        </strong></li>
                    </ul>
                </div>
            </div>
            <div class="col-md-6">
                <div class="p-3 rounded" style="background: rgba(255, 193, 7, 0.08); border-left: 4px solid #ffc107;">
                    <h6 class="text-warning weight-600 mb-2"><i class="fa fa-lightbulb-o mr-2"></i>Rekomendasi Strategis {{ $laporan['tahun'] + 1 }}</h6>
                    <ul class="mb-0 pl-3 style-list" style="list-style-type: square;">
                        <li class="mb-1 text-dark">Target peningkatan pengajuan: <strong class="text-blue">{{ number_format($laporan['ringkasan_tahunan']['total_pengajuan'] * 1.1) }}</strong> pengajuan</li>
                        <li class="mb-1 text-dark">Fokus peningkatan pada bulan {{ $worstMonth['bulan'] ?? '' }} yang biasanya rendah</li>
                        <li class="mb-1 text-dark">Optimalkan marketing pada bulan-bulan dengan potensi tinggi</li>
                        <li class="text-dark">Tingkatkan kualitas penilaian untuk mencapai approval rate lebih besar dari 75%</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Export Section Box Style -->
<div class="card-box mb-30 pd-20">
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <div class="m-1 text-dark weight-600">
            <i class="fa fa-download mr-2 text-blue font-18"></i>Export Dokumen Laporan Tahunan
        </div>
        <div class="m-1">
            <button onclick="exportLaporan('excel')" class="btn btn-success btn-sm mr-1">
                <i class="fa fa-file-excel-o mr-1"></i> Excel
            </button>
            <button onclick="exportLaporan('pdf')" class="btn btn-danger btn-sm mr-1">
                <i class="fa fa-file-pdf-o mr-1"></i> PDF
            </button>
            <button onclick="printLaporan()" class="btn btn-secondary btn-sm">
                <i class="fa fa-print mr-1"></i> Print
            </button>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .card-box-animate {
        transition: transform .2s ease, box-shadow .2s ease;
    }
    .card-box-animate:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0,0,0,.08);
    }
    .text-bronze { color: #cd7f32; }
    .style-list li { position: relative; }
    .dropdown-toggle::after { display: none !important; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.42.0/dist/apexcharts.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let monthlyChart, quarterChart;

    $(document).ready(function() {
        $('#kriteriaTable').DataTable({
            pageLength: 10,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
            },
            responsive: true,
            searching: false,
            paging: true,
            info: false,
            bLengthChange: false
        });

        loadMonthlyChart();
        loadQuarterChart();
    });

    function loadMonthlyChart() {
        const monthlyData = @json($laporan['per_bulan']);

        const options = {
            series: [
                {
                    name: 'Pengajuan',
                    type: 'column',
                    data: monthlyData.map(d => d.jumlah)
                },
                {
                    name: 'Disetujui',
                    type: 'column',
                    data: monthlyData.map(d => d.disetujui)
                },
                {
                    name: 'Ditolak',
                    type: 'column',
                    data: monthlyData.map(d => d.ditolak)
                }
            ],
            chart: {
                height: 380,
                type: 'line',
                fontFamily: 'Inter, sans-serif',
                toolbar: { show: true },
                stacked: false
            },
            stroke: {
                width: [0, 0, 0],
                curve: 'smooth'
            },
            plotOptions: {
                bar: {
                    columnWidth: '55%',
                    borderRadius: 4
                }
            },
            xaxis: {
                categories: monthlyData.map(d => d.bulan.substring(0, 3)),
                title: { text: 'Bulan' }
            },
            yaxis: {
                title: { text: 'Jumlah Pengajuan' },
                labels: {
                    formatter: function(val) {
                        return val.toLocaleString();
                    }
                }
            },
            colors: ['#1b00ff', '#28a745', '#dc3545'],
            tooltip: {
                shared: true,
                intersect: false,
                y: {
                    formatter: function(val) {
                        return val.toLocaleString();
                    }
                }
            },
            legend: {
                position: 'top',
                horizontalAlign: 'center'
            }
        };

        monthlyChart = new ApexCharts(document.querySelector("#monthlyChart"), options);
        monthlyChart.render();
    }

    function loadQuarterChart() {
        const quarterData = @json($laporan['trend_kuartal']);

        const options = {
            series: [{
                name: 'Jumlah Pengajuan',
                data: quarterData.map(d => d.jumlah)
            }],
            chart: {
                type: 'line',
                height: 300,
                fontFamily: 'Inter, sans-serif',
                toolbar: { show: false },
                zoom: { enabled: false }
            },
            stroke: {
                curve: 'smooth',
                width: 3,
                colors: ['#ffc107']
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.7,
                    opacityTo: 0.3,
                    stops: [0, 90, 100]
                }
            },
            markers: {
                size: 5,
                strokeWidth: 2,
                hover: { size: 7 }
            },
            xaxis: {
                categories: quarterData.map(d => d.kuartal),
                title: { text: 'Kuartal' }
            },
            yaxis: {
                title: { text: 'Jumlah Pengajuan' },
                labels: {
                    formatter: function(val) {
                        return val.toLocaleString();
                    }
                }
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return val.toLocaleString() + ' pengajuan';
                    }
                }
            },
            colors: ['#ffc107'],
            dataLabels: {
                enabled: true,
                formatter: function(val) {
                    return val.toLocaleString();
                }
            }
        };

        quarterChart = new ApexCharts(document.querySelector("#quarterChart"), options);
        quarterChart.render();
    }

    $('#tahunFilter').on('change', function() {
        const tahun = $(this).val();
        window.location.href = '{{ route("manager.laporan.tahunan") }}?tahun=' + tahun;
    });

    function exportLaporan(format) {
        const tahun = $('#tahunFilter').val();

        Swal.fire({
            title: 'Export Laporan',
            text: 'Sedang memproses export data...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        $.post('{{ route("manager.laporan.export.proses") }}', {
            jenis_laporan: 'tahunan',
            format: format,
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

@extends('manager.layouts.app')

@section('title', 'Rekap Penilaian')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('manager.dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('manager.penilaian.index') }}">Penilaian</a></li>
<li class="breadcrumb-item active">Rekap Penilaian</li>
@endsection

@section('content')
<!-- Header -->
<div class="row">
    <div class="col-12">
        <div class="card bg-gradient-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="text-white mb-2">
                            <i class="fas fa-chart-pie me-2"></i>Rekap Penilaian Tahun {{ $rekap['tahun'] }}
                        </h4>
                        <p class="text-white-50 mb-0">
                            Ringkasan dan analisis penilaian KPR per periode
                        </p>
                    </div>
                    <div class="d-none d-md-block">
                        <select id="tahunFilter" class="form-select bg-white" style="width: auto;">
                            @for($i = 2022; $i <= date('Y'); $i++)
                            <option value="{{ $i }}" {{ $rekap['tahun'] == $i ? 'selected' : '' }}>
                                Tahun {{ $i }}
                            </option>
                            @endfor
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Trend Skor Chart -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-line me-2"></i>Trend Skor Penilaian {{ $rekap['tahun'] }}
                </h5>
            </div>
            <div class="card-body">
                <div id="trendChart" style="height: 400px;"></div>
            </div>
        </div>
    </div>
</div>

<!-- Rekap Per Bulan -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-calendar-alt me-2"></i>Rekapitulasi Per Bulan
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="bulanTable">
                        <thead>
                            <tr>
                                <th>Bulan</th>
                                <th>Jumlah Penilaian</th>
                                <th>Rata-rata Skor</th>
                                <th>Skor Tertinggi</th>
                                <th>Skor Terendah</th>
                                <th>Layak</th>
                                <th>Tidak Layak</th>
                                <th>Trend</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rekap['per_bulan'] as $bulan)
                            <tr>
                                <td>
                                    <strong>{{ $bulan['nama_bulan'] }}</strong>
                                    <br>
                                    <small class="text-muted">Bulan {{ $bulan['bulan'] }}</small>
                                </td>
                                <td>{{ number_format($bulan['jumlah']) }}</td>
                                                                <td>{{ number_format($bulan['rata_skor'], 2) }}%</td>
                                <td>
                                    <span class="badge bg-success">{{ number_format($bulan['max_skor'], 2) }}%</span>
                                </td>
                                <td>
                                    <span class="badge bg-danger">{{ number_format($bulan['min_skor'], 2) }}%</span>
                                </td>
                                <td>
                                    <span class="badge bg-success">{{ number_format($bulan['layak'] ?? 0) }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-danger">{{ number_format($bulan['tidak_layak'] ?? 0) }}</span>
                                </td>
                                <td>
                                    @if($loop->iteration > 1)
                                        @php
                                            $prev = $rekap['per_bulan'][$loop->iteration - 2]['rata_skor'];
                                            $diff = $bulan['rata_skor'] - $prev;
                                        @endphp
                                        @if($diff > 0)
                                            <span class="text-success">
                                                <i class="fas fa-arrow-up"></i> +{{ number_format($diff, 2) }}
                                            </span>
                                        @elseif($diff < 0)
                                            <span class="text-danger">
                                                <i class="fas fa-arrow-down"></i> {{ number_format($diff, 2) }}
                                            </span>
                                        @else
                                            <span class="text-muted">
                                                <i class="fas fa-minus"></i> 0
                                            </span>
                                        @endif
                                    @else
                                        <span class="text-muted">-</span>
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

<!-- Rekap Per Penilai & Per Kriteria -->
<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-user-shield me-2"></i>Rekap Per Penilai
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="penilaiTable">
                        <thead>
                            <tr>
                                <th>Penilai</th>
                                <th>Total Penilaian</th>
                                <th>Rata-rata Skor</th>
                                <th>Layak</th>
                                <th>Performa</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rekap['per_penilai'] as $penilai)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-xs me-2">
                                            <div class="avatar-title bg-light rounded-circle">
                                                <i class="fas fa-user text-primary"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $penilai['penilai'] }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ number_format($penilai['total_penilaian']) }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar bg-primary"
                                                     style="width: {{ $penilai['rata_rata_skor'] }}%">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex-shrink-0 ms-2">
                                            {{ number_format($penilai['rata_rata_skor'], 2) }}%
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-success">{{ number_format($penilai['layak'] ?? 0) }}</span>
                                </td>
                                <td>
                                    @php
                                        $performance = $penilai['total_penilaian'] > 0
                                            ? (($penilai['layak'] ?? 0) / $penilai['total_penilaian']) * 100
                                            : 0;
                                    @endphp
                                    <span class="badge bg-info">{{ number_format($performance, 1) }}%</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-bar me-2"></i>Rekap Per Kriteria
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="kriteriaRekapTable">
                        <thead>
                            <tr>
                                <th>Kriteria</th>
                                <th>Jumlah Penilaian</th>
                                <th>Rata-rata Nilai</th>
                                <th>Kategori</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rekap['per_kriteria'] as $kriteria)
                            <tr>
                                <td>
                                    <strong>{{ $kriteria->nama }}</strong>
                                </td>
                                <td>{{ number_format($kriteria->jumlah_penilaian) }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <div class="progress" style="height: 8px;">
                                                <div class="progress-bar {{ $kriteria->rata_rata >= 75 ? 'bg-success' : ($kriteria->rata_rata >= 60 ? 'bg-warning' : 'bg-danger') }}"
                                                     style="width: {{ $kriteria->rata_rata }}%">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex-shrink-0 ms-2">
                                            {{ number_format($kriteria->rata_rata, 2) }}%
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($kriteria->rata_rata >= 75)
                                        <span class="badge bg-success">Sangat Baik</span>
                                    @elseif($kriteria->rata_rata >= 60)
                                        <span class="badge bg-warning">Baik</span>
                                    @elseif($kriteria->rata_rata >= 50)
                                        <span class="badge bg-info">Cukup</span>
                                    @else
                                        <span class="badge bg-danger">Perlu Perbaikan</span>
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

<!-- Ringkasan Eksekutif -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-file-alt me-2"></i>Ringkasan Eksekutif
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="alert alert-info">
                            <i class="fas fa-chart-line me-2"></i>
                            <strong>Analisis Trend:</strong>
                            <ul class="mb-0 mt-2">
                                @php
                                    $trendData = collect($rekap['trend_skor']);
                                    $bestMonth = $trendData->sortByDesc('skor')->first();
                                    $worstMonth = $trendData->sortBy('skor')->first();
                                    $avgScore = $trendData->avg('skor');
                                @endphp
                                <li>Rata-rata skor tahunan: <strong>{{ number_format($avgScore, 2) }}%</strong></li>
                                <li>Bulan dengan skor tertinggi: <strong>{{ $bestMonth['bulan'] ?? '-' }}</strong>
                                    ({{ number_format($bestMonth['skor'] ?? 0, 2) }}%)
                                </li>
                                <li>Bulan dengan skor terendah: <strong>{{ $worstMonth['bulan'] ?? '-' }}</strong>
                                    ({{ number_format($worstMonth['skor'] ?? 0, 2) }}%)
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="alert alert-warning">
                            <i class="fas fa-lightbulb me-2"></i>
                            <strong>Rekomendasi:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Fokus peningkatan pada kriteria dengan skor terendah</li>
                                <li>Evaluasi konsistensi penilaian antar admin</li>
                                <li>Target peningkatan skor rata-rata menjadi <strong>75%</strong> tahun depan</li>
                                <li>Identifikasi faktor penyebab penurunan skor pada bulan tertentu</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Export Section -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-download me-2"></i>
                        <span class="fw-semibold">Export Laporan Rekap</span>
                    </div>
                    <div>
                        <button onclick="exportRekap('excel')" class="btn btn-success btn-sm">
                            <i class="fas fa-file-excel"></i> Excel
                        </button>
                        <button onclick="exportRekap('pdf')" class="btn btn-danger btn-sm">
                            <i class="fas fa-file-pdf"></i> PDF
                        </button>
                        <button onclick="printRekap()" class="btn btn-secondary btn-sm">
                            <i class="fas fa-print"></i> Print
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .bg-gradient-info {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }
    .avatar-xs {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .avatar-title {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.42.0/dist/apexcharts.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let trendChart;

    $(document).ready(function() {
        $('#bulanTable, #penilaiTable, #kriteriaRekapTable').DataTable({
            pageLength: 12,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
            },
            responsive: true,
            searching: false,
            paging: false,
            info: false
        });

        loadTrendChart();
    });

    function loadTrendChart() {
        const trendData = @json($rekap['trend_skor']);

        const options = {
            series: [{
                name: 'Rata-rata Skor',
                type: 'line',
                data: trendData.map(d => d.skor)
            }],
            chart: {
                height: 380,
                type: 'line',
                toolbar: { show: true },
                zoom: { enabled: true }
            },
            stroke: {
                curve: 'smooth',
                width: 3,
                colors: ['#0d6efd']
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
            dataLabels: {
                enabled: true,
                formatter: function(val) {
                    return val.toFixed(2) + '%';
                },
                style: { fontSize: '11px' }
            },
            markers: {
                size: 6,
                strokeWidth: 2,
                hover: { size: 8 }
            },
            xaxis: {
                categories: trendData.map(d => d.bulan),
                title: { text: 'Bulan' }
            },
            yaxis: {
                title: { text: 'Skor (%)' },
                min: 0,
                max: 100,
                labels: {
                    formatter: function(val) {
                        return val + '%';
                    }
                }
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return val.toFixed(2) + '%';
                    }
                }
            },
            colors: ['#0d6efd'],
            grid: {
                borderColor: '#f1f1f1',
                row: { colors: ['transparent', 'transparent'], opacity: 0.5 }
            },
            annotations: {
                yaxis: [{
                    y: 75,
                    borderColor: '#198754',
                    label: {
                        borderColor: '#198754',
                        style: { color: '#fff', background: '#198754' },
                        text: 'Target Minimal (75%)'
                    }
                }]
            }
        };

        trendChart = new ApexCharts(document.querySelector("#trendChart"), options);
        trendChart.render();
    }

    $('#tahunFilter').on('change', function() {
        const tahun = $(this).val();
        window.location.href = '{{ route("manager.penilaian.rekap") }}?tahun=' + tahun;
    });

    function exportRekap(format) {
        const tahun = $('#tahunFilter').val();

        Swal.fire({
            title: 'Export Rekap',
            text: 'Sedang memproses export data...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        $.post('{{ route("manager.penilaian.export") }}', {
            format: format,
            jenis: 'rekap',
            tahun: tahun,
            _token: $('meta[name="csrf-token"]').attr('content')
        }, function(response) {
            Swal.close();
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: `Rekap tahun ${tahun} berhasil diekspor`,
                    confirmButtonText: 'OK'
                });
            }
        }).fail(function() {
            Swal.close();
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: 'Terjadi kesalahan saat export data'
            });
        });
    }

    function printRekap() {
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

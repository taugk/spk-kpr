@extends('manager.layouts.app')

@section('title', 'Tren Pengajuan')

@section('content')
<!-- Page Header -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-sm-between pb-3 border-bottom">
            <div>
                <h4 class="font-weight-bold text-dark mb-1">Tren Pengajuan KPR</h4>
                <p class="text-muted small mb-0">Analisis pertumbuhan, musiman, dan prediksi tren pengajuan berkala.</p>
            </div>
            <nav aria-label="breadcrumb" class="mt-2 mt-sm-0">
                <ol class="breadcrumb bg-transparent p-0 m-0">
                    <li class="breadcrumb-item"><a href="{{ route('manager.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="#" class="text-decoration-none">Analisis</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Tren Pengajuan</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<!-- Filter Panel -->
<div class="row mb-4">
    <div class="col-md-4 mb-3 mb-md-0">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body py-3">
                <label class="font-weight-600 text-secondary small text-uppercase mb-2">Periode Analisis</label>
                <select id="periodeFilter" class="form-control custom-select">
                    <option value="bulanan" {{ $periode == 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                    <option value="triwulan" {{ $periode == 'triwulan' ? 'selected' : '' }}>Triwulan</option>
                    <option value="tahunan" {{ $periode == 'tahunan' ? 'selected' : '' }}>Tahunan</option>
                </select>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3 mb-md-0">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body py-3">
                <label class="font-weight-600 text-secondary small text-uppercase mb-2">Tahun Akademik/Buku</label>
                <select id="tahunFilter" class="form-control custom-select">
                    @for($i = 2022; $i <= date('Y'); $i++)
                    <option value="{{ $i }}" {{ ($tahun ?? date('Y')) == $i ? 'selected' : '' }}>Tahun {{ $i }}</option>
                    @endfor
                </select>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-0 h-100 bg-light">
            <div class="card-body py-3 d-flex flex-column justify-content-end">
                <label class="d-none d-md-block mb-2">&nbsp;</label>
                <button class="btn btn-primary btn-block font-weight-600" onclick="exportTrend()">
                    <i class="fa fa-download mr-2"></i> Export Data Tren
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Main Trend Chart -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom-0 py-3 d-flex align-items-center">
                <div class="icon-shape bg-soft-primary text-primary rounded mr-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background-color: rgba(13, 110, 253, 0.1);">
                    <i class="fa fa-line-chart"></i>
                </div>
                <h5 class="card-title text-dark font-weight-bold mb-0">Grafik Tren Pengajuan</h5>
            </div>
            <div class="card-body pt-0">
                <div id="trendChart" style="height: 400px;"></div>
            </div>
        </div>
    </div>
</div>

<!-- Growth Metrics & Table -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom-0 py-3 d-flex align-items-center">
                <div class="icon-shape bg-soft-success text-success rounded mr-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background-color: rgba(40, 167, 69, 0.1);">
                    <i class="fa fa-table"></i>
                </div>
                <h5 class="card-title text-dark font-weight-bold mb-0">Analisis Pertumbuhan Angka</h5>
            </div>
            <div class="card-body pt-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle border-0" id="growthTable">
                        <thead class="bg-light text-secondary text-uppercase small font-weight-600">
                            <tr>
                                <th class="border-0">Periode</th>
                                <th class="border-0">Jumlah Pengajuan</th>
                                <th class="border-0">Pertumbuhan</th>
                                <th class="border-0">Status Trend</th>
                                <th class="border-0">Estimasi Proyeksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-dark">
                            @foreach($pertumbuhan as $item)
                            <tr>
                                <td class="font-weight-600">{{ $item['periode'] }}</td>
                                <td>{{ number_format($item['jumlah'] ?? 0) }}</td>
                                <td class="{{ $item['trend'] == 'naik' ? 'text-success' : 'text-danger' }} font-weight-bold">
                                    {{ $item['persentase'] >= 0 ? '+' : '' }}{{ number_format($item['persentase'], 2) }}%
                                </td>
                                <td>
                                    @if($item['trend'] == 'naik')
                                        <span class="badge badge-pill badge-success px-3 py-2">
                                            <i class="fa fa-arrow-up mr-1"></i> Meningkat
                                        </span>
                                    @else
                                        <span class="badge badge-pill badge-danger px-3 py-2">
                                            <i class="fa fa-arrow-down mr-1"></i> Menurun
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($item['trend'] == 'naik')
                                        @php $proyeksi = ($item['jumlah'] ?? 0) * (1 + abs($item['persentase'] / 100)); @endphp
                                        <span class="text-success font-weight-600">
                                            <i class="fa fa-chart-line mr-1"></i> {{ number_format(round($proyeksi)) }}
                                        </span>
                                    @else
                                        @php $proyeksi = ($item['jumlah'] ?? 0) * (1 - abs($item['persentase'] / 100)); @endphp
                                        <span class="text-warning font-weight-600">
                                            <i class="fa fa-chart-line mr-1"></i> {{ number_format(round($proyeksi)) }}
                                        </span>
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

<!-- Predictive & Insight Block -->
<div class="row mb-4">
    <div class="col-md-6 mb-4 mb-md-0">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-bottom-0 py-3 d-flex align-items-center">
                <div class="icon-shape bg-soft-info text-info rounded mr-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background-color: rgba(23, 162, 184, 0.1);">
                    <i class="fa fa-magic"></i>
                </div>
                <h5 class="card-title text-dark font-weight-bold mb-0">Prediksi Bulan Depan</h5>
            </div>
            <div class="card-body pt-0 d-flex align-items-center justify-content-center" id="prediksiContent" style="min-height: 200px;">
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="text-muted mt-2 small mb-0">Menghitung model historis...</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-bottom-0 py-3 d-flex align-items-center">
                <div class="icon-shape bg-soft-warning text-warning rounded mr-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background-color: rgba(255, 193, 7, 0.1);">
                    <i class="fa fa-lightbulb"></i>
                </div>
                <h5 class="card-title text-dark font-weight-bold mb-0">Insight Ringkas Tren</h5>
            </div>
            <div class="card-body pt-0">
                <div class="alert alert-info border-0 shadow-none rounded p-4" style="background-color: rgba(23, 162, 184, 0.08);">
                    <div class="d-flex align-items-start">
                        <i class="fa fa-info-circle text-info fa-lg mr-3 mt-1"></i>
                        <div>
                            <h6 class="font-weight-bold text-info mb-2">Kalkulasi Otomatis Dinamis:</h6>
                            @php
                                $totalGrowth = collect($pertumbuhan)->avg('persentase');
                                $bestPeriod = collect($pertumbuhan)->sortByDesc('persentase')->first();
                                $worstPeriod = collect($pertumbuhan)->sortBy('persentase')->first();
                            @endphp
                            <ul class="list-unstyled text-dark mb-0 style-list-custom">
                                <li class="mb-2"><i class="fa fa-circle text-info mr-2 small"></i> Rata-rata pertumbuhan: <strong class="text-dark">{{ number_format($totalGrowth, 2) }}%</strong> per periode</li>
                                <li class="mb-2"><i class="fa fa-circle text-info mr-2 small"></i> Pertumbuhan tertinggi: <strong class="text-success">{{ $bestPeriod['periode'] ?? '-' }}</strong> ({{ number_format($bestPeriod['persentase'] ?? 0, 2) }}%)</li>
                                <li><i class="fa fa-circle text-info mr-2 small"></i> Penurunan terdalam: <strong class="text-danger">{{ $worstPeriod['periode'] ?? '-' }}</strong> ({{ number_format($worstPeriod['persentase'] ?? 0, 2) }}%)</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Seasonal Analysis -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom-0 py-3 d-flex align-items-center">
                <div class="icon-shape bg-soft-dark text-dark rounded mr-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background-color: rgba(52, 58, 64, 0.1);">
                    <i class="fa fa-calendar"></i>
                </div>
                <h5 class="card-title text-dark font-weight-bold mb-0">Analisis Musiman (Seasonal)</h5>
            </div>
            <div class="card-body pt-0">
                <div class="row align-items-center">
                    <div class="col-lg-6 mb-4 mb-lg-0">
                        <div id="seasonalChart" style="height: 300px;"></div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card border-0 bg-light rounded p-3">
                            <div class="card-body p-2">
                                <h6 class="font-weight-bold text-dark mb-3">
                                    <i class="fa fa-graduation-cap text-success mr-2"></i>Rekomendasi Strategis Berbasis Data
                                </h6>
                                @php
                                    $koleksiTren = collect($trenData)->filter(function($item) { return isset($item['jumlah']); });
                                    $puncakBulan = $koleksiTren->sortByDesc('jumlah')->first();
                                    $terendahBulan = $koleksiTren->sortBy('jumlah')->first();
                                @endphp
                                <ul class="list-unstyled text-dark mb-0">
                                    <li class="d-flex align-items-start mb-2">
                                        <i class="fa fa-check-circle text-success mr-2 mt-1"></i>
                                        <span>Periode puncak pengajuan terdeteksi pada <strong>{{ $puncakBulan['periode'] ?? '-' }}</strong> dengan total <strong>{{ number_format($puncakBulan['jumlah'] ?? 0) }}</strong> pengajuan.</span>
                                    </li>
                                    <li class="d-flex align-items-start mb-2">
                                        <i class="fa fa-check-circle text-success mr-2 mt-1"></i>
                                        <span>Disarankan memperkuat alokasi tim pemasar (marketing) minimal 2 bulan sebelum masa puncak tiba.</span>
                                    </li>
                                    <li class="d-flex align-items-start mb-2">
                                        <i class="fa fa-check-circle text-success mr-2 mt-1"></i>
                                        <span>Evaluasi komprehensif diperlukan untuk mencari akar masalah penurunan drastis pada periode <strong>{{ $terendahBulan['periode'] ?? '-' }}</strong>.</span>
                                    </li>
                                    <li class="d-flex align-items-start">
                                        <i class="fa fa-check-circle text-success mr-2 mt-1"></i>
                                        <span>Menetapkan target eskalasi produktivitas minimum <strong>10%</strong> untuk siklus kerja berikutnya.</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .apexcharts-legend-series {
        display: inline-block !important;
        margin: 0 10px !important;
    }
    .style-list-custom li {
        font-size: 0.9rem;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.42.0/dist/apexcharts.min.js"></script>
<script>
    let trendChart, seasonalChart;

    $(document).ready(function() {
        $('#growthTable').DataTable({
            pageLength: 10,
            order: [[0, 'asc']],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
            }
        });

        loadTrendChart();
        loadSeasonalChart();
        loadPrediksi();
    });

    function loadTrendChart() {
        const trendData = @json($trenData);

        const options = {
            series: [{
                name: 'Jumlah Pengajuan',
                type: 'area',
                data: trendData.map(item => item.jumlah)
            }],
            chart: {
                height: 350,
                type: 'line',
                toolbar: { show: true },
                zoom: { enabled: true }
            },
            stroke: {
                curve: 'smooth',
                width: 3
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
                    return val.toLocaleString();
                }
            },
            markers: {
                size: 5,
                strokeWidth: 2,
                hover: { size: 7 }
            },
            xaxis: {
                categories: trendData.map(item => item.periode),
                title: { text: 'Periode' }
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
            colors: ['#0d6efd'],
            grid: {
                borderColor: '#f1f1f1',
                row: { colors: ['transparent', 'transparent'], opacity: 0.5 }
            }
        };

        trendChart = new ApexCharts(document.querySelector("#trendChart"), options);
        trendChart.render();
    }

    function loadSeasonalChart() {
        const rawTrendData = @json($trenData);
        const monthlyData = rawTrendData.map(item => ({
            bulan: item.periode ? item.periode.split(' ')[0] : (item.periode ?? ''),
            pengajuan: item.jumlah ?? 0
        }));

        const options = {
            series: [{
                name: 'Rata-rata Pengajuan',
                data: monthlyData.map(item => item.pengajuan)
            }],
            chart: {
                height: 300,
                type: 'bar',
                toolbar: { show: false }
            },
            plotOptions: {
                bar: {
                    borderRadius: 5,
                    columnWidth: '60%',
                    dataLabels: { position: 'top' }
                }
            },
            dataLabels: {
                enabled: true,
                offsetY: -20,
                formatter: function(val) {
                    return val.toLocaleString();
                },
                style: {
                    fontSize: '12px',
                    colors: ["#304758"]
                }
            },
            xaxis: {
                categories: monthlyData.map(item => item.bulan),
                position: 'bottom',
                title: { text: 'Bulan' }
            },
            yaxis: {
                title: { text: 'Rata-rata Pengajuan' },
                labels: {
                    formatter: function(val) {
                        return val.toLocaleString();
                    }
                }
            },
            colors: ['#198754'],
            title: {
                text: 'Pola Pengajuan per Bulan',
                align: 'center',
                style: { fontSize: '14px', color: '#333', fontWeight: 'bold' }
            }
        };

        seasonalChart = new ApexCharts(document.querySelector("#seasonalChart"), options);
        seasonalChart.render();
    }

    function loadPrediksi() {
        $.get('{{ route("manager.analisis.prediksi") }}', function(response) {
            if (response.success) {
                let trend = response.prediksi_bulan_depan > response.data_historis[0]?.jumlah ? 'naik' : 'turun';
                let percentDiff = response.data_historis[0]?.jumlah > 0
                    ? ((response.prediksi_bulan_depan - response.data_historis[0].jumlah) / response.data_historis[0].jumlah) * 100
                    : 0;

                let html = `
                    <div class="row w-100 mx-0">
                        <div class="col-12 px-0">
                            <div class="alert ${trend == 'naik' ? 'alert-success' : 'alert-warning'} border-0 mb-0 p-3">
                                <h6 class="font-weight-bold mb-1">Estimasi Target Bulan Depan</h6>
                                <h2 class="font-weight-bold text-dark mb-1">${response.prediksi_bulan_depan.toLocaleString()} <span class="h5 text-muted">Pengajuan</span></h2>
                                <small class="${trend == 'naik' ? 'text-success' : 'text-danger'} font-weight-bold">
                                    <i class="fa ${trend == 'naik' ? 'fa-arrow-up' : 'fa-arrow-down'} mr-1"></i> ${Math.abs(percentDiff).toFixed(1)}% dari periode sebelumnya
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3 w-100 mx-0">
                        <div class="col-12 px-0">
                            <h6 class="font-weight-bold text-dark small mb-2 text-uppercase">Data Historis 6 Bulan Terakhir:</h6>
                            <div class="table-responsive" style="max-height: 120px; overflow-y:auto;">
                                <table class="table table-sm table-borderless mb-0">
                                    <thead class="text-secondary small border-bottom">
                                        <tr><th>Periode</th><th>Jumlah</th><th class="text-center">Trend</th></tr>
                                    </thead>
                                    <tbody class="small text-dark">
                                        ${response.data_historis.map((item, index) => {
                                            let trendIcon = '';
                                            if (index > 0) {
                                                let prev = response.data_historis[index-1].jumlah;
                                                trendIcon = item.jumlah > prev ? '<i class="fa fa-arrow-up text-success"></i>' : (item.jumlah < prev ? '<i class="fa fa-arrow-down text-danger"></i>' : '<i class="fa fa-minus text-muted"></i>');
                                            }
                                            return `
                                            <tr>
                                                <td>${item.bulan}/${item.tahun}</td>
                                                <td><strong>${item.jumlah.toLocaleString()}</strong></td>
                                                <td class="text-center">${trendIcon}</td>
                                            </tr>
                                            `;
                                        }).join('')}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                `;
                $('#prediksiContent').html(html).removeClass('text-center');
            }
        }).fail(function() {
            $('#prediksiContent').html('<div class="alert alert-danger w-100 border-0 mb-0">Gagal memuat data prediksi</div>');
        });
    }

    $('#periodeFilter, #tahunFilter').on('change', function() {
        let periode = $('#periodeFilter').val();
        let tahun = $('#tahunFilter').val();
        window.location.href = '{{ route("manager.analisis.tren") }}?periode=' + periode + '&tahun=' + tahun;
    });

    function exportTrend() {
        let periode = $('#periodeFilter').val();
        let tahun = $('#tahunFilter').val();
        window.location.href = '{{ route("manager.laporan.export") }}?jenis=tren&periode=' + periode + '&tahun=' + tahun;
    }
</script>
@endpush

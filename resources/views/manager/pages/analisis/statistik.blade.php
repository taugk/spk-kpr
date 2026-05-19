@extends('manager.layouts.app')

@section('title', 'Statistik Penilaian')

@section('content')
<!-- Header Banner Ala DeskApp -->
<div class="page-header mb-4">
    <div class="row align-items-center">
        <div class="col-md-6 col-sm-12">
            <div class="title">
                <h4>Statistik Penilaian KPR</h4>
            </div>
            <nav aria-label="breadcrumb" role="navigation">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('manager.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="#">Analisis</a></li>
                    <li class="breadcrumb-item active">Statistik Penilaian</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<!-- Filter Tahun & Menu Aksi -->
<div class="row mb-4">
    <div class="col-xl-3 col-lg-4 col-md-12 mb-3">
        <div class="card-box p-3 d-flex flex-column justify-content-center" style="min-height: 90px;">
            <div class="form-group mb-0">
                <label class="weight-600 text-muted mb-1">Filter Tahun</label>
                <select id="tahunFilter" class="custom-select form-control">
                    @for($i = 2022; $i <= date('Y'); $i++)
                    <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>Tahun {{ $i }}</option>
                    @endfor
                </select>
            </div>
        </div>
    </div>
    <div class="col-xl-9 col-lg-8 col-md-12 mb-3">
        <div class="card-box p-3 d-flex align-items-center" style="min-height: 90px;">
            <div class="row w-100 no-gutters m-n1">
                <div class="col-sm-3 p-1">
                    <button class="btn btn-outline-primary btn-block" onclick="exportData('excel')">
                        <i class="fa fa-file-excel-o mr-1"></i> Export Excel
                    </button>
                </div>
                <div class="col-sm-3 p-1">
                    <button class="btn btn-outline-danger btn-block" onclick="exportData('pdf')">
                        <i class="fa fa-file-pdf-o mr-1"></i> Export PDF
                    </button>
                </div>
                <div class="col-sm-3 p-1">
                    <button class="btn btn-outline-success btn-block" onclick="refreshData()">
                        <i class="fa fa-refresh mr-1"></i> Refresh
                    </button>
                </div>
                <div class="col-sm-3 p-1">
                    <button class="btn btn-primary btn-block" onclick="printReport()">
                        <i class="fa fa-print mr-1"></i> Print
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistik Cards Model DeskApp Widget -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card-box p-3 d-flex align-items-center justify-content-between" style="min-height: 100px;">
            <div class="w-75">
                <p class="text-muted weight-500 mb-1">Total Pengajuan</p>
                <h4 class="mb-0 weight-700 font-24 text-primary">{{ number_format($statistik['total_pengajuan'] ?? 0) }}</h4>
                <small class="text-success weight-600"><i class="fa fa-level-up"></i> +12%</small>
            </div>
            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center p-3" style="width: 50px; height: 50px;">
                <i class="fa fa-file-text-o text-primary font-24"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card-box p-3 d-flex align-items-center justify-content-between" style="min-height: 100px;">
            <div class="w-75">
                <p class="text-muted weight-500 mb-1">Disetujui</p>
                <h4 class="mb-0 weight-700 font-24 text-success">{{ number_format($statistik['disetujui'] ?? 0) }}</h4>
                <small class="text-success weight-600"><i class="fa fa-level-up"></i> +8%</small>
            </div>
            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center p-3" style="width: 50px; height: 50px;">
                <i class="fa fa-check-circle-o text-success font-24"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card-box p-3 d-flex align-items-center justify-content-between" style="min-height: 100px;">
            <div class="w-75">
                <p class="text-muted weight-500 mb-1">Ditolak</p>
                <h4 class="mb-0 weight-700 font-24 text-danger">{{ number_format($statistik['ditolak'] ?? 0) }}</h4>
                <small class="text-danger weight-600"><i class="fa fa-level-down"></i> -5%</small>
            </div>
            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center p-3" style="width: 50px; height: 50px;">
                <i class="fa fa-times-circle-o text-danger font-24"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card-box p-3 d-flex align-items-center justify-content-between" style="min-height: 100px;">
            <div class="w-75">
                <p class="text-muted weight-500 mb-1">Rata-rata Skor</p>
                <h4 class="mb-0 weight-700 font-24 text-info">{{ number_format($statistik['rata_rata_skor'] ?? 0, 2) . '%' }}</h4>
                <small class="text-success weight-600"><i class="fa fa-level-up"></i> +3%</small>
            </div>
            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center p-3" style="width: 50px; height: 50px;">
                <i class="fa fa-line-chart text-info font-24"></i>
            </div>
        </div>
    </div>
</div>

<!-- Grafik Visualisasi -->
<div class="row mb-4">
    <div class="col-xl-6 mb-4">
        <div class="card-box mb-30 h-100">
            <div class="pd-20 border-bottom">
                <h5 class="text-blue h5 mb-0"><i class="fa fa-pie-chart mr-2"></i>Distribusi Status Pengajuan</h5>
            </div>
            <div class="pd-20">
                <div id="statusChart" style="min-height: 350px;"></div>
                <div class="mt-3" id="statusLegend"></div>
            </div>
        </div>
    </div>
    <div class="col-xl-6 mb-4">
        <div class="card-box mb-30 h-100">
            <div class="pd-20 border-bottom">
                <h5 class="text-blue h5 mb-0"><i class="fa fa-line-chart mr-2"></i>Tren Pengajuan per Bulan</h5>
            </div>
            <div class="pd-20">
                <div id="monthlyChart" style="min-height: 350px;"></div>
            </div>
        </div>
    </div>
</div>

<!-- Statistik Per Kriteria -->
<div class="card-box mb-30">
    <div class="pd-20">
        <h5 class="text-blue h4"><i class="fa fa-table mr-2"></i>Statistik Per Kriteria Penilaian</h5>
    </div>
    <div class="pb-20">
        <div class="table-responsive px-3">
            <table class="table table-hover nowrap" id="kriteriaTable" width="100%">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Kriteria</th>
                        <th>Rata-rata Nilai</th>
                        <th>Minimal</th>
                        <th>Maksimal</th>
                        <th class="datatable-nosort">Kategori</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($statistik['per_kriteria'] ?? [] as $kriteria)
                    <tr>
                        <td><span class="badge badge-secondary px-2 py-1">{{ $kriteria->kode }}</span></td>
                        <td class="weight-600 text-dark">{{ $kriteria->nama }}</td>
                        <td style="min-width: 180px;">
                            <div class="d-flex align-items-center">
                                <div class="w-100 mr-2">
                                    <div class="progress progress-sm" style="height: 8px;">
                                        <div class="progress-bar bg-blue" style="width: {{ $kriteria->rata_rata_nilai }}%"></div>
                                    </div>
                                </div>
                                <div class="weight-600 text-dark">
                                    {{ number_format($kriteria->rata_rata_nilai, 2) }}
                                </div>
                            </div>
                        </td>
                        <td class="weight-500">{{ number_format($kriteria->nilai_min, 2) }}</td>
                        <td class="weight-500">{{ number_format($kriteria->nilai_max, 2) }}</td>
                        <td>
                            @php
                                $category = $kriteria->rata_rata_nilai >= 80 ? 'Sangat Baik' :
                                           ($kriteria->rata_rata_nilai >= 60 ? 'Baik' :
                                           ($kriteria->rata_rata_nilai >= 40 ? 'Cukup' : 'Kurang'));
                                $badgeClass = $kriteria->rata_rata_nilai >= 80 ? 'badge-success' :
                                             ($kriteria->rata_rata_nilai >= 60 ? 'badge-info' :
                                             ($kriteria->rata_rata_nilai >= 40 ? 'badge-warning' : 'badge-danger'));
                            @endphp
                            <span class="badge {{ $badgeClass }} px-2 py-1">{{ $category }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Statistik Per Bulan Table -->
<div class="card-box mb-30">
    <div class="pd-20">
        <h5 class="text-blue h4"><i class="fa fa-calendar mr-2"></i>Statistik Per Bulan</h5>
    </div>
    <div class="pb-20">
        <div class="table-responsive px-3">
            <table class="table table-hover nowrap" id="bulanTable" width="100%">
                <thead>
                    <tr>
                        <th>Bulan</th>
                        <th>Total Pengajuan</th>
                        <th>Disetujui</th>
                        <th>Approval Rate</th>
                        <th class="datatable-nosort">Trend</th>
                    </tr>
                </thead>
                <tbody>
                   @php
    // Ambil semua data bulan dalam bentuk indexed array agar aman diakses menggunakan nomor urut
    $listBulan = array_values($statistik['per_bulan'] ?? []);
@endphp

@foreach($listBulan as $index => $bulan)
<tr>
    <td class="weight-600 text-dark">{{ $bulan['bulan'] }}</td>
    <td class="weight-500 text-dark">{{ number_format($bulan['pengajuan']) }}</td>
    <td class="weight-500 text-dark">{{ number_format($bulan['disetujui']) }}</td>
    <td style="min-width: 180px;">
        @php
            $rate = $bulan['pengajuan'] > 0 ? ($bulan['disetujui'] / $bulan['pengajuan']) * 100 : 0;
        @endphp
        <div class="d-flex align-items-center">
            <div class="w-100 mr-2">
                <div class="progress progress-sm" style="height: 8px;">
                    <div class="progress-bar bg-success" style="width: {{ $rate }}%"></div>
                </div>
            </div>
            <div class="weight-600 text-dark">
                {{ number_format($rate, 1) }}%
            </div>
        </div>
    </td>
    <td>
        @if($index > 0)
            @php
                // Mengambil data bulan sebelumnya dari array yang sudah dinormalisasi indeksnya
                $prevBulan = $listBulan[$index - 1];
                $prevRate = ($prevBulan['pengajuan'] > 0)
                    ? ($prevBulan['disetujui'] / $prevBulan['pengajuan']) * 100
                    : 0;
                $diff = $rate - $prevRate;
            @endphp
            @if($diff > 0)
                <span class="text-success weight-600"><i class="fa fa-arrow-up mr-1"></i>+{{ number_format($diff, 1) }}%</span>
            @elseif($diff < 0)
                <span class="text-danger weight-600"><i class="fa fa-arrow-down mr-1"></i>{{ number_format($diff, 1) }}%</span>
            @else
                <span class="text-muted"><i class="fa fa-minus mr-1"></i>0%</span>
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

<!-- Ringkasan Eksekutif -->
<div class="card-box mb-30">
    <div class="pd-20 border-bottom">
        <h5 class="text-blue h4 mb-0"><i class="fa fa-bar-chart mr-2"></i>Ringkasan Eksekutif</h5>
    </div>
    <div class="pd-20">
        <div class="row">
            <div class="col-md-6 mb-3">
                <div class="alert alert-secondary bg-light-blue border-0 text-blue" role="alert">
                    <h5 class="alert-heading text-blue h5 mb-2"><i class="fa fa-info-circle mr-2"></i>Insight</h5>
                    <ul class="pl-3 mb-0" style="list-style-type: square;">
                        <li class="mb-1">Total pengajuan: <strong>{{ number_format($statistik['total_pengajuan'] ?? 0) }}</strong> pengajuan</li>
                        <li class="mb-1">Tingkat persetujuan: <strong>{{ $statistik['total_pengajuan'] > 0 ? number_format(($statistik['disetujui'] / $statistik['total_pengajuan']) * 100, 1) : 0 }}%</strong></li>
                        <li class="mb-1">Rata-rata skor penilaian: <strong>{{ number_format($statistik['rata_rata_skor'] ?? 0, 2) }}%</strong></li>
                        <li>Kriteria dengan skor tertinggi:
                            <strong>{{ $statistik['per_kriteria']->sortByDesc('rata_rata_nilai')->first()->nama ?? '-' }}</strong>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="alert alert-secondary bg-light-orange border-0 text-warning" role="alert" style="color: #b25e00 !important;">
                    <h5 class="alert-heading h5 mb-2" style="color: #b25e00 !important;"><i class="fa fa-exclamation-triangle mr-2"></i>Rekomendasi</h5>
                    <ul class="pl-3 mb-0" style="list-style-type: square;">
                        @php
                            $lowestCriteria = $statistik['per_kriteria']->sortBy('rata_rata_nilai')->first();
                        @endphp
                        <li class="mb-1">Perlu perhatian pada kriteria: <strong>{{ $lowestCriteria->nama ?? '-' }}</strong> (skor {{ number_format($lowestCriteria->rata_rata_nilai ?? 0, 2) }})</li>
                        <li class="mb-1">Target peningkatan approval rate: <strong>+5%</strong> pada bulan depan</li>
                        <li>Fokus pada verifikasi dokumen untuk meningkatkan kualitas pengajuan</li>
                    </ul>
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
    .progress {
        background-color: #ecf0f4;
        border-radius: 4px;
    }
    .progress-bar {
        border-radius: 4px;
    }
    .bg-light-blue {
        background-color: rgba(0, 123, 255, 0.08) !important;
    }
    .bg-light-orange {
        background-color: rgba(255, 193, 7, 0.09) !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.42.0/dist/apexcharts.min.js"></script>
<script>
    let statusChart, monthlyChart;

    $(document).ready(function() {
        // Initialize DataTables
        $('#kriteriaTable, #bulanTable').DataTable({
            pageLength: 10,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
            },
            responsive: true,
            searching: false,
            paging: true,
            info: true,
            columnDefs: [{
                targets: "datatable-nosort",
                orderable: false,
            }]
        });

        // Load Charts
        loadStatusChart();
        loadMonthlyChart();
    });

    function loadStatusChart() {
        const chartData = @json($chartData);

        const options = {
            series: chartData.data,
            chart: {
                type: 'donut',
                height: 330,
                toolbar: { show: true }
            },
            labels: chartData.labels,
            colors: ['#28a745', '#dc3545', '#007bff', '#17a2b8'],
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
                            name: { show: true },
                            value: { show: true, formatter: function(val) { return val.toLocaleString(); } },
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
                        const total = chartData.data.reduce((a, b) => a + b, 0);
                        const percent = ((val / total) * 100).toFixed(1);
                        return val.toLocaleString() + ' (' + percent + '%)';
                    }
                }
            },
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: { width: '100%' },
                    legend: { position: 'bottom' }
                }
            }]
        };

        statusChart = new ApexCharts(document.querySelector("#statusChart"), options);
        statusChart.render();
    }

    function loadMonthlyChart() {
    // Memaksa data dari PHP menjadi array murni di Javascript menggunakan Object.values
    const rawMonthlyData = @json($statistik['per_bulan'] ?? []);
    const monthlyData = Array.isArray(rawMonthlyData) ? rawMonthlyData : Object.values(rawMonthlyData);

    if (monthlyData.length === 0) return;

    const options = {
        series: [
            {
                name: 'Pengajuan',
                type: 'column',
                data: monthlyData.map(item => Number(item.pengajuan ?? 0))
            },
            {
                name: 'Disetujui',
                type: 'column',
                data: monthlyData.map(item => Number(item.disetujui ?? 0))
            },
            {
                name: 'Approval Rate',
                type: 'line',
                data: monthlyData.map(item => {
                    const pengajuan = Number(item.pengajuan ?? 0);
                    const disetujui = Number(item.disetujui ?? 0);
                    return pengajuan > 0 ? parseFloat(((disetujui / pengajuan) * 100).toFixed(1)) : 0;
                })
            }
        ],
        chart: {
            height: 350,
            type: 'line',
            toolbar: { show: true },
            zoom: { enabled: false }
        },
        labels: monthlyData.map(item => {
            const bulan = item.bulan ?? '';
            return bulan.length > 3 ? bulan.substring(0, 3) : bulan;
        }),
        colors: ['#007bff', '#28a745', '#ffc107'],
        stroke: {
            width: [0, 0, 3],
            curve: 'smooth'
        },
        plotOptions: {
            bar: {
                columnWidth: '50%'
            }
        },
        fill: {
            opacity: [0.85, 0.85, 1],
            gradient: {
                inverseColors: false,
                shade: 'light',
                type: "vertical",
                opacityFrom: 0.85,
                opacityTo: 0.55
            }
        },
        markers: {
            size: 5,
            strokeWidth: 2,
            hover: { size: 7 }
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
        tooltip: {
            shared: true,
            intersect: false,
            y: {
                formatter: function(val, { seriesIndex }) {
                    if (typeof val === "undefined") return val;
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

    monthlyChart = new ApexCharts(document.querySelector("#monthlyChart"), options);
    monthlyChart.render();
}

    $('#tahunFilter').on('change', function() {
        const tahun = $(this).val();
        window.location.href = '{{ route("manager.analisis.statistik") }}?tahun=' + tahun;
    });

    function exportData(format) {
        const tahun = $('#tahunFilter').val();
        window.location.href = '{{ route("manager.laporan.export") }}?jenis=statistik&format=' + format + '&tahun=' + tahun;
    }

    function refreshData() {
        location.reload();
    }

    function printReport() {
        window.print();
    }
</script>
@endpush

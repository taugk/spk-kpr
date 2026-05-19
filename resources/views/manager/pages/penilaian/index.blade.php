@extends('manager.layouts.app')

@section('title', 'Data Penilaian KPR')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('manager.dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="#">Penilaian</a></li>
<li class="breadcrumb-item active">Data Penilaian</li>
@endsection

@section('content')
<!-- Header -->
<div class="row">
    <div class="col-12">
        <div class="card bg-gradient-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="text-white mb-2">
                            <i class="fas fa-star me-2"></i>Data Penilaian KPR
                        </h4>
                        <p class="text-white-50 mb-0">
                            Monitoring dan analisis hasil penilaian kelayakan KPR
                        </p>
                    </div>
                    <div class="d-none d-md-block">
                        <i class="fas fa-chart-line fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistik Cards -->
<div class="row">
    <div class="col-xl-3 col-md-6">
        <div class="card card-animate">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="text-muted mb-1 text-truncate">Total Penilaian</p>
                        <h4 class="mb-0 counter-value" data-target="{{ $statistik['total_penilaian'] }}">
                            {{ number_format($statistik['total_penilaian']) }}
                        </h4>
                    </div>
                    <div>
                        <div class="avatar-sm bg-primary-subtle rounded-circle p-2">
                            <i class="fas fa-file-alt text-primary fs-24"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card card-animate">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="text-muted mb-1 text-truncate">Layak</p>
                        <h4 class="mb-0 text-success counter-value" data-target="{{ $statistik['layak'] }}">
                            {{ number_format($statistik['layak']) }}
                        </h4>
                        <small class="text-success mt-2">
                            <i class="fas fa-arrow-up"></i> {{ number_format($statistik['persentase_kelayakan'], 1) }}%
                        </small>
                    </div>
                    <div>
                        <div class="avatar-sm bg-success-subtle rounded-circle p-2">
                            <i class="fas fa-check-circle text-success fs-24"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card card-animate">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="text-muted mb-1 text-truncate">Tidak Layak</p>
                        <h4 class="mb-0 text-danger counter-value" data-target="{{ $statistik['tidak_layak'] }}">
                            {{ number_format($statistik['tidak_layak']) }}
                        </h4>
                    </div>
                    <div>
                        <div class="avatar-sm bg-danger-subtle rounded-circle p-2">
                            <i class="fas fa-times-circle text-danger fs-24"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card card-animate">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="text-muted mb-1 text-truncate">Rata-rata Skor</p>
                        <h4 class="mb-0">{{ number_format($statistik['rata_rata_skor'], 2) }}%</h4>
                    </div>
                    <div>
                        <div class="avatar-sm bg-info-subtle rounded-circle p-2">
                            <i class="fas fa-chart-line text-info fs-24"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <label class="form-label">Filter Tanggal</label>
                <div class="row g-2">
                    <div class="col-6">
                        <input type="date" id="dateFrom" class="form-control" placeholder="Dari">
                    </div>
                    <div class="col-6">
                        <input type="date" id="dateTo" class="form-control" placeholder="Sampai">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <label class="form-label">Penilai</label>
                <select id="penilaiFilter" class="form-select">
                    <option value="">Semua Penilai</option>
                    @foreach($penilaiList ?? [] as $penilai)
                    <option value="{{ $penilai->id }}">{{ $penilai->nama_lengkap }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card">
            <div class="card-body">
                <label class="form-label">Skor Min</label>
                <input type="number" id="skorMin" class="form-control" placeholder="Min" step="1" min="0" max="100">
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card">
            <div class="card-body">
                <label class="form-label">Skor Max</label>
                <input type="number" id="skorMax" class="form-control" placeholder="Max" step="1" min="0" max="100">
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card">
            <div class="card-body">
                <label class="form-label">&nbsp;</label>
                <div class="d-grid">
                    <button class="btn btn-primary" onclick="applyFilters()">
                        <i class="fas fa-search"></i> Filter
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Distribusi Skor Chart -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-bar me-2"></i>Distribusi Skor Penilaian
                </h5>
            </div>
            <div class="card-body">
                <div id="distribusiChart" style="height: 350px;"></div>
            </div>
        </div>
    </div>
</div>

<!-- Tabel Penilaian -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-table me-2"></i>Daftar Penilaian
                    </h5>
                    <div>
                        <button onclick="exportPenilaian('excel')" class="btn btn-success btn-sm">
                            <i class="fas fa-file-excel"></i> Excel
                        </button>
                        <button onclick="exportPenilaian('pdf')" class="btn btn-danger btn-sm">
                            <i class="fas fa-file-pdf"></i> PDF
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="penilaianTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Kode Pengajuan</th>
                                <th>Debitur</th>
                                <th>Penilai</th>
                                <th>Skor Akhir</th>
                                <th>Threshold</th>
                                <th>Hasil</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($penilaian as $item)
                            <tr>
                                <td>#{{ $item->id }}</td>
                                <td>{{ $item->pengajuan->kode_pengajuan ?? '-' }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-xs me-2">
                                            <div class="avatar-title bg-light rounded-circle">
                                                <i class="fas fa-user text-secondary"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <div>{{ $item->pengajuan->user->nama_lengkap ?? '-' }}</div>
                                            <small class="text-muted">{{ $item->pengajuan->user->email ?? '-' }}</small>
                                        </div>
                                    </div>
                                 </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-xs me-2">
                                            <div class="avatar-title bg-light rounded-circle">
                                                <i class="fas fa-user-shield text-primary"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <div>{{ $item->admin->nama_lengkap ?? '-' }}</div>
                                            <small class="text-muted">{{ $item->admin->email ?? '-' }}</small>
                                        </div>
                                    </div>
                                 </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <div class="progress" style="height: 8px;">
                                                <div class="progress-bar {{ $item->skor_akhir >= 75 ? 'bg-success' : ($item->skor_akhir >= 50 ? 'bg-warning' : 'bg-danger') }}"
                                                     style="width: {{ $item->skor_akhir }}%">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex-shrink-0 ms-2">
                                            <span class="fw-bold">{{ number_format($item->skor_akhir, 2) }}%</span>
                                        </div>
                                    </div>
                                 </td>
                                <td>{{ number_format($item->threshold, 2) }}%</td>
                                <td>{!! $item->hasil_badge !!}</td>
                                <td>{{ $item->tgl_penilaian ? $item->tgl_penilaian->format('d/m/Y H:i') : '-' }}</td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-soft-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('manager.penilaian.show', $item->id) }}">
                                                    <i class="fas fa-eye"></i> Detail
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="{{ route('manager.pengajuan.show', $item->pengajuan_id) }}">
                                                    <i class="fas fa-file-alt"></i> Lihat Pengajuan
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                 </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end mt-3">
                    {{ $penilaian->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    .card-animate {
        transition: all 0.3s ease;
    }
    .card-animate:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    .avatar-sm {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
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
    .bg-primary-subtle { background-color: rgba(13, 110, 253, 0.1); }
    .bg-success-subtle { background-color: rgba(25, 135, 84, 0.1); }
    .bg-danger-subtle { background-color: rgba(220, 53, 69, 0.1); }
    .bg-info-subtle { background-color: rgba(13, 202, 240, 0.1); }
    .fs-24 { font-size: 24px; }
    .btn-soft-secondary {
        background-color: rgba(108, 117, 125, 0.1);
        border-color: transparent;
    }
    .btn-soft-secondary:hover {
        background-color: rgba(108, 117, 125, 0.2);
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.42.0/dist/apexcharts.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let distribusiChart;

    $(document).ready(function() {
        $('#penilaianTable').DataTable({
            pageLength: 25,
            order: [[0, 'desc']],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
            },
            responsive: true,
            searching: false,
            paging: false,
            info: false
        });

        loadDistribusiChart();

        // Counter animation
        $('.counter-value').each(function() {
            const target = parseInt($(this).data('target'));
            if (target) {
                let current = 0;
                const increment = Math.ceil(target / 50);
                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        clearInterval(timer);
                        current = target;
                    }
                    $(this).text(current.toLocaleString());
                }, 20);
            }
        });
    });

    function loadDistribusiChart() {
        const distribusiData = @json($distribusiSkor);

        const options = {
            series: [{
                name: 'Jumlah Penilaian',
                data: Object.values(distribusiData)
            }],
            chart: {
                type: 'bar',
                height: 350,
                toolbar: { show: true }
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
                style: { fontSize: '12px', colors: ["#304758"] },
                formatter: function(val) { return val.toLocaleString(); }
            },
            xaxis: {
                categories: Object.keys(distribusiData),
                title: { text: 'Range Skor (%)' }
            },
            yaxis: {
                title: { text: 'Jumlah Penilaian' },
                labels: { formatter: function(val) { return val.toLocaleString(); } }
            },
            colors: ['#0d6efd'],
            title: {
                text: 'Distribusi Skor Penilaian',
                align: 'center',
                style: { fontSize: '14px' }
            }
        };

        distribusiChart = new ApexCharts(document.querySelector("#distribusiChart"), options);
        distribusiChart.render();
    }

    function applyFilters() {
        let dateFrom = $('#dateFrom').val();
        let dateTo = $('#dateTo').val();
        let penilaiId = $('#penilaiFilter').val();
        let skorMin = $('#skorMin').val();
        let skorMax = $('#skorMax').val();

        window.location.href = '{{ route("manager.penilaian.index") }}?date_from=' + dateFrom +
            '&date_to=' + dateTo + '&penilai_id=' + penilaiId +
            '&skor_min=' + skorMin + '&skor_max=' + skorMax;
    }

    function exportPenilaian(format) {
        let dateFrom = $('#dateFrom').val();
        let dateTo = $('#dateTo').val();
        let penilaiId = $('#penilaiFilter').val();

        Swal.fire({
            title: 'Export Data',
            text: 'Sedang memproses export data...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        $.post('{{ route("manager.penilaian.export") }}', {
            format: format,
            date_from: dateFrom,
            date_to: dateTo,
            penilai_id: penilaiId,
            _token: $('meta[name="csrf-token"]').attr('content')
        }, function(response) {
            Swal.close();
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: `${response.total_data} data berhasil diekspor`,
                    confirmButtonText: 'OK'
                });

                const blob = new Blob([JSON.stringify(response.data, null, 2)], { type: 'application/json' });
                const link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.download = `penilaian_${new Date().toISOString().split('T')[0]}.json`;
                link.click();
                URL.revokeObjectURL(link.href);
            }
        }).fail(function() {
            Swal.close();
            Swal.fire({ icon: 'error', title: 'Gagal!', text: 'Terjadi kesalahan saat export data' });
        });
    }
</script>
@endpush

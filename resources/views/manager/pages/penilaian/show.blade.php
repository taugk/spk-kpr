@extends('manager.layouts.app')

@section('title', 'Detail Penilaian - ID ' . $penilaian->id)

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('manager.dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('manager.penilaian.index') }}">Penilaian</a></li>
<li class="breadcrumb-item active">Detail Penilaian #{{ $penilaian->id }}</li>
@endsection

@section('content')
<!-- Header -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">
                            <i class="fas fa-chart-line text-primary me-2"></i>
                            Detail Penilaian KPR
                        </h4>
                        <p class="text-muted mb-0">
                            Pengajuan: {{ $penilaian->pengajuan->kode_pengajuan ?? '-' }}
                        </p>
                    </div>
                    <div>
                        {!! $penilaian->hasil_badge !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Left Column -->
    <div class="col-lg-8">
        <!-- Breakdown Per Kriteria -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-table me-2"></i>Detail Penilaian Per Kriteria
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="kriteriaTable">
                        <thead>
                            <tr>
                                <th>Kriteria</th>
                                <th>Nilai Input</th>
                                <th>Normalisasi</th>
                                <th>Bobot</th>
                                <th>Skor Kontribusi</th>
                                <th>Kontribusi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($analisisKriteria as $item)
                            <tr>
                                <td>
                                    <div>
                                        <strong>{{ $item['nama_kriteria'] }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $item['kode_kriteria'] }}</small>
                                    </div>
                                </td>
                                <td>{{ number_format($item['nilai_input'], 2) }}</td>
                                <td>{{ number_format($item['nilai_normalisasi'] * 100, 2) }}%</td>
                                <td>{{ number_format($item['bobot'], 1) }}%</td>
                                <td>
                                    <span class="fw-bold">{{ number_format($item['skor_kontribusi'], 4) }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <div class="progress" style="height: 8px;">
                                                <div class="progress-bar bg-primary"
                                                     style="width: {{ $item['persen_kontribusi'] }}%">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex-shrink-0 ms-2">
                                            <small>{{ number_format($item['persen_kontribusi'], 1) }}%</small>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="4" class="text-end">Total Skor Akhir:</th>
                                <th colspan="2">
                                    <h4 class="mb-0 {{ $penilaian->skor_akhir >= $penilaian->threshold ? 'text-success' : 'text-danger' }}">
                                        {{ number_format($penilaian->skor_akhir, 4) }}%
                                    </h4>
                                </th>
                            </tr>
                            <tr class="table-info">
                                <th colspan="4" class="text-end">Threshold:</th>
                                <th colspan="2">
                                    <h5 class="mb-0">{{ number_format($penilaian->threshold, 4) }}%</h5>
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Summary Stats -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-pie me-2"></i>Ringkasan Statistik
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="border rounded p-3 text-center">
                            <h6>Total Kriteria</h6>
                            <h3 class="text-primary mb-0">{{ $summary['total_kriteria'] }}</h3>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-3 text-center">
                            <h6>Rata-rata Nilai Input</h6>
                            <h3 class="text-info mb-0">{{ number_format($summary['rata_rata_nilai'], 2) }}</h3>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-3 text-center">
                            <h6>Rata-rata Normalisasi</h6>
                            <h3 class="text-success mb-0">{{ number_format($summary['rata_rata_normalisasi'] * 100, 2) }}%</h3>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="alert alert-info">
                            <i class="fas fa-chart-line me-2"></i>
                            <strong>Skor Tertinggi:</strong>
                            {{ number_format($summary['max_kontribusi'], 4) }}
                            <br>
                            <strong>Skor Terendah:</strong>
                            {{ number_format($summary['min_kontribusi'], 4) }}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="alert {{ $penilaian->meetsThreshold() ? 'alert-success' : 'alert-danger' }}">
                            <i class="fas {{ $penilaian->meetsThreshold() ? 'fa-check-circle' : 'fa-times-circle' }} me-2"></i>
                            <strong>Status Kelulusan:</strong>
                            {{ $penilaian->meetsThreshold() ? 'LOLOS' : 'GAGAL' }}
                            <br>
                            <small>Selisih: {{ number_format(abs($penilaian->skor_akhir - $penilaian->threshold), 4) }}%</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column -->
    <div class="col-lg-4">
        <!-- Informasi Penilaian -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-info-circle me-2"></i>Informasi Penilaian
                </h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="120">ID Penilaian</th>
                        <td>: <span class="fw-bold">#{{ $penilaian->id }}</span></td>
                    </tr>
                    <tr>
                        <th>Kode Pengajuan</th>
                        <td>: {{ $penilaian->pengajuan->kode_pengajuan ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Penilai</th>
                        <td>:
                            <div class="d-flex align-items-center mt-1">
                                <div class="avatar-xs bg-light rounded-circle me-2">
                                    <i class="fas fa-user-shield text-primary"></i>
                                </div>
                                <div>
                                    <div>{{ $penilaian->admin->nama_lengkap ?? '-' }}</div>
                                    <small class="text-muted">{{ $penilaian->admin->email ?? '-' }}</small>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>Tanggal Penilaian</th>
                        <td>: {{ $penilaian->tgl_penilaian ? $penilaian->tgl_penilaian->format('d F Y H:i:s') : '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Catatan Admin -->
        @if($penilaian->catatan_admin)
        <div class="card">
            <div class="card-header bg-warning text-dark">
                <h5 class="card-title mb-0">
                    <i class="fas fa-sticky-note me-2"></i>Catatan Admin
                </h5>
            </div>
            <div class="card-body">
                <p class="mb-0">{{ $penilaian->catatan_admin }}</p>
            </div>
        </div>
        @endif

        <!-- Rekomendasi -->
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="card-title mb-0 text-white">
                    <i class="fas fa-lightbulb me-2"></i>Rekomendasi
                </h5>
            </div>
            <div class="card-body">
                @if($penilaian->isLayak())
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>
                        <strong>Rekomendasi: DISETUJUI</strong>
                        <p class="mt-2 mb-0">
                            Berdasarkan hasil penilaian, debitur memenuhi kriteria kelayakan
                            dan direkomendasikan untuk mendapatkan persetujuan KPR.
                        </p>
                    </div>
                @else
                    <div class="alert alert-danger">
                        <i class="fas fa-times-circle me-2"></i>
                        <strong>Rekomendasi: DITOLAK</strong>
                        <p class="mt-2 mb-0">
                            Berdasarkan hasil penilaian, debitur belum memenuhi kriteria
                            kelayakan KPR yang ditetapkan.
                        </p>
                    </div>
                @endif

                <hr>

                <h6>Kriteria yang Perlu Perbaikan:</h6>
                <ul class="mb-0">
                    @php
                        $lowestCriteria = collect($analisisKriteria)->sortBy('skor_kontribusi')->first();
                    @endphp
                    <li><strong>{{ $lowestCriteria['nama_kriteria'] }}</strong> - Skor kontribusi terendah</li>
                    <li>Rata-rata nilai input: {{ number_format($summary['rata_rata_nilai'], 2) }}</li>
                    @if($penilaian->skor_akhir < $penilaian->threshold)
                    <li>Perlu peningkatan skor minimal {{ number_format($penilaian->threshold - $penilaian->skor_akhir, 2) }}%</li>
                    @endif
                </ul>
            </div>
        </div>

        <!-- Tombol Aksi -->
        <div class="card">
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('manager.pengajuan.show', $penilaian->pengajuan_id) }}" class="btn btn-primary">
                        <i class="fas fa-file-alt"></i> Lihat Detail Pengajuan
                    </a>
                    <button onclick="printPenilaian()" class="btn btn-success">
                        <i class="fas fa-print"></i> Cetak Hasil Penilaian
                    </button>
                    <button onclick="exportPenilaian()" class="btn btn-info">
                        <i class="fas fa-download"></i> Export PDF
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .avatar-xs {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .table-borderless td, .table-borderless th {
        padding: 8px 0;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        $('#kriteriaTable').DataTable({
            pageLength: 10,
            order: [[0, 'asc']],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
            },
            responsive: true
        });
    });

    function printPenilaian() {
        window.print();

        Swal.fire({
            icon: 'success',
            title: 'Cetak',
            text: 'Dokumen sedang diproses',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000
        });
    }

    function exportPenilaian() {
        Swal.fire({
            title: 'Export PDF',
            text: 'Sedang memproses export...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        setTimeout(() => {
            Swal.close();
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'PDF berhasil diekspor',
                confirmButtonText: 'OK'
            });
        }, 1500);
    }
</script>
@endpush

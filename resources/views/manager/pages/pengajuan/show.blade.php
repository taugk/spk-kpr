@extends('manager.layouts.app')

@section('title', 'Detail Pengajuan - ' . $pengajuan->kode_pengajuan)

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('manager.dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('manager.pengajuan.semua') }}">Pengajuan</a></li>
<li class="breadcrumb-item active">{{ $pengajuan->kode_pengajuan }}</li>
@endsection

@section('content')
<!-- Header Page DeskApp Style -->
<div class="page-header mb-4">
    <div class="row align-items-center">
        <div class="col-md-7 col-sm-12">
            <div class="title">
                <h4><i class="fa fa-file-text-o text-blue mr-2"></i> Detail Pengajuan: {{ $pengajuan->kode_pengajuan }}</h4>
            </div>
            <nav aria-label="breadcrumb" role="navigation">
                <ol class="breadcrumb mb-0">
                    @yield('breadcrumb')
                </ol>
            </nav>
        </div>
        <div class="col-md-5 col-sm-12 text-md-right text-left mt-2 mt-md-0">
            <div class="d-inline-block vertical-align-middle text-md-right">
                {!! $pengajuan->status_badge !!}
                @if($pengajuan->isCompleted())
                    <div class="mt-1">
                        <small class="text-muted">
                            <i class="fa fa-clock-o"></i>
                            Selesai: {{ $pengajuan->tgl_selesai ? $pengajuan->tgl_selesai->format('d F Y H:i') : '-' }}
                        </small>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Left Column (Informasi Utama) -->
    <div class="col-lg-8 col-md-12 col-sm-12 mb-30">

        <!-- Informasi Debitur -->
        <div class="card-box mb-4">
            <div class="pd-20 border-bottom">
                <h5 class="child-title h5 mb-0"><i class="fa fa-user mr-2 text-blue"></i>Informasi Debitur</h5>
            </div>
            <div class="pd-20">
                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        <table class="table table-borderless table-sm font-14">
                            <tr>
                                <th width="140" class="weight-600 pl-0">Nama Lengkap</th>
                                <td>: {{ $pengajuan->user->nama_lengkap ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="weight-600 pl-0">Email</th>
                                <td>: {{ $pengajuan->user->email ?? '-' }}</td>
                            </tr>
                            @if($pengajuan->debiturPribadi)
                            <tr>
                                <th class="weight-600 pl-0">No. KTP</th>
                                <td>: {{ $pengajuan->debiturPribadi->nik ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="weight-600 pl-0">Tempat, Tgl Lahir</th>
                                <td>: {{ $pengajuan->debiturPribadi->tempat_lahir ?? '-' }},
                                    {{ $pengajuan->debiturPribadi->tanggal_lahir ? $pengajuan->debiturPribadi->tanggal_lahir->format('d/m/Y') : '-' }}
                                </td>
                            </tr>
                            <tr>
                                <th class="weight-600 pl-0">No. HP</th>
                                <td>: {{ $pengajuan->debiturPribadi->no_hp ?? '-' }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                    <div class="col-md-6 col-sm-12">
                        @if($pengajuan->debiturPekerjaan)
                        <table class="table table-borderless table-sm font-14">
                            <tr>
                                <th width="140" class="weight-600 pl-0">Status Pekerjaan</th>
                                <td>: {{ str_replace('_', ' ', ucfirst($pengajuan->debiturPekerjaan->status_pekerjaan ?? '-')) }}</td>
                            </tr>
                            <tr>
                                <th class="weight-600 pl-0">Perusahaan</th>
                                <td>: {{ $pengajuan->debiturPekerjaan->nama_perusahaan ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="weight-600 pl-0">Penghasilan</th>
                                <td>: Rp {{ number_format($pengajuan->debiturPekerjaan->total_penghasilan ?? 0, 0, ',', '.') }}</td>
                            </tr>
                        </table>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Unit & Pengajuan -->
        <div class="card-box mb-4">
            <div class="pd-20 border-bottom">
                <h5 class="child-title h5 mb-0"><i class="fa fa-home mr-2 text-blue"></i>Detail Unit & Pengajuan</h5>
            </div>
            <div class="pd-20">
                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        <table class="table table-borderless table-sm font-14">
                            <tr>
                                <th width="140" class="weight-600 pl-0">Proyek</th>
                                <td>: {{ $pengajuan->unit->tipeUnit->proyek->nama_proyek ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="weight-600 pl-0">Tipe Unit</th>
                                <td>: {{ $pengajuan->unit->tipeUnit->nama_tipe ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="weight-600 pl-0">Kode Unit</th>
                                <td>: {{ $pengajuan->unit->kode_unit ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="weight-600 pl-0">Harga Properti</th>
                                <td class="weight-600 text-dark">: Rp {{ number_format($pengajuan->harga_properti ?? 0, 0, ',', '.') }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6 col-sm-12">
                        <table class="table table-borderless table-sm font-14">
                            <tr>
                                <th width="140" class="weight-600 pl-0">Uang Muka (DP)</th>
                                <td>: Rp {{ number_format($pengajuan->uang_muka ?? 0, 0, ',', '.') }}
                                    ({{ number_format($pengajuan->persen_dp ?? 0, 1) }}%)
                                </td>
                            </tr>
                            <tr>
                                <th class="weight-600 pl-0">Jumlah Pinjaman</th>
                                <td class="weight-700 text-blue">: Rp {{ number_format($pengajuan->jumlah_pinjaman ?? 0, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <th class="weight-600 pl-0">Tenor</th>
                                <td>: {{ $pengajuan->tenor_tahun ?? 0 }} tahun</td>
                            </tr>
                            <tr>
                                <th class="weight-600 pl-0">Estimasi Angsuran</th>
                                <td class="weight-600 text-danger">: Rp {{ number_format($pengajuan->estimasi_angsuran ?? 0, 0, ',', '.') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                @if($pengajuan->catatan_debitur)
                <hr class="my-3">
                <div class="alert alert-info bg-light-info border-0 color-info mb-0">
                    <i class="fa fa-comment mr-2"></i>
                    <strong>Catatan Debitur:</strong><br>
                    {{ $pengajuan->catatan_debitur }}
                </div>
                @endif
            </div>
        </div>

        <!-- Timeline Pengajuan -->
        <div class="card-box mb-4">
            <div class="pd-20 border-bottom">
                <h5 class="child-title h5 mb-0">
                    <i class="fa fa-history mr-2 text-blue"></i>Timeline Pengajuan
                    @if($totalProcessingTime)
                    <small class="text-muted ml-2 font-13 weight-500">
                        (Total waktu proses: {{ $totalProcessingTime }})
                    </small>
                    @endif
                </h5>
            </div>
            <div class="pd-20">
                <div class="timeline-wrapper">
                    @forelse($timeline as $item)
                    <div class="timeline-item {{ $loop->last ? 'last' : '' }}">
                        <div class="timeline-icon bg-{{ $item['is_rejection'] ? 'danger' : ($item['is_approval'] ? 'success' : 'primary') }}">
                            @if($item['is_rejection'])
                                <i class="fa fa-times"></i>
                            @elseif($item['is_approval'])
                                <i class="fa fa-check"></i>
                            @else
                                <i class="fa fa-arrow-right"></i>
                            @endif
                        </div>
                        <div class="timeline-content">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="weight-600 font-14 mb-1">
                                        {{ $item['status_lama_text'] }} <i class="fa fa-long-arrow-right mx-1 text-muted"></i> {{ $item['status_baru_text'] }}
                                    </h6>
                                    <small class="text-muted">
                                        <i class="fa fa-user-circle-o"></i> {{ $item['pengubah'] }}
                                        {!! $item['pengubah_role'] !!}
                                    </small>
                                </div>
                                <div class="text-right">
                                    <small class="text-dark d-block weight-500">{{ $item['waktu'] }}</small>
                                    <small class="text-muted font-12">{{ $item['time_ago'] }}</small>
                                </div>
                            </div>
                            @if($item['keterangan'])
                            <div class="mt-2 pt-2 border-top border-light">
                                <small class="text-info weight-500">
                                    <i class="fa fa-sticky-note-o"></i> Catatan: {{ $item['keterangan'] }}
                                </small>
                            </div>
                            @endif
                            @if($item['duration_to_next'] && !$loop->last)
                            <div class="mt-1">
                                <small class="text-muted">
                                    <i class="fa fa-hourglass-half"></i> Durasi: {{ $item['duration_to_next'] }}
                                </small>
                            </div>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-4">
                        <i class="fa fa-inbox fa-3x mb-2 text-light"></i>
                        <p class="mb-0">Belum ada riwayat status</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column (Informasi Petugas & Pendukung) -->
    <div class="col-lg-4 col-md-12 col-sm-12 mb-30">

        <!-- Informasi Petugas -->
        <div class="card-box mb-4">
            <div class="pd-20 border-bottom">
                <h5 class="child-title h5 mb-0"><i class="fa fa-users mr-2 text-blue"></i>Petugas Internal</h5>
            </div>
            <div class="pd-20">
                <div class="mb-3 pb-3 border-bottom border-light">
                    <label class="text-muted font-12 weight-600 uppercase tracking-wide mb-2 d-block">MARKETING</label>
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm bg-light rounded-circle mr-3 d-flex align-items-center justify-content-center" style="width:40px; height:40px;">
                            <i class="fa fa-user-check text-success font-18"></i>
                        </div>
                        <div>
                            <div class="weight-600 text-dark font-14">{{ $pengajuan->marketing->nama_lengkap ?? 'Belum ditugaskan' }}</div>
                            <small class="text-muted">
                                {{ $pengajuan->tgl_marketing_proses ? 'Mulai: ' . $pengajuan->tgl_marketing_proses->format('d/m/Y H:i') : '-' }}
                            </small>
                        </div>
                    </div>
                </div>
                <div>
                    <label class="text-muted font-12 weight-600 uppercase tracking-wide mb-2 d-block">ADMIN PENILAI</label>
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm bg-light rounded-circle mr-3 d-flex align-items-center justify-content-center" style="width:40px; height:40px;">
                            <i class="fa fa-id-card-o text-primary font-18"></i>
                        </div>
                        <div>
                            <div class="weight-600 text-dark font-14">{{ $pengajuan->admin->nama_lengkap ?? 'Belum ditugaskan' }}</div>
                            <small class="text-muted">
                                {{ $pengajuan->tgl_admin_proses ? 'Mulai: ' . $pengajuan->tgl_admin_proses->format('d/m/Y H:i') : '-' }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hasil Penilaian -->
        @if($pengajuan->penilaian)
        <div class="card-box mb-4">
            <div class="pd-20 border-bottom">
                <h5 class="child-title h5 mb-0"><i class="fa fa-line-chart mr-2 text-blue"></i>Hasil Penilaian</h5>
            </div>
            <div class="pd-20">
                <div class="text-center mb-4 position-relative d-flex justify-content-center align-items-center" style="height: 120px;">
                    <div style="width: 200px; height: 100px; position: relative; overflow: hidden; margin-top: -30px;">
                        <canvas id="scoreGauge" width="200" height="200"></canvas>
                    </div>
                    <div class="position-absolute" style="top: 55px; width: 100%;">
                        <h3 class="mb-0 weight-700 font-24">{{ number_format($pengajuan->penilaian->skor_akhir, 1) }}%</h3>
                        <small class="text-muted weight-500 font-12">Skor Akhir</small>
                    </div>
                </div>

                <table class="table table-sm font-13 mb-3">
                    <tr>
                        <th class="pl-0 weight-600">Threshold Kelulusan</th>
                        <td class="text-right">: {{ number_format($pengajuan->penilaian->threshold, 1) }}%</td>
                    </tr>
                    <tr>
                        <th class="pl-0 weight-600">Hasil Sistem</th>
                        <td class="text-right d-flex justify-content-end align-items-center">:<span class="ml-1">{!! $pengajuan->penilaian->hasil_badge !!}</span></td>
                    </tr>
                    <tr>
                        <th class="pl-0 weight-600">Diverifikasi Oleh</th>
                        <td class="text-right">: {{ $pengajuan->penilaian->admin->nama_lengkap ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th class="pl-0 weight-600">Waktu Penilaian</th>
                        <td class="text-right">: {{ $pengajuan->penilaian->tgl_penilaian ? $pengajuan->penilaian->tgl_penilaian->format('d/m/Y H:i') : '-' }}</td>
                    </tr>
                </table>

                @if($pengajuan->penilaian->catatan_admin)
                <div class="alert alert-secondary bg-light border-0 text-muted p-2 font-13 mb-3">
                    <i class="fa fa-bookmark mr-1 text-secondary"></i>
                    <strong>Catatan Admin:</strong> {{ $pengajuan->penilaian->catatan_admin }}
                </div>
                @endif

                <a href="{{ route('manager.penilaian.show', $pengajuan->penilaian->id) }}" class="btn btn-outline-primary btn-block btn-sm">
                    <i class="fa fa-search mr-1"></i> Lihat Rincian Penilaian
                </a>
            </div>
        </div>
        @endif

        <!-- Dokumen -->
        <div class="card-box mb-4">
            <div class="pd-20 border-bottom d-flex justify-content-between align-items-center">
                <h5 class="child-title h5 mb-0"><i class="fa fa-folder-open-o mr-2 text-blue"></i>Dokumen Berkas</h5>
                <span class="badge badge-primary badge-pill px-2 py-1">{{ $pengajuan->dokumen->count() }}</span>
            </div>
            <div class="pd-0">
                @if($pengajuan->dokumen && $pengajuan->dokumen->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach($pengajuan->dokumen as $dokumen)
                    <div class="list-group-item px-3 py-3 border-left-0 border-right-0">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="w-75">
                                <i class="fa fa-file-{{ $dokumen->is_image ? 'image-o' : 'pdf-o' }} fa-lg text-primary mr-2"></i>
                                <span class="weight-600 text-dark font-13 d-inline-block text-truncate" style="max-width: 80%;">{{ $dokumen->nama_file }}</span>
                                <br>
                                <small class="text-muted ml-4 d-inline-block">{{ $dokumen->formatted_file_size }}</small>
                            </div>
                            <div class="btn-group">
                                <a href="{{ $dokumen->file_url }}" target="_blank" class="btn btn-sm btn-light border" title="Lihat">
                                    <i class="fa fa-eye text-info"></i>
                                </a>
                                <a href="{{ $dokumen->file_url }}" download class="btn btn-sm btn-light border" title="Download">
                                    <i class="fa fa-download text-success"></i>
                                </a>
                            </div>
                        </div>
                        <div class="mt-2 ml-4">
                            {!! $dokumen->status_badge !!}
                            @if($dokumen->catatan_verifikasi)
                            <small class="text-muted d-block mt-1 font-12 bg-light p-1 rounded">
                                <i class="fa fa-commenting-o"></i> {{ $dokumen->catatan_verifikasi }}
                            </small>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center text-muted py-4">
                    <i class="fa fa-folder-o fa-3x mb-2 text-light"></i>
                    <p class="mb-0">Belum ada dokumen terlampir</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Rekomendasi -->
        @if(count($rekomendasi) > 0)
        <div class="card-box mb-4 border border-warning" style="overflow: hidden;">
            <div class="bg-warning pd-15 text-dark">
                <h5 class="h6 mb-0 text-dark"><i class="fa fa-lightbulb-o mr-2"></i>Rekomendasi Manajer</h5>
            </div>
            <div class="pd-15">
                @foreach($rekomendasi as $rec)
                <div class="alert alert-{{ $rec['jenis'] }} border-0 mb-2 p-2 font-13">
                    <div class="d-flex align-items-start">
                        <div class="mr-2 mt-1">
                            <i class="fa fa-{{ $rec['jenis'] == 'success' ? 'check-circle' : ($rec['jenis'] == 'warning' ? 'exclamation-triangle' : 'times-circle') }}"></i>
                        </div>
                        <div>
                            <p class="mb-1 text-dark font-13 weight-500">{{ $rec['pesan'] }}</p>
                            <small class="text-muted d-block">{{ $rec['action'] }}</small>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
    .timeline-wrapper {
        position: relative;
        padding: 10px 0;
    }
    .timeline-item {
        position: relative;
        padding-left: 45px;
        padding-bottom: 25px;
    }
    .timeline-item.last {
        padding-bottom: 0;
    }
    .timeline-item:before {
        content: '';
        position: absolute;
        left: 19px;
        top: 24px;
        bottom: -5px;
        width: 2px;
        background: #ecf0f4;
    }
    .timeline-item.last:before {
        display: none;
    }
    .timeline-icon {
        position: absolute;
        left: 8px;
        top: 0;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 11px;
        z-index: 1;
    }
    .timeline-icon.bg-primary { background-color: #1b00ff; }
    .timeline-icon.bg-success { background-color: #28a745; }
    .timeline-icon.bg-danger { background-color: #dc3545; }
    .timeline-content {
        background: #fdfdfd;
        border: 1px solid #f4f4f4;
        padding: 12px 15px;
        border-radius: 6px;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    $(document).ready(function() {
        @if($pengajuan->penilaian)
        // Gauge Chart Initialization
        const score = {{ $pengajuan->penilaian->skor_akhir }};
        const ctx = document.getElementById('scoreGauge').getContext('2d');

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [score, 100 - score],
                    backgroundColor: ['#28a745', '#ecf0f4'],
                    borderWidth: 0,
                    circumference: 180,
                    rotation: 270,
                    cutout: '75%'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    tooltip: { enabled: false },
                    legend: { display: false }
                }
            }
        });
        @endif
    });
</script>
@endpush

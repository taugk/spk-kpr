@extends('manager.layouts.app')

@section('title', 'Semua Pengajuan KPR')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('manager.dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="#">Pengajuan</a></li>
<li class="breadcrumb-item active">Semua Pengajuan</li>
@endsection

@section('content')
<!-- Header Banner Ala DeskApp -->
<div class="page-header mb-4">
    <div class="row align-items-center">
        <div class="col-md-6 col-sm-12">
            <div class="title">
                <h4>Semua Pengajuan KPR</h4>
            </div>
            <nav aria-label="breadcrumb" role="navigation">
                <ol class="breadcrumb mb-0">
                    @yield('breadcrumb')
                </ol>
            </nav>
        </div>
        <div class="col-md-6 col-sm-12 text-md-right d-none d-md-block">
            <span class="text-muted">Monitoring seluruh pengajuan KPR dari debitur</span>
        </div>
    </div>
</div>

<!-- Statistik Cards DeskApp Style -->
<div class="row clearfix mb-4">
    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
        <div class="card-box p-3 text-center cursor-pointer" onclick="filterByStatus('')" style="min-height: 110px;">
            <div class="text-muted weight-500 mb-2">Total</div>
            <div class="h3 mb-0 text-primary font-30 weight-700" id="statSemua">{{ number_format($statistik['semua']) }}</div>
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
        <div class="card-box p-3 text-center cursor-pointer" onclick="filterByStatus('submitted')" style="min-height: 110px;">
            <div class="text-muted weight-500 mb-2">Submitted</div>
            <div class="h3 mb-0 text-blue font-30 weight-700" id="statSubmitted">{{ number_format($statistik['submitted']) }}</div>
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
        <div class="card-box p-3 text-center cursor-pointer" onclick="filterByStatus('verifikasi_marketing')" style="min-height: 110px;">
            <div class="text-muted weight-500 mb-2">Verifikasi</div>
            <div class="h3 mb-0 text-info font-30 weight-700" id="statVerifikasi">{{ number_format($statistik['verifikasi_marketing']) }}</div>
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
        <div class="card-box p-3 text-center cursor-pointer" onclick="filterByStatus('antrian_admin')" style="min-height: 110px;">
            <div class="text-muted weight-500 mb-2">Antrian Admin</div>
            <div class="h3 mb-0 text-warning font-30 weight-700" id="statAntrian">{{ number_format($statistik['antrian_admin']) }}</div>
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
        <div class="card-box p-3 text-center cursor-pointer" onclick="filterByStatus('penilaian_admin')" style="min-height: 110px;">
            <div class="text-muted weight-500 mb-2">Penilaian</div>
            <div class="h3 mb-0 text-secondary font-30 weight-700" id="statPenilaian">{{ number_format($statistik['penilaian_admin']) }}</div>
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
        <div class="card-box p-3 text-center cursor-pointer" onclick="filterByStatus('disetujui_sistem')" style="min-height: 110px;">
            <div class="text-muted weight-500 mb-2">Disetujui</div>
            <div class="h3 mb-0 text-success font-30 weight-700" id="statDisetujui">{{ number_format($statistik['disetujui']) }}</div>
        </div>
    </div>
</div>

<!-- Filter Section DeskApp Forms -->
<div class="card-box p-4 mb-4">
    <div class="h5 mb-3 font-16 text-blue">Filter Pencarian</div>
    <div class="row align-items-end">
        <div class="col-md-3 col-sm-12 form-group">
            <label class="weight-600">Status</label>
            <select id="statusFilter" class="custom-select form-control">
                <option value="">Semua Status</option>
                <option value="submitted">Submitted</option>
                <option value="verifikasi_marketing">Verifikasi Marketing</option>
                <option value="revisi_debitur">Revisi Debitur</option>
                <option value="ditolak_marketing">Ditolak Marketing</option>
                <option value="antrian_admin">Antrian Admin</option>
                <option value="penilaian_admin">Penilaian Admin</option>
                <option value="selesai_dinilai">Selesai Dinilai</option>
                <option value="ditolak_sistem">Ditolak Sistem</option>
                <option value="disetujui_sistem">Disetujui Sistem</option>
            </select>
        </div>
        <div class="col-md-3 col-sm-12 form-group">
            <label class="weight-600">Cari</label>
            <input type="text" id="searchInput" class="form-control" placeholder="Nama / Kode Pengajuan">
        </div>
        <div class="col-md-2 col-sm-6 form-group">
            <label class="weight-600">Dari Tanggal</label>
            <input type="date" id="dateFrom" class="form-control">
        </div>
        <div class="col-md-2 col-sm-6 form-group">
            <label class="weight-600">Sampai Tanggal</label>
            <input type="date" id="dateTo" class="form-control">
        </div>
        <div class="col-md-2 col-sm-12 form-group">
            <button class="btn btn-primary btn-block" onclick="applyFilters()">
                <i class="fa fa-search mr-1"></i> Filter
            </button>
        </div>
    </div>
</div>

<!-- Export Section Box -->
<div class="card-box p-3 mb-4">
    <div class="row align-items-center">
        <div class="col-sm-6 mb-2 mb-sm-0">
            <div class="weight-600 font-14">
                <i class="fa fa-download mr-2 text-primary"></i>Export Data Lapoaran
            </div>
        </div>
        <div class="col-sm-6 text-sm-right">
            <button onclick="exportData('excel')" class="btn btn-success btn-sm custom-btn mr-1">
                <i class="fa fa-file-excel-o"></i> Excel
            </button>
            <button onclick="exportData('csv')" class="btn btn-info btn-sm custom-btn mr-1">
                <i class="fa fa-file-text-o"></i> CSV
            </button>
            <button onclick="exportData('pdf')" class="btn btn-danger btn-sm custom-btn">
                <i class="fa fa-file-pdf-o"></i> PDF
            </button>
        </div>
    </div>
</div>

<!-- Tabel Pengajuan DeskApp Datatable Layout -->
<div class="card-box mb-30">
    <div class="pd-20">
        <h5 class="text-blue h4"><i class="fa fa-table mr-2"></i>Data Pengajuan KPR</h5>
    </div>
    <div class="pb-20">
        <div class="table-responsive px-3">
            <table class="table table-hover nowrap" id="pengajuanTable" width="100%">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Debitur</th>
                        <th>Marketing</th>
                        <th>Unit</th>
                        <th>Jumlah Pinjaman</th>
                        <th>Status</th>
                        <th>Skor</th>
                        <th>Tanggal</th>
                        <th class="datatable-nosort">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pengajuan as $item)
                    <tr>
                        <td>
                            <span class="weight-600 text-dark">{{ $item->kode_pengajuan }}</span>
                            <br>
                            <small class="text-muted">#{{ $item->id }}</small>
                        </td>
                        <td>
                            <div class="user-info-dropdown">
                                <span class="user-icon bg-light rounded-circle d-inline-flex align-items-center justify-content-center mr-2" style="width: 32px; height: 32px;">
                                    <i class="ti-user text-secondary"></i>
                                </span>
                                <span class="user-name d-inline-block vertical-align-middle">
                                    <div class="weight-500 text-dark">{{ $item->user->nama_lengkap ?? '-' }}</div>
                                    <small class="text-muted">{{ $item->user->email ?? '-' }}</small>
                                </span>
                            </div>
                        </td>
                        <td>
                            @if($item->marketing)
                                <div class="user-info-dropdown">
                                    <span class="user-icon bg-light rounded-circle d-inline-flex align-items-center justify-content-center mr-2" style="width: 32px; height: 32px;">
                                        <i class="fa fa-user-circle text-success"></i>
                                    </span>
                                    <span class="user-name d-inline-block vertical-align-middle">
                                        <div class="weight-500 text-dark">{{ $item->marketing->nama_lengkap }}</div>
                                        <small class="text-muted">{{ $item->tgl_marketing_proses ? $item->tgl_marketing_proses->format('d/m/Y') : '-' }}</small>
                                    </span>
                                </div>
                            @else
                                <span class="text-muted font-13 italic">Belum diambil</span>
                            @endif
                        </td>
                        <td>
                            @if($item->unit)
                                <div>
                                    <div class="weight-500 text-dark">{{ $item->unit->kode_unit ?? '-' }}</div>
                                    <small class="text-muted">{{ $item->unit->tipeUnit->nama_tipe ?? '-' }}</small>
                                </div>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="weight-600 text-dark">Rp {{ number_format($item->jumlah_pinjaman ?? 0, 0, ',', '.') }}</td>
                        <td>{!! $item->status_badge !!}</td>
                        <td>
                            @if($item->penilaian)
                                @php
                                    $badgeClass = $item->penilaian->skor_akhir >= 75 ? 'badge-success' : ($item->penilaian->skor_akhir >= 50 ? 'badge-warning' : 'badge-danger');
                                @endphp
                                <span class="badge {{ $badgeClass }}">
                                    {{ number_format($item->penilaian->skor_akhir, 2) }}%
                                </span>
                            @else
                                <span class="badge badge-secondary">-</span>
                            @endif
                        </td>
                        <td>
                            <div>
                                <div class="weight-500 text-dark">{{ $item->created_at?->format('d/m/Y') ?? '-' }}</div>
                                <small class="text-muted">{{ $item->created_at?->format('H:i') ?? '' }}</small>
                            </div>
                        </td>
                        <td>
                            <div class="dropdown">
                                <a class="btn btn-link font-24 p-0 line-height-1 no-arrow dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                                    <i class="dw dw-more"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
                                    <a class="dropdown-item" href="{{ route('manager.pengajuan.show', $item->id) }}">
                                        <i class="dw dw-eye"></i> Detail
                                    </a>
                                    @if($item->penilaian)
                                    <a class="dropdown-item" href="{{ route('manager.penilaian.show', $item->penilaian->id) }}">
                                        <i class="fa fa-star-o"></i> Lihat Penilaian
                                    </a>
                                    @endif
                                    @if($item->dokumen && $item->dokumen->count() > 0)
                                    <a class="dropdown-item" href="#" onclick="viewDocuments({{ $item->id }})">
                                        <i class="dw dw-file"></i> Dokumen ({{ $item->dokumen->count() }})
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-end mt-4 px-3">
            {{ $pengajuan->links() }}
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .cursor-pointer {
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .cursor-pointer:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    .custom-btn {
        font-weight: 500;
        letter-spacing: 0.3px;
    }
    .dropdown-toggle::after {
        display: none !important;
    }
    .italic {
        font-style: italic;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let currentStatus = '';

    $(document).ready(function() {
        $('#pengajuanTable').DataTable({
            pageLength: 25,
            order: [[7, 'desc']],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
            },
            responsive: true,
            searching: false,
            paging: false,
            info: false,
            columnDefs: [{
                targets: "datatable-nosort",
                orderable: false,
            }]
        });
    });

    function filterByStatus(status) {
        currentStatus = status;
        $('#statusFilter').val(status).trigger('change');
        applyFilters();
    }

    function applyFilters() {
        let status = $('#statusFilter').val();
        let search = $('#searchInput').val();
        let dateFrom = $('#dateFrom').val();
        let dateTo = $('#dateTo').val();

        window.location.href = '{{ route("manager.pengajuan.semua") }}?status=' + status +
            '&search=' + encodeURIComponent(search) +
            '&date_from=' + dateFrom + '&date_to=' + dateTo;
    }

    function exportData(format) {
        let status = $('#statusFilter').val();
        let search = $('#searchInput').val();
        let dateFrom = $('#dateFrom').val();
        let dateTo = $('#dateTo').val();

        Swal.fire({
            title: 'Export Data',
            text: 'Sedang memproses export data...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.post('{{ route("manager.pengajuan.export") }}', {
            status: status,
            search: search,
            date_from: dateFrom,
            date_to: dateTo,
            format: format,
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

                // Create download link
                const blob = new Blob([JSON.stringify(response.data, null, 2)], { type: 'application/json' });
                const link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.download = `pengajuan_${new Date().toISOString().split('T')[0]}.json`;
                link.click();
                URL.revokeObjectURL(link.href);
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

    function viewDocuments(pengajuanId) {
        $.get('/manager/pengajuan/' + pengajuanId + '/dokumen', function(response) {
            let html = '<div class="list-group text-left">';
            response.dokumen.forEach(doc => {
                let fileIcon = doc.is_image ? 'fa-file-image-o text-warning' : 'fa-file-pdf-o text-danger';
                html += `
                    <div class="list-group-item px-2 py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <i class="fa ${fileIcon} fa-2x mr-3"></i>
                                <div class="d-inline-block">
                                    <div class="weight-600 font-14">${doc.nama_file}</div>
                                    <small class="text-muted">${doc.formatted_file_size} • ${doc.mime_type}</small>
                                </div>
                            </div>
                            <div>
                                <a href="${doc.file_url}" target="_blank" class="btn btn-sm btn-info mr-1">
                                    <i class="fa fa-eye"></i>
                                </a>
                                <a href="${doc.file_url}" download class="btn btn-sm btn-success">
                                    <i class="fa fa-download"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                `;
            });
            html += '</div>';

            Swal.fire({
                title: 'Dokumen Pengajuan',
                html: html,
                width: '600px',
                confirmButtonText: 'Tutup'
            });
        }).fail(function() {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Tidak dapat memuat dokumen'
            });
        });
    }
</script>
@endpush

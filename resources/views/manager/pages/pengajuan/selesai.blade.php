@extends('manager.layouts.app')

@section('title', 'Pengajuan Selesai')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('manager.dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="#">Pengajuan</a></li>
<li class="breadcrumb-item active">Selesai</li>
@endsection

@section('content')
<!-- Header Banner Ala DeskApp -->
<div class="page-header mb-4">
    <div class="row align-items-center">
        <div class="col-md-6 col-sm-12">
            <div class="title">
                <h4>Pengajuan Selesai</h4>
            </div>
            <nav aria-label="breadcrumb" role="navigation">
                <ol class="breadcrumb mb-0">
                    @yield('breadcrumb')
                </ol>
            </nav>
        </div>
        <div class="col-md-6 col-sm-12 text-md-right d-none d-md-block">
            <span class="text-muted">Pengajuan yang telah selesai diproses (Disetujui/Ditolak)</span>
        </div>
    </div>
</div>

<!-- Statistik Mini Widget DeskApp Style -->
<div class="row mb-4">
    <div class="col-md-6 mb-3">
        <div class="card-box p-3 d-flex align-items-center justify-content-between" style="min-height: 100px;">
            <div class="w-75">
                <p class="text-muted weight-500 mb-1">Disetujui</p>
                <h4 class="mb-0 weight-700 font-24 text-success" id="totalDisetujui">0</h4>
            </div>
            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center p-3" style="width: 50px; height: 50px;">
                <i class="fa fa-check-circle-o text-success font-24"></i>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-3">
        <div class="card-box p-3 d-flex align-items-center justify-content-between" style="min-height: 100px;">
            <div class="w-75">
                <p class="text-muted weight-500 mb-1">Ditolak</p>
                <h4 class="mb-0 weight-700 font-24 text-danger" id="totalDitolak">0</h4>
            </div>
            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center p-3" style="width: 50px; height: 50px;">
                <i class="fa fa-times-circle-o text-danger font-24"></i>
            </div>
        </div>
    </div>
</div>

<!-- Tabel Pengajuan Selesai Layout -->
<div class="card-box mb-30">
    <div class="pd-20">
        <h5 class="text-blue h4"><i class="fa fa-history mr-2"></i>Riwayat Pengajuan Selesai</h5>
    </div>
    <div class="pb-20">
        <div class="table-responsive px-3">
            <table class="table table-hover nowrap" id="selesaiTable" width="100%">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Debitur</th>
                        <th>Marketing</th>
                        <th>Admin</th>
                        <th>Jumlah Pinjaman</th>
                        <th>Hasil</th>
                        <th>Skor</th>
                        <th>Tanggal Selesai</th>
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
                        <td class="weight-500 text-dark">{{ $item->marketing->nama_lengkap ?? '-' }}</td>
                        <td class="weight-500 text-dark">{{ $item->admin->nama_lengkap ?? '-' }}</td>
                        <td class="weight-600 text-dark">Rp {{ number_format($item->jumlah_pinjaman ?? 0, 0, ',', '.') }}</td>
                        <td>
                            @if($item->status == App\Models\Pengajuan::STATUS_DISETUJUI_SISTEM)
                                <span class="badge badge-success px-2 py-1">
                                    <i class="fa fa-check-circle mr-1"></i> DISETUJUI
                                </span>
                            @else
                                <span class="badge badge-danger px-2 py-1">
                                    <i class="fa fa-times-circle mr-1"></i> DITOLAK
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($item->penilaian)
                                @php
                                    $badgeClass = $item->penilaian->skor_akhir >= 75 ? 'badge-success' : 'badge-danger';
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
                                <div class="weight-500 text-dark">{{ $item->tgl_selesai?->format('d/m/Y') ?? '-' }}</div>
                                <small class="text-muted">{{ $item->tgl_selesai?->format('H:i') ?? '' }}</small>
                            </div>
                        </td>
                        <td>
                            <a href="{{ route('manager.pengajuan.show', $item->id) }}" class="btn btn-sm btn-info mr-1">
                                <i class="fa fa-eye"></i>
                            </a>
                            @if($item->penilaian && $item->status == App\Models\Pengajuan::STATUS_DISETUJUI_SISTEM)
                            <button onclick="printSuratPersetujuan({{ $item->id }})" class="btn btn-sm btn-success">
                                <i class="fa fa-print"></i>
                            </button>
                            @endif
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
    .dropdown-toggle::after {
        display: none !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        const table = $('#selesaiTable').DataTable({
            pageLength: 25,
            order: [[7, 'desc']],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
            },
            responsive: true,
            searching: true,
            paging: false,
            info: false,
            columnDefs: [{
                targets: "datatable-nosort",
                orderable: false,
            }],
            drawCallback: function() {
                updateStats();
            }
        });

        function updateStats() {
            let disetujui = 0;
            let ditolak = 0;

            $('#selesaiTable tbody tr').each(function() {
                const statusText = $(this).find('td:eq(5)').text();
                if (statusText.includes('DISETUJUI')) {
                    disetujui++;
                } else if (statusText.includes('DITOLAK')) {
                    ditolak++;
                }
            });

            $('#totalDisetujui').text(disetujui.toLocaleString());
            $('#totalDitolak').text(ditolak.toLocaleString());
        }
    });

    function printSuratPersetujuan(pengajuanId) {
        window.open('/manager/pengajuan/' + pengajuanId + '/cetak-persetujuan', '_blank');

        Swal.fire({
            icon: 'success',
            title: 'Cetak Surat',
            text: 'Surat persetujuan sedang diproses',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000
        });
    }
</script>
@endpush

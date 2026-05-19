@extends('manager.layouts.app')

@section('title', 'Pengajuan Sedang Diproses')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('manager.dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="#">Pengajuan</a></li>
<li class="breadcrumb-item active">Sedang Diproses</li>
@endsection

@section('content')
<!-- Header Banner Ala DeskApp -->
<div class="page-header mb-4">
    <div class="row align-items-center">
        <div class="col-md-6 col-sm-12">
            <div class="title">
                <h4>Pengajuan Sedang Diproses</h4>
            </div>
            <nav aria-label="breadcrumb" role="navigation">
                <ol class="breadcrumb mb-0">
                    @yield('breadcrumb')
                </ol>
            </nav>
        </div>
        <div class="col-md-6 col-sm-12 text-md-right d-none d-md-block">
            <span class="text-muted">Pengajuan yang sedang dalam proses verifikasi dan penilaian</span>
        </div>
    </div>
</div>

<!-- Info Cards DeskApp Mini Widget Style -->
<div class="row mb-4">
    <div class="col-md-4 mb-3">
        <div class="card-box p-3 d-flex align-items-center justify-content-between" style="min-height: 100px;">
            <div class="w-75">
                <p class="text-muted weight-500 mb-1">Rata-rata Waktu Proses</p>
                <h4 class="mb-0 weight-700 font-24 text-dark">{{ number_format($rataWaktu, 1) }} hari</h4>
            </div>
            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center p-3" style="width: 50px; height: 50px;">
                <i class="fa fa-clock-o text-info font-24"></i>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card-box p-3 d-flex align-items-center justify-content-between" style="min-height: 100px;">
            <div class="w-75">
                <p class="text-muted weight-500 mb-1">Total Dalam Proses</p>
                <h4 class="mb-0 weight-700 font-24 text-dark">{{ number_format($pengajuan->total()) }}</h4>
            </div>
            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center p-3" style="width: 50px; height: 50px;">
                <i class="fa fa-hourglass-half text-warning font-24"></i>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card-box p-3 d-flex align-items-center justify-content-between" style="min-height: 100px;">
            <div class="w-75">
                <p class="text-muted weight-500 mb-1">Melebihi SLA (7 hari)</p>
                <h4 class="mb-0 weight-700 font-24 text-danger" id="melebihiSLA">0</h4>
            </div>
            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center p-3" style="width: 50px; height: 50px;">
                <i class="fa fa-exclamation-triangle text-danger font-24"></i>
            </div>
        </div>
    </div>
</div>

<!-- Tabel Pengajuan Diproses DeskApp Layout -->
<div class="card-box mb-30">
    <div class="pd-20">
        <h5 class="text-blue h4"><i class="fa fa-list mr-2"></i>Daftar Pengajuan Dalam Proses</h5>
    </div>
    <div class="pb-20">
        <div class="table-responsive px-3">
            <table class="table table-hover nowrap" id="prosesTable" width="100%">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Debitur</th>
                        <th>Marketing</th>
                        <th>Status Saat Ini</th>
                        <th>Lama Proses</th>
                        <th>Terakhir Update</th>
                        <th class="datatable-nosort">Aksi</th>
                    </tr>
                </thead>
               <tbody>
    @foreach($pengajuan as $item)
    @php
        $startDate = $item->tgl_submitted ?? $item->created_at;
        // Dibungkus round() untuk memaksa hasil selisih hari menjadi angka bulat
        $lamaHari = $startDate ? round($startDate->diffInDays(now())) : 0;
        $isOverdue = $lamaHari > 7;
    @endphp
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
        <td class="weight-500 text-dark">
            {{ $item->marketing->nama_lengkap ?? 'Belum diambil' }}
        </td>
        <td>{!! $item->status_badge !!}</td>
        <td>
            @if($isOverdue)
                <span class="badge badge-danger sla-overdue-badge">
                    <!-- number_format dengan parameter 0 memastikan tidak ada angka di belakang koma -->
                    <i class="fa fa-clock-o mr-1"></i>{{ number_format($lamaHari, 0, ',', '.') }} hari
                </span>
                <br>
                <small class="text-danger weight-600">Melebihi SLA!</small>
            @else
                <span class="badge badge-warning">
                    <i class="fa fa-hourglass-half mr-1"></i>{{ number_format($lamaHari, 0, ',', '.') }} hari
                </span>
            @endif
        </td>
        <td>
            <div>
                <div class="weight-500 text-dark">{{ $item->updated_at?->format('d/m/Y') ?? '-' }}</div>
                <small class="text-muted">{{ $item->updated_at?->format('H:i') ?? '' }}</small>
            </div>
        </td>
        <td>
            <a href="{{ route('manager.pengajuan.show', $item->id) }}" class="btn btn-sm btn-info custom-btn">
                <i class="fa fa-eye mr-1"></i> Detail
            </a>
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
    .custom-btn {
        font-weight: 500;
        letter-spacing: 0.3px;
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        $('#prosesTable').DataTable({
            pageLength: 25,
            order: [[4, 'desc']],
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
            }]
        });

        // Hitung jumlah melebihi SLA menggunakan class penanda khusus
        let overdueCount = $('.sla-overdue-badge').length;
        $('#melebihiSLA').text(overdueCount);
    });
</script>
@endpush

@extends('admin.layouts.app')

@section('title', 'Laporan Pengajuan')

@section('content')
<div class="container-fluid">
    <div class="card-box mb-30">
        <div class="pd-20">
            <h4 class="text-blue h4">Laporan Pengajuan</h4>
            
            <!-- Filter Form -->
            <form method="GET" action="{{ route('admin.laporan.index') }}" class="row mt-3">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Dari Tanggal</label>
                        <input type="date" name="dari" class="form-control" value="{{ request('dari') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Sampai Tanggal</label>
                        <input type="date" name="sampai" class="form-control" value="{{ request('sampai') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="icon-copy dw dw-filter"></i> Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>
        
        <div class="pb-20">
            <table class="table table-striped table-bordered dt-responsive nowrap" style="width:100%">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nomor Laporan</th>
                        <th>Nama Debitur</th>
                        <th>No. KTP</th>
                        <th>Jenis Pengajuan</th>
                        <th>Plafon</th>
                        <th>Status</th>
                        <th>Tgl Cetak</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($laporan ?? [] as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <code>{{ $item->nomor_laporan ?? '-' }}</code>
                        </td>
                        <td>{{ $item->nama_debitur ?? '-' }}</td>
                        <td>{{ $item->no_ktp ?? '-' }}</td>
                        <td>{{ $item->jenis_pengajuan ?? '-' }}</td>
                        <td>Rp {{ number_format($item->plafon_pengajuan ?? 0, 0, ',', '.') }}</td>
                        <td>
                            @php
                                $status = $item->status_pengajuan ?? 'pending';
                                $badge = match($status) {
                                    'approved' => 'success',
                                    'rejected' => 'danger',
                                    'process' => 'warning',
                                    default => 'secondary'
                                };
                            @endphp
                            <span class="badge badge-{{ $badge }}">{{ ucfirst($status ?? 'Pending') }}</span>
                        </td>
                        <td>{{ $item->tgl_cetak ? date('d M Y H:i', strtotime($item->tgl_cetak)) : '-' }}</td>
                        <td>
                            @if($item->path_file)
                            <a href="{{ asset('storage/' . $item->path_file) }}" class="btn btn-sm btn-info" target="_blank">
                                <i class="icon-copy dw dw-download"></i> Unduh
                            </a>
                            @else
                            <button class="btn btn-sm btn-success generate-laporan" data-id="{{ $item->pengajuan_id }}">
                                <i class="icon-copy dw dw-printer"></i> Cetak
                            </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center">Tidak ada data laporan</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('.generate-laporan').on('click', function() {
        const id = $(this).data('id');
        const btn = $(this);
        
        Swal.fire({
            title: 'Konfirmasi',
            text: 'Cetak laporan untuk pengajuan ini?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Cetak',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                btn.html('<i class="icon-copy dw dw-loading"></i> Memproses...');
                btn.prop('disabled', true);
                
                $.ajax({
                    url: '{{ route("admin.laporan.cetak") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        pengajuan_id: id
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Berhasil!', 'Laporan berhasil dicetak', 'success')
                                .then(() => location.reload());
                        } else {
                            Swal.fire('Error!', response.message, 'error');
                            btn.html('<i class="icon-copy dw dw-printer"></i> Cetak');
                            btn.prop('disabled', false);
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error!', 'Terjadi kesalahan', 'error');
                        btn.html('<i class="icon-copy dw dw-printer"></i> Cetak');
                        btn.prop('disabled', false);
                    }
                });
            }
        });
    });
});
</script>
@endsection
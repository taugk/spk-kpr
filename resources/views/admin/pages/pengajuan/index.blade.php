@extends('admin.layouts.app')

@section('title', 'Pengajuan KPR')

@section('page_action')
<a href="{{ route('admin.pengajuan.export') }}" class="btn btn-outline-primary">
    <i class="dw dw-download"></i> Export
</a>
@endsection

@section('content')
<div class="card-box mb-30">
    <div class="pd-20">
        <form method="GET" class="row">
            <div class="col-md-4 mb-2">
                <input type="text" name="keyword" value="{{ request('keyword') }}" class="form-control" placeholder="Cari kode/debitur/proyek">
            </div>
            <div class="col-md-3 mb-2">
                <select name="status" class="custom-select">
                    <option value="">Semua Status</option>
                    @foreach(['draft', 'submitted', 'verifikasi_marketing', 'revisi_debitur', 'ditolak_marketing', 'antrian_admin', 'penilaian_admin', 'selesai_dinilai', 'ditolak_sistem', 'disetujui_sistem'] as $status)
                        <option value="{{ $status }}" @selected(request('status') == $status)>{{ Str::headline(str_replace('_', ' ', $status)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 mb-2">
                <button class="btn btn-primary btn-block">
                    <i class="dw dw-filter"></i> Filter
                </button>
            </div>
            <div class="col-md-2 mb-2">
                <a href="{{ route('admin.pengajuan.index') }}" class="btn btn-secondary btn-block">
                    <i class="dw dw-refresh"></i> Reset
                </a>
            </div>
            <div class="col-md-1 mb-2">
                <button type="button" class="btn btn-info btn-block" id="quickFilterPenilaian" title="Filter antrian penilaian">
                    <i class="dw dw-analytics-21"></i>
                </button>
            </div>
        </form>
    </div>

    <div class="pb-20">
        <table class="data-table table stripe hover nowrap">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Debitur</th>
                    <th>Proyek / Unit</th>
                    <th>Harga</th>
                    <th>DP</th>
                    <th>Pinjaman</th>
                    <th>Status</th>
                    <th class="datatable-nosort">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pengajuan as $item)
                    <tr>
                        <td>
                            <strong>{{ $item->kode_pengajuan }}</strong>
                            <br>
                            <small class="text-muted">{{ $item->created_at ? $item->created_at->format('d/m/Y') : '-' }}</small>
                        </td>
                        <td>
                            {{ $item->nama_debitur ?? '-' }}
                            <br>
                            <small class="text-muted">{{ $item->email_debitur ?? '' }}</small>
                         </td>
                        <td>
                            {{ $item->nama_proyek ?? '-' }}
                            <br>
                            <small>{{ $item->kode_unit ?? '-' }}</small>
                         </td>
                        <td>Rp {{ number_format($item->harga_properti ?? 0, 0, ',', '.') }}</td>
                        <td>
                            {{ number_format($item->persen_dp ?? 0, 0, ',', '.') }}%
                            <br>
                            <small>Rp {{ number_format($item->uang_muka ?? 0, 0, ',', '.') }}</small>
                         </td>
                        <td>Rp {{ number_format($item->jumlah_pinjaman ?? 0, 0, ',', '.') }}</td>
                        <td>
                            @include('admin.components.status-badge', ['status' => $item->status])
                            @if(in_array($item->status, ['disetujui_sistem', 'ditolak_sistem']) && isset($item->skor_akhir))
                                <br>
                                <small class="text-{{ $item->status == 'disetujui_sistem' ? 'success' : 'danger' }}">
                                    Skor: {{ number_format($item->skor_akhir, 2) }}
                                </small>
                            @endif
                            @if($item->status == 'antrian_admin')
                                <br>
                                <span class="badge badge-warning badge-sm mt-1">
                                    <i class="dw dw-clock"></i> Menunggu Penilaian
                                </span>
                            @endif
                            @if($item->status == 'penilaian_admin')
                                <br>
                                <span class="badge badge-info badge-sm mt-1">
                                    <i class="dw dw-analytics-21"></i> Sedang Dinilai
                                </span>
                            @endif
                         </td>
                        <td>
                            <div class="dropdown">
                                <a class="btn btn-link font-24 p-0 line-height-1 no-arrow dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                                    <i class="dw dw-more"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
                                    <!-- Detail -->
                                    <a class="dropdown-item" href="{{ route('admin.pengajuan.show', $item->pengajuan_id) }}">
                                        <i class="dw dw-eye"></i> Detail
                                    </a>
                                    
                                    <!-- Tombol Penilaian SMART -->
                                    @if(in_array($item->status, ['antrian_admin', 'penilaian_admin', 'selesai_dinilai']) && !isset($item->hasil_smart))
                                        <a class="dropdown-item text-primary" href="{{ route('admin.penilaian.create', ['pengajuan_id' => $item->pengajuan_id]) }}">
                                            <i class="dw dw-analytics-21"></i> Nilai SMART
                                        </a>
                                    @endif
                                    
                                    <!-- Tombol Penilaian Ulang -->
                                    @if(in_array($item->status, ['selesai_dinilai', 'ditolak_sistem']) && isset($item->hasil_smart))
                                        <a class="dropdown-item text-warning" href="{{ route('admin.penilaian.create', ['pengajuan_id' => $item->pengajuan_id, 'revisi' => true]) }}">
                                            <i class="dw dw-refresh"></i> Nilai Ulang SMART
                                        </a>
                                    @endif
                                    
                                    <!-- Tombol Penilaian Manual -->
                                    @if(in_array($item->status, ['antrian_admin', 'penilaian_admin', 'selesai_dinilai', 'ditolak_sistem']))
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item text-success" href="#" onclick="manualAssessment({{ $item->pengajuan_id }})">
                                            <i class="dw dw-edit-2"></i> Penilaian Manual
                                        </a>
                                    @endif
                                    
                                    <!-- Cetak Persetujuan -->
                                    @if($item->status == 'disetujui_sistem')
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item text-info" href="{{ route('admin.laporan.cetak', $item->pengajuan_id) }}" target="_blank">
                                            <i class="dw dw-printer"></i> Cetak Persetujuan
                                        </a>
                                    @endif
                                    
                                    <!-- Cetak Penolakan -->
                                    @if($item->status == 'ditolak_sistem')
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item text-danger" href="{{ route('admin.laporan.cetak-penolakan', $item->pengajuan_id) }}" target="_blank">
                                            <i class="dw dw-printer"></i> Cetak Penolakan
                                        </a>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Button Penilaian Cepat (visible di mobile) -->
                            @if(in_array($item->status, ['antrian_admin', 'penilaian_admin', 'selesai_dinilai']) && !isset($item->hasil_smart))
                                <div class="d-block d-sm-none mt-2">
                                    <a href="{{ route('admin.penilaian.create', ['pengajuan_id' => $item->pengajuan_id]) }}" 
                                       class="btn btn-sm btn-primary btn-block">
                                        <i class="dw dw-analytics-21"></i> Nilai
                                    </a>
                                </div>
                            @endif
                         </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <i class="dw dw-information mr-1"></i> 
                            Data pengajuan belum tersedia.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if(method_exists($pengajuan, 'links') && $pengajuan->hasPages())
    <div class="d-flex justify-content-center mt-3">
        {{ $pengajuan->appends(request()->query())->links() }}
    </div>
@endif

<!-- Modal Penilaian Manual -->
<div class="modal fade" id="manualAssessmentModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Penilaian Manual</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="manualAssessmentForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="dw dw-info"></i>
                        Penilaian manual digunakan untuk menentukan kelayakan pengajuan secara langsung tanpa perhitungan SMART.
                    </div>
                    
                    <div class="form-group">
                        <label>Keputusan</label>
                        <select name="status" class="form-control" required>
                            <option value="disetujui_sistem">Disetujui</option>
                            <option value="ditolak_sistem">Ditolak</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Catatan Penilaian</label>
                        <textarea name="catatan" class="form-control" rows="3" placeholder="Masukkan catatan penilaian (opsional)"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Skor Akhir (opsional)</label>
                        <input type="number" step="0.01" name="skor_akhir" class="form-control" placeholder="0 - 100">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Simpan Penilaian</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Penilaian Ulang -->
<div class="modal fade" id="reassessmentModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Penilaian Ulang</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Penilaian ulang akan menghapus hasil penilaian sebelumnya dan memulai penilaian baru.</p>
                <p class="text-warning"><i class="dw dw-warning"></i> Apakah Anda yakin?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <a href="#" id="reassessmentLink" class="btn btn-warning">Ya, Nilai Ulang</a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .badge-sm {
        font-size: 10px;
        padding: 3px 6px;
    }
    
    .dropdown-menu-icon-list .dropdown-item i {
        margin-right: 8px;
    }
    
    @media (max-width: 768px) {
        .data-table {
            font-size: 12px;
        }
        
        .dropdown-menu-icon-list .dropdown-item {
            font-size: 12px;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    // Quick filter untuk antrian penilaian
    $('#quickFilterPenilaian').on('click', function() {
        $('select[name="status"]').val('antrian_admin');
        $('form').submit();
    });
    
    // Manual assessment
    window.manualAssessment = function(pengajuanId) {
        $('#manualAssessmentForm').attr('action', '/admin/pengajuan/' + pengajuanId + '/manual-assessment');
        $('#manualAssessmentModal').modal('show');
    };
    
    // Submit manual assessment
    $('#manualAssessmentForm').on('submit', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const url = form.attr('action');
        const data = form.serialize();
        
        Swal.fire({
            title: 'Konfirmasi',
            text: 'Pastikan data sudah benar sebelum menyimpan penilaian!',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Simpan',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
                    method: 'POST',
                    data: data,
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Berhasil!', response.message, 'success')
                                .then(() => location.reload());
                        } else {
                            Swal.fire('Gagal!', response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        let errorMsg = 'Terjadi kesalahan';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        Swal.fire('Error!', errorMsg, 'error');
                    }
                });
            }
        });
    });
    
    // Reassessment confirmation
    $('.dropdown-item[href*="revisi=true"]').on('click', function(e) {
        e.preventDefault();
        const url = $(this).attr('href');
        $('#reassessmentLink').attr('href', url);
        $('#reassessmentModal').modal('show');
    });
    
    // Tooltip untuk tombol
    $('[title]').tooltip();
});
</script>
@endpush
@extends('marketing.layouts.app')

@section('title', 'Verifikasi Dokumen Pengajuan KPR')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    :root {
        --primary: #1b0032;
        --primary-light: #2a1a3a;
        --success: #00ac4f;
        --danger: #dc3545;
        --warning: #ffa000;
        --info: #00bcd4;
        --gray-bg: #f8f9fa;
    }

    body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f5f7fc; }

    .card-box { border-radius: 12px; overflow: hidden; margin-bottom: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); background: white; }
    .pd-20 { padding: 20px; }
    .border-top { border-top: 1px solid #eef2f6; }
    .border-bottom { border-bottom: 1px solid #eef2f6; }

    .info-row { display: flex; padding: 10px 0; border-bottom: 1px solid #f0f2f5; }
    .info-row:last-child { border-bottom: none; }
    .info-label { width: 140px; font-weight: 600; color: #6c757d; font-size: 13px; }
    .info-value { flex: 1; font-weight: 500; color: #1b0032; font-size: 14px; }

    .badge-status { padding: 5px 14px; border-radius: 20px; font-size: 12px; font-weight: 600; }
    .badge-warning-light { background: #fff8e1; color: #ff8f00; }
    .badge-success-light { background: #e8f5e9; color: #2e7d32; }
    .badge-danger-light { background: #ffebee; color: #c62828; }
    .badge-info-light { background: #e3f2fd; color: #1565c0; }

    .dokumen-card { border: 1px solid #eef2f6; border-radius: 12px; padding: 16px; transition: all 0.3s ease; background: #fff; height: 100%; }
    .dokumen-card:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(0,0,0,0.08); border-color: var(--primary); }
    .dokumen-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 22px; }
    .dokumen-icon.pdf { background: #ffebee; color: #dc2626; }
    .dokumen-icon.image { background: #e3f2fd; color: #3b82f6; }
    .dokumen-icon.default { background: #f3e5f5; color: var(--primary); }
    .dokumen-nama { font-weight: 700; font-size: 14px; margin: 12px 0 4px; color: #1b0032; }
    .dokumen-ukuran { font-size: 11px; color: #9e9e9e; margin-bottom: 12px; }

    .btn-dokumen { padding: 6px 12px; font-size: 12px; font-weight: 600; border-radius: 8px; border: none; cursor: pointer; transition: all 0.2s; margin-right: 6px; }
    .btn-preview { background: #e8eaf6; color: #3949ab; }
    .btn-preview:hover { background: #3949ab; color: #fff; }
    .btn-download { background: #e8f5e9; color: #2e7d32; }
    .btn-download:hover { background: #2e7d32; color: #fff; }

    .verifikasi-item { border: 1px solid #eef2f6; border-radius: 12px; padding: 20px; margin-bottom: 16px; background: #fff; transition: all 0.2s; }
    .verifikasi-item:hover { border-color: #d0d7de; }
    .verifikasi-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; flex-wrap: wrap; gap: 10px; }
    .verifikasi-nama { font-weight: 700; font-size: 15px; color: #1b0032; }

    .spinner-wrapper { display: inline-block; width: 18px; height: 18px; border: 2px solid rgba(255,255,255,.3); border-radius: 50%; border-top-color: #fff; animation: spin 0.8s linear infinite; margin-right: 8px; }
    @keyframes spin { to { transform: rotate(360deg); } }

    .form-control:focus, .custom-select:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(27,0,50,0.1); }
    .btn-primary { background: var(--primary); border: none; padding: 10px 24px; border-radius: 10px; font-weight: 600; }
    .btn-primary:hover { background: var(--primary-light); transform: translateY(-1px); }

    .text-blue { color: var(--primary); }
    .bg-light { background: var(--gray-bg); }
    .gap-2 { gap: 8px; }
    .text-danger { color: var(--danger); }

    /* Timeline */
    .timeline-wrapper { position: relative; padding-left: 30px; }
    .timeline-line { position: absolute; left: 8px; top: 0; bottom: 0; width: 2px; background: linear-gradient(to bottom, var(--primary) 0%, #e0e0e0 100%); }
    .timeline-dot { position: absolute; left: -22px; top: 8px; width: 12px; height: 12px; border-radius: 50%; background: var(--primary); border: 2px solid white; box-shadow: 0 0 0 2px rgba(27,0,50,0.2); z-index: 1; }
    .timeline-bubble { background: white; border-radius: 12px; padding: 14px 18px; margin-left: 10px; border: 1px solid #eef2f6; box-shadow: 0 2px 8px rgba(0,0,0,0.04); }

    /* Status badge */
    .status-valid { background: #e8f5e9; color: #2e7d32; }
    .status-invalid { background: #ffebee; color: #c62828; }
    .status-pending { background: #fff8e1; color: #ff8f00; }
    .status-badge-sm { display: inline-block; padding: 2px 8px; border-radius: 12px; font-size: 10px; font-weight: 600; }
</style>
@endpush

@section('content')
<div class="pd-20">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1"><i class="fa fa-file-alt text-blue mr-2"></i> Verifikasi Dokumen KPR</h4>
            <p class="text-muted mb-0">Lakukan verifikasi kelengkapan dan keabsahan dokumen pengajuan</p>
        </div>
        <a href="{{ route('marketing.verifikasi.dokumen') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fa fa-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- Informasi Pengajuan -->
    <div class="card-box">
        <div class="pd-20 d-flex justify-content-between align-items-center border-bottom">
            <h5 class="mb-0 text-blue"><i class="fa fa-info-circle mr-2"></i> Informasi Pengajuan</h5>
            <span class="badge-status {{ $pengajuan->status == 'verifikasi_marketing' ? 'badge-warning-light' : 'badge-info-light' }}">
                {{ ucfirst(str_replace('_', ' ', $pengajuan->status ?? 'Menunggu Verifikasi')) }}
            </span>
        </div>
        <div class="pd-20">
            <div class="row">
                <div class="col-md-6">
                    <div class="info-row"><div class="info-label">Kode Pengajuan</div><div class="info-value"><strong>{{ $pengajuan->kode_pengajuan }}</strong></div></div>
                    <div class="info-row"><div class="info-label">Nama Debitur</div><div class="info-value">{{ $pengajuan->user->nama_lengkap ?? '-' }}</div></div>
                    <div class="info-row"><div class="info-label">NIK / No. KTP</div><div class="info-value">{{ $pengajuan->debiturPribadi->nik ?? '-' }}</div></div>
                    <div class="info-row"><div class="info-label">No. HP / WA</div><div class="info-value">{{ $pengajuan->debiturPribadi->no_hp ?? '-' }}</div></div>
                </div>
                <div class="col-md-6">
                    <div class="info-row"><div class="info-label">Proyek</div><div class="info-value">{{ $pengajuan->unit->tipeUnit->proyek->nama_proyek ?? '-' }}</div></div>
                    <div class="info-row"><div class="info-label">Tipe Unit</div><div class="info-value">{{ $pengajuan->unit->tipeUnit->nama_tipe ?? '-' }}</div></div>
                    <div class="info-row"><div class="info-label">Kode Unit</div><div class="info-value">{{ $pengajuan->unit->kode_unit ?? '-' }}</div></div>
                    <div class="info-row"><div class="info-label">Jumlah Pinjaman</div><div class="info-value"><strong class="text-blue">Rp {{ number_format($pengajuan->jumlah_pinjaman, 0, ',', '.') }}</strong></div></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Daftar Dokumen -->
    <div class="card-box">
        <div class="pd-20 d-flex justify-content-between align-items-center border-bottom">
            <h5 class="mb-0 text-blue"><i class="fa fa-folder-open mr-2"></i> Dokumen yang Diupload</h5>
            <span class="text-muted small"><i class="fa fa-file"></i> {{ $pengajuan->dokumen->count() }} dokumen terupload</span>
        </div>
        <div class="pd-20">
            <div class="row">
                @forelse($pengajuan->dokumen as $dokumen)
                @php
                    $icons = [
                        'ktp'             => 'fa-id-card',
                        'kk'              => 'fa-users',
                        'npwp'            => 'fa-file-text',
                        'slip_gaji'       => 'fa-money',
                        'rekening_koran'  => 'fa-bank',
                        'sk_kerja'        => 'fa-building',
                        'slik'            => 'fa-chart-line',
                        'buku_nikah'      => 'fa-heart',
                        'ktp_pasangan'    => 'fa-id-card',
                        'pas_foto'        => 'fa-camera',
                    ];
                    $icon = $icons[$dokumen->jenis_dokumen] ?? 'fa-file';

                    // FIX: gunakan mime_type (bukan file_type)
                    $isPdf   = $dokumen->mime_type === 'application/pdf';
                    $isImage = in_array($dokumen->mime_type, ['image/jpeg', 'image/jpg', 'image/png', 'image/gif']);

                    // FIX: field mapping sesuai jenis_dokumen di database
                    $verifikasi   = $pengajuan->verifikasiMarketing;
                    $fieldMapping = [
                        'ktp'            => 'dok_ktp_valid',
                        'kk'             => 'dok_kk_valid',
                        'npwp'           => 'dok_npwp_valid',
                        'slip_gaji'      => 'dok_slip_gaji_valid',
                        'rekening_koran' => 'dok_rek_koran_valid',
                        'slik'           => 'dok_slik_valid',
                        'sk_kerja'       => 'dok_surat_kerja_valid',
                    ];
                    $fieldName  = $fieldMapping[$dokumen->jenis_dokumen] ?? null;
                    $statusValid = $verifikasi && $fieldName ? ($verifikasi->$fieldName ?? null) : null;

                    $statusText  = 'Belum Diverifikasi';
                    $statusClass = 'status-pending';
                    if ($statusValid === true) {
                        $statusText  = 'Valid';
                        $statusClass = 'status-valid';
                    } elseif ($statusValid === false) {
                        $statusText  = 'Tidak Valid';
                        $statusClass = 'status-invalid';
                    }
                @endphp
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="dokumen-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="dokumen-icon {{ $isPdf ? 'pdf' : ($isImage ? 'image' : 'default') }}">
                                <i class="fa {{ $icon }}"></i>
                            </div>
                            <span class="status-badge-sm {{ $statusClass }}">
                                <i class="fa {{ $statusValid === true ? 'fa-check' : ($statusValid === false ? 'fa-times' : 'fa-clock-o') }}"></i>
                                {{ $statusText }}
                            </span>
                        </div>
                        <div class="dokumen-nama">{{ ucfirst(str_replace('_', ' ', $dokumen->jenis_dokumen)) }}</div>
                        <div class="dokumen-ukuran">
                            {{-- FIX: gunakan nama_file dan ukuran_file --}}
                            {{ strlen($dokumen->nama_file) > 35 ? substr($dokumen->nama_file, 0, 35) . '...' : $dokumen->nama_file }}<br>
                            {{ number_format($dokumen->ukuran_file / 1024, 2) }} KB
                        </div>
                        <div>
                            @if($isPdf || $isImage)
                                <button type="button"
                                        class="btn-dokumen btn-preview"
                                        {{-- FIX: gunakan path_file dan mime_type --}}
                                        onclick="previewDocument('{{ $dokumen->path_file }}', '{{ $dokumen->nama_file }}', '{{ $dokumen->mime_type }}')">
                                    <i class="fa fa-eye"></i> Preview
                                </button>
                            @endif
                            <button type="button"
                                    class="btn-dokumen btn-download"
                                    {{-- FIX: gunakan path_file dan nama_file --}}
                                    onclick="downloadDocument('{{ $dokumen->path_file }}', '{{ $dokumen->nama_file }}')">
                                <i class="fa fa-download"></i> Download
                            </button>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="fa fa-folder-open fa-3x text-muted"></i>
                        <p class="mt-2 text-muted">Belum ada dokumen yang diupload</p>
                    </div>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Form Verifikasi -->
    <div class="card-box">
        <div class="pd-20 border-bottom">
            <h5 class="mb-0 text-blue"><i class="fa fa-clipboard-list mr-2"></i> Verifikasi Dokumen</h5>
        </div>
        <div class="pd-20">
            <form id="formVerifikasi" method="POST" action="{{ route('marketing.verifikasi.proses', $pengajuan->id) }}">
                @csrf
                <div id="verifikasiDokumenList">
                    @foreach($pengajuan->dokumen as $dokumen)
                    @php
                        // FIX: field mapping sesuai jenis_dokumen di database
                        $statusMapping = [
                            'ktp'            => 'dok_ktp_valid',
                            'kk'             => 'dok_kk_valid',
                            'npwp'           => 'dok_npwp_valid',
                            'slip_gaji'      => 'dok_slip_gaji_valid',
                            'rekening_koran' => 'dok_rek_koran_valid',
                            'slik'           => 'dok_slik_valid',
                            'sk_kerja'       => 'dok_surat_kerja_valid',
                        ];
                        $fieldName     = $statusMapping[$dokumen->jenis_dokumen] ?? null;
                        $currentStatus = null;
                        if ($fieldName && $pengajuan->verifikasiMarketing) {
                            $currentStatus = $pengajuan->verifikasiMarketing->$fieldName;
                        }
                    @endphp
                    <div class="verifikasi-item">
                        <div class="verifikasi-header">
                            <div class="verifikasi-nama">
                                {{ ucfirst(str_replace('_', ' ', $dokumen->jenis_dokumen)) }}
                                <span class="badge badge-info px-2 py-1 ml-2" style="font-size:10px">Wajib</span>
                            </div>
                            <select name="verifikasi_dokumen[{{ $dokumen->jenis_dokumen }}][status]" class="form-control form-control-sm" style="width:150px">
                                <option value="belum_diverifikasi" {{ $currentStatus === null ? 'selected' : '' }}>Belum Diverifikasi</option>
                                <option value="lengkap" {{ $currentStatus === true ? 'selected' : '' }}>✓ Lengkap & Valid</option>
                                <option value="tidak_valid" {{ $currentStatus === false ? 'selected' : '' }}>✗ Tidak Valid</option>
                            </select>
                        </div>
                        <textarea name="verifikasi_dokumen[{{ $dokumen->jenis_dokumen }}][catatan]"
                                  class="form-control" rows="2"
                                  placeholder="Catatan verifikasi (opsional)"></textarea>
                    </div>
                    @endforeach
                </div>

                <div class="bg-light p-4 rounded mt-4">
                    <h6 class="mb-3 text-blue"><i class="fa fa-check-circle mr-2"></i> Kesimpulan Verifikasi</h6>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Rekomendasi <span class="text-danger">*</span></label>
                            <select name="rekomendasi" id="rekomendasi" class="custom-select" required>
                                <option value="">Pilih Rekomendasi</option>
                                <option value="layak" {{ ($pengajuan->verifikasiMarketing->rekomendasi_marketing ?? '') == 'layak' ? 'selected' : '' }}>Layak</option>
                                <option value="perlu_pertimbangan" {{ ($pengajuan->verifikasiMarketing->rekomendasi_marketing ?? '') == 'perlu_pertimbangan' ? 'selected' : '' }}>Perlu Pertimbangan</option>
                                <option value="tidak_layak" {{ ($pengajuan->verifikasiMarketing->rekomendasi_marketing ?? '') == 'tidak_layak' ? 'selected' : '' }}>Tidak Layak</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Keputusan <span class="text-danger">*</span></label>
                            <select name="keputusan" id="keputusan" class="custom-select" required>
                                <option value="">Pilih Keputusan</option>
                                <option value="ajukan_ke_admin" {{ ($pengajuan->verifikasiMarketing->keputusan ?? '') == 'ajukan_ke_admin' ? 'selected' : '' }}>Ajukan ke Admin</option>
                                <option value="minta_revisi" {{ ($pengajuan->verifikasiMarketing->keputusan ?? '') == 'minta_revisi' ? 'selected' : '' }}>Minta Revisi</option>
                                <option value="tolak" {{ ($pengajuan->verifikasiMarketing->keputusan ?? '') == 'tolak' ? 'selected' : '' }}>Tolak</option>
                            </select>
                        </div>
                        <div class="col-12 form-group" id="alasanGroup"
                             style="display: {{ in_array($pengajuan->verifikasiMarketing->keputusan ?? '', ['minta_revisi', 'tolak']) ? 'block' : 'none' }};">
                            <label>Alasan Keputusan <span class="text-danger">*</span></label>
                            <textarea name="alasan_keputusan" id="alasanKeputusan" class="form-control" rows="3"
                                      placeholder="Isikan alasan keputusan...">{{ $pengajuan->verifikasiMarketing->alasan_keputusan ?? '' }}</textarea>
                        </div>
                        <div class="col-12 form-group">
                            <label>Catatan Tambahan</label>
                            <textarea name="catatan_verifikasi" id="catatanVerifikasi" class="form-control" rows="2"
                                      placeholder="Catatan tambahan (opsional)">{{ $pengajuan->verifikasiMarketing->catatan_verifikasi ?? '' }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="text-right mt-4">
                    <button type="submit" class="btn btn-primary btn-lg px-4" id="btnSimpan">
                        <i class="fa fa-save"></i> Simpan Verifikasi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Riwayat Verifikasi -->
    <div class="card-box" id="riwayatCard" style="display: {{ $riwayat->count() > 0 ? 'block' : 'none' }};">
        <div class="pd-20 border-bottom">
            <h5 class="mb-0 text-blue"><i class="fa fa-history mr-2"></i> Riwayat Verifikasi</h5>
        </div>
        <div class="pd-20">
            <div class="timeline-wrapper">
                <div class="timeline-line"></div>
                @foreach($riwayat as $item)
                <div class="position-relative mb-4">
                    <div class="timeline-dot"></div>
                    <div class="timeline-bubble shadow-sm">
                        <p class="mb-2 text-dark font-weight-medium" style="font-size: 14px;">
                            {{ $item->keterangan ?? ucfirst(str_replace('_', ' ', $item->status_baru)) }}
                        </p>
                        <div class="mb-2">
                            <span class="badge px-2 py-1" style="background-color: #0d6efd; font-size: 11px; color:white;">
                                <i class="fa fa-calendar"></i> {{ $item->created_at->format('d/m/Y H:i') }}
                            </span>
                        </div>
                        @if($item->pengubah)
                        <div class="text-muted text-uppercase font-weight-bold" style="font-size: 10px;">
                            <i class="fa fa-user-circle mr-1"></i> OLEH: {{ $item->pengubah->nama_lengkap ?? $item->pengubah->name ?? 'Sistem' }}
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Modal Preview -->
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-file-alt mr-2"></i> Preview Dokumen</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center" id="previewContainer" style="min-height: 400px;">
                <div class="py-5"><i class="fa fa-spinner fa-spin fa-2x"></i><p class="mt-2">Memuat dokumen...</p></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-success" id="downloadFromModal" style="display: none;">
                    <i class="fa fa-download"></i> Download
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
let currentBlobUrl = null;

// Route untuk getFile
const getFileUrl = '{{ route("marketing.verifikasi.get-file") }}';

// Toggle alasan keputusan
document.getElementById('keputusan')?.addEventListener('change', function() {
    const group = document.getElementById('alasanGroup');
    const input = document.getElementById('alasanKeputusan');
    if (this.value === 'minta_revisi' || this.value === 'tolak') {
        group.style.display = 'block';
        input.required = true;
    } else {
        group.style.display = 'none';
        input.required = false;
    }
});

// Preview dokumen via getFile
async function previewDocument(filePath, fileName, mimeType) {
    const modal      = new bootstrap.Modal(document.getElementById('previewModal'));
    const container  = document.getElementById('previewContainer');
    const downloadBtn = document.getElementById('downloadFromModal');

    container.innerHTML  = '<div class="py-5"><i class="fa fa-spinner fa-spin fa-2x"></i><p class="mt-2">Memuat dokumen...</p></div>';
    downloadBtn.style.display = 'none';
    modal.show();

    try {
        const response = await fetch(getFileUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ path: filePath })
        });

        if (!response.ok) throw new Error('Gagal memuat file (HTTP ' + response.status + ')');

        const blob = await response.blob();

        if (currentBlobUrl) URL.revokeObjectURL(currentBlobUrl);
        currentBlobUrl = URL.createObjectURL(blob);

        downloadBtn.style.display = 'inline-flex';
        downloadBtn.onclick = () => {
            const link = document.createElement('a');
            link.href     = currentBlobUrl;
            link.download = fileName;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        };

        if (mimeType && mimeType.startsWith('image/')) {
            container.innerHTML = `<img src="${currentBlobUrl}" style="max-width:100%;max-height:70vh;object-fit:contain;">`;
        } else if (mimeType === 'application/pdf') {
            container.innerHTML = `<iframe src="${currentBlobUrl}#toolbar=1" width="100%" height="500px" style="border:none;"></iframe>`;
        } else {
            container.innerHTML = `
                <div class="py-5">
                    <i class="fa fa-file fa-3x text-muted"></i>
                    <p>Preview tidak tersedia untuk tipe file ini</p>
                    <button class="btn btn-primary" onclick="document.getElementById('downloadFromModal').click()">Download</button>
                </div>`;
        }

    } catch (error) {
        console.error('Preview error:', error);
        container.innerHTML = `
            <div class="py-5">
                <i class="fa fa-exclamation-triangle fa-3x text-danger"></i>
                <p>Gagal memuat dokumen</p>
                <p class="text-muted small">${error.message}</p>
            </div>`;
        downloadBtn.style.display = 'none';
    }
}

// Download dokumen via getFile
async function downloadDocument(filePath, fileName) {
    Swal.fire({ title: 'Mengunduh...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

    try {
        const response = await fetch(getFileUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ path: filePath })
        });

        if (!response.ok) throw new Error('HTTP ' + response.status);

        const blob = await response.blob();
        const url  = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href     = url;
        link.download = fileName;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        setTimeout(() => URL.revokeObjectURL(url), 100);

        Swal.fire({ icon: 'success', title: 'Berhasil!', text: 'File berhasil diunduh.', timer: 1500, showConfirmButton: false });

    } catch (error) {
        console.error('Download error:', error);
        Swal.fire({ icon: 'error', title: 'Gagal!', text: 'Gagal mengunduh file: ' + error.message });
    }
}

// Bersihkan blob URL saat modal ditutup
document.getElementById('previewModal').addEventListener('hidden.bs.modal', function () {
    if (currentBlobUrl) {
        URL.revokeObjectURL(currentBlobUrl);
        currentBlobUrl = null;
    }
});

// Submit form
document.getElementById('formVerifikasi')?.addEventListener('submit', function() {
    const btn = document.getElementById('btnSimpan');
    btn.disabled = true;
    btn.innerHTML = '<div class="spinner-wrapper"></div> Menyimpan...';
});
</script>
@endpush

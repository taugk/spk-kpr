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

    .card-box { border-radius: 12px; overflow: hidden; margin-bottom: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
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
            <span class="badge-status {{ $pengajuan->status == 'verifikasi_marketing' ? 'badge-warning-light' : ($pengajuan->status == 'antrian_admin' ? 'badge-success-light' : 'badge-danger-light') }}">
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
            <h5 class="mb-0 text-blue"><i class="fa fa-folder-open mr-2"></i> Dokumen yang Diperlukan</h5>
            <span class="text-muted small"><i class="fa fa-file"></i> <span id="totalUploadedCount">0</span> dari <span id="totalRequiredCount">0</span> terupload</span>
        </div>
        <div class="pd-20">
            <div class="row" id="dokumenList">
                <div class="col-12 text-center py-5"><i class="fa fa-spinner fa-spin fa-2x text-muted"></i><p class="mt-2">Memuat dokumen...</p></div>
            </div>
        </div>
    </div>

    <!-- Form Verifikasi -->
    <div class="card-box">
        <div class="pd-20 border-bottom">
            <h5 class="mb-0 text-blue"><i class="fa fa-clipboard-list mr-2"></i> Verifikasi Dokumen</h5>
        </div>
        <div class="pd-20">
            <form id="formVerifikasi">
                @csrf
                <input type="hidden" name="pengajuan_id" value="{{ $pengajuan->id }}">
                <div id="verifikasiDokumenList"></div>

                <div class="bg-light p-4 rounded mt-4">
                    <h6 class="mb-3 text-blue"><i class="fa fa-check-circle mr-2"></i> Kesimpulan Verifikasi</h6>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Rekomendasi <span class="text-danger">*</span></label>
                            <select name="rekomendasi" id="rekomendasi" class="custom-select" required>
                                <option value="">Pilih Rekomendasi</option>
                                <option value="layak">Layak</option>
                                <option value="perlu_pertimbangan">Perlu Pertimbangan</option>
                                <option value="tidak_layak">Tidak Layak</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Keputusan <span class="text-danger">*</span></label>
                            <select name="keputusan" id="keputusan" class="custom-select" required>
                                <option value="">Pilih Keputusan</option>
                                <option value="ajukan_ke_admin">Ajukan ke Admin</option>
                                <option value="minta_revisi">Minta Revisi</option>
                                <option value="tolak">Tolak</option>
                            </select>
                        </div>
                        <div class="col-12 form-group" id="alasanGroup" style="display: none;">
                            <label>Alasan Keputusan <span class="text-danger">*</span></label>
                            <textarea name="alasan_keputusan" id="alasanKeputusan" class="form-control" rows="3" placeholder="Isikan alasan keputusan..."></textarea>
                        </div>
                        <div class="col-12 form-group">
                            <label>Catatan Tambahan</label>
                            <textarea name="catatan_verifikasi" id="catatanVerifikasi" class="form-control" rows="2" placeholder="Catatan tambahan (opsional)"></textarea>
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
    <div class="card-box" id="riwayatCard" style="display: none;">
        <div class="pd-20 border-bottom">
            <h5 class="mb-0 text-blue"><i class="fa fa-history mr-2"></i> Riwayat Verifikasi</h5>
        </div>
        <div class="pd-20">
            <div class="timeline" id="timelineList"></div>
        </div>
    </div>
</div>

<!-- Modal Preview -->
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-file-alt mr-2"></i> Preview Dokumen</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body text-center" id="previewContainer" style="min-height: 400px;">
                <div class="py-5"><i class="fa fa-spinner fa-spin fa-2x"></i><p class="mt-2">Memuat dokumen...</p></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-success" id="downloadFromModal" style="display: none;"><i class="fa fa-download"></i> Download</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
let currentDocuments = [];

const routes = {
    getDokumen: '{{ route("marketing.verifikasi.get-dokumen", $pengajuan->id) }}',
    downloadDokumen: (id) => `/marketing/verifikasi/dokumen/download/${id}`,
    previewDokumen: (id) => `/marketing/verifikasi/dokumen/preview/${id}`,
    prosesVerifikasi: '{{ route("marketing.verifikasi.proses", $pengajuan->id) }}',
    detailRiwayat: '{{ route("marketing.verifikasi.detail", $pengajuan->id) }}'
};

// Toggle alasan
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

async function loadDocuments() {
    try {
        const res = await fetch(routes.getDokumen);
        const data = await res.json();
        if (data.success) {
            currentDocuments = data.data;
            renderDocuments();
            renderVerificationItems();
            document.getElementById('totalRequiredCount').innerText = currentDocuments.length;
        }
    } catch (error) {
        Swal.fire('Error', 'Gagal memuat dokumen', 'error');
    }
}

function renderDocuments() {
    const container = document.getElementById('dokumenList');
    const uploaded = currentDocuments.filter(d => d.is_uploaded).length;
    document.getElementById('totalUploadedCount').innerText = uploaded;

    if (!currentDocuments.length) {
        container.innerHTML = '<div class="col-12 text-center py-5"><i class="fa fa-folder-open fa-3x text-muted"></i><p class="mt-2">Belum ada dokumen</p></div>';
        return;
    }

    container.innerHTML = currentDocuments.map(doc => `
        <div class="col-md-6 col-lg-4 mb-3">
            <div class="dokumen-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="dokumen-icon ${doc.is_uploaded ? (doc.file_type === 'application/pdf' ? 'pdf' : (doc.file_type?.includes('image/') ? 'image' : 'default')) : 'default'}">
                        <i class="fa ${doc.is_uploaded ? (doc.file_type === 'application/pdf' ? 'fa-file-pdf' : (doc.file_type?.includes('image/') ? 'fa-file-image' : 'fa-file-alt')) : 'fa-file'}"></i>
                    </div>
                    <span class="badge ${doc.wajib ? 'badge-danger' : 'badge-info'} px-2 py-1" style="font-size:10px">${doc.wajib ? 'Wajib' : 'Opsional'}</span>
                </div>
                <div class="dokumen-nama">${escapeHtml(doc.nama_dokumen)}</div>
                <div class="dokumen-ukuran">${doc.is_uploaded ? escapeHtml(doc.file_name) + '<br>' + formatFileSize(doc.file_size) : '<span class="text-danger">Belum diupload</span>'}</div>
                ${doc.is_uploaded ? `
                    <div>
                        <button class="btn-dokumen btn-preview" onclick="previewDocument(${doc.id})"><i class="fa fa-eye"></i> Preview</button>
                        <button class="btn-dokumen btn-download" onclick="downloadDocument(${doc.id})"><i class="fa fa-download"></i> Download</button>
                    </div>
                ` : ''}
            </div>
        </div>
    `).join('');
}

function renderVerificationItems() {
    const container = document.getElementById('verifikasiDokumenList');
    if (!container) return;
    container.innerHTML = currentDocuments.map(doc => `
        <div class="verifikasi-item">
            <div class="verifikasi-header">
                <div class="verifikasi-nama">${escapeHtml(doc.nama_dokumen)} ${doc.wajib ? '<span class="badge badge-danger px-2 py-1 ml-2" style="font-size:10px">Wajib</span>' : ''}</div>
                <select name="verifikasi_dokumen[${doc.jenis}][status]" class="form-control form-control-sm" style="width:150px">
                    <option value="belum_diverifikasi" ${doc.verifikasi_status === 'belum_diverifikasi' ? 'selected' : ''}>Belum Diverifikasi</option>
                    <option value="lengkap" ${doc.verifikasi_status === 'lengkap' ? 'selected' : ''}>Lengkap</option>
                    <option value="tidak_lengkap" ${doc.verifikasi_status === 'tidak_lengkap' ? 'selected' : ''}>Tidak Lengkap</option>
                    <option value="tidak_valid" ${doc.verifikasi_status === 'tidak_valid' ? 'selected' : ''}>Tidak Valid</option>
                </select>
            </div>
            <textarea name="verifikasi_dokumen[${doc.jenis}][catatan]" class="form-control" rows="2" placeholder="Catatan verifikasi">${escapeHtml(doc.verifikasi_catatan || '')}</textarea>
        </div>
    `).join('');
}

async function previewDocument(id) {
    const modal = $('#previewModal');
    const container = document.getElementById('previewContainer');
    const downloadBtn = document.getElementById('downloadFromModal');
    container.innerHTML = '<div class="py-5"><i class="fa fa-spinner fa-spin fa-2x"></i><p class="mt-2">Memuat...</p></div>';
    downloadBtn.style.display = 'none';
    modal.modal('show');
    try {
        const res = await fetch(routes.previewDokumen(id));
        if (res.ok) {
            const blob = await res.blob();
            const url = URL.createObjectURL(blob);
            let html = '';
            if (blob.type.includes('image/')) html = `<img src="${url}" style="max-width:100%">`;
            else if (blob.type === 'application/pdf') html = `<iframe src="${url}#toolbar=1" width="100%" height="500px"></iframe>`;
            else html = `<div class="py-5"><i class="fa fa-file fa-3x"></i><p>File tidak dapat ditampilkan</p><button class="btn btn-primary" onclick="downloadDocument(${id})">Download</button></div>`;
            container.innerHTML = html;
            downloadBtn.style.display = 'inline-flex';
            downloadBtn.onclick = () => downloadDocument(id);
        } else throw new Error();
    } catch (error) {
        container.innerHTML = `<div class="py-5"><i class="fa fa-exclamation-triangle fa-3x text-danger"></i><p>Gagal memuat</p><button class="btn btn-primary" onclick="downloadDocument(${id})">Download</button></div>`;
        downloadBtn.style.display = 'inline-flex';
    }
}

async function downloadDocument(id) {
    Swal.fire({ title: 'Mengunduh...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
    try {
        const res = await fetch(routes.downloadDokumen(id), { headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });
        if (res.ok) {
            const blob = await res.blob();
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'dokumen';
            a.click();
            URL.revokeObjectURL(url);
            Swal.fire('Berhasil!', 'File diunduh', 'success');
        } else throw new Error();
    } catch (error) {
        Swal.fire('Error!', 'Gagal mengunduh', 'error');
    }
}

document.getElementById('formVerifikasi')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    const rekomendasi = document.getElementById('rekomendasi').value;
    const keputusan = document.getElementById('keputusan').value;
    if (!rekomendasi || !keputusan) {
        Swal.fire('Validasi Gagal', 'Pilih rekomendasi dan keputusan', 'error');
        return;
    }
    if ((keputusan === 'minta_revisi' || keputusan === 'tolak') && !document.getElementById('alasanKeputusan').value.trim()) {
        Swal.fire('Validasi Gagal', 'Alasan wajib diisi', 'error');
        return;
    }
    const btn = document.getElementById('btnSimpan');
    const original = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<div class="spinner-wrapper"></div> Menyimpan...';
    try {
        const formData = new FormData(this);
        const res = await fetch(routes.prosesVerifikasi, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: formData
        });
        const result = await res.json();
        if (result.success) {
            Swal.fire({ icon: 'success', title: 'Berhasil!', text: result.message, timer: 1500, showConfirmButton: false }).then(() => {
                window.location.href = result.redirect || '{{ route("marketing.verifikasi.dokumen") }}';
            });
        } else {
            Swal.fire('Gagal!', result.message, 'error');
        }
    } catch (error) {
        Swal.fire('Error!', 'Terjadi kesalahan', 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = original;
    }
});

function formatFileSize(bytes) {
    if (!bytes) return '0 KB';
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(1024));
    return parseFloat((bytes / Math.pow(1024, i)).toFixed(2)) + ' ' + sizes[i];
}

function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
}

async function loadRiwayat() {
    try {
        const res = await fetch(routes.detailRiwayat);
        const data = await res.json();

        if (data.success && data.data?.length) {
            const container = document.getElementById('timelineList');
            // Tambahkan wrapper dan garis vertikal
            container.innerHTML = `
                <div class="timeline-wrapper">
                    <div class="timeline-line"></div>
                    <div id="timelineItems"></div>
                </div>
            `;

            const itemsContainer = document.getElementById('timelineItems');
            itemsContainer.innerHTML = data.data.map(item => `
                <div class="position-relative mb-4">
                    <div class="timeline-dot"></div>
                    <div class="timeline-bubble shadow-sm">
                        <!-- Teks Keterangan -->
                        <p class="mb-2 text-dark font-weight-medium" style="font-size: 14px;">
                            ${escapeHtml(item.keterangan || item.status_label)}
                        </p>

                        <!-- Badge Waktu (Biru seperti image_a37138.png) -->
                        <div class="mb-2">
                            <span class="badge badge-primary px-2 py-1" style="background-color: #0d6efd; font-size: 11px;">
                                ${new Date(item.created_at).toLocaleDateString('id-ID')},
                                ${new Date(item.created_at).toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'})}
                            </span>
                        </div>

                        <!-- Info User -->
                        ${item.pengubah ? `
                            <div class="text-muted text-uppercase font-weight-bold" style="font-size: 10px; letter-spacing: 0.5px;">
                                <i class="fa fa-user-circle mr-1"></i> OLEH: ${escapeHtml(item.pengubah)}
                            </div>` : ''}
                    </div>
                </div>
            `).join('');

            document.getElementById('riwayatCard').style.display = 'block';
        }
    } catch (error) {
        console.error('Gagal memuat riwayat:', error);
    }
}

// Set nilai awal
document.getElementById('rekomendasi').value = '{{ $verifikasi->rekomendasi_marketing ?? "" }}';
document.getElementById('keputusan').value = '{{ $verifikasi->keputusan ?? "" }}';
document.getElementById('alasanKeputusan').value = '{{ $verifikasi->alasan_keputusan ?? "" }}';
document.getElementById('catatanVerifikasi').value = '{{ $verifikasi->catatan_verifikasi ?? "" }}';
if (['minta_revisi', 'tolak'].includes(document.getElementById('keputusan').value)) {
    document.getElementById('alasanGroup').style.display = 'block';
}

loadDocuments();
loadRiwayat();
</script>
@endpush

@extends('marketing.layouts.app')

@section('title', 'Detail Pengajuan KPR - ' . ($pengajuan->kode_pengajuan ?? ''))

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
:root {
    --primary:      #4361ee;
    --primary-light:#eef0ff;
    --success:      #17c964;
    --success-light:#e8fdf0;
    --danger:       #f5222d;
    --danger-light: #fff1f0;
    --warning:      #f59e0b;
    --warning-light:#fffbeb;
    --info:         #06b6d4;
    --info-light:   #ecfeff;
    --bg:           #f0f2f8;
    --card:         #ffffff;
    --text:         #1e293b;
    --muted:        #64748b;
    --border:       #e2e8f0;
    --radius:       14px;
    --shadow:       0 1px 3px rgba(0,0,0,.06), 0 4px 16px rgba(0,0,0,.04);
    --shadow-hover: 0 8px 30px rgba(67,97,238,.14);
}

body {
    background: var(--bg) !important;
    font-family: 'Plus Jakarta Sans', sans-serif !important;
    color: var(--text) !important;
}

/* Page Header */
.page-header-custom {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    margin-bottom: 26px;
}
.page-title h4 {
    font-size: 1.3rem;
    font-weight: 800;
    color: var(--text);
    margin: 0 0 4px;
    letter-spacing: -.3px;
}
.page-title p {
    font-size: .85rem;
    color: var(--muted);
    margin: 0;
}
.btn-back {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: .8rem;
    font-weight: 600;
    color: var(--muted);
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 9px;
    padding: 7px 14px;
    text-decoration: none;
    transition: all .2s;
}
.btn-back:hover {
    border-color: var(--primary);
    color: var(--primary);
    text-decoration: none;
}

/* Cards */
.card {
    border: 1px solid var(--border) !important;
    border-radius: var(--radius) !important;
    box-shadow: var(--shadow) !important;
    background: var(--card) !important;
    transition: box-shadow .25s;
    margin-bottom: 20px;
}
.card:hover {
    box-shadow: var(--shadow-hover) !important;
}
.card-header {
    border-bottom: 1px solid var(--border) !important;
    background: transparent !important;
    padding: 14px 20px !important;
    border-radius: var(--radius) var(--radius) 0 0 !important;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 10px;
}
.card-title {
    font-size: .9rem !important;
    font-weight: 700 !important;
    color: var(--text) !important;
    margin: 0 !important;
    display: flex;
    align-items: center;
    gap: 8px;
}
.card-body {
    padding: 20px !important;
}

/* Info Grid */
.info-row {
    display: flex;
    margin-bottom: 14px;
    font-size: .85rem;
}
.info-label {
    width: 140px;
    font-weight: 600;
    color: var(--muted);
    flex-shrink: 0;
}
.info-value {
    flex: 1;
    color: var(--text);
    font-weight: 500;
}
.divider {
    border-top: 1px solid var(--border);
    margin: 16px 0;
}

/* Status Badge */
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 14px;
    border-radius: 20px;
    font-size: .75rem;
    font-weight: 600;
}
.status-badge.submitted { background: #e0e7ff; color: #4338ca; }
.status-badge.verifikasi_marketing { background: var(--info-light); color: var(--info); }
.status-badge.antrian_admin { background: var(--primary-light); color: var(--primary); }
.status-badge.revisi_debitur { background: var(--warning-light); color: var(--warning); }
.status-badge.ditolak_marketing,
.status-badge.ditolak_sistem { background: var(--danger-light); color: var(--danger); }
.status-badge.disetujui_sistem { background: var(--success-light); color: var(--success); }

/* Document Cards */
.doc-card {
    border: 1px solid var(--border);
    border-radius: 10px;
    padding: 12px;
    transition: all .2s;
    background: #fff;
}
.doc-card:hover {
    border-color: var(--primary);
    background: var(--primary-light);
}
.doc-icon {
    width: 45px;
    height: 45px;
    background: var(--primary-light);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    color: var(--primary);
}
.doc-status {
    font-size: .7rem;
    font-weight: 600;
    padding: 2px 8px;
    border-radius: 20px;
    display: inline-block;
}
.doc-status.valid { background: var(--success-light); color: var(--success); }
.doc-status.invalid { background: var(--danger-light); color: var(--danger); }
.doc-status.pending { background: var(--warning-light); color: var(--warning); }
.doc-status.warning { background: var(--warning-light); color: var(--warning); }

/* Timeline */
.timeline {
    position: relative;
    padding-left: 30px;
}
.timeline-item {
    position: relative;
    padding-bottom: 20px;
    border-left: 2px solid var(--border);
    margin-left: 10px;
    padding-left: 20px;
}
.timeline-item:last-child {
    border-left: 2px solid transparent;
}
.timeline-dot {
    position: absolute;
    left: -8px;
    top: 0;
    width: 14px;
    height: 14px;
    border-radius: 50%;
    background: var(--primary);
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px var(--primary-light);
}
.timeline-date {
    font-size: .7rem;
    color: var(--muted);
    margin-bottom: 4px;
}
.timeline-title {
    font-weight: 700;
    font-size: .85rem;
    margin-bottom: 4px;
}
.timeline-desc {
    font-size: .78rem;
    color: var(--muted);
}

/* Button Actions */
.btn-action {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: .8rem;
    font-weight: 600;
    padding: 10px 20px;
    border-radius: 10px;
    text-decoration: none;
    transition: all .2s;
}
.btn-action.primary {
    background: var(--primary);
    color: #fff;
    border: none;
}
.btn-action.primary:hover {
    background: #3451d1;
    color: #fff;
    text-decoration: none;
}
.btn-action.warning {
    background: var(--warning);
    color: #fff;
}
.btn-action.warning:hover {
    background: #d97706;
    color: #fff;
}
.btn-action.danger {
    background: var(--danger);
    color: #fff;
}
.btn-action.danger:hover {
    background: #cf1322;
    color: #fff;
}
.btn-action.secondary {
    background: #f1f5f9;
    color: var(--muted);
    border: 1px solid var(--border);
}
.btn-action.secondary:hover {
    background: #e2e8f0;
    color: var(--text);
}

/* Score Card */
.score-card {
    background: linear-gradient(135deg, var(--primary) 0%, #3451d1 100%);
    border-radius: var(--radius);
    padding: 20px;
    color: #fff;
    text-align: center;
}
.score-value {
    font-size: 2.5rem;
    font-weight: 800;
}
.score-label {
    font-size: .75rem;
    opacity: .8;
    margin-top: 5px;
}

/* Modal Preview */
.modal-preview-content {
    min-height: 500px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f5f5f5;
    border-radius: 0 0 var(--radius) var(--radius);
}
.preview-loading {
    text-align: center;
}
.preview-loading i {
    font-size: 2rem;
    color: var(--primary);
    animation: spin 1s linear infinite;
}
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

@keyframes fadeUp {
    from { opacity:0; transform:translateY(14px); }
    to { opacity:1; transform:translateY(0); }
}
.card {
    animation: fadeUp .3s ease both;
}

/* Verification Status Select */
.verif-status-select {
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 6px 10px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}
.verif-status-select.status-valid {
    background: var(--success-light);
    color: var(--success);
    border-color: var(--success);
}
.verif-status-select.status-invalid {
    background: var(--danger-light);
    color: var(--danger);
    border-color: var(--danger);
}
.verif-status-select.status-pending {
    background: var(--warning-light);
    color: var(--warning);
    border-color: var(--warning);
}
.verif-status-select:hover {
    transform: translateY(-1px);
}

.save-indicator {
    display: inline-block;
    width: 16px;
    height: 16px;
    margin-left: 8px;
    color: var(--success);
    font-size: 12px;
}
</style>
@endpush

@section('content')
<div class="container-fluid px-3 py-2">

    {{-- Header --}}
    <div class="page-header-custom">
        <div class="page-title">
            <h4>
                <i class="dw dw-file-31 mr-2" style="color:var(--primary)"></i>
                Detail Pengajuan KPR
            </h4>
            <p>{{ $pengajuan->kode_pengajuan ?? '-' }}</p>
        </div>
        <div class="d-flex" style="gap:10px">
            <a href="{{ url()->previous() }}" class="btn-back">
                <i class="dw dw-arrow-left"></i> Kembali
            </a>
            @if(in_array($pengajuan->status, ['submitted', 'verifikasi_marketing']))
                <form action="{{ route('marketing.pengajuan.ambil', $pengajuan) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn-action primary" style="border: none; cursor: pointer;">
                        <i class="dw dw-edit-2"></i> Verifikasi Pengajuan
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="row">
        {{-- Left Column --}}
        <div class="col-lg-8">

            {{-- Status Banner --}}
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <i class="dw dw-info-circle" style="font-size:1.5rem;color:var(--primary)"></i>
                            <div>
                                <div style="font-size:.75rem;color:var(--muted)">Status Pengajuan</div>
                                <div class="status-badge {{ str_replace('_', '-', $pengajuan->status) }}">
                                    <i class="dw dw-check-circle"></i>
                                    @php
                                        $statusLabels = [
                                            'submitted' => 'Menunggu Verifikasi',
                                            'verifikasi_marketing' => 'Verifikasi Marketing',
                                            'antrian_admin' => 'Diteruskan ke Admin',
                                            'revisi_debitur' => 'Perlu Revisi',
                                            'ditolak_marketing' => 'Ditolak Marketing',
                                            'ditolak_sistem' => 'Ditolak Sistem',
                                            'disetujui_sistem' => 'Disetujui Sistem',
                                        ];
                                    @endphp
                                    {{ $statusLabels[$pengajuan->status] ?? ucfirst(str_replace('_', ' ', $pengajuan->status)) }}
                                </div>
                            </div>
                        </div>
                        <div>
                            <small class="text-muted">
                                <i class="dw dw-calendar"></i>
                                Diajukan: {{ \Carbon\Carbon::parse($pengajuan->created_at)->format('d/m/Y H:i') }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Debitur Information --}}
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title">
                        <i class="dw dw-user" style="color:var(--primary)"></i>
                        Informasi Debitur
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-row">
                                <div class="info-label">Nama Lengkap</div>
                                <div class="info-value">{{ $pengajuan->user->nama_lengkap ?? '-' }}</div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">NIK / KTP</div>
                                <div class="info-value">{{ $pengajuan->debiturPribadi->nik ?? '-' }}</div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Tempat/Tgl Lahir</div>
                                <div class="info-value">
                                    {{ $pengajuan->debiturPribadi->tempat_lahir ?? '-' }},
                                    {{ $pengajuan->debiturPribadi->tanggal_lahir ? \Carbon\Carbon::parse($pengajuan->debiturPribadi->tanggal_lahir)->format('d/m/Y') : '-' }}
                                </div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Jenis Kelamin</div>
                                <div class="info-value">{{ $pengajuan->debiturPribadi->jenis_kelamin ?? '-' }}</div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Status Perkawinan</div>
                                <div class="info-value">{{ $pengajuan->debiturPribadi->status_pernikahan ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-row">
                                <div class="info-label">No. HP</div>
                                <div class="info-value">{{ $pengajuan->debiturPribadi->no_hp ?? '-' }}</div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Email</div>
                                <div class="info-value">{{ $pengajuan->user->email ?? '-' }}</div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Alamat KTP</div>
                                <div class="info-value">{{ $pengajuan->debiturPribadi->alamat_ktp ?? '-' }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="divider"></div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-row">
                                <div class="info-label">Pekerjaan</div>
                                <div class="info-value">{{ $pengajuan->debiturPekerjaan->jabatan ?? '-' }}</div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Nama Perusahaan</div>
                                <div class="info-value">{{ $pengajuan->debiturPekerjaan->nama_perusahaan ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-row">
                                <div class="info-label">Penghasilan Bulanan</div>
                                <div class="info-value">
                                    {{ App\Helpers\MarketingHelper::formatRupiah($pengajuan->debiturPekerjaan->total_penghasilan ?? 0) }}
                                </div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Lama Bekerja</div>
                                <div class="info-value">{{ $pengajuan->debiturPekerjaan->lama_bekerja_tahun ?? '-' }} Tahun</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Property & KPR Information --}}
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title">
                        <i class="dw dw-building" style="color:var(--primary)"></i>
                        Informasi Properti & KPR
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-row">
                                <div class="info-label">Proyek</div>
                                <div class="info-value">{{ $pengajuan->unit->tipeUnit->proyek->nama_proyek ?? '-' }}</div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Tipe Unit</div>
                                <div class="info-value">{{ $pengajuan->unit->tipeUnit->nama_tipe ?? '-' }}</div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Kode Unit</div>
                                <div class="info-value">{{ $pengajuan->unit->kode_unit ?? '-' }}</div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Luas Bangunan</div>
                                <div class="info-value">{{ $pengajuan->unit->tipeUnit->luas_bangunan ?? '-' }} m²</div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Luas Tanah</div>
                                <div class="info-value">{{ $pengajuan->unit->tipeUnit->luas_tanah ?? '-' }} m²</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-row">
                                <div class="info-label">Harga Properti</div>
                                <div class="info-value font-weight-bold">
                                    {{ App\Helpers\MarketingHelper::formatRupiah($pengajuan->harga_properti ?? 0) }}
                                </div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">DP / Uang Muka</div>
                                <div class="info-value">
                                    {{ App\Helpers\MarketingHelper::formatRupiah($pengajuan->uang_muka ?? 0) }}
                                </div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Jumlah Pinjaman</div>
                                <div class="info-value" style="color:var(--primary);font-weight:700">
                                    {{ App\Helpers\MarketingHelper::formatRupiah($pengajuan->jumlah_pinjaman ?? 0) }}
                                </div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Tenor / Jangka Waktu</div>
                                <div class="info-value">{{ $pengajuan->tenor_tahun ?? '-' }} Tahun</div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Estimasi Angsuran</div>
                                <div class="info-value">
                                    {{ App\Helpers\MarketingHelper::formatRupiah($pengajuan->estimasi_angsuran ?? 0) }}
                                    <small class="text-muted">/ bulan</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Submitted Documents with Verification Status --}}
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title">
                        <i class="dw dw-file-31" style="color:var(--primary)"></i>
                        Dokumen yang Diupload
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row" id="documentList">
                        @forelse($pengajuan->dokumen as $dokumen)
                        @php
                            // Ambil status verifikasi dari data verifikasi marketing
                            $verifikasi = $pengajuan->verifikasiMarketing;
                            $statusMap = [
                                'dok_ktp_valid' => 'ktp',
                                'dok_kk_valid' => 'kk',
                                'dok_npwp_valid' => 'npwp',
                                'dok_slip_gaji_valid' => 'slip_gaji',
                                'dok_rek_koran_valid' => 'rekening_koran',
                                'dok_slik_valid' => 'slik',
                                'dok_surat_kerja_valid' => 'sk_kerja',
                            ];
                            $fieldName = array_search($dokumen->jenis_dokumen, $statusMap);
                            $statusValid = $verifikasi && $fieldName ? ($verifikasi->$fieldName ?? null) : null;
                            $statusText = '';
                            $statusClass = 'pending';
                            if ($statusValid === true) {
                                $statusText = 'Valid';
                                $statusClass = 'valid';
                            } elseif ($statusValid === false) {
                                $statusText = 'Tidak Valid';
                                $statusClass = 'invalid';
                            } else {
                                $statusText = 'Belum Diverifikasi';
                                $statusClass = 'pending';
                            }
                        @endphp
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="doc-card" data-dokumen-id="{{ $dokumen->id }}">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="doc-icon">
                                        @php
                                            $icons = [
                                                'ktp' => 'dw dw-id-card',
                                                'kk' => 'dw dw-house',
                                                'npwp' => 'dw dw-id-card',
                                                'slip_gaji' => 'dw dw-money',
                                                'rekening_koran' => 'dw dw-file',
                                                'sk_kerja' => 'dw dw-file-31',
                                                'surat_keterangan_kerja' => 'dw dw-file-31',
                                                'slik' => 'dw dw-analytics',
                                                'slik_ojk' => 'dw dw-analytics',
                                                'buku_nikah' => 'dw dw-heart',
                                                'ktp_pasangan' => 'dw dw-id-card',
                                                'foto_diri' => 'dw dw-camera',
                                                'pas_foto' => 'dw dw-camera',
                                            ];
                                            $icon = $icons[$dokumen->jenis_dokumen] ?? 'dw dw-file';
                                        @endphp
                                        <i class="{{ $icon }}"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div style="font-weight:600;font-size:.85rem">
                                            {{ ucfirst(str_replace('_', ' ', $dokumen->jenis_dokumen)) }}
                                        </div>
                                        <div style="font-size:.7rem;color:var(--muted)">
                                            {{ strlen($dokumen->nama_file) > 30 ? substr($dokumen->nama_file, 0, 30) . '...' : $dokumen->nama_file }}
                                        </div>
                                        <div style="font-size:.65rem;color:var(--muted)">
                                            {{ number_format($dokumen->ukuran_file / 1024, 2) }} KB
                                        </div>
                                        <div class="mt-2">
                                            <span class="doc-status {{ $statusClass }}">
                                                <i class="dw {{ $statusValid === true ? 'dw-check' : ($statusValid === false ? 'dw-close' : 'dw-time') }}"></i>
                                                {{ $statusText }}
                                            </span>
                                        </div>
                                        @php
                                            $isImage = in_array($dokumen->mime_type, ['image/jpeg', 'image/jpg', 'image/png', 'image/gif']);
                                            $isPdf = $dokumen->mime_type === 'application/pdf';
                                        @endphp
                                        <div class="mt-2">
                                            @if($isImage || $isPdf)
                                                <button type="button" 
                                                        class="btn-preview-doc" 
                                                        data-path="{{ $dokumen->path_file }}"
                                                        data-nama="{{ $dokumen->nama_file }}"
                                                        data-mime="{{ $dokumen->mime_type }}"
                                                        style="background:var(--primary-light);border:none;border-radius:6px;padding:4px 10px;font-size:.7rem;color:var(--primary);margin-right:5px;">
                                                    <i class="dw dw-eye"></i> Preview
                                                </button>
                                            @endif
                                            <button type="button" 
                                                    class="btn-download-doc" 
                                                    data-path="{{ $dokumen->path_file }}"
                                                    data-nama="{{ $dokumen->nama_file }}"
                                                    style="background:var(--primary);border:none;border-radius:6px;padding:4px 10px;font-size:.7rem;color:white;">
                                                <i class="dw dw-download"></i> Download
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-12">
                            <div class="text-center py-4">
                                <i class="dw dw-inbox" style="font-size:2rem;color:var(--muted)"></i>
                                <p class="mt-2 text-muted">Belum ada dokumen yang diupload</p>
                            </div>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column --}}
        <div class="col-lg-4">

            {{-- Marketing Verification Summary --}}
            @if($pengajuan->verifikasiMarketing)
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title">
                        <i class="dw dw-edit-2" style="color:var(--primary)"></i>
                        Hasil Verifikasi Marketing
                    </h6>
                </div>
                <div class="card-body">
                    <div class="info-row">
                        <div class="info-label">Rekomendasi</div>
                        <div class="info-value">
                            @php
                                $rekomendasiLabels = [
                                    'layak' => 'Layak Dilanjutkan',
                                    'perlu_pertimbangan' => 'Perlu Pertimbangan',
                                    'tidak_layak' => 'Tidak Layak',
                                ];
                                $rekomendasiClass = [
                                    'layak' => 'success',
                                    'perlu_pertimbangan' => 'warning',
                                    'tidak_layak' => 'danger',
                                ];
                            @endphp
                            <span class="badge badge-{{ $rekomendasiClass[$pengajuan->verifikasiMarketing->rekomendasi_marketing] ?? 'secondary' }}">
                                {{ $rekomendasiLabels[$pengajuan->verifikasiMarketing->rekomendasi_marketing] ?? '-' }}
                            </span>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Keputusan</div>
                        <div class="info-value">
                            @php
                                $keputusanLabels = [
                                    'ajukan_ke_admin' => 'Diteruskan ke Admin',
                                    'minta_revisi' => 'Minta Revisi',
                                    'tolak' => 'Ditolak',
                                ];
                            @endphp
                            {{ $keputusanLabels[$pengajuan->verifikasiMarketing->keputusan] ?? '-' }}
                        </div>
                    </div>
                    @if($pengajuan->verifikasiMarketing->alasan_keputusan)
                    <div class="info-row">
                        <div class="info-label">Alasan</div>
                        <div class="info-value">{{ $pengajuan->verifikasiMarketing->alasan_keputusan }}</div>
                    </div>
                    @endif
                    <div class="info-row">
                        <div class="info-label">Tgl Keputusan</div>
                        <div class="info-value">
                            {{ $pengajuan->verifikasiMarketing->tgl_keputusan ? \Carbon\Carbon::parse($pengajuan->verifikasiMarketing->tgl_keputusan)->format('d/m/Y H:i') : '-' }}
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Document Verification Summary --}}
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title">
                        <i class="dw dw-check" style="color:var(--primary)"></i>
                        Ringkasan Verifikasi Dokumen
                    </h6>
                </div>
                <div class="card-body">
                    @php
                        $totalDocs = $pengajuan->dokumen->count();
                        $validDocs = 0;
                        $invalidDocs = 0;
                        $pendingDocs = 0;
                        
                        $verifikasi = $pengajuan->verifikasiMarketing;
                        $statusMap = [
                            'dok_ktp_valid' => 'ktp',
                            'dok_kk_valid' => 'kk',
                            'dok_npwp_valid' => 'npwp',
                            'dok_slip_gaji_valid' => 'slip_gaji',
                            'dok_rek_koran_valid' => 'rekening_koran',
                            'dok_slik_valid' => 'slik',
                            'dok_surat_kerja_valid' => 'sk_kerja',
                        ];
                        
                        foreach ($pengajuan->dokumen as $doc) {
                            $fieldName = array_search($doc->jenis_dokumen, $statusMap);
                            $status = $verifikasi && $fieldName ? ($verifikasi->$fieldName ?? null) : null;
                            
                            if ($status === true) {
                                $validDocs++;
                            } elseif ($status === false) {
                                $invalidDocs++;
                            } else {
                                $pendingDocs++;
                            }
                        }
                    @endphp
                    <div class="text-center mb-3">
                        <div style="font-size:2rem;font-weight:800;color:var(--primary)">
                            {{ $validDocs }}/{{ $totalDocs }}
                        </div>
                        <small class="text-muted">Dokumen Valid</small>
                    </div>
                    <div class="progress mb-3" style="height:8px">
                        <div class="progress-bar bg-success" style="width:{{ $totalDocs > 0 ? ($validDocs/$totalDocs)*100 : 0 }}%"></div>
                    </div>
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="text-success font-weight-bold">{{ $validDocs }}</div>
                            <small class="text-muted">Valid</small>
                        </div>
                        <div class="col-4">
                            <div class="text-danger font-weight-bold">{{ $invalidDocs }}</div>
                            <small class="text-muted">Tidak Valid</small>
                        </div>
                        <div class="col-4">
                            <div class="text-warning font-weight-bold">{{ $pendingDocs }}</div>
                            <small class="text-muted">Pending</small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Status Timeline --}}
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title">
                        <i class="dw dw-time" style="color:var(--primary)"></i>
                        Riwayat Status
                    </h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        @forelse($riwayat as $history)
                        <div class="timeline-item">
                            <div class="timeline-dot"></div>
                            <div class="timeline-date">
                                {{ \Carbon\Carbon::parse($history->created_at)->format('d/m/Y H:i') }}
                            </div>
                            <div class="timeline-title">
                                @php
                                    $historyLabels = [
                                        'submitted' => 'Pengajuan Diajukan',
                                        'verifikasi_marketing' => 'Verifikasi Marketing',
                                        'antrian_admin' => 'Diteruskan ke Admin',
                                        'revisi_debitur' => 'Perlu Revisi',
                                        'ditolak_marketing' => 'Ditolak Marketing',
                                        'ditolak_sistem' => 'Ditolak Sistem',
                                        'disetujui_sistem' => 'Disetujui',
                                    ];
                                @endphp
                                {{ $historyLabels[$history->status_baru] ?? ucfirst(str_replace('_', ' ', $history->status_baru)) }}
                            </div>
                            @if($history->keterangan)
                            <div class="timeline-desc">
                                <i class="dw dw-info-circle"></i> {{ $history->keterangan }}
                            </div>
                            @endif
                            @if($history->pengubah)
                            <div class="timeline-desc">
                                <i class="dw dw-user"></i> {{ $history->pengubah->nama_lengkap ?? $history->pengubah->name ?? 'Sistem' }}
                            </div>
                            @endif
                        </div>
                        @empty
                        <div class="text-center text-muted py-3">
                            <i class="dw dw-inbox"></i>
                            <p>Belum ada riwayat</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title">
                        <i class="dw dw-settings" style="color:var(--primary)"></i>
                        Aksi Cepat
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-column gap-2">
                        @if(in_array($pengajuan->status, ['submitted', 'verifikasi_marketing']))
                            <button type="button" class="btn-action primary justify-content-center" id="btnVerifikasi">
                                <i class="dw dw-edit-2"></i> Verifikasi Pengajuan
                            </button>
                        @endif
                        <a href="{{ route('marketing.pengajuan.show', $pengajuan) }}" class="btn-action secondary justify-content-center" onclick="window.print();return false;">
                            <i class="dw dw-printer"></i> Cetak Detail
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>

{{-- Modal Preview Dokumen --}}
<div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content" style="border-radius: var(--radius);">
            <div class="modal-header" style="border-bottom: 1px solid var(--border);">
                <h6 class="modal-title" id="previewModalLabel">
                    <i class="dw dw-eye"></i> Preview Dokumen
                </h6>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close" style="background: none; border: none; font-size: 1.5rem; line-height: 1;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 0;">
                <div id="previewContent" class="modal-preview-content">
                    <div class="preview-loading">
                        <i class="dw dw-loading"></i>
                        <p class="mt-2">Memuat dokumen...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="border-top: 1px solid var(--border);">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" id="downloadFromModal" class="btn btn-primary">
                    <i class="dw dw-download"></i> Download
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Base URL untuk akses file
const baseUrl = '{{ url("/") }}';
let currentBlobUrl = null;

// Tombol Verifikasi
document.getElementById('btnVerifikasi')?.addEventListener('click', function() {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("marketing.pengajuan.ambil", $pengajuan) }}';
    
    const csrf = document.createElement('input');
    csrf.type = 'hidden';
    csrf.name = '_token';
    csrf.value = '{{ csrf_token() }}';
    form.appendChild(csrf);
    
    document.body.appendChild(form);
    form.submit();
});

// Fungsi untuk download file via AJAX
async function downloadFileViaAjax(path, fileName) {
    try {
        const response = await fetch('{{ route("marketing.verifikasi.get-file") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ path: path })
        });
        
        if (!response.ok) {
            throw new Error('Download failed');
        }
        
        const blob = await response.blob();
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = fileName;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        setTimeout(() => URL.revokeObjectURL(url), 100);
        
        return true;
    } catch (error) {
        console.error('Download error:', error);
        const downloadUrl = `{{ url("/marketing/verifikasi/download-dokumen") }}/${encodeURIComponent(path)}`;
        window.open(downloadUrl, '_blank');
        return false;
    }
}

// Fungsi untuk preview file
async function previewDocument(path, fileName, mimeType) {
    const modalElement = document.getElementById('previewModal');
    const modal = new bootstrap.Modal(modalElement);
    const previewContent = document.getElementById('previewContent');
    const modalTitle = document.getElementById('previewModalLabel');
    const downloadBtn = document.getElementById('downloadFromModal');
    
    // Reset content
    previewContent.innerHTML = `
        <div class="preview-loading">
            <i class="dw dw-loading"></i>
            <p class="mt-2">Memuat dokumen...</p>
        </div>
    `;
    modalTitle.innerHTML = `<i class="dw dw-eye"></i> Preview: ${fileName}`;
    
    // Show modal
    modal.show();
    
    try {
        const response = await fetch('{{ route("marketing.verifikasi.get-file") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ path: path })
        });
        
        if (!response.ok) {
            throw new Error('Failed to load file');
        }
        
        const blob = await response.blob();
        
        if (currentBlobUrl) {
            URL.revokeObjectURL(currentBlobUrl);
        }
        
        currentBlobUrl = URL.createObjectURL(blob);
        
        downloadBtn.onclick = () => {
            const link = document.createElement('a');
            link.href = currentBlobUrl;
            link.download = fileName;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        };
        
        previewContent.innerHTML = '';
        
        if (mimeType.startsWith('image/')) {
            const img = document.createElement('img');
            img.src = currentBlobUrl;
            img.style.maxWidth = '100%';
            img.style.maxHeight = '70vh';
            img.style.objectFit = 'contain';
            img.style.borderRadius = '0 0 var(--radius) var(--radius)';
            previewContent.appendChild(img);
        } 
        else if (mimeType === 'application/pdf') {
            const iframe = document.createElement('iframe');
            iframe.src = currentBlobUrl;
            iframe.style.width = '100%';
            iframe.style.height = '70vh';
            iframe.style.border = 'none';
            iframe.style.borderRadius = '0 0 var(--radius) var(--radius)';
            previewContent.appendChild(iframe);
        }
        else {
            previewContent.innerHTML = `
                <div class="text-center p-5">
                    <i class="dw dw-file" style="font-size: 3rem; color: var(--muted);"></i>
                    <p class="mt-3">Preview tidak tersedia untuk tipe file ini</p>
                    <p class="text-muted small">Tipe file: ${mimeType}</p>
                    <button class="btn btn-primary mt-2" onclick="document.getElementById('downloadFromModal').click()">
                        <i class="dw dw-download"></i> Download File
                    </button>
                </div>
            `;
        }
        
    } catch (error) {
        console.error('Preview error:', error);
        previewContent.innerHTML = `
            <div class="text-center p-5">
                <i class="dw dw-warning" style="font-size: 3rem; color: var(--danger);"></i>
                <p class="mt-3 text-danger">Gagal memuat dokumen</p>
                <p class="text-muted small">${error.message}</p>
                <button class="btn btn-primary mt-2" onclick="window.open('{{ url("/marketing/verifikasi/download-dokumen") }}/${encodeURIComponent(path)}', '_blank')">
                    <i class="dw dw-download"></i> Buka di Tab Baru
                </button>
            </div>
        `;
    }
}

// Event listeners untuk tombol preview dan download
document.addEventListener('DOMContentLoaded', function() {
    // Preview buttons
    document.querySelectorAll('.btn-preview-doc').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const path = this.dataset.path;
            const nama = this.dataset.nama;
            const mime = this.dataset.mime;
            previewDocument(path, nama, mime);
        });
    });
    
    // Download buttons
    document.querySelectorAll('.btn-download-doc').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const path = this.dataset.path;
            const nama = this.dataset.nama;
            downloadFileViaAjax(path, nama);
        });
    });
});

// Cleanup blob URL when modal is closed
document.getElementById('previewModal').addEventListener('hidden.bs.modal', function() {
    if (currentBlobUrl) {
        URL.revokeObjectURL(currentBlobUrl);
        currentBlobUrl = null;
    }
});
</script>
@endpush
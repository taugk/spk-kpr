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
}
.doc-status.valid { background: var(--success-light); color: var(--success); }
.doc-status.invalid { background: var(--danger-light); color: var(--danger); }
.doc-status.pending { background: var(--warning-light); color: var(--warning); }

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

@keyframes fadeUp {
    from { opacity:0; transform:translateY(14px); }
    to { opacity:1; transform:translateY(0); }
}
.card {
    animation: fadeUp .3s ease both;
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
                <a href="{{ route('marketing.verifikasi.dokumen.create', $pengajuan) }}" class="btn-action primary">
                    <i class="dw dw-edit-2"></i> Verifikasi Pengajuan
                </a>
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
                                <div class="info-value">{{ $pengajuan->debiturPribadi->status_perkawinan ?? '-' }}</div>
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
                            <div class="info-row">
                                <div class="info-label">Alamat Domisili</div>
                                <div class="info-value">{{ $pengajuan->debiturPribadi->alamat_domisili ?? '-' }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="divider"></div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-row">
                                <div class="info-label">Pekerjaan</div>
                                <div class="info-value">{{ $pengajuan->debiturPekerjaan->jenis_pekerjaan ?? '-' }}</div>
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
                                <div class="info-value">{{ $pengajuan->debiturPekerjaan->lama_bekerja ?? '-' }} Tahun</div>
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
                                    {{ App\Helpers\MarketingHelper::formatRupiah($pengajuan->dp ?? 0) }}
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

            {{-- Financial Capability (Debt to Income) --}}
            @if(isset($kemampuanBayar))
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title">
                        <i class="dw dw-analytics" style="color:var(--primary)"></i>
                        Analisis Kemampuan Bayar (Debt to Income Ratio)
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="score-card">
                                <div class="score-value">{{ $kemampuanBayar['dsr'] ?? 0 }}%</div>
                                <div class="score-label">DSR</div>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-6">
                                    <div class="info-row">
                                        <div class="info-label">Penghasilan Bulanan</div>
                                        <div class="info-value">{{ App\Helpers\MarketingHelper::formatRupiah($kemampuanBayar['penghasilan_bulanan'] ?? 0) }}</div>
                                    </div>
                                    <div class="info-row">
                                        <div class="info-label">Estimasi Angsuran</div>
                                        <div class="info-value">{{ App\Helpers\MarketingHelper::formatRupiah($kemampuanBayar['estimasi_angsuran'] ?? 0) }}</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="info-row">
                                        <div class="info-label">Cicilan Lain</div>
                                        <div class="info-value">{{ App\Helpers\MarketingHelper::formatRupiah($kemampuanBayar['cicilan_lain'] ?? 0) }}</div>
                                    </div>
                                    <div class="info-row">
                                        <div class="info-label">Total Kewajiban</div>
                                        <div class="info-value">{{ App\Helpers\MarketingHelper::formatRupiah($kemampuanBayar['total_kewajiban'] ?? 0) }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="alert alert-{{ $kemampuanBayar['status'] == 'aman' ? 'success' : ($kemampuanBayar['status'] == 'waspada' ? 'warning' : 'danger') }} mt-2 mb-0">
                                <i class="dw dw-info-circle"></i>
                                {{ $kemampuanBayar['keterangan'] ?? '' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Submitted Documents --}}
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title">
                        <i class="dw dw-file-31" style="color:var(--primary)"></i>
                        Dokumen yang Diupload
                    </h6>
                </div>
                <div class="card-body">
                    @php
                        $dokumenList = [
                            'ktp' => ['icon' => 'dw dw-id-card', 'name' => 'KTP / Identitas'],
                            'kk' => ['icon' => 'dw dw-house', 'name' => 'Kartu Keluarga'],
                            'slip_gaji' => ['icon' => 'dw dw-money', 'name' => 'Slip Gaji'],
                            'rek_koran' => ['icon' => 'dw dw-file', 'name' => 'Rekening Koran'],
                            'slik' => ['icon' => 'dw dw-analytics', 'name' => 'SLIK OJK'],
                            'surat_kerja' => ['icon' => 'dw dw-file-31', 'name' => 'Surat Keterangan Kerja'],
                            'npwp' => ['icon' => 'dw dw-id-card', 'name' => 'NPWP'],
                        ];
                    @endphp
                    <div class="row">
                        @foreach($dokumenList as $jenis => $info)
                            @php
                                $dok = $pengajuan->dokumen->where('jenis_dokumen', $jenis)->first();
                                $verif = $pengajuan->verifikasiMarketing;
                                $isValid = null;
                                if ($verif) {
                                    $fieldMap = [
                                        'ktp' => 'dok_ktp_valid',
                                        'kk' => 'dok_kk_valid',
                                        'slip_gaji' => 'dok_slip_gaji_valid',
                                        'rek_koran' => 'dok_rek_koran_valid',
                                        'slik' => 'dok_slik_valid',
                                        'surat_kerja' => 'dok_surat_kerja_valid',
                                        'npwp' => 'dok_npwp_valid',
                                    ];
                                    $field = $fieldMap[$jenis] ?? null;
                                    if ($field && isset($verif->$field)) {
                                        $isValid = $verif->$field;
                                    }
                                }
                            @endphp
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="doc-card">
                                    <div class="d-flex align-items-start gap-3">
                                        <div class="doc-icon">
                                            <i class="{{ $info['icon'] }}"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div style="font-weight:600;font-size:.85rem">{{ $info['name'] }}</div>
                                            <div style="font-size:.7rem;color:var(--muted)">{{ ucfirst($jenis) }}</div>
                                            @if($dok)
                                                <div style="font-size:.7rem;margin-top:5px">
                                                    <i class="dw dw-check-circle"></i> Terupload
                                                    <br><small>{{ $dok->file_name }}</small>
                                                </div>
                                                @if(!is_null($isValid))
                                                    <span class="doc-status {{ $isValid ? 'valid' : 'invalid' }} mt-2 d-inline-block">
                                                        <i class="dw {{ $isValid ? 'dw-check' : 'dw-cancel' }}"></i>
                                                        {{ $isValid ? 'Valid' : 'Tidak Valid' }}
                                                    </span>
                                                @else
                                                    <span class="doc-status pending mt-2 d-inline-block">
                                                        <i class="dw dw-hourglass"></i> Belum Diverifikasi
                                                    </span>
                                                @endif
                                            @else
                                                <div style="font-size:.7rem;color:var(--danger);margin-top:5px">
                                                    <i class="dw dw-cancel"></i> Belum diupload
                                                </div>
                                            @endif
                                        </div>
                                        @if($dok)
                                            <a href="{{ route('marketing.verifikasi.download-dokumen', $dok) }}"
                                               class="text-muted" title="Download">
                                                <i class="dw dw-download"></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
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

            {{-- Document Verification Status --}}
            @if($pengajuan->verifikasiMarketing)
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title">
                        <i class="dw dw-check" style="color:var(--primary)"></i>
                        Status Verifikasi Dokumen
                    </h6>
                </div>
                <div class="card-body">
                    @php
                        $docFields = [
                            'dok_ktp_valid' => 'KTP',
                            'dok_kk_valid' => 'Kartu Keluarga',
                            'dok_slip_gaji_valid' => 'Slip Gaji',
                            'dok_rek_koran_valid' => 'Rekening Koran',
                            'dok_slik_valid' => 'SLIK',
                            'dok_surat_kerja_valid' => 'Surat Kerja',
                            'dok_npwp_valid' => 'NPWP',
                        ];
                        $validCount = 0;
                        $totalCount = 0;
                        foreach ($docFields as $field => $label) {
                            if (!is_null($pengajuan->verifikasiMarketing->$field)) {
                                $totalCount++;
                                if ($pengajuan->verifikasiMarketing->$field) $validCount++;
                            }
                        }
                    @endphp
                    <div class="text-center mb-3">
                        <div style="font-size:2rem;font-weight:800;color:var(--primary)">
                            {{ $validCount }}/{{ $totalCount }}
                        </div>
                        <small class="text-muted">Dokumen Valid</small>
                    </div>
                    <div class="progress mb-3" style="height:8px">
                        <div class="progress-bar bg-success" style="width:{{ $totalCount > 0 ? ($validCount/$totalCount)*100 : 0 }}%"></div>
                    </div>
                    @foreach($docFields as $field => $label)
                        @php
                            $value = $pengajuan->verifikasiMarketing->$field;
                        @endphp
                        @if(!is_null($value))
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span style="font-size:.75rem">{{ $label }}</span>
                            <span class="badge badge-{{ $value ? 'success' : 'danger' }}">
                                {{ $value ? 'Valid' : 'Tidak Valid' }}
                            </span>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>
            @endif

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
                            <a href="{{ route('marketing.verifikasi.dokumen.create', $pengajuan) }}" class="btn-action primary justify-content-center">
                                <i class="dw dw-edit-2"></i> Verifikasi Pengajuan
                            </a>
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
@endsection

@extends('debitur.layouts.app')

@section('title', 'Detail Pengajuan KPR | Debitur Citra Pasada')

@section('content')
<style>
    :root {
        --kpr-primary: #0C447C;
        --kpr-primary-soft: #E6F1FB;
        --kpr-success: #0F7B4F;
        --kpr-success-soft: #E1F5EE;
        --kpr-danger: #E24B4A;
        --kpr-warning: #F59E0B;
        --kpr-info: #3B82F6;
        --kpr-bg: #F6F8FB;
        --kpr-card: #FFFFFF;
        --kpr-border: #E4E8EE;
        --kpr-border-2: #D5DCE5;
        --kpr-text: #1F2937;
        --kpr-muted: #6B7280;
        --kpr-radius-lg: 18px;
        --kpr-radius-md: 12px;
        --kpr-shadow: 0 16px 40px rgba(15, 23, 42, .07);
    }

    body { background: var(--kpr-bg); }

    .kpr-page-title {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 16px;
        flex-wrap: wrap;
    }

    .kpr-title h5 {
        margin: 0;
        font-size: 20px;
        font-weight: 700;
        color: var(--kpr-text);
    }

    .kpr-title p {
        margin: 4px 0 0;
        font-size: 12px;
        color: var(--kpr-muted);
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
    }

    .status-draft { background: #FEF3C7; color: #92400E; border: 1px solid #FDE68A; }
    .status-proses { background: #E6F1FB; color: #0C447C; border: 1px solid #BFDBFE; }
    .status-verifikasi { background: #FEF3C7; color: #92400E; border: 1px solid #FDE68A; }
    .status-disetujui { background: #E1F5EE; color: #0F7B4F; border: 1px solid #A7DCC4; }
    .status-ditolak { background: #FEE2E2; color: #991B1B; border: 1px solid #FECACA; }
    .status-cair { background: #D1FAE5; color: #065F46; border: 1px solid #A7F3D0; }

    .detail-shell {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 320px;
        gap: 22px;
        align-items: start;
    }

    .detail-main,
    .detail-side-card {
        background: var(--kpr-card);
        border: 1px solid var(--kpr-border);
        border-radius: var(--kpr-radius-lg);
        box-shadow: var(--kpr-shadow);
    }

    .detail-main { overflow: hidden; }

    .section-header {
        padding: 18px 22px;
        border-bottom: 1px solid var(--kpr-border);
        background: linear-gradient(135deg, #FFFFFF 0%, #F2F7FC 100%);
    }

    .section-title {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .section-icon {
        width: 40px;
        height: 40px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }

    .section-icon.primary { background: #E6F1FB; color: #0C447C; }
    .section-icon.success { background: #E1F5EE; color: #0F7B4F; }
    .section-icon.warning { background: #FEF3C7; color: #92400E; }
    .section-icon.info { background: #E0F2FE; color: #0369A1; }
    .section-icon.purple { background: #F3E8FF; color: #6B21A5; }
    .section-icon.orange { background: #FFEDD5; color: #9A3412; }

    .section-icon i { font-size: 18px; }

    .section-text h6 {
        margin: 0;
        font-size: 16px;
        font-weight: 700;
        color: var(--kpr-text);
    }

    .section-text small {
        font-size: 11px;
        color: var(--kpr-muted);
    }

    .detail-body { padding: 22px; }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
    }

    .info-item {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .info-label {
        font-size: 11px;
        font-weight: 600;
        color: var(--kpr-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .info-value {
        font-size: 13px;
        font-weight: 600;
        color: var(--kpr-text);
        word-break: break-word;
    }

    .info-value.large {
        font-size: 20px;
        font-weight: 700;
        color: var(--kpr-primary);
    }

    .divider {
        border: 0;
        border-top: 1px solid var(--kpr-border);
        margin: 18px 0;
    }

    .sub-section {
        margin-top: 20px;
    }

    .sub-section-title {
        font-size: 13px;
        font-weight: 700;
        color: var(--kpr-text);
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .document-list {
        display: grid;
        gap: 10px;
    }

    .document-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px;
        background: #F8FAFC;
        border: 1px solid var(--kpr-border);
        border-radius: 12px;
    }

    .document-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .document-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: #E6F1FB;
        color: var(--kpr-primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
    }

    .document-name {
        font-size: 12px;
        font-weight: 600;
        color: var(--kpr-text);
    }

    .document-size {
        font-size: 10px;
        color: var(--kpr-muted);
    }

    .btn-download {
        padding: 6px 12px;
        border-radius: 8px;
        background: #fff;
        border: 1px solid var(--kpr-border);
        font-size: 11px;
        color: var(--kpr-primary);
        text-decoration: none;
        transition: .2s ease;
    }

    .btn-download:hover {
        background: var(--kpr-primary);
        color: #fff;
        border-color: var(--kpr-primary);
    }

    .timeline {
        position: relative;
        padding-left: 24px;
    }

    .timeline-item {
        position: relative;
        padding-bottom: 20px;
    }

    .timeline-item::before {
        content: '';
        position: absolute;
        left: -20px;
        top: 6px;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: var(--kpr-primary);
        border: 2px solid #fff;
        box-shadow: 0 0 0 2px var(--kpr-primary);
    }

    .timeline-item::after {
        content: '';
        position: absolute;
        left: -16px;
        top: 16px;
        width: 2px;
        height: calc(100% - 10px);
        background: var(--kpr-border);
    }

    .timeline-item:last-child::after { display: none; }

    .timeline-date {
        font-size: 10px;
        color: var(--kpr-muted);
        margin-bottom: 4px;
    }

    .timeline-title {
        font-size: 12px;
        font-weight: 600;
        color: var(--kpr-text);
        margin-bottom: 2px;
    }

    .timeline-desc {
        font-size: 11px;
        color: var(--kpr-muted);
    }

    .timeline-item.pending::before { background: var(--kpr-warning); box-shadow: 0 0 0 2px var(--kpr-warning); }
    .timeline-item.completed::before { background: var(--kpr-success); box-shadow: 0 0 0 2px var(--kpr-success); }
    .timeline-item.rejected::before { background: var(--kpr-danger); box-shadow: 0 0 0 2px var(--kpr-danger); }

    .sidebar-sticky {
        position: sticky;
        top: 18px;
        display: grid;
        gap: 16px;
    }

    .detail-side-card { padding: 18px; }

    .side-title {
        font-size: 14px;
        font-weight: 800;
        color: var(--kpr-text);
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .summary-card {
        background: linear-gradient(135deg, #0C447C, #1769B5);
        color: #fff;
        border-radius: 16px;
        padding: 16px;
        margin-bottom: 16px;
    }

    .summary-label {
        font-size: 11px;
        opacity: 0.8;
        margin-bottom: 4px;
    }

    .summary-value {
        font-size: 20px;
        font-weight: 700;
    }

    .summary-small {
        font-size: 10px;
        opacity: 0.7;
        margin-top: 4px;
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid var(--kpr-border);
        font-size: 12px;
    }

    .info-row:last-child { border-bottom: 0; }

    .info-row-label { color: var(--kpr-muted); }
    .info-row-value { font-weight: 600; color: var(--kpr-text); }

    .action-buttons {
        display: flex;
        gap: 10px;
        margin-top: 16px;
    }

    .btn-action {
        flex: 1;
        padding: 10px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
        text-align: center;
        text-decoration: none;
        transition: .2s ease;
    }

    .btn-edit {
        background: var(--kpr-primary);
        color: #fff;
        border: none;
    }

    .btn-edit:hover { background: #0A3562; color: #fff; }

    .btn-cancel {
        background: #fff;
        color: var(--kpr-danger);
        border: 1px solid var(--kpr-danger);
    }

    .btn-cancel:hover { background: var(--kpr-danger); color: #fff; }

    .btn-print {
        background: #F1F5F9;
        color: #475569;
        border: 1px solid var(--kpr-border);
    }

    .btn-print:hover { background: #E2E8F0; }

    @media (max-width: 1199px) {
        .detail-shell { grid-template-columns: 1fr; }
        .sidebar-sticky { position: static; }
    }

    @media (max-width: 768px) {
        .kpr-page-title { flex-direction: column; align-items: flex-start; }
        .detail-header, .detail-body { padding: 14px; }
        .info-grid { grid-template-columns: 1fr; }
        .action-buttons { flex-direction: column; }
    }
</style>

<div class="row">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('debitur.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('debitur.pengajuan-kpr') }}" class="text-decoration-none">Pengajuan KPR</a></li>
                <li class="breadcrumb-item active">Detail Pengajuan</li>
            </ol>
        </nav>
    </div>
</div>

<div class="kpr-page-title">
    <div class="kpr-title">
        <h5>Detail Pengajuan KPR</h5>
        <p>Nomor Pengajuan: {{ $pengajuan->no_pengajuan ?? 'KPR/' . date('Ymd') . '/' . str_pad($pengajuan->id ?? 1, 4, '0', STR_PAD_LEFT) }}</p>
    </div>
    <div class="status-badge status-{{ $pengajuan->status ?? 'draft' }}">
        <i class="bi bi-{{ $statusIcon[$pengajuan->status ?? 'draft'] ?? 'clock-history' }}"></i>
        {{ $statusText[$pengajuan->status ?? 'draft'] ?? 'Draft' }}
    </div>
</div>

<div class="detail-shell">
    <div class="detail-main">
        <!-- Data Pribadi -->
        <div class="section-header">
            <div class="section-title">
                <div class="section-icon primary"><i class="bi bi-person"></i></div>
                <div class="section-text">
                    <h6>Data Pribadi</h6>
                    <small>Informasi identitas debitur</small>
                </div>
            </div>
        </div>
        <div class="detail-body">
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Nama Lengkap</div>
                    <div class="info-value">{{ $pengajuan->user->nama_lengkap ?? '-' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">NIK</div>
                    <div class="info-value">{{ $pengajuan->debiturPribadi->nik ?? '-' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Tempat, Tanggal Lahir</div>
                    <div class="info-value">{{ $pengajuan->debiturPribadi->tempat_lahir ?? '-' }}, {{ isset($pengajuan->debiturPribadi->tanggal_lahir) ? date('d/m/Y', strtotime($pengajuan->debiturPribadi->tanggal_lahir)) : '-' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Jenis Kelamin</div>
                    <div class="info-value">{{ $pengajuan->debiturPribadi->jenis_kelamin ?? '-' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Agama</div>
                    <div class="info-value">{{ $pengajuan->debiturPribadi->agama ?? '-' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Status Pernikahan</div>
                    <div class="info-value">{{ $pengajuan->debiturPribadi->status_pernikahan ?? '-' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Jumlah Tanggungan</div>
                    <div class="info-value">{{ $pengajuan->jumlah_tanggungan ?? '0' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Pendidikan</div>
                    <div class="info-value">{{ $pengajuan->debiturPribadi->pendidikan_terakhir ?? '-' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Kewarganegaraan</div>
                    <div class="info-value">{{ $pengajuan->kewarganegaraan ?? 'WNI' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Nama Ibu Kandung</div>
                    <div class="info-value">{{ $pengajuan->debiturPribadi->nama_ibu_kandung ?? '-' }}</div>
                </div>
                <div class="info-item ff">
                    <div class="info-label">Alamat KTP</div>
                    <div class="info-value">{{ $pengajuan->debiturPribadi->alamat_ktp ?? '-' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Kota/Kabupaten</div>
                    <div class="info-value">{{ $pengajuan->debiturPribadi->kota ?? '-' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Provinsi</div>
                    <div class="info-value">{{ $pengajuan->debiturPribadi->provinsi ?? '-' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Kode Pos</div>
                    <div class="info-value">{{ $pengajuan->debiturPribadi->kode_pos ?? '-' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Status Tempat Tinggal</div>
                    <div class="info-value">{{ $pengajuan->debiturPribadi->status_tempat_tinggal ?? '-' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">No. Telepon/HP</div>
                    <div class="info-value">{{ $pengajuan->debiturPribadi->no_hp ?? '-' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Email</div>
                    <div class="info-value">{{ $pengajuan->user->email ?? '-' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">No. KK</div>
                    <div class="info-value">{{ $pengajuan->debiturPribadi->no_kk ?? '-' }}</div>
                </div>
                @if($pengajuan->nama_pasangan)
                <div class="info-item">
                    <div class="info-label">Nama Pasangan</div>
                    <div class="info-value">{{ $pengajuan->nama_pasangan ?? '-' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">NIK Pasangan</div>
                    <div class="info-value">{{ $pengajuan->nik_pasangan ?? '-' }}</div>
                </div>
                @endif
            </div>
        </div>

        <div class="divider"></div>

        <!-- Pekerjaan & Penghasilan -->
        <div class="section-header">
            <div class="section-title">
                <div class="section-icon success"><i class="bi bi-briefcase"></i></div>
                <div class="section-text">
                    <h6>Pekerjaan & Penghasilan</h6>
                    <small>Informasi pekerjaan dan pendapatan</small>
                </div>
            </div>
        </div>
        <div class="detail-body">
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Status Pekerjaan</div>
                    <div class="info-value">{{ $pengajuan->debiturPekerjaan->status_pekerjaan ?? '-' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Nama Perusahaan</div>
                    <div class="info-value">{{ $pengajuan->debiturPekerjaan->nama_perusahaan ?? '-' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Bidang Usaha</div>
                    <div class="info-value">{{ $pengajuan->debiturPekerjaan->bidang_usaha ?? '-' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Jabatan</div>
                    <div class="info-value">{{ $pengajuan->debiturPekerjaan->jabatan ?? '-' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Lama Bekerja</div>
                    <div class="info-value">{{ $pengajuan->debiturPekerjaan->lama_bekerja_tahun ?? '-' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Status Kepegawaian</div>
                    <div class="info-value">{{ $pengajuan->debiturPekerjaan->status_kepegawaian ?? '-' }}</div>
                </div>
                <div class="info-item ff">
                    <div class="info-label">Alamat Perusahaan</div>
                    <div class="info-value">{{ $pengajuan->debiturPekerjaan->alamat_perusahaan ?? '-' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Telp Perusahaan</div>
                    <div class="info-value">{{ $pengajuan->debiturPekerjaan->telp_perusahaan ?? '-' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">NPWP</div>
                    <div class="info-value">{{ $pengajuan->debiturPekerjaan->npwp ?? '-' }}</div>
                </div>
            </div>

            <div class="sub-section">
                <div class="sub-section-title"><i class="bi bi-calculator"></i> Rincian Penghasilan</div>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Penghasilan Pokok/Bulan</div>
                        <div class="info-value">{{ number_format($pengajuan->debiturPekerjaan->penghasilan_pokok ?? 0, 0, ',', '.') }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Tunjangan Tetap/Bulan</div>
                        <div class="info-value">{{ number_format($pengajuan->debiturPekerjaan->tunjangan_tetap ?? 0, 0, ',', '.') }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Penghasilan Lain/Bulan</div>
                        <div class="info-value">{{ number_format($pengajuan->debiturPekerjaan->penghasilan_lain ?? 0, 0, ',', '.') }}</div>
                    </div>
                    <div class="info-item">
                    <div class="info-label">Total Penghasilan/Bulan</div>
                    <div class="info-value large">
                        Rp {{ number_format($pengajuan->debiturPekerjaan->total_penghasilan ?? 0, 0, ',', '.') }}
                    </div>
                </div>
                </div>
            </div>
        </div>

        <div class="divider"></div>

        <!-- Keuangan & Bank -->
        <div class="section-header">
            <div class="section-title">
                <div class="section-icon info"><i class="bi bi-bank"></i></div>
                <div class="section-text">
                    <h6>Data Keuangan & Bank</h6>
                    <small>Informasi rekening dan kewajiban</small>
                </div>
            </div>
        </div>
        <div class="detail-body">
            <div class="sub-section-title">Rekening Bank Utama</div>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Nama Bank</div>
                    <div class="info-value">{{ $pengajuan->debiturKeuangan->nama_bank ?? '-' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Nomor Rekening</div>
                    <div class="info-value">{{ $pengajuan->debiturKeuangan->nomor_rekening ?? '-' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Pemilik Rekening</div>
                    <div class="info-value">{{ $pengajuan->debiturKeuangan->nama_pemilik_rekening ?? '-' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Jenis Rekening</div>
                    <div class="info-value">{{ $pengajuan->debiturKeuangan->jenis_rekening ?? 'Tabungan' }}</div>
                </div>
            </div>

            <div class="sub-section">
                <div class="sub-section-title">Kewajiban / Cicilan Aktif</div>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Total Cicilan/Bulan</div>
                        <div class="info-value">{{ number_format($pengajuan->debiturKeuangan->total_cicilan ?? 0, 0, ',', '.') }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Jumlah Kredit Aktif</div>
                        <div class="info-value">{{ $pengajuan->debiturKeuangan->jumlah_kredit_aktif ?? '0' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Limit Kartu Kredit</div>
                        <div class="info-value">{{ number_format($pengajuan->debiturKeuangan->limit_kartu_kredit ?? 0, 0, ',', '.') }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Memiliki KPR Aktif</div>
                        <div class="info-value">{{ isset($pengajuan->debiturKeuangan) && $pengajuan->debiturKeuangan->memiliki_kpr_aktif == 1 ? 'Ya' : 'Tidak' }}</div>
                    </div>
                </div>
            </div>

            <div class="sub-section">
                <div class="sub-section-title">Riwayat Kredit SLIK OJK</div>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Status Kredit Saat Ini</div>
                        <div class="info-value">{{ $pengajuan->debiturKeuangan->status_kredit ?? '-' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Pernah Gagal Bayar</div>
                        <div class="info-value">{{ isset($pengajuan->debiturKeuangan->pernah_gagal_bayar) && $pengajuan->debiturKeuangan->pernah_gagal_bayar == 1 ? 'Ya' : 'Tidak' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="divider"></div>

        <!-- Properti -->
        <div class="section-header">
            <div class="section-title">
                <div class="section-icon warning"><i class="bi bi-house"></i></div>
                <div class="section-text">
                    <h6>Data Properti</h6>
                    <small>Informasi properti yang diajukan</small>
                </div>
            </div>
        </div>
        <div class="detail-body">
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Nama Proyek</div>
                    <div class="info-value">{{ $pengajuan->unit->tipeUnit->proyek->nama_proyek ?? $pengajuan->nama_proyek ?? '-' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Tipe Unit</div>
                    <div class="info-value">{{ $pengajuan->unit->tipeUnit->nama_tipe ?? '-' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Nomor Unit</div>
                    <div class="info-value">{{ $pengajuan->unit->kode_unit ?? '-' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Luas Tanah</div>
                    <div class="info-value">{{ $pengajuan->unit->tipeUnit->luas_tanah ?? '-' }} m²</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Luas Bangunan</div>
                    <div class="info-value">{{ $pengajuan->unit->tipeUnit->luas_bangunan ?? '-' }} m²</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Harga Properti</div>
                    <div class="info-value">{{ number_format($pengajuan->harga_properti ?? 0, 0, ',', '.') }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Uang Muka (DP)</div>
                    <div class="info-value">{{ number_format($pengajuan->uang_muka ?? 0, 0, ',', '.') }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Persentase DP</div>
                    <div class="info-value">{{ $pengajuan->persen_dp ?? 0 }}%</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Jumlah Pinjaman</div>
                    <div class="info-value">{{ number_format($pengajuan->jumlah_pinjaman ?? 0, 0, ',', '.') }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Tenor KPR</div>
                    <div class="info-value">{{ $pengajuan->tenor_tahun ?? '-' }} tahun</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Estimasi Angsuran/Bulan</div>
                    <div class="info-value large">{{ number_format($pengajuan->estimasi_angsuran ?? 0, 0, ',', '.') }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Tujuan Pembelian</div>
                    <div class="info-value">{{ $pengajuan->tujuan_pembelian ?? 'Hunian sendiri' }}</div>
                </div>
            </div>
        </div>

        <div class="divider"></div>

        <!-- Dokumen -->
<div class="section-header">
    <div class="section-title">
        <div class="section-icon purple"><i class="bi bi-file-text"></i></div>
        <div class="section-text">
            <h6>Dokumen Pendukung</h6>
            <small>File yang telah diupload</small>
        </div>
    </div>
</div>
<div class="detail-body">
    <div class="document-list">
        @php
            // Mapping jenis dokumen ke label dan icon
            $documentConfig = [
                'ktp' => ['label' => 'KTP', 'icon' => 'bi-file-earmark-person'],
                'kk' => ['label' => 'Kartu Keluarga', 'icon' => 'bi-file-earmark-text'],
                'npwp' => ['label' => 'NPWP', 'icon' => 'bi-file-earmark-text'],
                'slip_gaji' => ['label' => 'Slip Gaji', 'icon' => 'bi-file-earmark-spreadsheet'],
                'rekening_koran' => ['label' => 'Rekening Koran', 'icon' => 'bi-file-earmark-text'],
                'sk_kerja' => ['label' => 'Surat Keterangan Kerja', 'icon' => 'bi-file-earmark-text'],
                'slik' => ['label' => 'Hasil SLIK OJK', 'icon' => 'bi-file-earmark-check'],
                'buku_nikah' => ['label' => 'Buku Nikah', 'icon' => 'bi-file-earmark-heart'],
                'ktp_pasangan' => ['label' => 'KTP Pasangan', 'icon' => 'bi-file-earmark-person'],
                'foto_diri' => ['label' => 'Foto Diri', 'icon' => 'bi-image'],
                'sk_pengangkatan' => ['label' => 'SK Pengangkatan', 'icon' => 'bi-file-earmark-text'],
                'spt' => ['label' => 'SPT PPH21', 'icon' => 'bi-file-earmark-text'],
                'tagihan_kartu_kredit' => ['label' => 'Tagihan Kartu Kredit', 'icon' => 'bi-credit-card'],
                'bukti_cicilan' => ['label' => 'Bukti Cicilan Aktif', 'icon' => 'bi-receipt'],
                'izin_usaha' => ['label' => 'SIUP/NIB', 'icon' => 'bi-building'],
                'laporan_keuangan' => ['label' => 'Laporan Keuangan Usaha', 'icon' => 'bi-graph-up'],
                'rekening_usaha' => ['label' => 'Rekening Koran Usaha', 'icon' => 'bi-bank'],
                'sip' => ['label' => 'Surat Izin Praktik', 'icon' => 'bi-file-earmark-medical'],
            ];
        @endphp
        
        @forelse($pengajuan->dokumen->groupBy('jenis_dokumen') as $jenis => $dokumenList)
            @php
                $config = $documentConfig[$jenis] ?? ['label' => ucfirst(str_replace('_', ' ', $jenis)), 'icon' => 'bi-file-earmark'];
                $firstDoc = $dokumenList->first();
            @endphp
            <div class="document-item">
                <div class="document-info">
                    <div class="document-icon purple">
                        <i class="bi {{ $config['icon'] }}"></i>
                    </div>
                    <div>
                        <div class="document-name">{{ $config['label'] }}</div>
                        <div class="document-size">
                            {{ $dokumenList->count() }} file • 
                            {{ number_format($dokumenList->sum('ukuran_file') / 1024, 2) }} KB
                        </div>
                        @if($dokumenList->count() > 1)
                            <small class="text-muted">{{ $dokumenList->count() }} file terupload</small>
                        @endif
                    </div>
                </div>
                <div class="document-actions">
                    @foreach($dokumenList as $dokumen)
                        <a href="{{ route('debitur.dokumen.download', $dokumen->id) }}" 
                           class="btn-download" 
                           target="_blank"
                           title="Download {{ $dokumen->nama_file }}">
                            <i class="bi bi-download"></i>
                            @if($loop->first && $dokumenList->count() == 1)
                                Unduh
                            @elseif($loop->first)
                                Unduh ({{ $dokumenList->count() }} file)
                            @endif
                        </a>
                        @break($loop->first)
                    @endforeach
                    
                    @if($dokumenList->count() > 1)
                        <button type="button" class="btn-list" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $jenis }}">
                            <i class="bi bi-list-ul"></i> Lihat semua
                        </button>
                    @endif
                </div>
            </div>
            
            <!-- List all files for documents with multiple files -->
            @if($dokumenList->count() > 1)
            <div class="collapse mt-2 mb-3" id="collapse-{{ $jenis }}">
                <div class="card card-body bg-light">
                    <small class="text-muted mb-2">Semua file {{ $config['label'] }}:</small>
                    <ul class="list-unstyled mb-0">
                        @foreach($dokumenList as $dokumen)
                        <li class="mb-2">
                            <a href="{{ route('debitur.dokumen.download', $dokumen->id) }}" 
                               target="_blank"
                               class="text-decoration-none">
                                <i class="bi bi-file-earmark-pdf text-danger me-2"></i>
                                {{ $dokumen->nama_file }}
                            </a>
                            <span class="text-muted ms-2 small">
                                ({{ number_format($dokumen->ukuran_file / 1024, 2) }} KB)
                            </span>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif
            
        @empty
            <div class="alert alert-info text-center py-4">
                <i class="bi bi-info-circle fs-1"></i>
                <p class="mb-0 mt-2">Belum ada dokumen yang diupload.</p>
            </div>
        @endforelse
    </div>
</div>

        <div class="divider"></div>

        <!-- Persetujuan -->
        <div class="section-header">
            <div class="section-title">
                <div class="section-icon orange"><i class="bi bi-check-circle"></i></div>
                <div class="section-text">
                    <h6>Pernyataan & Persetujuan</h6>
                    <small>Persetujuan dari debitur</small>
                </div>
            </div>
        </div>
        <div class="detail-body">
            <div class="agreement-box" style="background: #F8FAFC; border: 1px solid var(--kpr-border); border-radius: 14px; padding: 12px;">
                <i class="bi bi-check-circle-fill text-success"></i>
                <span style="font-size: 12px; color: #4B5563;">Saya menyatakan bahwa semua data dan dokumen yang saya sampaikan adalah benar dan dapat dipertanggungjawabkan.</span>
            </div>
            <div class="agreement-box" style="background: #F8FAFC; border: 1px solid var(--kpr-border); border-radius: 14px; padding: 12px; margin-top: 10px;">
                <i class="bi bi-check-circle-fill text-success"></i>
                <span style="font-size: 12px; color: #4B5563;">Saya memberikan persetujuan kepada PT Citra Pasada Properti untuk memverifikasi data saya termasuk pengecekan SLIK OJK.</span>
            </div>
            <div class="agreement-box" style="background: #F8FAFC; border: 1px solid var(--kpr-border); border-radius: 14px; padding: 12px; margin-top: 10px;">
                <i class="bi bi-check-circle-fill text-success"></i>
                <span style="font-size: 12px; color: #4B5563;">Saya memahami bahwa keputusan persetujuan KPR sepenuhnya menjadi kewenangan PT Citra Pasada Properti.</span>
            </div>
        </div>
    </div>

    <aside class="sidebar-sticky">
        <div class="detail-side-card">
            <div class="side-title"><i class="bi bi-graph-up-arrow"></i> Ringkasan Pengajuan</div>
            
            <div class="summary-card">
                <div class="summary-label">Total Pinjaman</div>
                <div class="summary-value">Rp {{ number_format(($pengajuan->harga_properti ?? 0) - ($pengajuan->dp ?? 0), 0, ',', '.') }}</div>
                <div class="summary-small">DP: Rp {{ number_format($pengajuan->dp ?? 0, 0, ',', '.') }} ({{ $pengajuan->harga_properti > 0 ? round(($pengajuan->dp ?? 0) / ($pengajuan->harga_properti ?? 1) * 100, 1) : 0 }}%)</div>
            </div>

            <div class="info-row">
                <span class="info-row-label">Tenor</span>
                <span class="info-row-value">{{ $pengajuan->tenor ?? '-' }} tahun</span>
            </div>
            <div class="info-row">
                <span class="info-row-label">Angsuran/Bulan</span>
                <span class="info-row-value">Rp {{ number_format($pengajuan->estimasi_angsuran ?? 0, 0, ',', '.') }}</span>
            </div>
            <div class="info-row">
                <span class="info-row-label">Penghasilan/Bulan</span>
                <span class="info-row-value">Rp {{ number_format(($pengajuan->penghasilan_pokok ?? 0) + ($pengajuan->tunjangan ?? 0) + ($pengajuan->penghasilan_lain ?? 0), 0, ',', '.') }}</span>
            </div>
            <div class="info-row">
                <span class="info-row-label">Rasio Angsuran</span>
                <span class="info-row-value">{{ $pengajuan->harga_properti > 0 && ($pengajuan->penghasilan_pokok ?? 0) > 0 ? round(($pengajuan->estimasi_angsuran ?? 0) / (($pengajuan->penghasilan_pokok ?? 0) + ($pengajuan->tunjangan ?? 0) + ($pengajuan->penghasilan_lain ?? 0)) * 100, 1) : 0 }}%</span>
            </div>

            <div class="action-buttons">
    {{-- Debug: cek apakah $pengajuan ada --}}
    @if(!isset($pengajuan))
        <p class="text-danger">Error: Variabel $pengajuan tidak tersedia di view</p>
    @else
        <p class="text-success">ID Pengajuan: {{ $pengajuan->id }}</p>
        <p class="text-info">Status: {{ $pengajuan->status ?? 'draft' }}</p>
        
        @if(in_array($pengajuan->status ?? 'draft', ['draft', 'revisi']))
        <a href="{{ route('debitur.pengajuan.edit', ['id' => $pengajuan->id]) }}" class="btn-action btn-edit">
            <i class="bi bi-pencil"></i> Edit Pengajuan
        </a>
        @endif
        <button class="btn-action btn-print" onclick="window.print()">
            <i class="bi bi-printer"></i> Cetak
        </button>
    @endif
</div>
        </div>

        <div class="detail-side-card">
            <div class="side-title"><i class="bi bi-clock-history"></i> Timeline Pengajuan</div>
            <div class="timeline">
                <div class="timeline-item completed">
                    <div class="timeline-date">{{ $pengajuan->created_at ? date('d/m/Y H:i', strtotime($pengajuan->created_at)) : '-' }}</div>
                    <div class="timeline-title">Pengajuan Dibuat</div>
                    <div class="timeline-desc">Form pengajuan KPR telah dibuat</div>
                </div>
                
                @if($pengajuan->submitted_at)
                <div class="timeline-item completed">
                    <div class="timeline-date">{{ date('d/m/Y H:i', strtotime($pengajuan->submitted_at)) }}</div>
                    <div class="timeline-title">Pengajuan Dikirim</div>
                    <div class="timeline-desc">Form pengajuan telah dikirim ke sistem</div>
                </div>
                @endif

                @if($pengajuan->verified_at)
                <div class="timeline-item completed">
                    <div class="timeline-date">{{ date('d/m/Y H:i', strtotime($pengajuan->verified_at)) }}</div>
                    <div class="timeline-title">Verifikasi Dokumen</div>
                    <div class="timeline-desc">Dokumen telah diverifikasi oleh admin</div>
                </div>
                @endif

                @if($pengajuan->approved_at)
                <div class="timeline-item completed">
                    <div class="timeline-date">{{ date('d/m/Y H:i', strtotime($pengajuan->approved_at)) }}</div>
                    <div class="timeline-title">Pengajuan Disetujui</div>
                    <div class="timeline-desc">Pengajuan KPR telah disetujui</div>
                </div>
                @endif

                @if($pengajuan->status == 'ditolak')
                <div class="timeline-item rejected">
                    <div class="timeline-date">{{ $pengajuan->rejected_at ? date('d/m/Y H:i', strtotime($pengajuan->rejected_at)) : '-' }}</div>
                    <div class="timeline-title">Pengajuan Ditolak</div>
                    <div class="timeline-desc">{{ $pengajuan->alasan_ditolak ?? 'Dokumen tidak lengkap atau tidak memenuhi syarat' }}</div>
                </div>
                @endif

                @if($pengajuan->status == 'draft')
                <div class="timeline-item pending">
                    <div class="timeline-date">Belum dikirim</div>
                    <div class="timeline-title">Menunggu Pengiriman</div>
                    <div class="timeline-desc">Silakan lengkapi data dan kirim pengajuan</div>
                </div>
                @elseif($pengajuan->status == 'proses')
                <div class="timeline-item pending">
                    <div class="timeline-date">Sedang diproses</div>
                    <div class="timeline-title">Verifikasi Dokumen</div>
                    <div class="timeline-desc">Admin sedang memverifikasi dokumen Anda</div>
                </div>
                @elseif($pengajuan->status == 'verifikasi')
                <div class="timeline-item pending">
                    <div class="timeline-date">Proses verifikasi</div>
                    <div class="timeline-title">Survey & Analisa</div>
                    <div class="timeline-desc">Tim sedang melakukan survey dan analisa</div>
                </div>
                @endif
            </div>
        </div>

        <div class="detail-side-card">
            <div class="side-title"><i class="bi bi-headset"></i> Butuh Bantuan?</div>
            <div class="d-grid gap-2">
                <a href="#" class="btn btn-sm btn-outline-success"><i class="bi bi-whatsapp"></i> WhatsApp</a>
                <a href="#" class="btn btn-sm btn-outline-primary"><i class="bi bi-telephone"></i> Call Center</a>
            </div>
        </div>
    </aside>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });
    });
</script>
@endpush
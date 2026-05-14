@extends('debitur.layouts.app')

@section('title', 'Profil Saya | Debitur PT Pasada Indonesia')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,wght@0,400;0,600;0,700;1,400&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
:root {
    --teal-900:   #0a3d3c;
    --teal-800:   #0f5b5a;
    --teal-700:   #167472;
    --teal-600:   #1b8c89;
    --teal-50:    #f0fafa;
    --teal-100:   #d4efee;

    --gold-500:   #e2a526;
    --gold-400:   #edb83f;
    --gold-100:   #fdf3db;
    --gold-50:    #fffbf0;

    --green-500:  #16a34a;
    --green-50:   #f0fdf4;
    --green-100:  #dcfce7;

    --red-500:    #dc2626;
    --red-50:     #fef2f2;

    --slate-900:  #0f172a;
    --slate-800:  #1e293b;
    --slate-700:  #334155;
    --slate-500:  #64748b;
    --slate-300:  #cbd5e1;
    --slate-100:  #f1f5f9;
    --slate-50:   #f8fafc;

    --surface:    #ffffff;
    --bg:         #f3f6f8;
    --border:     #e2e8f0;

    --radius-sm:  8px;
    --radius:     14px;
    --radius-lg:  20px;
    --radius-xl:  28px;

    --shadow-xs:  0 1px 2px rgba(15,31,53,.05);
    --shadow-sm:  0 2px 8px rgba(15,31,53,.07), 0 1px 2px rgba(15,31,53,.04);
    --shadow-md:  0 8px 24px rgba(15,31,53,.10), 0 2px 6px rgba(15,31,53,.04);
    --shadow-lg:  0 20px 48px rgba(15,31,53,.14);

    --font-display: 'Fraunces', Georgia, serif;
    --font-body:    'Outfit', sans-serif;
}

body { font-family: var(--font-body); background: var(--bg); color: var(--slate-900); }

.profile-wrapper {
    padding: 28px 32px 48px;
    max-width: 1280px;
    margin: 0 auto;
    animation: pageFadeIn .5s ease both;
}
@keyframes pageFadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* Profile Header */
.profile-header {
    background: var(--surface);
    border-radius: var(--radius-xl);
    padding: 32px 40px;
    margin-bottom: 28px;
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--border);
    position: relative;
    overflow: hidden;
}
.profile-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 400px;
    height: 400px;
    background: linear-gradient(135deg, var(--teal-50) 0%, transparent 70%);
    border-radius: 50%;
    pointer-events: none;
}
.profile-header-content {
    display: flex;
    gap: 32px;
    align-items: center;
    position: relative;
    z-index: 1;
    flex-wrap: wrap;
}
.profile-avatar {
    position: relative;
    flex-shrink: 0;
}
.profile-avatar-img {
    width: 120px;
    height: 120px;
    background: linear-gradient(135deg, var(--teal-700), var(--teal-800));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--gold-400);
    font-size: 48px;
    border: 4px solid var(--gold-500);
    box-shadow: var(--shadow-md);
}
.profile-avatar-badge {
    position: absolute;
    bottom: 5px;
    right: 5px;
    background: var(--gold-500);
    border-radius: 50%;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--teal-900);
    cursor: pointer;
    transition: all .25s;
    border: 2px solid var(--surface);
}
.profile-avatar-badge:hover {
    transform: scale(1.1);
    background: var(--gold-400);
}
.profile-info {
    flex: 1;
}
.profile-name {
    font-family: var(--font-display);
    font-size: 32px;
    font-weight: 700;
    color: var(--slate-900);
    margin-bottom: 8px;
}
.profile-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: var(--teal-50);
    color: var(--teal-700);
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    margin-bottom: 16px;
}
.profile-detail-grid {
    display: grid;
    grid-template-columns: repeat(3, auto);
    gap: 20px 32px;
    margin-top: 8px;
}
.profile-detail-item {
    display: flex;
    align-items: center;
    gap: 10px;
}
.profile-detail-icon {
    width: 32px;
    height: 32px;
    background: var(--slate-100);
    border-radius: var(--radius-sm);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--teal-700);
    font-size: 14px;
}
.profile-detail-text {
    font-size: 13px;
}
.profile-detail-label {
    color: var(--slate-500);
    font-weight: 500;
    margin-bottom: 2px;
}
.profile-detail-value {
    font-weight: 700;
    color: var(--slate-900);
}
.profile-actions {
    display: flex;
    gap: 12px;
    flex-shrink: 0;
    flex-wrap: wrap;
}
.btn-edit {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: var(--teal-700);
    color: white;
    padding: 10px 20px;
    border-radius: var(--radius);
    text-decoration: none;
    font-size: 13px;
    font-weight: 600;
    transition: all .25s;
    border: none;
    cursor: pointer;
}
.btn-edit:hover {
    background: var(--teal-800);
    transform: translateY(-2px);
    box-shadow: var(--shadow-sm);
}
.btn-change-password {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: transparent;
    color: var(--slate-700);
    padding: 10px 20px;
    border-radius: var(--radius);
    text-decoration: none;
    font-size: 13px;
    font-weight: 600;
    transition: all .25s;
    border: 1px solid var(--border);
    cursor: pointer;
}
.btn-change-password:hover {
    background: var(--slate-50);
    border-color: var(--slate-300);
}

/* Tabs */
.profile-tabs {
    display: flex;
    gap: 8px;
    margin-bottom: 28px;
    border-bottom: 2px solid var(--border);
    padding-bottom: 0;
}
.tab-btn {
    padding: 12px 24px;
    background: transparent;
    border: none;
    font-family: var(--font-body);
    font-size: 14px;
    font-weight: 600;
    color: var(--slate-500);
    cursor: pointer;
    transition: all .25s;
    position: relative;
}
.tab-btn:hover {
    color: var(--teal-700);
}
.tab-btn.active {
    color: var(--teal-700);
}
.tab-btn.active::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 0;
    right: 0;
    height: 2px;
    background: var(--teal-700);
    border-radius: 2px;
}
.tab-content {
    display: none;
    animation: fadeIn .3s ease;
}
.tab-content.active {
    display: block;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Form Card */
.form-card {
    background: var(--surface);
    border-radius: var(--radius-lg);
    border: 1px solid var(--border);
    padding: 28px 32px;
    box-shadow: var(--shadow-sm);
}
.form-section {
    margin-bottom: 32px;
    padding-bottom: 24px;
    border-bottom: 1px solid var(--border);
}
.form-section:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}
.form-section-title {
    font-family: var(--font-display);
    font-size: 18px;
    font-weight: 600;
    color: var(--slate-900);
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}
.form-section-title i {
    color: var(--teal-700);
    font-size: 20px;
}
.form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
}
.form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.form-group.full-width {
    grid-column: span 2;
}
.form-label {
    font-size: 13px;
    font-weight: 600;
    color: var(--slate-700);
}
.form-label i {
    margin-right: 5px;
    color: var(--teal-600);
}
.form-control {
    padding: 10px 14px;
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    font-family: var(--font-body);
    font-size: 13px;
    transition: all .25s;
    background: var(--surface);
}
.form-control:focus {
    outline: none;
    border-color: var(--teal-600);
    box-shadow: 0 0 0 3px rgba(27, 140, 137, 0.1);
}
.form-control[readonly] {
    background: var(--slate-50);
    cursor: not-allowed;
}
textarea.form-control {
    resize: vertical;
    min-height: 80px;
}
.btn-save {
    background: var(--teal-700);
    color: white;
    border: none;
    padding: 12px 28px;
    border-radius: var(--radius);
    font-weight: 600;
    font-size: 13px;
    cursor: pointer;
    transition: all .25s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}
.btn-save:hover {
    background: var(--teal-800);
    transform: translateY(-2px);
    box-shadow: var(--shadow-sm);
}
.btn-cancel {
    background: var(--slate-100);
    color: var(--slate-700);
    border: none;
    padding: 12px 28px;
    border-radius: var(--radius);
    font-weight: 600;
    font-size: 13px;
    cursor: pointer;
    transition: all .25s;
    margin-left: 12px;
}
.btn-cancel:hover {
    background: var(--slate-200);
}
.form-actions {
    margin-top: 24px;
    padding-top: 20px;
    border-top: 1px solid var(--border);
}

/* Information Cards */
.info-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
}
.info-card {
    background: var(--surface);
    border-radius: var(--radius-lg);
    border: 1px solid var(--border);
    padding: 24px;
    transition: all .25s;
}
.info-card:hover {
    box-shadow: var(--shadow-md);
    transform: translateY(-2px);
}
.info-card-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 20px;
    padding-bottom: 12px;
    border-bottom: 1px solid var(--border);
}
.info-card-icon {
    width: 44px;
    height: 44px;
    background: var(--teal-50);
    border-radius: var(--radius);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--teal-700);
    font-size: 20px;
}
.info-card-title {
    font-family: var(--font-display);
    font-size: 16px;
    font-weight: 600;
    color: var(--slate-900);
}
.info-card-content {
    display: flex;
    flex-direction: column;
    gap: 12px;
}
.info-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 13px;
}
.info-label {
    color: var(--slate-500);
    font-weight: 500;
}
.info-value {
    color: var(--slate-900);
    font-weight: 600;
}
.info-badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
}
.info-badge.active {
    background: var(--green-100);
    color: var(--green-500);
}
.info-badge.pending {
    background: var(--gold-100);
    color: var(--gold-500);
}

/* Responsive */
@media (max-width: 768px) {
    .profile-wrapper { padding: 16px; }
    .profile-header { padding: 24px; }
    .profile-header-content { flex-direction: column; text-align: center; }
    .profile-detail-grid { grid-template-columns: 1fr; }
    .form-grid { grid-template-columns: 1fr; }
    .form-group.full-width { grid-column: span 1; }
    .info-grid { grid-template-columns: 1fr; }
    .profile-tabs { overflow-x: auto; }
    .tab-btn { white-space: nowrap; }
}
</style>
@endpush

@section('content')
<div class="profile-wrapper">
    
    {{-- Profile Header --}}
    <div class="profile-header">
        <div class="profile-header-content">
            <div class="profile-avatar">
                <div class="profile-avatar-img">
                    <i class="bi bi-person-circle"></i>
                </div>
                <div class="profile-avatar-badge" id="changePhotoBtn">
                    <i class="bi bi-camera"></i>
                </div>
            </div>
            
            <div class="profile-info">
                <h1 class="profile-name">{{ Auth::user()->nama_lengkap ?? 'Budi Santoso' }}</h1>
                <div class="profile-badge">
                    <i class="bi bi-shield-check"></i> Debitur Terdaftar
                </div>
                <div class="profile-detail-grid">
                    <div class="profile-detail-item">
                        <div class="profile-detail-icon"><i class="bi bi-envelope"></i></div>
                        <div class="profile-detail-text">
                            <div class="profile-detail-label">Email</div>
                            <div class="profile-detail-value">{{ Auth::user()->email ?? 'budi.santoso@email.com' }}</div>
                        </div>
                    </div>
                    <div class="profile-detail-item">
                        <div class="profile-detail-icon"><i class="bi bi-telephone"></i></div>
                        <div class="profile-detail-text">
                            <div class="profile-detail-label">No. HP</div>
                            <div class="profile-detail-value">{{ Auth::user()->no_hp ?? '0812-3456-7890' }}</div>
                        </div>
                    </div>
                    <div class="profile-detail-item">
                        <div class="profile-detail-icon"><i class="bi bi-calendar3"></i></div>
                        <div class="profile-detail-text">
                            <div class="profile-detail-label">Terdaftar Sejak</div>
                            <div class="profile-detail-value">{{ Auth::user()->created_at ? Auth::user()->created_at->format('d F Y') : '15 Januari 2025' }}</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="profile-actions">
                <button class="btn-edit" id="editProfileBtn">
                    <i class="bi bi-pencil-square"></i> Edit Profil
                </button>
                <button class="btn-change-password" id="changePasswordBtn">
                    <i class="bi bi-key"></i> Ganti Password
                </button>
            </div>
        </div>
    </div>
    
    {{-- Tabs --}}
    <div class="profile-tabs">
        <button class="tab-btn active" data-tab="data-diri">
            <i class="bi bi-person-badge"></i> Data Diri
        </button>
        <button class="tab-btn" data-tab="informasi-keuangan">
            <i class="bi bi-graph-up"></i> Informasi Keuangan
        </button>
        <button class="tab-btn" data-tab="dokumen">
            <i class="bi bi-folder2-open"></i> Dokumen
        </button>
        <button class="tab-btn" data-tab="aktivitas">
            <i class="bi bi-clock-history"></i> Aktivitas
        </button>
    </div>
    
    {{-- Tab 1: Data Diri (View Mode) --}}
    <div id="data-diri" class="tab-content active">
        <div class="form-card">
            <div class="form-section">
                <div class="form-section-title">
                    <i class="bi bi-person"></i>
                    Informasi Pribadi
                </div>
                <div class="form-grid" id="dataDiriView">
                    <div class="form-group">
                        <div class="form-label">Nama Lengkap</div>
                        <div class="form-control" readonly>Budi Santoso</div>
                    </div>
                    <div class="form-group">
                        <div class="form-label">NIK (KTP)</div>
                        <div class="form-control" readonly>3172011501900001</div>
                    </div>
                    <div class="form-group">
                        <div class="form-label">Tempat, Tanggal Lahir</div>
                        <div class="form-control" readonly>Jakarta, 15 Januari 1990</div>
                    </div>
                    <div class="form-group">
                        <div class="form-label">Jenis Kelamin</div>
                        <div class="form-control" readonly>Laki-laki</div>
                    </div>
                    <div class="form-group">
                        <div class="form-label">Status Pernikahan</div>
                        <div class="form-control" readonly>Menikah</div>
                    </div>
                    <div class="form-group">
                        <div class="form-label">Agama</div>
                        <div class="form-control" readonly>Islam</div>
                    </div>
                    <div class="form-group full-width">
                        <div class="form-label">Alamat Sesuai KTP</div>
                        <div class="form-control" readonly>Jl. Mawar No. 123, RT 05 RW 03, Kel. Kebon Jeruk, Kec. Kebon Jeruk, Jakarta Barat 11530</div>
                    </div>
                    <div class="form-group full-width">
                        <div class="form-label">Alamat Domisili</div>
                        <div class="form-control" readonly>Jl. Melati No. 45, Perumahan Green Residence, Tangerang Selatan 15321</div>
                    </div>
                </div>
            </div>
            
            <div class="form-section">
                <div class="form-section-title">
                    <i class="bi bi-envelope-paper"></i>
                    Informasi Kontak
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <div class="form-label">Email</div>
                        <div class="form-control" readonly>budi.santoso@email.com</div>
                    </div>
                    <div class="form-group">
                        <div class="form-label">Nomor HP</div>
                        <div class="form-control" readonly>0812-3456-7890</div>
                    </div>
                    <div class="form-group">
                        <div class="form-label">Nomor Telepon Rumah</div>
                        <div class="form-control" readonly>(021) 1234-5678</div>
                    </div>
                    <div class="form-group">
                        <div class="form-label">Nomor Darurat</div>
                        <div class="form-control" readonly>0813-9876-5432 (Istri)</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Tab 2: Informasi Keuangan --}}
    <div id="informasi-keuangan" class="tab-content">
        <div class="info-grid">
            <div class="info-card">
                <div class="info-card-header">
                    <div class="info-card-icon"><i class="bi bi-briefcase"></i></div>
                    <div class="info-card-title">Data Pekerjaan</div>
                </div>
                <div class="info-card-content">
                    <div class="info-row">
                        <span class="info-label">Status Pekerjaan</span>
                        <span class="info-value">Karyawan Swasta</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Nama Perusahaan</span>
                        <span class="info-value">PT Teknologi Nusantara</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Lama Bekerja</span>
                        <span class="info-value">5 Tahun</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Jabatan</span>
                        <span class="info-value">Manajer IT</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Alamat Kantor</span>
                        <span class="info-value">SCBD Lot 8, Jakarta Selatan</span>
                    </div>
                </div>
            </div>
            
            <div class="info-card">
                <div class="info-card-header">
                    <div class="info-card-icon"><i class="bi bi-calculator"></i></div>
                    <div class="info-card-title">Informasi Penghasilan</div>
                </div>
                <div class="info-card-content">
                    <div class="info-row">
                        <span class="info-label">Penghasilan Bulanan</span>
                        <span class="info-value">Rp 15.000.000</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Penghasilan Pasangan</span>
                        <span class="info-value">Rp 8.000.000</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Penghasilan Lainnya</span>
                        <span class="info-value">Rp 2.000.000</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Total Penghasilan</span>
                        <span class="info-value"><strong>Rp 25.000.000</strong></span>
                    </div>
                </div>
            </div>
            
            <div class="info-card">
                <div class="info-card-header">
                    <div class="info-card-icon"><i class="bi bi-credit-card"></i></div>
                    <div class="info-card-title">Kewajiban Bulanan</div>
                </div>
                <div class="info-card-content">
                    <div class="info-row">
                        <span class="info-label">Cicilan Kredit Lain</span>
                        <span class="info-value">Rp 2.500.000</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Pengeluaran Rumah Tangga</span>
                        <span class="info-value">Rp 5.000.000</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Kewajiban Lainnya</span>
                        <span class="info-value">Rp 1.500.000</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Total Kewajiban</span>
                        <span class="info-value"><strong>Rp 9.000.000</strong></span>
                    </div>
                </div>
            </div>
            
            <div class="info-card">
                <div class="info-card-header">
                    <div class="info-card-icon"><i class="bi bi-building"></i></div>
                    <div class="info-card-title">Informasi Aset</div>
                </div>
                <div class="info-card-content">
                    <div class="info-row">
                        <span class="info-label">Kepemilikan Rumah Saat Ini</span>
                        <span class="info-value">Kontrak</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Kepemilikan Kendaraan</span>
                        <span class="info-value">1 Mobil, 1 Motor</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Tabungan / Deposito</span>
                        <span class="info-value">Rp 150.000.000</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Investasi</span>
                        <span class="info-value">Rp 50.000.000</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Tab 3: Dokumen --}}
    <div id="dokumen" class="tab-content">
        <div class="form-card">
            <div class="form-section">
                <div class="form-section-title">
                    <i class="bi bi-file-earmark-text"></i>
                    Dokumen yang Telah Diupload
                </div>
                <div style="display: flex; flex-direction: column; gap: 16px;">
                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 12px; background: var(--green-50); border-radius: var(--radius-sm);">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <i class="bi bi-check-circle-fill" style="color: var(--green-500); font-size: 20px;"></i>
                            <div>
                                <div style="font-weight: 600;">KTP Debitur</div>
                                <div style="font-size: 12px; color: var(--slate-500);">Upload: 10 Mei 2025</div>
                            </div>
                        </div>
                        <a href="#" style="color: var(--teal-700);"><i class="bi bi-eye"></i> Lihat</a>
                    </div>
                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 12px; background: var(--green-50); border-radius: var(--radius-sm);">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <i class="bi bi-check-circle-fill" style="color: var(--green-500); font-size: 20px;"></i>
                            <div>
                                <div style="font-weight: 600;">Kartu Keluarga</div>
                                <div style="font-size: 12px; color: var(--slate-500);">Upload: 10 Mei 2025</div>
                            </div>
                        </div>
                        <a href="#" style="color: var(--teal-700);"><i class="bi bi-eye"></i> Lihat</a>
                    </div>
                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 12px; background: var(--green-50); border-radius: var(--radius-sm);">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <i class="bi bi-check-circle-fill" style="color: var(--green-500); font-size: 20px;"></i>
                            <div>
                                <div style="font-weight: 600;">Surat Keterangan Kerja</div>
                                <div style="font-size: 12px; color: var(--slate-500);">Upload: 12 Mei 2025</div>
                            </div>
                        </div>
                        <a href="#" style="color: var(--teal-700);"><i class="bi bi-eye"></i> Lihat</a>
                    </div>
                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 12px; background: var(--gold-50); border-radius: var(--radius-sm);">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <i class="bi bi-clock-history" style="color: var(--gold-500); font-size: 20px;"></i>
                            <div>
                                <div style="font-weight: 600;">Rekening Koran</div>
                                <div style="font-size: 12px; color: var(--slate-500);">Upload: 15 Mei 2025 · Menunggu Verifikasi</div>
                            </div>
                        </div>
                        <a href="#" style="color: var(--teal-700);"><i class="bi bi-eye"></i> Lihat</a>
                    </div>
                </div>
            </div>
            
            <div class="form-section">
                <div class="form-section-title">
                    <i class="bi bi-cloud-upload"></i>
                    Upload Dokumen Baru
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <div class="form-label">Slip Gaji 3 Bulan Terakhir</div>
                        <input type="file" class="form-control" accept=".pdf,.jpg,.png">
                        <small style="font-size: 11px; color: var(--slate-500);">PDF/JPG, maks 5MB</small>
                    </div>
                    <div class="form-group">
                        <div class="form-label">NPWP</div>
                        <input type="file" class="form-control" accept=".pdf,.jpg,.png">
                        <small style="font-size: 11px; color: var(--slate-500);">PDF/JPG, maks 5MB</small>
                    </div>
                    <div class="form-group full-width">
                        <div class="form-label">Dokumen Pendukung Lainnya</div>
                        <input type="file" class="form-control" accept=".pdf,.jpg,.png" multiple>
                        <small style="font-size: 11px; color: var(--slate-500);">Bisa upload multiple file</small>
                    </div>
                </div>
                <div class="form-actions">
                    <button class="btn-save"><i class="bi bi-cloud-arrow-up"></i> Upload Dokumen</button>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Tab 4: Aktivitas --}}
    <div id="aktivitas" class="tab-content">
        <div class="form-card">
            <div class="form-section">
                <div class="form-section-title">
                    <i class="bi bi-clock-history"></i>
                    Riwayat Aktivitas Terbaru
                </div>
                <div style="display: flex; flex-direction: column; gap: 20px;">
                    <div style="display: flex; gap: 12px; padding-bottom: 12px; border-bottom: 1px solid var(--border);">
                        <div style="width: 40px; height: 40px; background: var(--teal-100); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-file-earmark-check" style="color: var(--teal-700);"></i>
                        </div>
                        <div style="flex: 1;">
                            <div style="font-weight: 600; margin-bottom: 4px;">Pengajuan KPR Dibuat</div>
                            <div style="font-size: 12px; color: var(--slate-500);">10 Mei 2025 - 14:30</div>
                            <div style="font-size: 12px; margin-top: 4px;">Pengajuan KPR dengan kode KPR-2025-00001 berhasil dibuat</div>
                        </div>
                    </div>
                    <div style="display: flex; gap: 12px; padding-bottom: 12px; border-bottom: 1px solid var(--border);">
                        <div style="width: 40px; height: 40px; background: var(--teal-100); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-upload" style="color: var(--teal-700);"></i>
                        </div>
                        <div style="flex: 1;">
                            <div style="font-weight: 600; margin-bottom: 4px;">Upload Dokumen</div>
                            <div style="font-size: 12px; color: var(--slate-500);">12 Mei 2025 - 09:15</div>
                            <div style="font-size: 12px; margin-top: 4px;">Mengupload KTP, KK, dan Surat Keterangan Kerja</div>
                        </div>
                    </div>
                    <div style="display: flex; gap: 12px; padding-bottom: 12px; border-bottom: 1px solid var(--border);">
                        <div style="width: 40px; height: 40px; background: var(--gold-100); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-eye" style="color: var(--gold-500);"></i>
                        </div>
                        <div style="flex: 1;">
                            <div style="font-weight: 600; margin-bottom: 4px;">Verifikasi Dokumen</div>
                            <div style="font-size: 12px; color: var(--slate-500);">15 Mei 2025 - 11:00</div>
                            <div style="font-size: 12px; margin-top: 4px;">Dokumen KTP, KK, dan SKK telah diverifikasi oleh marketing</div>
                        </div>
                    </div>
                    <div style="display: flex; gap: 12px;">
                        <div style="width: 40px; height: 40px; background: var(--slate-100); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-hourglass-split" style="color: var(--slate-500);"></i>
                        </div>
                        <div style="flex: 1;">
                            <div style="font-weight: 600; margin-bottom: 4px;">Proses Penilaian Kelayakan</div>
                            <div style="font-size: 12px; color: var(--slate-500);">Sedang Berlangsung</div>
                            <div style="font-size: 12px; margin-top: 4px;">Sistem sedang memproses penilaian kelayakan KPR Anda</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Edit Profil (Hidden by default) --}}
<div id="editProfileModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: var(--radius-lg); max-width: 800px; width: 90%; max-height: 90vh; overflow-y: auto;">
        <div style="padding: 24px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center;">
            <h3 style="font-family: var(--font-display); font-size: 20px;">Edit Profil</h3>
            <button id="closeModalBtn" style="background: none; border: none; font-size: 24px; cursor: pointer;">&times;</button>
        </div>
        <div style="padding: 24px;">
            <form id="editProfileForm">
                <div class="form-grid">
                    <div class="form-group full-width">
                        <div class="form-label">Nama Lengkap</div>
                        <input type="text" class="form-control" value="Budi Santoso">
                    </div>
                    <div class="form-group">
                        <div class="form-label">Email</div>
                        <input type="email" class="form-control" value="budi.santoso@email.com">
                    </div>
                    <div class="form-group">
                        <div class="form-label">Nomor HP</div>
                        <input type="text" class="form-control" value="0812-3456-7890">
                    </div>
                    <div class="form-group">
                        <div class="form-label">Tempat Lahir</div>
                        <input type="text" class="form-control" value="Jakarta">
                    </div>
                    <div class="form-group">
                        <div class="form-label">Tanggal Lahir</div>
                        <input type="date" class="form-control" value="1990-01-15">
                    </div>
                    <div class="form-group full-width">
                        <div class="form-label">Alamat</div>
                        <textarea class="form-control" rows="3">Jl. Mawar No. 123, RT 05 RW 03, Kel. Kebon Jeruk, Kec. Kebon Jeruk, Jakarta Barat 11530</textarea>
                    </div>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn-save"><i class="bi bi-save"></i> Simpan Perubahan</button>
                    <button type="button" class="btn-cancel" id="cancelEditBtn">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab switching
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const tabId = this.dataset.tab;
            
            tabBtns.forEach(b => b.classList.remove('active'));
            tabContents.forEach(c => c.classList.remove('active'));
            
            this.classList.add('active');
            document.getElementById(tabId).classList.add('active');
        });
    });
    
    // Edit Profile Modal
    const editProfileBtn = document.getElementById('editProfileBtn');
    const modal = document.getElementById('editProfileModal');
    const closeModalBtn = document.getElementById('closeModalBtn');
    const cancelEditBtn = document.getElementById('cancelEditBtn');
    
    if (editProfileBtn) {
        editProfileBtn.addEventListener('click', function() {
            modal.style.display = 'flex';
        });
    }
    
    const closeModal = () => {
        modal.style.display = 'none';
    };
    
    if (closeModalBtn) closeModalBtn.addEventListener('click', closeModal);
    if (cancelEditBtn) cancelEditBtn.addEventListener('click', closeModal);
    
    // Click outside modal to close
    modal.addEventListener('click', function(e) {
        if (e.target === modal) closeModal();
    });
    
    // Change Password
    const changePasswordBtn = document.getElementById('changePasswordBtn');
    if (changePasswordBtn) {
        changePasswordBtn.addEventListener('click', function() {
            alert('Fitur ganti password akan segera tersedia');
        });
    }
    
    // Upload document
    const uploadBtn = document.querySelector('.btn-save');
    if (uploadBtn) {
        uploadBtn.addEventListener('click', function(e) {
            if (this.closest('#dokumen')) {
                e.preventDefault();
                alert('Dokumen berhasil diupload dan akan segera diverifikasi');
            }
        });
    }
    
    // Edit photo
    const changePhotoBtn = document.getElementById('changePhotoBtn');
    if (changePhotoBtn) {
        changePhotoBtn.addEventListener('click', function() {
            alert('Fitur ganti foto profil akan segera tersedia');
        });
    }
    
    // Animate cards on scroll
    const cards = document.querySelectorAll('.info-card');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '0';
                entry.target.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    entry.target.style.transition = 'all 0.5s ease';
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }, 100);
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });
    
    cards.forEach(card => observer.observe(card));
});
</script>
@endsection
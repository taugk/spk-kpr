
@extends('debitur.layouts.app')

@section('title', 'Pengajuan KPR | Debitur Citra Pasada')

@section('content')
<style>
    :root {
        --kpr-primary: #0C447C;
        --kpr-primary-soft: #E6F1FB;
        --kpr-success: #0F7B4F;
        --kpr-success-soft: #E1F5EE;
        --kpr-danger: #E24B4A;
        --kpr-warning: #F59E0B;
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

    .draft-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 12px;
        border-radius: 999px;
        border: 1px solid var(--kpr-border);
        background: #fff;
        color: var(--kpr-muted);
        font-size: 12px;
        white-space: nowrap;
    }

    .wizard-shell {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 320px;
        gap: 22px;
        align-items: start;
    }

    .wizard-main,
    .wizard-side-card {
        background: var(--kpr-card);
        border: 1px solid var(--kpr-border);
        border-radius: var(--kpr-radius-lg);
        box-shadow: var(--kpr-shadow);
    }

    .wizard-main { overflow: hidden; }

    .wizard-header {
        padding: 18px 22px;
        border-bottom: 1px solid var(--kpr-border);
        background: linear-gradient(135deg, #FFFFFF 0%, #F2F7FC 100%);
    }

    .progress-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
        gap: 14px;
    }

    .progress-label {
        font-size: 12px;
        color: var(--kpr-muted);
    }

    .progress-percent {
        font-size: 13px;
        color: var(--kpr-primary);
        font-weight: 700;
    }

    .progress-track {
        width: 100%;
        height: 9px;
        background: #E8EEF5;
        border-radius: 999px;
        overflow: hidden;
    }

    .progress-fill {
        width: 0%;
        height: 100%;
        background: linear-gradient(90deg, #0C447C, #2F80ED);
        border-radius: 999px;
        transition: width .35s ease;
    }

    .step-tabs {
        display: grid;
        grid-template-columns: repeat(6, minmax(0, 1fr));
        gap: 8px;
        margin-top: 16px;
    }

    .step-tab {
        border: 1px solid var(--kpr-border);
        background: #fff;
        border-radius: 14px;
        padding: 10px 8px;
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        transition: .2s ease;
        min-width: 0;
    }

    .step-tab:hover { border-color: #9CC3EA; transform: translateY(-1px); }

    .step-number {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: #F1F5F9;
        color: #64748B;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: 700;
        flex: 0 0 auto;
        border: 1px solid #E2E8F0;
    }

    .step-tab span {
        font-size: 11px;
        font-weight: 600;
        color: #64748B;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .step-tab.active {
        border-color: #73A9E4;
        background: var(--kpr-primary-soft);
    }

    .step-tab.active .step-number {
        background: var(--kpr-primary);
        color: #fff;
        border-color: var(--kpr-primary);
    }

    .step-tab.active span { color: var(--kpr-primary); }

    .step-tab.completed {
        border-color: #A7DCC4;
        background: var(--kpr-success-soft);
    }

    .step-tab.completed .step-number {
        background: var(--kpr-success);
        color: #fff;
        border-color: var(--kpr-success);
    }

    .step-tab.completed span { color: var(--kpr-success); }

    .wizard-body { padding: 22px; }

    .form-step {
        display: none;
        opacity: 0;
        transform: translateY(10px);
    }

    .form-step.active {
        display: block;
        animation: stepFade .32s ease forwards;
    }

    @keyframes stepFade {
        to { opacity: 1; transform: translateY(0); }
    }

    .fc {
        background: #fff;
        border: 1px solid var(--kpr-border);
        border-radius: var(--kpr-radius-lg);
        overflow: hidden;
    }

    .sh {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px 18px;
        border-bottom: 1px solid var(--kpr-border);
        background: #FBFCFE;
    }

    .sb {
        width: 34px;
        height: 34px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 auto;
    }

    .s-blue { background: #E6F1FB; color: #0C447C; }
    .s-teal { background: #E1F5EE; color: #085041; }
    .s-purple { background: #EEEDFE; color: #3C3489; }
    .s-amber { background: #FAEEDA; color: #633806; }
    .s-coral { background: #FAECE7; color: #712B13; }
    .s-green { background: #EAF3DE; color: #27500A; }

    .st { font-size: 15px; font-weight: 700; color: var(--kpr-text); }
    .st small { display: block; font-size: 11px; font-weight: 400; color: var(--kpr-muted); margin-top: 2px; }

    .tag-required,
    .tag-optional {
        display: inline-flex;
        align-items: center;
        padding: 2px 8px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 700;
        margin-left: 6px;
    }

    .tag-required { background: #E6F1FB; color: #185FA5; }
    .tag-optional { background: #F3F4F6; color: #6B7280; }

    .sb2 { padding: 18px; }

    .fg,
    .fg3 {
        display: grid;
        gap: 14px;
    }

    .fg { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .fg3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    .ff { grid-column: 1 / -1; }

    .fl {
        font-size: 12px;
        color: #4B5563;
        margin-bottom: 6px;
        font-weight: 600;
    }

    .fr { color: var(--kpr-danger); margin-left: 2px; }

    .fi,
    .fs {
        width: 100%;
        border: 1px solid var(--kpr-border-2);
        background: #F8FAFC;
        border-radius: 12px;
        padding: 10px 12px;
        font-size: 13px;
        color: var(--kpr-text);
        outline: none;
        transition: .18s ease;
        box-sizing: border-box;
    }

    .fi:focus,
    .fs:focus {
        border-color: #3B82F6;
        background: #fff;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, .10);
    }

    .field-error {
        display: none;
        margin-top: 5px;
        font-size: 11px;
        color: var(--kpr-danger);
    }

    .is-invalid-field {
        border-color: var(--kpr-danger) !important;
        background: #FFF5F5 !important;
    }

    .is-valid-field {
        border-color: #22C55E !important;
        background: #F7FFF9 !important;
    }

    .divider {
        border: 0;
        border-top: 1px solid var(--kpr-border);
        margin: 18px 0;
    }

    .sub-title {
        font-size: 13px;
        font-weight: 700;
        color: var(--kpr-text);
        margin-bottom: 10px;
    }

    .note {
        font-size: 12px;
        color: #5B6472;
        background: #F8FAFC;
        border: 1px solid #E8EEF5;
        border-radius: 12px;
        padding: 10px 12px;
        margin-top: 14px;
        line-height: 1.6;
    }

    .upload-box {
        position: relative;
        border: 1.5px dashed #B7C4D4;
        border-radius: 16px;
        background: #F8FAFC;
        padding: 16px 12px;
        min-height: 128px;
        text-align: center;
        cursor: pointer;
        transition: .2s ease;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 4px;
    }

    .upload-box:hover,
    .upload-box.dragover {
        border-color: var(--kpr-primary);
        background: #EFF6FF;
        transform: translateY(-1px);
    }

    .upload-box.uploaded {
        border-style: solid;
        border-color: #22C55E;
        background: #F0FDF4;
    }

    .upload-icon {
        width: 38px;
        height: 38px;
        border-radius: 14px;
        background: #E6F1FB;
        color: var(--kpr-primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        margin-bottom: 4px;
    }

    .upload-title { font-size: 12px; color: var(--kpr-text); font-weight: 700; }
    .upload-hint { font-size: 11px; color: var(--kpr-muted); }

    .upload-preview {
        width: 100%;
        margin-top: 8px;
        display: none;
        gap: 8px;
        flex-direction: column;
    }

    .preview-item {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px;
        border-radius: 12px;
        background: #fff;
        border: 1px solid #E5E7EB;
        text-align: left;
    }

    .preview-thumb {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        object-fit: cover;
        background: #EFF6FF;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--kpr-primary);
        font-size: 18px;
        flex: 0 0 auto;
    }

    .preview-name {
        font-size: 11px;
        color: var(--kpr-text);
        font-weight: 700;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .preview-size { font-size: 10px; color: var(--kpr-muted); }

    .file-input-hidden { display: none; }

    .wizard-actions {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-top: 18px;
    }

    .wizard-actions-right {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .btn-wizard {
        border: 0;
        border-radius: 13px;
        padding: 11px 18px;
        font-size: 13px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        transition: .2s ease;
        text-decoration: none;
    }

    .btn-prev { background: #F1F5F9; color: #475569; }
    .btn-next { background: var(--kpr-primary); color: #fff; }
    .btn-draft { background: #fff; color: #475569; border: 1px solid var(--kpr-border); }
    .btn-submit { background: linear-gradient(135deg, #0C447C, #1769B5); color: #fff; }

    .btn-wizard:hover { transform: translateY(-1px); opacity: .94; }
    .btn-wizard:disabled { opacity: .65; cursor: not-allowed; transform: none; }

    .spinner-mini {
        width: 14px;
        height: 14px;
        border: 2px solid rgba(255,255,255,.45);
        border-top-color: #fff;
        border-radius: 50%;
        display: none;
        animation: spin .75s linear infinite;
    }

    .btn-submit.loading .spinner-mini { display: inline-block; }
    .btn-submit.loading .submit-text { opacity: .85; }

    @keyframes spin { to { transform: rotate(360deg); } }

    .sidebar-sticky {
        position: sticky;
        top: 18px;
        display: grid;
        gap: 16px;
    }

    .wizard-side-card { padding: 18px; }

    .side-title {
        font-size: 14px;
        font-weight: 800;
        color: var(--kpr-text);
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .side-progress-circle {
        width: 116px;
        height: 116px;
        border-radius: 50%;
        margin: 8px auto 14px;
        background: conic-gradient(var(--kpr-primary) 0deg, #E8EEF5 0deg);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: .35s ease;
    }

    .side-progress-inner {
        width: 88px;
        height: 88px;
        border-radius: 50%;
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
    }

    .side-progress-inner strong { color: var(--kpr-primary); font-size: 22px; }
    .side-progress-inner span { color: var(--kpr-muted); font-size: 10px; }

    .side-list { list-style: none; padding: 0; margin: 0; display: grid; gap: 8px; }
    .side-list li { font-size: 12px; color: #4B5563; display: flex; align-items: center; gap: 8px; }
    .side-list i { color: var(--kpr-success); }

    .autosave-status {
        font-size: 11px;
        color: var(--kpr-muted);
        display: flex;
        align-items: center;
        gap: 6px;
        margin-top: 12px;
        padding-top: 12px;
        border-top: 1px solid var(--kpr-border);
    }

    .agreement-box {
        display: flex;
        gap: 10px;
        align-items: flex-start;
        padding: 12px;
        border: 1px solid var(--kpr-border);
        border-radius: 14px;
        background: #F8FAFC;
        margin-bottom: 10px;
    }

    .agreement-box input { width: 16px; height: 16px; margin-top: 2px; }
    .agreement-box label { font-size: 12px; color: #4B5563; line-height: 1.6; margin: 0; cursor: pointer; }

    @media (max-width: 1199px) {
        .wizard-shell { grid-template-columns: 1fr; }
        .sidebar-sticky { position: static; }
    }

    @media (max-width: 768px) {
        .kpr-page-title { flex-direction: column; align-items: flex-start; }
        .wizard-header, .wizard-body { padding: 14px; }
        .step-tabs { display: flex; overflow-x: auto; padding-bottom: 6px; scroll-snap-type: x mandatory; }
        .step-tab { min-width: 130px; scroll-snap-align: start; }
        .fg, .fg3 { grid-template-columns: 1fr; }
        .wizard-actions { flex-direction: column; align-items: stretch; }
        .wizard-actions-right { width: 100%; display: grid; grid-template-columns: 1fr; }
        .btn-wizard { width: 100%; }
        .draft-pill { width: 100%; justify-content: center; }
    }
</style>

<div class="row">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('debitur.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item active">Pengajuan KPR</li>
            </ol>
        </nav>
    </div>
</div>

<div class="kpr-page-title">
    <div class="kpr-title">
        <h5>Form Pengajuan KPR</h5>
        <p>PT Citra Pasada Properti · Isi data secara bertahap sampai dokumen persetujuan.</p>
    </div>
    <div class="draft-pill" id="draftHeaderStatus">
        <i class="bi bi-save"></i>
        Draft otomatis aktif
    </div>
</div>

<form method="POST" action="{{ route('debitur.pengajuan.store') }}" enctype="multipart/form-data" id="kprForm" novalidate>
    @csrf

    <div class="wizard-shell">
        <div class="wizard-main">
            <div class="wizard-header">
                <div class="progress-top">
                    <div class="progress-label" id="progressStepLabel">Section 1 dari 6</div>
                    <div class="progress-percent" id="progressPercent">0%</div>
                </div>
                <div class="progress-track">
                    <div class="progress-fill" id="progressFill"></div>
                </div>

                <div class="step-tabs" id="stepTabs">
                    <div class="step-tab active" data-step="0"><div class="step-number">1</div><span>Data diri</span></div>
                    <div class="step-tab" data-step="1"><div class="step-number">2</div><span>Pekerjaan</span></div>
                    <div class="step-tab" data-step="2"><div class="step-number">3</div><span>Keuangan</span></div>
                    <div class="step-tab" data-step="3"><div class="step-number">4</div><span>Properti</span></div>
                    <div class="step-tab" data-step="4"><div class="step-number">5</div><span>Dokumen</span></div>
                    <div class="step-tab" data-step="5"><div class="step-number">6</div><span>Persetujuan</span></div>
                </div>
            </div>

            <div class="wizard-body">
                <div class="form-step active" data-step-title="Data pribadi">
                    <div class="fc">
                        <div class="sh">
                            <div class="sb s-blue"><i class="bi bi-person"></i></div>
                            <div class="st">1. Data pribadi <span class="tag-required">Wajib</span><small>Data identitas debitur sesuai dokumen resmi.</small></div>
                        </div>
                        <div class="sb2">
                            <div class="fg">
                                <div>
                                    <div class="fl">Nama lengkap sesuai KTP<span class="fr">*</span></div>
                                    <input type="text" name="nama_lengkap" class="fi" 
                                        value="{{ old('nama_lengkap', Auth::check() ? Auth::user()->nama_lengkap : '') }}" 
                                        placeholder="Masukkan nama lengkap" required>
                                    <div class="field-error">Nama lengkap wajib diisi.</div>
                                </div>
                                <div>
                                    <div class="fl">NIK 16 digit<span class="fr">*</span></div>
                                    <input type="text" name="nik" class="fi" maxlength="16" inputmode="numeric" placeholder="16 digit NIK" required data-minlength="16">
                                    <div class="field-error">NIK wajib 16 digit.</div>
                                </div>
                                <div>
                                    <div class="fl">Tempat lahir<span class="fr">*</span></div>
                                    <input type="text" name="tempat_lahir" class="fi" placeholder="Nama kota" required>
                                    <div class="field-error">Tempat lahir wajib diisi.</div>
                                </div>
                                <div>
                                    <div class="fl">Tanggal lahir<span class="fr">*</span></div>
                                    <input type="date" name="tanggal_lahir" class="fi" required>
                                    <div class="field-error">Tanggal lahir wajib diisi.</div>
                                </div>
                                <div>
                                    <div class="fl">Jenis kelamin<span class="fr">*</span></div>
                                    <select name="jenis_kelamin" class="fs" required>
                                        <option value="">Pilih...</option>
                                        <option value="Laki-laki">Laki-laki</option>
                                        <option value="Perempuan">Perempuan</option>
                                    </select>
                                    <div class="field-error">Jenis kelamin wajib dipilih.</div>
                                </div>
                                <div>
                                    <div class="fl">Agama</div>
                                    <select name="agama" class="fs">
                                        <option value="">Pilih agama...</option>
                                        <option value="Islam">Islam</option>
                                        <option value="Kristen">Kristen</option>
                                        <option value="Katolik">Katolik</option>
                                        <option value="Hindu">Hindu</option>
                                        <option value="Buddha">Buddha</option>
                                        <option value="Konghucu">Konghucu</option>
                                    </select>
                                </div>
                                <div>
                                    <div class="fl">Status pernikahan<span class="fr">*</span></div>
                                    <select name="status_pernikahan" class="fs" required>
                                        <option value="">Pilih...</option>
                                        <option value="belum_menikah">Belum Menikah</option>
                                        <option value="sudah_menikah">Sudah Menikah</option>
                                        <option value="cerai">Cerai</option>
                                    </select>
                                    <div class="field-error">Status pernikahan wajib dipilih.</div>
                                </div>
                                <div>
                                    <div class="fl">Jumlah tanggungan<span class="fr">*</span></div>
                                    <select name="jumlah_tanggungan" class="fs" required>
                                        <option value="0">0</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value=">3">&gt;3</option>
                                    </select>
                                    <div class="field-error">Jumlah tanggungan wajib dipilih.</div>
                                </div>
                                <div>
                                    <div class="fl">Pendidikan terakhir</div>
                                    <select name="pendidikan" class="fs">
                                        <option value="">Pilih...</option><option value="SD">SD</option><option value="SMP">SMP</option><option value="SMA">SMA</option><option value="D3">D3</option><option value="S1">S1</option><option value="S2">S2</option><option value="S3">S3</option>
                                    </select>
                                </div>
                                <div>
                                    <div class="fl">Kewarganegaraan</div>
                                    <select name="kewarganegaraan" class="fs"><option value="WNI">WNI</option><option value="WNA">WNA</option></select>
                                </div>
                                <div class="ff">
                                    <div class="fl">Alamat sesuai KTP<span class="fr">*</span></div>
                                    <textarea name="alamat_ktp" class="fi" rows="2" placeholder="Jalan, No., RT/RW, Kelurahan, Kecamatan" required></textarea>
                                    <div class="field-error">Alamat KTP wajib diisi.</div>
                                </div>
                                <div>
                                    <div class="fl">Kota / Kabupaten<span class="fr">*</span></div>
                                    <input type="text" name="kota" class="fi" placeholder="Nama kota" required>
                                    <div class="field-error">Kota wajib diisi.</div>
                                </div>
                                <div>
                                    <div class="fl">Provinsi<span class="fr">*</span></div>
                                    <select name="provinsi" class="fs" required>
                                        <option value="">Pilih provinsi...</option>
                                        <option value="DKI Jakarta">DKI Jakarta</option><option value="Jawa Barat">Jawa Barat</option><option value="Jawa Tengah">Jawa Tengah</option><option value="Jawa Timur">Jawa Timur</option><option value="Banten">Banten</option><option value="Yogyakarta">Yogyakarta</option><option value="Bali">Bali</option><option value="Sumatera Utara">Sumatera Utara</option><option value="Sumatera Barat">Sumatera Barat</option><option value="Sumatera Selatan">Sumatera Selatan</option><option value="Sulawesi Selatan">Sulawesi Selatan</option><option value="Kalimantan Timur">Kalimantan Timur</option><option value="Papua">Papua</option>
                                    </select>
                                    <div class="field-error">Provinsi wajib dipilih.</div>
                                </div>
                                <div><div class="fl">Kode pos</div><input type="text" name="kode_pos" class="fi" placeholder="XXXXX"></div>
                                <div>
                                    <div class="fl">Status tempat tinggal saat ini</div>
                                    <select name="status_tempat_tinggal" class="fs"><option value="Milik sendiri">Milik sendiri</option><option value="Sewa">Sewa</option><option value="Keluarga">Keluarga</option></select>
                                </div>
                                <div>
                                    <div class="fl">Nomor telepon / HP<span class="fr">*</span></div>
                                    <input type="tel" name="no_hp" class="fi" placeholder="08xx-xxxx-xxxx" required>
                                    <div class="field-error">Nomor HP wajib diisi.</div>
                                </div>
                                <div>
                                    <div class="fl">Email aktif<span class="fr">*</span></div>
                                    <input type="email" name="email" class="fi" placeholder="email@domain.com" required>
                                    <div class="field-error">Email wajib diisi.</div>
                                </div>
                                <div><div class="fl">Nomor KK</div><input type="text" name="no_kk" class="fi" placeholder="16 digit nomor KK"></div>
                                <div>
                                    <div class="fl">Nama ibu kandung<span class="fr">*</span></div>
                                    <input type="text" name="nama_ibu" class="fi" placeholder="Untuk verifikasi identitas" required>
                                    <div class="field-error">Nama ibu kandung wajib diisi.</div>
                                </div>
                                <div><div class="fl">Nama pasangan</div><input type="text" name="nama_pasangan" class="fi" placeholder="Jika sudah menikah"></div>
                                <div><div class="fl">NIK pasangan</div><input type="text" name="nik_pasangan" class="fi" placeholder="Jika sudah menikah"></div>
                            </div>
                        </div>
                    </div>
                    <div class="wizard-actions">
                        <a href="{{ route('debitur.dashboard') }}" class="btn-wizard btn-prev"><i class="bi bi-arrow-left"></i> Dashboard</a>
                        <button type="button" class="btn-wizard btn-next next-step">Selanjutnya <i class="bi bi-arrow-right"></i></button>
                    </div>
                </div>

                <div class="form-step" data-step-title="Pekerjaan & penghasilan">
                    <div class="fc">
                        <div class="sh">
                            <div class="sb s-teal"><i class="bi bi-briefcase"></i></div>
                            <div class="st">2. Pekerjaan & penghasilan <span class="tag-required">Wajib</span><small>Data pekerjaan dan sumber pendapatan debitur.</small></div>
                        </div>
                        <div class="sb2">
                            <div class="fg">
                                <div>
                                    <div class="fl">Status pekerjaan<span class="fr">*</span></div>
                                    <select name="status_pekerjaan" class="fs" required>
                                        <option value="">Pilih...</option><option value="Karyawan">Karyawan</option><option value="Wiraswasta">Wiraswasta</option><option value="Profesional">Profesional</option><option value="PNS">PNS</option><option value="TNI-Polri">TNI-Polri</option>
                                    </select>
                                    <div class="field-error">Status pekerjaan wajib dipilih.</div>
                                </div>
                                <div>
                                    <div class="fl">Nama perusahaan / instansi<span class="fr">*</span></div>
                                    <input type="text" name="nama_perusahaan" class="fi" placeholder="PT / CV / Instansi" required>
                                    <div class="field-error">Nama perusahaan wajib diisi.</div>
                                </div>
                                <div>
                                    <div class="fl">Bidang usaha / industri</div>
                                    <select name="bidang_usaha" class="fs"><option value="">Pilih sektor...</option><option value="Perbankan">Perbankan</option><option value="Teknologi">Teknologi</option><option value="Manufaktur">Manufaktur</option><option value="Perdagangan">Perdagangan</option><option value="Jasa">Jasa</option><option value="Pendidikan">Pendidikan</option><option value="Kesehatan">Kesehatan</option></select>
                                </div>
                                <div><div class="fl">Jabatan / posisi</div><input type="text" name="jabatan" class="fi" placeholder="Staff / Manajer / Direktur"></div>
                                <div>
                                    <div class="fl">Lama bekerja / usaha<span class="fr">*</span></div>
                                    <select name="lama_bekerja" class="fs" required><option value="">Pilih...</option><option value="<1 th">&lt;1 th</option><option value="1-2 th">1-2 th</option><option value="2-5 th">2-5 th</option><option value=">5 th">&gt;5 th</option></select>
                                    <div class="field-error">Lama bekerja wajib dipilih.</div>
                                </div>
                                <div>
                                    <div class="fl">Status kepegawaian</div>
                                    <select name="status_kepegawaian" class="fs"><option value="">Pilih...</option><option value="Tetap">Tetap</option><option value="Kontrak">Kontrak</option><option value="Percobaan">Percobaan</option></select>
                                </div>
                                <div class="ff"><div class="fl">Alamat perusahaan</div><textarea name="alamat_perusahaan" class="fi" rows="2" placeholder="Jalan, Kota, Provinsi"></textarea></div>
                                <div><div class="fl">Telepon perusahaan</div><input type="text" name="telp_perusahaan" class="fi" placeholder="021-xxxx-xxxx"></div>
                                <div>
                                    <div class="fl">NPWP<span class="fr">*</span></div>
                                    <input type="text" name="npwp" class="fi" placeholder="XX.XXX.XXX.X-XXX.XXX" required>
                                    <div class="field-error">NPWP wajib diisi.</div>
                                </div>
                            </div>
                            <hr class="divider">
                            <div class="sub-title">Rincian penghasilan</div>
                            <div class="fg">
                                <div>
                                    <div class="fl">Penghasilan pokok / bulan<span class="fr">*</span></div>
                                    <input type="number" name="penghasilan_pokok" class="fi" id="penghasilan_pokok" placeholder="Rp 0" required>
                                    <div class="field-error">Penghasilan pokok wajib diisi.</div>
                                </div>
                                <div><div class="fl">Tunjangan tetap / bulan</div><input type="number" name="tunjangan" class="fi" id="tunjangan" placeholder="Rp 0"></div>
                                <div><div class="fl">Penghasilan lain-lain / bulan</div><input type="number" name="penghasilan_lain" class="fi" id="penghasilan_lain" placeholder="Sewa, usaha sampingan, dll"></div>
                                <div><div class="fl">Total penghasilan / bulan<span class="fr">*</span></div><input type="text" class="fi" id="total_penghasilan" readonly style="font-weight:700;background:#EEF2F7" placeholder="Otomatis terhitung"></div>
                            </div>
                            <div class="note"><i class="bi bi-info-circle me-1"></i> Penghasilan akan diverifikasi melalui slip gaji dan rekening koran.</div>
                        </div>
                    </div>
                    <div class="wizard-actions">
                        <button type="button" class="btn-wizard btn-prev prev-step"><i class="bi bi-arrow-left"></i> Sebelumnya</button>
                        <button type="button" class="btn-wizard btn-next next-step">Selanjutnya <i class="bi bi-arrow-right"></i></button>
                    </div>
                </div>

                <div class="form-step" data-step-title="Keuangan & bank">
                    <div class="fc">
                        <div class="sh">
                            <div class="sb s-purple"><i class="bi bi-building-bank"></i></div>
                            <div class="st">3. Data keuangan & bank <span class="tag-required">Wajib</span><small>Rekening, cicilan, SLIK, dan aset debitur.</small></div>
                        </div>
                        <div class="sb2">
                            <div class="sub-title">Rekening bank utama</div>
                            <div class="fg">
                                <div>
                                    <div class="fl">Nama bank<span class="fr">*</span></div>
                                    <select name="nama_bank" class="fs" required><option value="">Pilih bank...</option><option value="BCA">BCA</option><option value="BRI">BRI</option><option value="BNI">BNI</option><option value="Mandiri">Mandiri</option><option value="Lainnya">Lainnya</option></select>
                                    <div class="field-error">Nama bank wajib dipilih.</div>
                                </div>
                                <div>
                                    <div class="fl">Nomor rekening<span class="fr">*</span></div>
                                    <input type="text" name="nomor_rekening" class="fi" placeholder="Nomor rekening" required>
                                    <div class="field-error">Nomor rekening wajib diisi.</div>
                                </div>
                                <div>
                                    <div class="fl">Nama pemilik rekening<span class="fr">*</span></div>
                                    <input type="text" name="pemilik_rekening" class="fi" placeholder="Sesuai buku tabungan" required>
                                    <div class="field-error">Pemilik rekening wajib diisi.</div>
                                </div>
                                <div><div class="fl">Jenis rekening</div><select name="jenis_rekening" class="fs"><option value="tabungan">Tabungan</option><option value="giro">Giro</option></select></div>
                                <div><div class="fl">Rata-rata saldo 3 bln terakhir</div><input type="number" name="rata_saldo" class="fi" placeholder="Rp 0"></div>
                                <div><div class="fl">Rata-rata mutasi kredit / bulan</div><input type="number" name="rata_mutasi" class="fi" placeholder="Rp 0"></div>
                            </div>

                            <hr class="divider">
                            <div class="sub-title">Kewajiban / cicilan aktif</div>
                            <div class="fg">
                                <div>
                                    <div class="fl">Total cicilan / bulan<span class="fr">*</span></div>
                                    <input type="number" name="total_cicilan" class="fi" id="total_cicilan" placeholder="Rp 0" required>
                                    <div class="field-error">Total cicilan wajib diisi.</div>
                                </div>
                                <div><div class="fl">Jumlah kredit aktif</div><select name="jumlah_kredit_aktif" class="fs"><option value="0">0</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value=">3">&gt;3</option></select></div>
                                <div><div class="fl">Limit kartu kredit</div><input type="number" name="limit_kartu_kredit" class="fi" placeholder="Rp 0"></div>
                                <div><div class="fl">Tagihan kartu kredit / bulan</div><input type="number" name="tagihan_kartu_kredit" class="fi" placeholder="Rp 0"></div>
                                <div>
                                    <div class="fl">Memiliki KPR aktif?<span class="fr">*</span></div>
                                    <select name="memiliki_kpr_aktif" class="fs" required><option value="Tidak">Tidak</option><option value="Ya">Ya</option></select>
                                    <div class="field-error">Wajib dipilih.</div>
                                </div>
                                <div><div class="fl">Sisa pokok KPR aktif</div><input type="number" name="sisa_pokok_kpr" class="fi" placeholder="Rp 0"></div>
                            </div>

                            <hr class="divider">
                            <div class="sub-title">Riwayat kredit SLIK OJK</div>
                            <div class="fg">
                                <div><div class="fl">Status kredit saat ini<span class="fr">*</span></div><select name="status_kredit" class="fs" required><option value="Lancar">Lancar</option><option value="DPK">DPK</option><option value="Kurang lancar">Kurang lancar</option><option value="Macet">Macet</option></select><div class="field-error">Status kredit wajib dipilih.</div></div>
                                <div><div class="fl">Pernah gagal bayar?<span class="fr">*</span></div><select name="pernah_gagal_bayar" class="fs" required><option value="Tidak pernah">Tidak pernah</option><option value="Pernah (sudah lunas)">Pernah (sudah lunas)</option></select><div class="field-error">Wajib dipilih.</div></div>
                            </div>
                            <div class="note"><i class="bi bi-exclamation-triangle me-1"></i> Riwayat kredit akan diverifikasi admin melalui SLIK OJK.</div>

                            <hr class="divider">
                            <div class="sub-title">Aset yang dimiliki <span class="tag-optional">Opsional</span></div>
                            <div class="fg">
                                <div><div class="fl">Aset properti lain</div><input type="text" name="aset_properti" class="fi" placeholder="Nilai estimasi"></div>
                                <div><div class="fl">Kendaraan bermotor</div><input type="text" name="aset_kendaraan" class="fi" placeholder="Nilai estimasi"></div>
                                <div><div class="fl">Tabungan / deposito</div><input type="text" name="aset_tabungan" class="fi" placeholder="Total saldo"></div>
                                <div><div class="fl">Aset lainnya</div><input type="text" name="aset_lainnya" class="fi" placeholder="Investasi, logam mulia, dll"></div>
                            </div>
                        </div>
                    </div>
                    <div class="wizard-actions">
                        <button type="button" class="btn-wizard btn-prev prev-step"><i class="bi bi-arrow-left"></i> Sebelumnya</button>
                        <button type="button" class="btn-wizard btn-next next-step">Selanjutnya <i class="bi bi-arrow-right"></i></button>
                    </div>
                </div>

                <div class="form-step" data-step-title="Properti yang diajukan">
                    <div class="fc">
                        <div class="sh">
                            <div class="sb s-amber"><i class="bi bi-house"></i></div>
                            <div class="st">4. Data properti yang diajukan <span class="tag-required">Wajib</span><small>Unit, harga, DP, tenor, dan estimasi angsuran.</small></div>
                        </div>
                        <div class="sb2">
                            <div class="fg">
                                <div>
                                    <div class="fl">Nama proyek / cluster<span class="fr">*</span></div>
                                   <select name="id_properti" class="fs" id="pilih_properti" required>
                                        <option value="">Pilih proyek tersedia...</option>

                                        @foreach($properti ?? [] as $item)
                                            <option 
                                                value="{{ $item->id }}"
                                                data-harga="{{ $item->harga }}"
                                                data-luas_tanah="{{ $item->luas_tanah }}"
                                                data-luas_bangunan="{{ $item->luas_bangunan }}"
                                                data-tipe="{{ $item->tipe }}"
                                            >
                                                {{ $item->nama_proyek }} - {{ $item->tipe }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="field-error">Properti wajib dipilih.</div>
                                </div>
                                <div>
                                    <div class="fl">Tipe unit<span class="fr">*</span></div>
                                    <input type="text" name="tipe_unit" class="fi" id="tipe_unit" readonly placeholder="Otomatis dari properti" required>
                                    <div class="field-error">Tipe unit wajib diisi.</div>
                                </div>
                                <div><div class="fl">Nomor unit / kavling</div><input type="text" name="nomor_unit" class="fi" placeholder="Dari daftar ketersediaan"></div>
                                <div><div class="fl">Luas tanah m²</div><input type="text" name="luas_tanah" class="fi" id="luas_tanah" readonly placeholder="Otomatis"></div>
                                <div><div class="fl">Luas bangunan m²</div><input type="text" name="luas_bangunan" class="fi" id="luas_bangunan" readonly placeholder="Otomatis"></div>
                                <div><div class="fl">Harga properti<span class="fr">*</span></div><input type="text" name="harga_properti" class="fi" id="harga_properti" readonly style="font-weight:700" placeholder="Rp 0"></div>
                                <div>
                                    <div class="fl">Uang muka / DP<span class="fr">*</span></div>
                                    <input type="number" name="dp" class="fi" id="dp" placeholder="Rp 0" required>
                                    <div class="field-error">DP wajib diisi.</div>
                                </div>
                                <div><div class="fl">% DP dari harga</div><input type="text" class="fi" id="persen_dp" readonly placeholder="Otomatis"></div>
                                <div><div class="fl">Jumlah pinjaman</div><input type="text" class="fi" id="jumlah_pinjaman" readonly style="font-weight:700" placeholder="Rp 0"></div>
                                <div>
                                    <div class="fl">Tenor KPR<span class="fr">*</span></div>
                                    <select name="tenor" class="fs" id="tenor" required><option value="5">5 tahun</option><option value="10">10 tahun</option><option value="15">15 tahun</option><option value="20">20 tahun</option><option value="25">25 tahun</option></select>
                                    <div class="field-error">Tenor wajib dipilih.</div>
                                </div>
                                <div><div class="fl">Estimasi angsuran / bulan</div><input type="text" class="fi" id="estimasi_angsuran_kpr" readonly style="font-weight:700" placeholder="Rp 0"></div>
                                <div><div class="fl">Rasio angsuran / penghasilan</div><input type="text" class="fi" id="rasio_angsuran" readonly placeholder="%"></div>
                                <div><div class="fl">Tujuan pembelian</div><select name="tujuan_pembelian" class="fs"><option value="hunian_sendiri">Hunian sendiri</option><option value="investasi">Investasi</option></select></div>
                                <div><div class="fl">Sumber DP</div><select name="sumber_dp" class="fs"><option value="Tabungan">Tabungan</option><option value="Keluarga">Keluarga</option><option value="Jual aset">Jual aset</option></select></div>
                            </div>
                            <div class="note"><i class="bi bi-calculator me-1"></i> Estimasi angsuran memakai simulasi bunga 8,5% per tahun. Rasio lebih dari 40% diberi peringatan.</div>
                        </div>
                    </div>
                    <div class="wizard-actions">
                        <button type="button" class="btn-wizard btn-prev prev-step"><i class="bi bi-arrow-left"></i> Sebelumnya</button>
                        <button type="button" class="btn-wizard btn-next next-step">Selanjutnya <i class="bi bi-arrow-right"></i></button>
                    </div>
                </div>

                <div class="form-step" data-step-title="Upload dokumen">
                    <div class="fc">
                        <div class="sh">
                            <div class="sb s-coral"><i class="bi bi-cloud-upload"></i></div>
                            <div class="st">5. Upload dokumen pendukung <span class="tag-required">Wajib</span><small>Drag & drop file atau klik area upload.</small></div>
                        </div>
                        <div class="sb2">
                            <div class="sub-title">Dokumen identitas <span class="tag-required">Wajib</span></div>
                            <div class="fg">
                                @php
                                    $uploads = [
                                        ['ktp','file_ktp','KTP','Upload KTP','JPG/PNG/PDF · maks 2MB','image/*,.pdf',true,false],
                                        ['kk','file_kk','Kartu keluarga','Upload KK','JPG/PNG/PDF · maks 2MB','image/*,.pdf',true,false],
                                        ['dokumen_npwp','file_npwp','NPWP','Upload NPWP','JPG/PNG/PDF · maks 2MB','image/*,.pdf',false,false],
                                        ['buku_nikah','file_buku_nikah','Buku nikah / akta cerai','Upload dokumen','Jika sudah menikah/cerai','image/*,.pdf',false,false],
                                        ['ktp_pasangan','file_ktp_pasangan','KTP pasangan','Upload KTP pasangan','Jika sudah menikah','image/*,.pdf',false,false],
                                        ['foto_diri','file_foto','Pas foto 3x4','Upload foto','JPG/PNG · maks 1MB','image/*',false,false],
                                    ];
                                @endphp
                                @foreach($uploads as $u)
                                    <div>
                                        <div class="fl">{{ $u[2] }} @if($u[6])<span class="fr">*</span>@endif</div>
                                        <div class="upload-box" data-input="{{ $u[1] }}">
                                            <div class="upload-icon"><i class="bi bi-cloud-upload"></i></div>
                                            <div class="upload-title">{{ $u[3] }}</div>
                                            <div class="upload-hint">{{ $u[4] }}</div>
                                            <div class="upload-preview"></div>
                                        </div>
                                        <input type="file" name="{{ $u[0] }}" id="{{ $u[1] }}" class="file-input-hidden" accept="{{ $u[5] }}" @if($u[6]) required @endif>
                                        <div class="field-error">{{ $u[2] }} wajib diupload.</div>
                                    </div>
                                @endforeach
                            </div>

                            <hr class="divider">
                            <div class="sub-title">Dokumen pekerjaan & penghasilan <span class="tag-required">Wajib</span></div>
                            <div class="fg">
                                @php
                                    $uploads2 = [
                                        ['slip_gaji[]','file_slip_gaji','Slip gaji 3 bln terakhir','Upload slip gaji','PDF · maks 5MB · 3 bulan','application/pdf',true,true],
                                        ['sk_kerja','file_sk_kerja','Surat keterangan kerja','Upload surat','PDF · maks 2MB','application/pdf',true,false],
                                        ['sk_pengangkatan','file_sk_pengangkatan','SK pengangkatan / kontrak','Upload SK','PDF · maks 2MB','application/pdf',false,false],
                                        ['spt','file_spt','SPT PPh 21 / tahunan','Upload SPT','PDF · maks 2MB','application/pdf',false,false],
                                    ];
                                @endphp
                                @foreach($uploads2 as $u)
                                    <div>
                                        <div class="fl">{{ $u[2] }} @if($u[6])<span class="fr">*</span>@endif</div>
                                        <div class="upload-box" data-input="{{ $u[1] }}">
                                            <div class="upload-icon"><i class="bi bi-cloud-upload"></i></div>
                                            <div class="upload-title">{{ $u[3] }}</div>
                                            <div class="upload-hint">{{ $u[4] }}</div>
                                            <div class="upload-preview"></div>
                                        </div>
                                        <input type="file" name="{{ $u[0] }}" id="{{ $u[1] }}" class="file-input-hidden" accept="{{ $u[5] }}" @if($u[6]) required @endif @if($u[7]) multiple @endif>
                                        <div class="field-error">{{ $u[2] }} wajib diupload.</div>
                                    </div>
                                @endforeach
                            </div>

                            <hr class="divider">
                            <div class="sub-title">Dokumen bank & keuangan <span class="tag-required">Wajib</span></div>
                            <div class="fg">
                                @php
                                    $uploads3 = [
                                        ['rekening_koran','file_rekening_koran','Rekening koran 3 bln','Upload rekening koran','PDF resmi bank · maks 10MB','application/pdf',true,false],
                                        ['slik','file_slik','Hasil SLIK OJK','Upload SLIK OJK','PDF cetak resmi OJK','application/pdf',true,false],
                                        ['tagihan_kartu_kredit[]','file_tagihan_kk','Tagihan kartu kredit','Upload tagihan','3 bln terakhir · PDF','application/pdf',false,true],
                                        ['bukti_cicilan','file_bukti_cicilan','Bukti cicilan aktif','Upload bukti cicilan','KPR/KKB/KTA aktif','application/pdf',false,false],
                                    ];
                                @endphp
                                @foreach($uploads3 as $u)
                                    <div>
                                        <div class="fl">{{ $u[2] }} @if($u[6])<span class="fr">*</span>@endif</div>
                                        <div class="upload-box" data-input="{{ $u[1] }}">
                                            <div class="upload-icon"><i class="bi bi-cloud-upload"></i></div>
                                            <div class="upload-title">{{ $u[3] }}</div>
                                            <div class="upload-hint">{{ $u[4] }}</div>
                                            <div class="upload-preview"></div>
                                        </div>
                                        <input type="file" name="{{ $u[0] }}" id="{{ $u[1] }}" class="file-input-hidden" accept="{{ $u[5] }}" @if($u[6]) required @endif @if($u[7]) multiple @endif>
                                        <div class="field-error">{{ $u[2] }} wajib diupload.</div>
                                    </div>
                                @endforeach
                            </div>

                            <hr class="divider">
                            <div class="sub-title">Dokumen wiraswasta <span class="tag-optional">Jika wiraswasta</span></div>
                            <div class="fg">
                                @php
                                    $uploads4 = [
                                        ['izin_usaha','file_izin_usaha','SIUP / NIB / TDP','Upload izin usaha','PDF · maks 2MB','application/pdf',false,false],
                                        ['laporan_keuangan','file_laporan_keuangan','Laporan keuangan usaha','Upload laporan','2 tahun terakhir · PDF','application/pdf',false,false],
                                        ['rekening_usaha','file_rekening_usaha','Rekening koran usaha','Upload rekening usaha','6 bln terakhir · PDF','application/pdf',false,false],
                                        ['sip','file_sip','Surat izin praktik','Upload SIP','Untuk tenaga profesional','application/pdf',false,false],
                                    ];
                                @endphp
                                @foreach($uploads4 as $u)
                                    <div>
                                        <div class="fl">{{ $u[2] }}</div>
                                        <div class="upload-box" data-input="{{ $u[1] }}">
                                            <div class="upload-icon"><i class="bi bi-cloud-upload"></i></div>
                                            <div class="upload-title">{{ $u[3] }}</div>
                                            <div class="upload-hint">{{ $u[4] }}</div>
                                            <div class="upload-preview"></div>
                                        </div>
                                        <input type="file" name="{{ $u[0] }}" id="{{ $u[1] }}" class="file-input-hidden" accept="{{ $u[5] }}">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="wizard-actions">
                        <button type="button" class="btn-wizard btn-prev prev-step"><i class="bi bi-arrow-left"></i> Sebelumnya</button>
                        <button type="button" class="btn-wizard btn-next next-step">Selanjutnya <i class="bi bi-arrow-right"></i></button>
                    </div>
                </div>

                <div class="form-step" data-step-title="Pernyataan & persetujuan">
                    <div class="fc">
                        <div class="sh">
                            <div class="sb s-green"><i class="bi bi-check-circle"></i></div>
                            <div class="st">6. Pernyataan & persetujuan <span class="tag-required">Wajib</span><small>Pastikan semua data sudah benar sebelum dikirim.</small></div>
                        </div>
                        <div class="sb2">
                            <div class="agreement-box">
                                <input type="checkbox" name="pernyataan1" id="pernyataan1" required>
                                <label for="pernyataan1">Saya menyatakan bahwa semua data dan dokumen yang saya sampaikan adalah benar dan dapat dipertanggungjawabkan.</label>
                            </div>
                            <div class="agreement-box">
                                <input type="checkbox" name="pernyataan2" id="pernyataan2" required>
                                <label for="pernyataan2">Saya memberikan persetujuan kepada PT Citra Pasada Properti untuk memverifikasi data saya termasuk pengecekan SLIK OJK.</label>
                            </div>
                            <div class="agreement-box">
                                <input type="checkbox" name="pernyataan3" id="pernyataan3" required>
                                <label for="pernyataan3">Saya memahami bahwa keputusan persetujuan KPR sepenuhnya menjadi kewenangan PT Citra Pasada Properti.</label>
                            </div>
                        </div>
                    </div>
                    <div class="wizard-actions">
                        <button type="button" class="btn-wizard btn-prev prev-step"><i class="bi bi-arrow-left"></i> Sebelumnya</button>
                        <div class="wizard-actions-right">
                            <button type="button" class="btn-wizard btn-draft" id="draftBtn"><i class="bi bi-save"></i> Simpan Draft</button>
                            <button type="submit" class="btn-wizard btn-submit" id="submitBtn">
                                <span class="spinner-mini"></span>
                                <span class="submit-text">Kirim Pengajuan</span>
                                <i class="bi bi-send"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <aside class="sidebar-sticky">
            <div class="wizard-side-card">
                <div class="side-title"><i class="bi bi-graph-up-arrow"></i> Progress Pengisian</div>
                <div class="side-progress-circle" id="sideProgressCircle">
                    <div class="side-progress-inner">
                        <strong id="sideProgressText">0%</strong>
                        <span>selesai</span>
                    </div>
                </div>
                <ul class="side-list" id="sideChecklist">
                    <li data-side="0"><i class="bi bi-circle"></i> Data diri</li>
                    <li data-side="1"><i class="bi bi-circle"></i> Pekerjaan</li>
                    <li data-side="2"><i class="bi bi-circle"></i> Keuangan</li>
                    <li data-side="3"><i class="bi bi-circle"></i> Properti</li>
                    <li data-side="4"><i class="bi bi-circle"></i> Dokumen</li>
                    <li data-side="5"><i class="bi bi-circle"></i> Persetujuan</li>
                </ul>
                <div class="autosave-status" id="autosaveStatus"><i class="bi bi-cloud-check"></i> Draft belum tersimpan</div>
            </div>

            <div class="wizard-side-card">
                <div class="side-title"><i class="bi bi-info-circle"></i> Persyaratan KPR</div>
                <ul class="side-list">
                    <li><i class="bi bi-check-lg"></i>KTP & KK</li>
                    <li><i class="bi bi-check-lg"></i>Slip gaji 3 bulan terakhir</li>
                    <li><i class="bi bi-check-lg"></i>Rekening koran 3 bulan</li>
                    <li><i class="bi bi-check-lg"></i>NPWP jika ada</li>
                    <li><i class="bi bi-check-lg"></i>Surat keterangan kerja</li>
                    <li><i class="bi bi-check-lg"></i>Hasil SLIK OJK</li>
                </ul>
            </div>

            <div class="wizard-side-card">
                <div class="side-title"><i class="bi bi-headset"></i> Butuh Bantuan?</div>
                <div class="d-grid gap-2">
                    <a href="#" class="btn btn-sm btn-outline-success"><i class="bi bi-whatsapp"></i> WhatsApp</a>
                    <a href="#" class="btn btn-sm btn-outline-primary"><i class="bi bi-telephone"></i> Call Center</a>
                </div>
            </div>
        </aside>
    </div>
</form>
@endsection


@push('scripts')
<script>
(function () {
    const form = document.getElementById('kprForm');
    const formSteps = document.querySelectorAll('.form-step');
    const stepTabs = document.querySelectorAll('.step-tab');
    const nextBtns = document.querySelectorAll('.next-step');
    const prevBtns = document.querySelectorAll('.prev-step');
    const progressFill = document.getElementById('progressFill');
    const progressPercent = document.getElementById('progressPercent');
    const progressStepLabel = document.getElementById('progressStepLabel');
    const sideProgressCircle = document.getElementById('sideProgressCircle');
    const sideProgressText = document.getElementById('sideProgressText');
    const autosaveStatus = document.getElementById('autosaveStatus');
    const draftHeaderStatus = document.getElementById('draftHeaderStatus');
    const submitBtn = document.getElementById('submitBtn');
    const draftKey = 'kpr_pengajuan_draft_{{ Auth::id() ?? "guest" }}';

    let currentStep = 0;
    let autosaveTimer = null;
    let toastContainer = null;

    // ==================== MODERN TOAST NOTIFICATION ====================
    function createToastContainer() {
        if (toastContainer) return toastContainer;
        
        const container = document.createElement('div');
        container.id = 'modern-toast-container';
        container.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 10000;
            display: flex;
            flex-direction: column;
            gap: 12px;
            max-width: 380px;
            pointer-events: none;
        `;
        document.body.appendChild(container);
        toastContainer = container;
        return container;
    }

    function showToast(message, type = 'success', duration = 4000) {
        const container = createToastContainer();
        
        const toast = document.createElement('div');
        toast.style.cssText = `
            background: white;
            border-radius: 16px;
            padding: 16px 20px;
            display: flex;
            align-items: center;
            gap: 14px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.12), 0 4px 12px rgba(0,0,0,0.06);
            transform: translateX(400px);
            transition: transform 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            pointer-events: auto;
            cursor: pointer;
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255,255,255,0.3);
        `;
        
        const icons = {
            success: `<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="12" cy="12" r="10" fill="#10B981" stroke="white" stroke-width="1.5"/>
                <path d="M8 12L11 15L16 9" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>`,
            error: `<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="12" cy="12" r="10" fill="#EF4444" stroke="white" stroke-width="1.5"/>
                <path d="M12 8V12M12 16H12.01" stroke="white" stroke-width="2" stroke-linecap="round"/>
            </svg>`,
            warning: `<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 9V13M12 17H12.01" stroke="#F59E0B" stroke-width="2" stroke-linecap="round"/>
                <circle cx="12" cy="12" r="10" stroke="#F59E0B" stroke-width="1.5"/>
            </svg>`,
            info: `<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="12" cy="12" r="10" fill="#3B82F6" stroke="white" stroke-width="1.5"/>
                <path d="M12 12V16M12 8H12.01" stroke="white" stroke-width="2" stroke-linecap="round"/>
            </svg>`
        };
        
        const colors = {
            success: 'linear-gradient(135deg, #10B981 0%, #059669 100%)',
            error: 'linear-gradient(135deg, #EF4444 0%, #DC2626 100%)',
            warning: 'linear-gradient(135deg, #F59E0B 0%, #D97706 100%)',
            info: 'linear-gradient(135deg, #3B82F6 0%, #2563EB 100%)'
        };
        
        toast.innerHTML = `
            <div style="flex-shrink:0;">${icons[type] || icons.success}</div>
            <div style="flex:1; font-size: 14px; color: #1F2937; font-weight: 500; line-height: 1.4;">${message}</div>
            <div style="flex-shrink:0; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; opacity: 0.5;">
                <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                    <path d="M13 1L1 13M1 1L13 13" stroke="#6B7280" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
            </div>
        `;
        
        const gradientBar = document.createElement('div');
        gradientBar.style.cssText = `
            position: absolute;
            bottom: 0;
            left: 0;
            height: 3px;
            background: ${colors[type]};
            border-radius: 0 0 16px 16px;
            width: 100%;
            animation: toastProgress ${duration}ms linear forwards;
        `;
        
        toast.style.position = 'relative';
        toast.style.overflow = 'hidden';
        toast.appendChild(gradientBar);
        
        const style = document.createElement('style');
        style.textContent = `
            @keyframes toastProgress {
                0% { width: 100%; }
                100% { width: 0%; }
            }
        `;
        document.head.appendChild(style);
        
        toast.onclick = () => closeToast(toast);
        
        container.appendChild(toast);
        
        setTimeout(() => {
            toast.style.transform = 'translateX(0)';
        }, 10);
        
        setTimeout(() => {
            closeToast(toast);
        }, duration);
        
        return toast;
    }
    
    function closeToast(toast) {
        toast.style.transform = 'translateX(400px)';
        setTimeout(() => {
            if (toast.parentNode) toast.parentNode.removeChild(toast);
        }, 300);
    }

    // ==================== UTILITY FUNCTIONS ====================
    const rupiah = (value) => 'Rp ' + (parseInt(value) || 0).toLocaleString('id-ID');
    const numberOnly = (value) => parseInt(String(value || '').replace(/[^0-9]/g, '')) || 0;

    function getFieldValue(field) {
        if (field.type === 'checkbox') return field.checked ? '1' : '';
        if (field.type === 'radio') return field.checked ? field.value : null;
        if (field.type === 'file') return field.files;
        return field.value;
    }

    function saveDraft(sectionIndex = currentStep) {
        const data = {};
        form.querySelectorAll('input, select, textarea').forEach(field => {
            if (!field.name || field.type === 'file') return;
            const value = getFieldValue(field);
            if (value !== null) data[field.name] = value;
        });

        localStorage.setItem(draftKey, JSON.stringify({
            data,
            sectionIndex,
            updatedAt: new Date().toISOString()
        }));

        const time = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
        if (autosaveStatus) autosaveStatus.innerHTML = `<i class="bi bi-cloud-check"></i> Draft tersimpan otomatis ${time}`;
        if (draftHeaderStatus) draftHeaderStatus.innerHTML = `<i class="bi bi-save"></i> Draft tersimpan ${time}`;
    }

    function loadDraft() {
        const raw = localStorage.getItem(draftKey);
        if (!raw) return;

        try {
            const draft = JSON.parse(raw);
            Object.entries(draft.data || {}).forEach(([name, value]) => {
                const fields = form.querySelectorAll(`[name="${CSS.escape(name)}"]`);
                fields.forEach(field => setFieldValue(field, value));
            });

            currentStep = Math.min(parseInt(draft.sectionIndex || 0), formSteps.length - 1);
            if (autosaveStatus) autosaveStatus.innerHTML = `<i class="bi bi-cloud-check"></i> Draft terakhir dimuat`;
            if (draftHeaderStatus) draftHeaderStatus.innerHTML = `<i class="bi bi-save"></i> Draft ditemukan`;
        } catch (e) {
            console.warn('Draft tidak bisa dimuat:', e);
        }
    }

    function setFieldValue(field, value) {
        if (field.type === 'checkbox') field.checked = value === '1' || value === true;
        else if (field.type !== 'file') field.value = value ?? '';
    }

    function scheduleAutosave() {
        clearTimeout(autosaveTimer);
        if (autosaveStatus) autosaveStatus.innerHTML = `<i class="bi bi-arrow-repeat"></i> Menyimpan draft...`;
        autosaveTimer = setTimeout(() => saveDraft(currentStep), 550);
    }

    function validateField(field, showError = true) {
        if (field.disabled || field.readOnly && !field.required) return true;

        const wrapper = field.closest('div');
        const error = wrapper ? wrapper.querySelector('.field-error') : null;
        let valid = true;

        if (field.required) {
            if (field.type === 'checkbox') valid = field.checked;
            else if (field.type === 'file') valid = field.files && field.files.length > 0;
            else valid = String(field.value || '').trim() !== '';
        }

        if (valid && field.dataset.minlength) {
            valid = String(field.value || '').trim().length >= parseInt(field.dataset.minlength);
        }

        if (valid && field.type === 'email' && field.value) {
            valid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(field.value);
        }

        if (!showError) return valid;

        if (!valid) {
            field.classList.add('is-invalid-field');
            field.classList.remove('is-valid-field');
            if (error) error.style.display = 'block';
        } else {
            field.classList.remove('is-invalid-field');
            if (field.required && field.type !== 'file') field.classList.add('is-valid-field');
            if (error) error.style.display = 'none';
        }

        return valid;
    }

    function validateStep(index, showError = true) {
        const step = formSteps[index];
        const requiredFields = step.querySelectorAll('[required]');
        let valid = true;
        let firstInvalid = null;

        requiredFields.forEach(field => {
            if (!validateField(field, showError)) {
                valid = false;
                if (!firstInvalid) firstInvalid = field;
            }
        });

        if (!valid && firstInvalid && showError) {
            firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
            setTimeout(() => firstInvalid.focus({ preventScroll: true }), 350);
        }

        return valid;
    }

    function updateProgress() {
        let completed = 0;
        formSteps.forEach((_, i) => {
            if (validateStep(i, false)) completed++;
        });

        const percent = Math.round((completed / formSteps.length) * 100);
        if (progressFill) progressFill.style.width = percent + '%';
        if (progressPercent) progressPercent.textContent = percent + '%';
        if (sideProgressText) sideProgressText.textContent = percent + '%';
        if (sideProgressCircle) sideProgressCircle.style.background = `conic-gradient(var(--kpr-primary) ${percent * 3.6}deg, #E8EEF5 0deg)`;
        if (progressStepLabel) progressStepLabel.textContent = `Section ${currentStep + 1} dari ${formSteps.length} · ${formSteps[currentStep].dataset.stepTitle}`;

        document.querySelectorAll('#sideChecklist li').forEach((li, i) => {
            const icon = li.querySelector('i');
            const ok = validateStep(i, false);
            icon.className = ok ? 'bi bi-check-circle-fill' : (i === currentStep ? 'bi bi-record-circle' : 'bi bi-circle');
            li.style.color = ok ? 'var(--kpr-success)' : (i === currentStep ? 'var(--kpr-primary)' : '#4B5563');
            li.style.fontWeight = i === currentStep ? '700' : '400';
        });

        stepTabs.forEach((tab, i) => {
            tab.classList.remove('active', 'completed');
            const num = tab.querySelector('.step-number');
            if (validateStep(i, false)) {
                tab.classList.add('completed');
                if (num) num.innerHTML = '<i class="bi bi-check-lg"></i>';
            } else {
                if (num) num.textContent = i + 1;
            }
            if (i === currentStep) tab.classList.add('active');
        });
    }

    function showStep(index) {
        currentStep = index;
        formSteps.forEach((step, i) => step.classList.toggle('active', i === index));
        updateProgress();
        saveDraft(index);
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function hitungTotalPenghasilan() {
        const pokok = numberOnly(document.getElementById('penghasilan_pokok')?.value);
        const tunjangan = numberOnly(document.getElementById('tunjangan')?.value);
        const lain = numberOnly(document.getElementById('penghasilan_lain')?.value);
        const total = pokok + tunjangan + lain;
        const target = document.getElementById('total_penghasilan');
        if (target) target.value = rupiah(total);
        return total;
    }

    function calculateKPR() {
        const harga = numberOnly(document.getElementById('harga_properti')?.value);
        const dp = numberOnly(document.getElementById('dp')?.value);
        const tenor = numberOnly(document.getElementById('tenor')?.value) || 20;
        const plafon = Math.max(harga - dp, 0);
        const persenDp = harga > 0 ? (dp / harga * 100).toFixed(1) : '0.0';

        const persenDpEl = document.getElementById('persen_dp');
        if (persenDpEl) persenDpEl.value = persenDp + '%';
        
        const jumlahPinjamanEl = document.getElementById('jumlah_pinjaman');
        if (jumlahPinjamanEl) jumlahPinjamanEl.value = rupiah(plafon);

        const bungaPerBulan = 0.085 / 12;
        const bulan = tenor * 12;
        let cicilan = 0;
        if (plafon > 0) {
            cicilan = plafon * bungaPerBulan * Math.pow(1 + bungaPerBulan, bulan) / (Math.pow(1 + bungaPerBulan, bulan) - 1);
        }

        const estimasiAngsuranEl = document.getElementById('estimasi_angsuran_kpr');
        if (estimasiAngsuranEl) estimasiAngsuranEl.value = rupiah(Math.round(cicilan));

        const totalPenghasilan = hitungTotalPenghasilan();
        const totalCicilan = numberOnly(document.getElementById('total_cicilan')?.value);
        const rasio = totalPenghasilan > 0 ? ((totalCicilan + cicilan) / totalPenghasilan * 100).toFixed(1) : '0.0';
        const rasioEl = document.getElementById('rasio_angsuran');
        if (rasioEl) {
            rasioEl.value = rasio + '% (maks 40%)';
            rasioEl.style.color = rasio > 40 ? '#E24B4A' : (rasio > 30 ? '#F59E0B' : '#0F7B4F');
        }

        const minDp = Math.round(harga * 0.1);
        const dpEl = document.getElementById('dp');
        if (dpEl && harga > 0) {
            dpEl.setAttribute('min', minDp);
            dpEl.placeholder = `Minimal ${rupiah(minDp)}`;
        }
    }

    function setupProperty() {
        const select = document.getElementById('pilih_properti');
        if (!select) return;

        select.addEventListener('change', function () {
            const selected = this.options[this.selectedIndex];
            const harga = selected.getAttribute('data-harga') || 0;
            const luasTanah = selected.getAttribute('data-luas_tanah') || '';
            const luasBangunan = selected.getAttribute('data-luas_bangunan') || '';
            const tipe = selected.getAttribute('data-tipe') || selected.textContent.split('-').pop()?.trim() || '';

            const hargaPropertiEl = document.getElementById('harga_properti');
            if (hargaPropertiEl) hargaPropertiEl.value = harga ? rupiah(harga) : '';
            
            const luasTanahEl = document.getElementById('luas_tanah');
            if (luasTanahEl) luasTanahEl.value = luasTanah;
            
            const luasBangunanEl = document.getElementById('luas_bangunan');
            if (luasBangunanEl) luasBangunanEl.value = luasBangunan;
            
            const tipeUnitEl = document.getElementById('tipe_unit');
            if (tipeUnitEl) tipeUnitEl.value = tipe;
            
            calculateKPR();
            if (tipeUnitEl) validateField(tipeUnitEl);
            scheduleAutosave();
            updateProgress();
        });
    }

    function readableSize(bytes) {
        if (!bytes) return '0 KB';
        if (bytes < 1024 * 1024) return Math.round(bytes / 1024) + ' KB';
        return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
    }

    function renderPreview(input) {
        const box = document.querySelector(`.upload-box[data-input="${input.id}"]`);
        if (!box) return;

        const preview = box.querySelector('.upload-preview');
        if (!preview) return;
        
        preview.innerHTML = '';

        if (!input.files || input.files.length === 0) {
            box.classList.remove('uploaded');
            preview.style.display = 'none';
            return;
        }

        Array.from(input.files).slice(0, 3).forEach(file => {
            const item = document.createElement('div');
            item.className = 'preview-item';

            const thumb = document.createElement(file.type.startsWith('image/') ? 'img' : 'div');
            thumb.className = 'preview-thumb';

            if (file.type.startsWith('image/')) {
                thumb.src = URL.createObjectURL(file);
            } else {
                thumb.innerHTML = '<i class="bi bi-file-earmark-pdf"></i>';
            }

            item.appendChild(thumb);
            item.insertAdjacentHTML('beforeend', `<div style="min-width:0;flex:1"><div class="preview-name">${file.name}</div><div class="preview-size">${readableSize(file.size)}</div></div>`);
            preview.appendChild(item);
        });

        if (input.files.length > 3) {
            preview.insertAdjacentHTML('beforeend', `<div class="preview-size">+${input.files.length - 3} file lainnya</div>`);
        }

        preview.style.display = 'flex';
        box.classList.add('uploaded');
    }

    function setupUploads() {
        document.querySelectorAll('.upload-box').forEach(box => {
            const input = document.getElementById(box.dataset.input);
            if (!input) return;

            box.addEventListener('click', () => input.click());

            box.addEventListener('dragover', e => {
                e.preventDefault();
                box.classList.add('dragover');
            });

            box.addEventListener('dragleave', () => box.classList.remove('dragover'));

            box.addEventListener('drop', e => {
                e.preventDefault();
                box.classList.remove('dragover');
                input.files = e.dataTransfer.files;
                renderPreview(input);
                validateField(input);
                scheduleAutosave();
                updateProgress();
            });

            input.addEventListener('change', () => {
                renderPreview(input);
                validateField(input);
                updateProgress();
                scheduleAutosave();
            });
        });
    }

    // ==================== DEBUG AUTO FILL (Development Only) ====================
    function debugAutoFill() {
        const debugData = {
            nama_lengkap: 'Budi Santoso Debug',
            nik: '1234567890123456',
            tempat_lahir: 'Jakarta',
            tanggal_lahir: '1990-01-01',
            jenis_kelamin: 'Laki-laki',
            agama: 'Islam',
            status_pernikahan: 'sudah_menikah',
            jumlah_tanggungan: '2',
            pendidikan: 'S1',
            kewarganegaraan: 'WNI',
            alamat_ktp: 'Jl. Debug No. 123, RT 01/RW 02, Kelurahan Debug, Kecamatan Test',
            kota: 'Jakarta Selatan',
            provinsi: 'DKI Jakarta',
            kode_pos: '12345',
            status_tempat_tinggal: 'Milik sendiri',
            no_hp: '081234567890',
            email: 'debug@example.com',
            no_kk: '1234567890123456',
            nama_ibu: 'Ibu Debug',
            nama_pasangan: 'Pasangan Debug',
            nik_pasangan: '1234567890123457',
            status_pekerjaan: 'Karyawan',
            nama_perusahaan: 'PT Debug Solution',
            bidang_usaha: 'Teknologi Informasi',
            jabatan: 'Senior Developer',
            lama_bekerja: '>5 th',
            status_kepegawaian: 'Tetap',
            alamat_perusahaan: 'Jl. Teknologi No. 45, Jakarta',
            telp_perusahaan: '0211234567',
            npwp: '1234567890123456',
            penghasilan_pokok: '15000000',
            tunjangan: '2000000',
            penghasilan_lain: '1000000',
            nama_bank: 'BCA',
            nomor_rekening: '1234567890',
            pemilik_rekening: 'Budi Santoso',
            jenis_rekening: 'tabungan',
            rata_saldo: '50000000',
            rata_mutasi: '15000000',
            total_cicilan: '5000000',
            jumlah_kredit_aktif: '1',
            limit_kartu_kredit: '20000000',
            tagihan_kartu_kredit: '0',
            memiliki_kpr_aktif: 'Tidak',
            sisa_pokok_kpr: '0',
            status_kredit: 'Lancar',
            pernah_gagal_bayar: 'Tidak pernah',
            aset_properti: '500000000',
            aset_kendaraan: '250000000',
            aset_tabungan: '100000000',
            aset_lainnya: '50000000',
            id_properti: '7',
            tipe_unit: 'Tipe 45/90',
            nomor_unit: 'B-12J',
            luas_tanah: '90',
            luas_bangunan: '45',
            harga_properti: '615000000',
            dp: '100000000',
            tenor: '15',
            tujuan_pembelian: 'hunian_sendiri',
            sumber_dp: 'Tabungan',
            pernyataan1: 'on',
            pernyataan2: 'on',
            pernyataan3: 'on'
        };
        
        Object.entries(debugData).forEach(([name, value]) => {
            const field = form.querySelector(`[name="${name}"]`);
            if (field && field.type !== 'file') {
                if (field.type === 'checkbox') {
                    field.checked = value === 'on' || value === true;
                } else if (field.type === 'radio') {
                    const radio = form.querySelector(`[name="${name}"][value="${value}"]`);
                    if (radio) radio.checked = true;
                } else {
                    field.value = value;
                    field.dispatchEvent(new Event('input', { bubbles: true }));
                    field.dispatchEvent(new Event('change', { bubbles: true }));
                }
            }
        });
        
        hitungTotalPenghasilan();
        calculateKPR();
        updateProgress();
        
        showToast('Form berisi dengan data debug! Silakan upload file yang diperlukan.', 'success', 3000);
    }

    function addDebugButton() {
        if (window.location.hostname === '127.0.0.1' || window.location.hostname === 'localhost') {
            const debugBtn = document.createElement('button');
            debugBtn.id = 'debugAutoFillBtn';
            debugBtn.innerHTML = `
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin-right: 6px;">
                    <path d="M12 8V12M12 16H12.01M12 4C8.13401 4 5 7.13401 5 11C5 13.0353 5.84836 14.8839 7.19125 16.2268L7.19125 16.2268C8.53414 17.5696 10.3827 18.418 12.418 18.418C14.4533 18.418 16.3019 17.5696 17.6447 16.2268C18.9876 14.8839 19.836 13.0353 19.836 11C19.836 7.13401 16.702 4 12.836 4H12Z" stroke="white" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
                Debug Auto Fill
            `;
            debugBtn.style.cssText = `
                position: fixed;
                bottom: 20px;
                right: 20px;
                z-index: 9999;
                background: linear-gradient(135deg, #6366F1 0%, #4F46E5 100%);
                color: white;
                border: none;
                padding: 10px 18px;
                border-radius: 40px;
                cursor: pointer;
                font-size: 13px;
                font-weight: 600;
                box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
                transition: all 0.3s ease;
                display: flex;
                align-items: center;
                font-family: inherit;
            `;
            
            debugBtn.onmouseover = () => {
                debugBtn.style.transform = 'scale(1.05)';
                debugBtn.style.boxShadow = '0 6px 20px rgba(99, 102, 241, 0.4)';
            };
            debugBtn.onmouseout = () => {
                debugBtn.style.transform = 'scale(1)';
                debugBtn.style.boxShadow = '0 4px 15px rgba(99, 102, 241, 0.3)';
            };
            debugBtn.onclick = debugAutoFill;
            
            document.body.appendChild(debugBtn);
        }
    }

    // ==================== EVENT LISTENERS ====================
    nextBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            if (!validateStep(currentStep, true)) {
                showToast('Mohon lengkapi data wajib pada halaman ini', 'warning', 3000);
                return;
            }
            if (currentStep < formSteps.length - 1) showStep(currentStep + 1);
        });
    });

    prevBtns.forEach(btn => btn.addEventListener('click', () => {
        if (currentStep > 0) showStep(currentStep - 1);
    }));

    stepTabs.forEach((tab, index) => {
        tab.addEventListener('click', () => {
            if (index <= currentStep || validateStep(currentStep, true)) showStep(index);
        });
    });

    if (form) {
        form.querySelectorAll('input, select, textarea').forEach(field => {
            field.addEventListener('input', () => {
                validateField(field);
                hitungTotalPenghasilan();
                calculateKPR();
                updateProgress();
                scheduleAutosave();
            });
            field.addEventListener('change', () => {
                validateField(field);
                hitungTotalPenghasilan();
                calculateKPR();
                updateProgress();
                scheduleAutosave();
            });
            field.addEventListener('blur', () => validateField(field));
        });
    }

    document.getElementById('draftBtn')?.addEventListener('click', () => {
        saveDraft(currentStep);
        showToast('Draft berhasil disimpan', 'success', 2000);
    });

    // ==================== SUBMIT HANDLER ====================
    if (submitBtn) {
        const newSubmitBtn = submitBtn.cloneNode(true);
        submitBtn.parentNode.replaceChild(newSubmitBtn, submitBtn);
        const finalSubmitBtn = document.getElementById('submitBtn');
        
        finalSubmitBtn.addEventListener('click', async function(e) {
            e.preventDefault();
            
            // Validasi semua step
            let allValid = true;
            let firstInvalidStep = 0;
            
            formSteps.forEach((step, i) => {
                if (!validateStep(i, false) && allValid) {
                    allValid = false;
                    firstInvalidStep = i;
                }
            });
            
            if (!allValid) {
                showStep(firstInvalidStep);
                setTimeout(() => validateStep(firstInvalidStep, true), 350);
                showToast('Masih ada data wajib yang belum lengkap', 'warning', 3000);
                return;
            }
            
            // Loading state
            finalSubmitBtn.classList.add('loading');
            finalSubmitBtn.disabled = true;
            const submitText = finalSubmitBtn.querySelector('.submit-text');
            if (submitText) submitText.textContent = 'Mengirim...';
            
            // Hapus draft
            localStorage.removeItem(draftKey);
            
            // Kumpulkan data
            const formData = new FormData(form);
            
            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    }
                });
                
                const contentType = response.headers.get('content-type');
                let responseData;
                
                if (contentType && contentType.includes('application/json')) {
                    responseData = await response.json();
                } else {
                    const text = await response.text();
                    responseData = { message: text };
                }
                
                if (response.ok) {
                    showToast('Pengajuan KPR berhasil dikirim!', 'success', 4000);
                    
                    if (responseData.redirect_url) {
                        setTimeout(() => {
                            window.location.href = responseData.redirect_url;
                        }, 1500);
                    } else if (response.redirected) {
                        setTimeout(() => {
                            window.location.href = response.url;
                        }, 1500);
                    } else {
                        setTimeout(() => {
                            window.location.href = '{{ route("debitur.dashboard") }}';
                        }, 1500);
                    }
                } else {
                    finalSubmitBtn.classList.remove('loading');
                    finalSubmitBtn.disabled = false;
                    if (submitText) submitText.textContent = 'Kirim Pengajuan';
                    
                    if (responseData && responseData.errors) {
                        let errorMsg = 'Validasi gagal';
                        const firstError = Object.values(responseData.errors)[0];
                        if (firstError && firstError[0]) {
                            errorMsg = firstError[0];
                        }
                        showToast(errorMsg, 'error', 4000);
                    } else {
                        showToast(`Submit gagal: ${response.status}`, 'error', 4000);
                    }
                }
                
            } catch (error) {
                finalSubmitBtn.classList.remove('loading');
                finalSubmitBtn.disabled = false;
                if (submitText) submitText.textContent = 'Kirim Pengajuan';
                showToast('Terjadi kesalahan jaringan. Silakan coba lagi.', 'error', 4000);
            }
        });
    }

    // ==================== INITIALIZATION ====================
    loadDraft();
    setupProperty();
    setupUploads();
    hitungTotalPenghasilan();
    calculateKPR();
    showStep(currentStep);
    addDebugButton();
    
    // Tampilkan selamat datang
    setTimeout(() => {
        showToast('Selamat datang! Silakan isi data pengajuan KPR Anda', 'info', 4000);
    }, 500);
})();
</script>
@endpush
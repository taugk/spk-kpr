@extends('marketing.layouts.app')

@section('title', 'Verifikasi Dokumen KPR')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
:root {
    --ink: #0f172a;
    --ink-2: #334155;
    --ink-3: #64748b;
    --ink-4: #94a3b8;
    --surface: #ffffff;
    --surface-2: #f8fafc;
    --surface-3: #f1f5f9;
    --border: #e2e8f0;
    --border-2: #cbd5e1;

    --brand: #4361ee;
    --brand-2: #3451d1;
    --brand-3: #2a3fb8;
    --brand-light: #e8edfe;
    --brand-pale: #f0f3ff;

    --gold: #b45309;
    --gold-light: #fef3c7;
    --ok: #15803d;
    --ok-light: #dcfce7;
    --warn: #b45309;
    --warn-light: #fde68a;
    --err: #b91c1c;
    --err-light: #fee2e2;
    --radius-sm: 6px;
    --radius: 10px;
    --radius-lg: 14px;
    --shadow-sm: 0 1px 2px rgba(0,0,0,.05);
    --shadow: 0 1px 3px rgba(0,0,0,.07);
    --shadow-md: 0 4px 16px rgba(67,97,238,.15);
}

* { box-sizing: border-box; }
body {
    background: var(--surface-2) !important;
    font-family: 'DM Sans', sans-serif !important;
    color: var(--ink) !important;
    font-size: 14px;
    line-height: 1.5;
}

.vd-wrap { padding: 24px 20px 40px; max-width: 1400px; margin: 0 auto; }

/* Header */
.vd-page-header {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 14px;
    margin-bottom: 28px;
}
.vd-breadcrumb {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    color: var(--ink-3);
    margin-bottom: 6px;
}
.vd-breadcrumb a { color: var(--brand); text-decoration: none; }
.vd-breadcrumb a:hover { text-decoration: underline; color: var(--brand-3); }
.vd-page-title {
    font-size: 1.35rem;
    font-weight: 700;
    color: var(--ink);
    margin: 0 0 3px;
}
.vd-page-sub { font-size: 13px; color: var(--ink-3); margin: 0; }
.btn-back-home {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    font-size: 13px;
    font-weight: 600;
    color: var(--ink-2);
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 8px 16px;
    text-decoration: none;
    transition: all .18s;
}
.btn-back-home:hover {
    border-color: var(--brand);
    color: var(--brand);
    text-decoration: none;
}

/* KPI Cards */
.kpi-strip {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 14px;
    margin-bottom: 24px;
}
@media (max-width: 900px) { .kpi-strip { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 540px) { .kpi-strip { grid-template-columns: 1fr; } }

.kpi-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    padding: 18px 20px;
    position: relative;
    overflow: hidden;
    transition: all .2s;
}
.kpi-card:hover { transform: translateY(-2px); box-shadow: var(--shadow-md); }
.kpi-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
}
.kpi-card.k-total::before  { background: var(--brand); }
.kpi-card.k-pending::before{ background: var(--warn); }
.kpi-card.k-done::before   { background: var(--ok); }
.kpi-card.k-avg::before    { background: var(--gold); }

.kpi-top { display: flex; justify-content: space-between; margin-bottom: 10px; }
.kpi-icon {
    width: 40px; height: 40px; border-radius: var(--radius);
    display: flex; align-items: center; justify-content: center;
    font-size: 20px;
}
.kpi-icon.i-brand  { background: var(--brand-pale); color: var(--brand); }
.kpi-icon.i-warn   { background: #fffbeb; color: var(--warn); }
.kpi-icon.i-ok     { background: #f0fdf4; color: var(--ok); }
.kpi-icon.i-gold   { background: #fffbeb; color: var(--gold); }
.kpi-num {
    font-size: 2rem;
    font-weight: 700;
    line-height: 1;
    font-family: 'DM Mono', monospace;
}
.kpi-label { font-size: 11px; font-weight: 600; text-transform: uppercase; color: var(--ink-3); margin-bottom: 4px; }
.kpi-foot  { font-size: 12px; color: var(--ink-4); margin: 0; }

/* Filter Card */
.filter-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    padding: 16px 20px;
    margin-bottom: 20px;
}
.filter-label {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    color: var(--ink-3);
    display: block;
    margin-bottom: 6px;
}
.filter-input {
    width: 100%;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 8px 12px;
    font-size: 13px;
    background: var(--surface-2);
    height: 38px;
    transition: all .2s;
}
.filter-input:focus {
    outline: none;
    border-color: var(--brand);
    box-shadow: 0 0 0 3px var(--brand-pale);
}
.btn-filter-apply {
    height: 38px;
    padding: 0 18px;
    background: var(--brand);
    color: #fff;
    border: none;
    border-radius: var(--radius);
    font-weight: 600;
    cursor: pointer;
    transition: all .2s;
}
.btn-filter-apply:hover {
    background: var(--brand-2);
    transform: translateY(-1px);
    box-shadow: var(--shadow-md);
}
.btn-filter-reset {
    height: 38px;
    padding: 0 14px;
    background: var(--surface-3);
    color: var(--ink-3);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    transition: all .2s;
}
.btn-filter-reset:hover {
    background: var(--border);
    color: var(--ink-2);
    border-color: var(--brand-light);
}

/* Table Card */
.table-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    overflow: hidden;
    margin-bottom: 20px;
}
.table-card-header {
    padding: 16px 20px;
    border-bottom: 1px solid var(--border);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
}
.table-card-title {
    font-size: 14px;
    font-weight: 700;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
}
.count-chip {
    font-size: 11px;
    font-weight: 700;
    padding: 2px 9px;
    border-radius: 20px;
    background: var(--brand-pale);
    color: var(--brand);
}

.vd-table { width: 100%; font-size: 13px; border-collapse: collapse; }
.vd-table thead th {
    font-size: 10.5px;
    font-weight: 700;
    text-transform: uppercase;
    color: var(--ink-3);
    background: var(--surface-2);
    border-bottom: 1px solid var(--border);
    padding: 12px 14px;
}
.vd-table tbody td {
    padding: 14px;
    border-bottom: 1px solid var(--border);
    vertical-align: middle;
}
.vd-table tbody tr:hover td { background: var(--brand-pale); }

.kode-pengajuan { font-weight: 700; color: var(--brand); font-family: 'DM Mono', monospace; font-size: 12px; }
.deb-avatar {
    width: 34px; height: 34px; border-radius: 8px;
    background: var(--brand-light); color: var(--brand);
    font-weight: 700; display: flex;
    align-items: center; justify-content: center;
}
.deb-name { font-weight: 600; color: var(--ink); font-size: 13px; }
.deb-meta { font-size: 11px; color: var(--ink-3); margin-top: 2px; }

/* Progress Bar */
.verif-prog-label {
    display: flex;
    justify-content: space-between;
    font-size: 11px;
    margin-bottom: 4px;
}
.verif-prog-bar {
    height: 5px;
    border-radius: 3px;
    background: var(--surface-3);
    overflow: hidden;
    margin-bottom: 8px;
}
.verif-prog-fill { height: 100%; border-radius: 3px; transition: width .3s ease; }
.fill-ok { background: var(--ok); }
.fill-warn { background: var(--warn); }
.fill-err { background: var(--err); }

/* Document Pills */
.dok-pills { display: flex; flex-wrap: wrap; gap: 4px; margin-top: 6px; }
.dok-pill {
    display: inline-flex; align-items: center; gap: 3px;
    font-size: 10px; font-weight: 600;
    padding: 2px 6px; border-radius: 4px;
}
.dp-ok { background: #f0fdf4; color: var(--ok); }
.dp-err { background: #fef2f2; color: var(--err); }
.dp-pending { background: var(--surface-3); color: var(--ink-4); }

/* Recommendation Badge */
.rek-badge {
    display: inline-flex; align-items: center; gap: 4px;
    font-size: 10px; font-weight: 700;
    padding: 2px 8px; border-radius: 4px;
    margin-top: 5px;
}
.rek-layak { background: #f0fdf4; color: var(--ok); }
.rek-perlu { background: #fffbeb; color: var(--warn); }
.rek-tidak { background: #fef2f2; color: var(--err); }

/* Date */
.date-main {
    font-size: 12px;
    font-weight: 600;
    font-family: 'DM Mono', monospace;
}
.date-time { font-size: 10px; color: var(--ink-4); margin-top: 2px; }
.date-diff {
    font-size: 11px;
    margin-top: 4px;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 3px;
    padding: 2px 6px;
    border-radius: 4px;
    background: rgba(21, 128, 61, 0.1);
    color: var(--ok);
}
.date-diff.late {
    background: rgba(180, 83, 9, 0.1);
    color: var(--warn);
}
.date-diff.future {
    background: rgba(67, 97, 238, 0.1);
    color: var(--brand);
}

/* Action Buttons */
.action-buttons {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.btn-vd {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    font-size: 11px;
    font-weight: 600;
    padding: 6px 12px;
    border-radius: var(--radius-sm);
    text-decoration: none;
    transition: all .2s ease;
    cursor: pointer;
    border: none;
    white-space: nowrap;
}

.btn-vd-verify {
    background: var(--brand);
    color: #fff;
    border: 1px solid var(--brand);
}

.btn-vd-verify:hover {
    background: var(--brand-2);
    border-color: var(--brand-2);
    color: #fff;
    text-decoration: none;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(67, 97, 238, 0.3);
}

.btn-vd-verify:active {
    transform: translateY(0);
}

.btn-vd-detail {
    background: var(--surface);
    color: var(--ink-2);
    border: 1px solid var(--border);
}

.btn-vd-detail:hover {
    background: var(--brand-pale);
    border-color: var(--brand-light);
    color: var(--brand);
    text-decoration: none;
    transform: translateY(-1px);
}

/* Pagination */
.pagi-wrap {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
    padding: 14px 20px;
    border-top: 1px solid var(--border);
    background: var(--surface-2);
}
.pagi-info { font-size: 12px; color: var(--ink-3); }

.pagination { margin: 0; }
.pagination .page-link {
    color: var(--ink-2);
    border: 1px solid var(--border);
    font-size: 12px;
    font-weight: 600;
    padding: 5px 11px;
    margin: 0 2px;
    border-radius: var(--radius-sm);
    transition: all .2s;
}
.pagination .page-link:hover {
    background: var(--brand-pale);
    color: var(--brand);
    border-color: var(--brand-light);
}
.pagination .page-item.active .page-link {
    background: var(--brand);
    border-color: var(--brand);
    color: #fff;
}

/* Panduan Card */
.panduan-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    overflow: hidden;
}
.panduan-header {
    padding: 14px 20px;
    border-bottom: 1px solid var(--border);
    background: var(--surface-2);
}
.panduan-header strong {
    font-size: 13px;
    color: var(--ink);
}
.panduan-list {
    list-style: none;
    padding: 0;
    margin: 0;
}
.panduan-list li {
    display: flex;
    gap: 10px;
    padding: 8px 0;
    border-bottom: 1px solid var(--border);
    font-size: 12px;
}
.panduan-list li:last-child { border-bottom: none; }
.panduan-list .p-num {
    font-weight: 700;
    color: var(--brand);
    min-width: 24px;
    font-family: 'DM Mono', monospace;
    font-size: 11px;
}
.panduan-list .p-name { font-weight: 600; color: var(--ink); min-width: 140px; }
.panduan-list .p-desc { color: var(--ink-3); }

/* Empty State */
.empty-state { text-align: center; padding: 52px 20px; }
.empty-state-icon { font-size: 48px; opacity: .18; margin-bottom: 14px; }

.btn-empty-cta {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: var(--brand);
    color: #fff;
    border: none;
    border-radius: var(--radius);
    padding: 9px 20px;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    cursor: pointer;
    transition: all .2s;
}
.btn-empty-cta:hover {
    background: var(--brand-2);
    color: #fff;
    text-decoration: none;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(67, 97, 238, 0.3);
}

/* Utility */
.gap-2 { gap: 8px; }
.mb-3 { margin-bottom: 16px; }
.my-3 { margin-top: 16px; margin-bottom: 16px; }
.fw-bold { font-weight: 700; }
.fw-semibold { font-weight: 600; }
.text-muted { color: var(--ink-3); }
.text-primary { color: var(--brand); }

/* Animation */
@keyframes fadeSlide {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
.kpi-card { animation: fadeSlide .35s ease both; }
.filter-card { animation: fadeSlide .35s ease .05s both; }
.table-card { animation: fadeSlide .4s ease .1s both; }
.panduan-card { animation: fadeSlide .4s ease .15s both; }
</style>
@endpush

@section('content')
<div class="vd-wrap">

    {{-- Header --}}
    <div class="vd-page-header">
        <div>
            <div class="vd-breadcrumb">
                <a href="{{ route('marketing.dashboard') }}">Dashboard</a>
                <span>/</span>
                <span>Verifikasi Dokumen KPR</span>
            </div>
            <h1 class="vd-page-title">
                Verifikasi Dokumen KPR
            </h1>
            <p class="vd-page-sub">Periksa kelengkapan dan keabsahan dokumen debitur sebelum diteruskan ke admin</p>
        </div>
        <a href="{{ route('marketing.dashboard') }}" class="btn-back-home">
            ← Kembali ke Dashboard
        </a>
    </div>

    {{-- KPI Cards --}}
    <div class="kpi-strip">
        <div class="kpi-card k-total">
            <div class="kpi-top">
                <div>
                    <div class="kpi-label">Total Pengajuan Aktif</div>
                    <div class="kpi-num">{{ $pengajuan->total() ?? 0 }}</div>
                </div>
                <div class="kpi-icon i-brand">📋</div>
            </div>
            <p class="kpi-foot">Pengajuan sedang dalam proses verifikasi</p>
        </div>

        <div class="kpi-card k-pending">
            <div class="kpi-top">
                <div>
                    <div class="kpi-label">Belum Diperiksa</div>
                    <div class="kpi-num">{{ $belumDiverifikasi ?? 0 }}</div>
                </div>
                <div class="kpi-icon i-warn">⏳</div>
            </div>
            <p class="kpi-foot">Menunggu tindakan verifikasi segera</p>
        </div>

        <div class="kpi-card k-done">
            <div class="kpi-top">
                <div>
                    <div class="kpi-label">Sedang Diproses</div>
                    <div class="kpi-num">{{ $sedangDiverifikasi ?? 0 }}</div>
                </div>
                <div class="kpi-icon i-ok">✓</div>
            </div>
            <p class="kpi-foot">Verifikasi dokumen sedang berjalan</p>
        </div>

        <div class="kpi-card k-avg">
            <div class="kpi-top">
                <div>
                    <div class="kpi-label">Rata-rata Kelengkapan</div>
                    <div class="kpi-num">{{ number_format($avgPersentase ?? 0, 0) }}<span style="font-size:1rem">%</span></div>
                </div>
                <div class="kpi-icon i-gold">📊</div>
            </div>
            <p class="kpi-foot">Rata-rata kelengkapan dokumen</p>
        </div>
    </div>

    {{-- Filter Form --}}
    <div class="filter-card">
        <form method="GET" action="{{ route('marketing.verifikasi.dokumen') }}">
            <div class="row">
                <div class="col-md-4">
                    <label class="filter-label">🔍 Cari Pengajuan</label>
                    <input type="text" name="search" class="filter-input"
                           placeholder="Kode pengajuan atau nama debitur..."
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label class="filter-label">📅 Dari Tanggal</label>
                    <input type="date" name="start_date" class="filter-input" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-3">
                    <label class="filter-label">📅 Sampai Tanggal</label>
                    <input type="date" name="end_date" class="filter-input" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-2">
                    <label class="filter-label" style="opacity: 0;">Aksi</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn-filter-apply w-100">
                            Filter
                        </button>
                        <a href="{{ route('marketing.verifikasi.dokumen') }}" class="btn-filter-reset">
                            Reset
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- Main Table --}}
    <div class="table-card">
        <div class="table-card-header">
            <h6 class="table-card-title">
                <span>📋</span>
                Daftar Antrian Verifikasi Dokumen
                <span class="count-chip">{{ $pengajuan->total() }} Pengajuan</span>
            </h6>
            <small class="text-muted">Diurutkan berdasarkan tanggal masuk (terlama lebih dulu)</small>
        </div>

        <div class="table-responsive">
            <table class="vd-table">
                <thead>
                    <tr>
                        <th style="width:5%">No</th>
                        <th style="width:10%">Kode Pengajuan</th>
                        <th style="width:15%">Data Debitur</th>
                        <th style="width:15%">Unit & Pengajuan</th>
                        <th style="width:22%">Status Verifikasi Dokumen</th>
                        <th style="width:10%">Keputusan</th>
                        <th style="width:10%">Tanggal Masuk</th>
                        <th style="width:13%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pengajuan as $index => $item)
                    @php
                        $verifikasi = $item->verifikasiMarketing;
                        $dokFields = [
                            'ktp' => 'dok_ktp_valid',
                            'kk' => 'dok_kk_valid',
                            'slip_gaji' => 'dok_slip_gaji_valid',
                            'rekening_koran' => 'dok_rek_koran_valid',
                            'slik' => 'dok_slik_valid',
                            'sk_kerja' => 'dok_surat_kerja_valid',
                            'npwp' => 'dok_npwp_valid',
                        ];

                        $validCount = 0;
                        $totalDok = count($dokFields);
                        foreach ($dokFields as $key => $col) {
                            if ($verifikasi && $verifikasi->$col === true) $validCount++;
                        }

                        $progress = $totalDok > 0 ? round(($validCount / $totalDok) * 100) : 0;
                        $fillClass = $progress >= 80 ? 'fill-ok' : ($progress >= 50 ? 'fill-warn' : 'fill-err');

                        // Handle date difference correctly (no decimals, no negatives)
                        $tglProses = $item->tgl_marketing_proses ?? $item->created_at;
                        $now = \Carbon\Carbon::now();
                        $diffInSeconds = $now->diffInSeconds($tglProses, false);
                        $diffDays = (int) floor(abs($diffInSeconds) / 86400);
                        $isFuture = $tglProses->isFuture();

                        // Format the date difference text
                        if ($isFuture) {
                            $diffText = 'Mendatang';
                            $diffClass = 'future';
                        } else {
                            if ($diffDays == 0) {
                                $diffText = 'Hari ini';
                            } elseif ($diffDays == 1) {
                                $diffText = '1 hari lalu';
                            } else {
                                $diffText = $diffDays . ' hari lalu';
                            }
                            $diffClass = ($diffDays > 3) ? 'late' : '';
                        }
                    @endphp
                    <tr @if(!$isFuture && $diffDays > 3) style="background: rgba(67,97,238,.03)" @endif>
                        <td style="text-align: center">
                            <span style="font-family: 'DM Mono', monospace; color: var(--ink-4);">
                                {{ $pengajuan->firstItem() + $index }}
                            </span>
                        </td>

                        <td>
                            <div class="kode-pengajuan">{{ $item->kode_pengajuan }}</div>
                            <div class="deb-meta">ID: #{{ $item->id }}</div>
                        </td>

                        <td>
                            <div class="d-flex align-items-start" style="gap: 10px;">
                                <div class="deb-avatar">
                                    {{ strtoupper(substr($item->user->nama_lengkap ?? 'D', 0, 1)) }}
                                </div>
                                <div>
                                    <div class="deb-name">{{ $item->user->nama_lengkap ?? 'Tidak tersedia' }}</div>
                                    <div class="deb-meta">📞 {{ $item->debiturPribadi->no_hp ?? '-' }}</div>
                                    <div class="deb-meta">🆔 NIK: {{ substr($item->debiturPribadi->nik ?? '-', 0, 8) }}...</div>
                                </div>
                            </div>
                        </td>

                        <td>
                            <div class="fw-semibold" style="margin-bottom: 4px;">
                                {{ $item->unit->tipeUnit->proyek->nama_proyek ?? '-' }}
                            </div>
                            <div class="text-muted" style="font-size: 11px;">
                                {{ $item->unit->tipeUnit->nama_tipe ?? '-' }} · {{ $item->unit->kode_unit ?? '-' }}
                            </div>
                            <div class="text-primary fw-bold" style="margin-top: 6px; font-size: 12px;">
                                Rp {{ number_format($item->jumlah_pinjaman ?? 0, 0, ',', '.') }}
                            </div>
                            <div class="text-muted" style="font-size: 10px;">
                                Tenor {{ $item->tenor_tahun ?? '-' }} tahun
                            </div>
                        </td>

                        <td>
                            <div class="verif-prog-label">
                                <span>Kelengkapan Dokumen</span>
                                <span style="font-family: 'DM Mono', monospace; font-weight: 600;">
                                    {{ $validCount }}/{{ $totalDok }} ({{ $progress }}%)
                                </span>
                            </div>
                            <div class="verif-prog-bar">
                                <div class="verif-prog-fill {{ $fillClass }}" style="width: {{ $progress }}%"></div>
                            </div>
                            <div class="dok-pills">
                                @foreach(['ktp' => 'KTP', 'kk' => 'KK', 'slip_gaji' => 'Gaji', 'rekening_koran' => 'Rek Koran', 'slik' => 'SLIK', 'sk_kerja' => 'SK Kerja', 'npwp' => 'NPWP'] as $key => $label)
                                    @php $val = $verifikasi ? ($verifikasi->{$dokFields[$key]} ?? null) : null; @endphp
                                    @if($val === true)
                                        <span class="dok-pill dp-ok">✓ {{ $label }}</span>
                                    @elseif($val === false)
                                        <span class="dok-pill dp-err">✗ {{ $label }}</span>
                                    @else
                                        <span class="dok-pill dp-pending">{{ $label }}</span>
                                    @endif
                                @endforeach
                            </div>
                        </td>

                        <td style="text-align: center;">
                            @php
                                $statusHtml = '<span class="status-pill sp-pending">⏳ Belum diproses</span>';
                                if ($verifikasi && $verifikasi->rekomendasi_marketing) {
                                    $rekClass = match($verifikasi->rekomendasi_marketing) {
                                        'layak' => 'rek-layak',
                                        'perlu_pertimbangan' => 'rek-perlu',
                                        'tidak_layak' => 'rek-tidak',
                                        default => 'rek-perlu'
                                    };
                                    $rekLabel = match($verifikasi->rekomendasi_marketing) {
                                        'layak' => '✓ Layak',
                                        'perlu_pertimbangan' => '⚠️ Perlu Review',
                                        'tidak_layak' => '✗ Tolak',
                                        default => '-'
                                    };
                                    $statusHtml = '<div class="' . $rekClass . '" style="display: inline-flex; align-items: center; gap: 6px; padding: 5px 10px; border-radius: 6px; font-size: 11px; font-weight: 600;">' . $rekLabel . '</div>';
                                }
                            @endphp
                            {!! $statusHtml !!}
                        </td>

                        <td>
                            <div class="date-main">{{ \Carbon\Carbon::parse($tglProses)->format('d/m/Y') }}</div>
                            <div class="date-time">{{ \Carbon\Carbon::parse($tglProses)->format('H:i') }}</div>
                            <div class="date-diff {{ $diffClass }}">
                                <span>⏱️</span> {{ $diffText }}
                            </div>
                        </td>

                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('marketing.verifikasi.dokumen.show', $item->id) }}" class="btn-vd btn-vd-verify">
                                    ✓ Verifikasi
                                </a>
                                <a href="{{ route('marketing.pengajuan.show', $item->id) }}" class="btn-vd btn-vd-detail">
                                    👁️ Detail
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8">
                            <div class="empty-state">
                                <div class="empty-state-icon">📭</div>
                                <h6 style="margin-bottom: 8px;">Tidak ada pengajuan untuk diverifikasi</h6>
                                <p style="margin-bottom: 20px;">Semua pengajuan telah selesai diverifikasi atau belum ada pengajuan baru yang masuk.</p>
                                <a href="{{ route('marketing.pengajuan.masuk') }}" class="btn-empty-cta">
                                    Lihat Antrian Pengajuan Masuk
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pengajuan->hasPages())
        <div class="pagi-wrap">
            <div class="pagi-info">
                Menampilkan <strong>{{ $pengajuan->firstItem() }}</strong> - <strong>{{ $pengajuan->lastItem() }}</strong>
                dari <strong>{{ $pengajuan->total() }}</strong> pengajuan
            </div>
            <div>
                {{ $pengajuan->withQueryString()->links('pagination::bootstrap-4') }}
            </div>
        </div>
        @endif
    </div>

    {{-- Panduan Verifikasi --}}
    <div class="panduan-card">
        <div class="panduan-header">
            <strong>📋 Panduan Verifikasi Dokumen KPR</strong>
        </div>
        <div class="panduan-body" style="padding: 20px;">
            <div class="row">
                <div class="col-md-5">
                    <h6 style="margin-bottom: 12px; font-weight: 700; color: var(--brand);">📄 Dokumen yang Wajib Diperiksa</h6>
                    <ul class="panduan-list">
                        <li><span class="p-num">01</span><span class="p-name">KTP Debitur</span><span class="p-desc">Sesuai identitas, terbaca jelas, tidak expired</span></li>
                        <li><span class="p-num">02</span><span class="p-name">Kartu Keluarga</span><span class="p-desc">Data sesuai dengan KTP, lengkap</span></li>
                        <li><span class="p-num">03</span><span class="p-name">Slip Gaji</span><span class="p-desc">3 bulan terakhir, sesuai penghasilan yang dilaporkan</span></li>
                        <li><span class="p-num">04</span><span class="p-name">Rekening Koran</span><span class="p-desc">3 bulan terakhir, mutasi sesuai penghasilan</span></li>
                        <li><span class="p-num">05</span><span class="p-name">Laporan SLIK OJK</span><span class="p-desc">Riwayat kredit lancar (kolektibilitas 1)</span></li>
                        <li><span class="p-num">06</span><span class="p-name">SK / Surat Kerja</span><span class="p-desc">Dikeluarkan resmi oleh perusahaan, ada tanda tangan</span></li>
                        <li><span class="p-num">07</span><span class="p-name">NPWP</span><span class="p-desc">Wajib jika penghasilan > Rp 4.500.000/bulan</span></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h6 style="margin-bottom: 12px; font-weight: 700; color: var(--brand);">✓ Status Verifikasi Dokumen</h6>
                    <ul class="panduan-list">
                        <li>
                            <span class="dok-pill dp-ok" style="min-width: 70px;">✓ Valid</span>
                            <span class="p-desc">Dokumen asli, jelas, masih berlaku, sesuai data debitur</span>
                        </li>
                        <li>
                            <span class="dok-pill dp-err" style="min-width: 70px;">✗ Tidak Valid</span>
                            <span class="p-desc">Tidak sesuai, buram, expired, atau terindikasi palsu</span>
                        </li>
                        <li>
                            <span class="dok-pill dp-pending" style="min-width: 70px;">⏳ Belum</span>
                            <span class="p-desc">Dokumen terupload tapi belum diverifikasi marketing</span>
                        </li>
                    </ul>

                    <hr style="margin: 16px 0; border-color: var(--border);">

                    <h6 style="margin-bottom: 12px; font-weight: 700; color: var(--brand);">📊 Ambang Rekomendasi</h6>
                    <ul class="panduan-list">
                        <li>
                            <span class="rek-badge rek-layak" style="min-width: 70px;">✓ Layak</span>
                            <span class="p-desc">≥ 85% dokumen valid → teruskan ke admin</span>
                        </li>
                        <li>
                            <span class="rek-badge rek-perlu" style="min-width: 70px;">⚠️ Perlu</span>
                            <span class="p-desc">60–84% dokumen valid → pertimbangkan</span>
                        </li>
                        <li>
                            <span class="rek-badge rek-tidak" style="min-width: 70px;">✗ Tolak</span>
                            <span class="p-desc">&lt; 60% dokumen valid → minta revisi/tolak</span>
                        </li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h6 style="margin-bottom: 12px; font-weight: 700; color: var(--brand);">➡️ Alur Verifikasi</h6>
                    <ul class="panduan-list">
                        <li><span class="p-num">1</span><span class="p-desc">Klik tombol <strong>Verifikasi</strong> pada baris pengajuan</span></li>
                        <li><span class="p-num">2</span><span class="p-desc">Preview / download setiap dokumen debitur</span></li>
                        <li><span class="p-num">3</span><span class="p-desc">Tandai tiap dokumen: Valid / Tidak Valid</span></li>
                        <li><span class="p-num">4</span><span class="p-desc">Tentukan rekomendasi (Layak/Perlu/Tolak)</span></li>
                        <li><span class="p-num">5</span><span class="p-desc">Tentukan keputusan akhir dan simpan</span></li>
                        <li><span class="p-num">6</span><span class="p-desc">Sistem akan mengirim notifikasi otomatis</span></li>
                    </ul>
                    <div style="margin-top: 16px; padding: 10px; background: var(--brand-pale); border-radius: var(--radius); font-size: 11px; color: var(--ink-2);">
                        <strong>💡 Tips:</strong> Prioritaskan pengajuan dengan status "Belum Diperiksa" dan tanggal masuk paling lama untuk menghindari keterlambatan proses KPR.
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Optional: Add any additional JavaScript functionality here
    console.log('Verifikasi dokumen page loaded');
});
</script>
@endpush

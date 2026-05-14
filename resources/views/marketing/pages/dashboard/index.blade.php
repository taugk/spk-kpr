@extends('marketing.layouts.app')

@section('title', 'Dashboard Marketing KPR')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
:root {
    --primary:       #4361ee;
    --primary-light: #eef0ff;
    --success:       #17c964;
    --success-light: #e8fdf0;
    --danger:        #f5222d;
    --danger-light:  #fff1f0;
    --warning:       #f59e0b;
    --warning-light: #fffbeb;
    --info:          #06b6d4;
    --info-light:    #ecfeff;
    --purple:        #7c3aed;
    --purple-light:  #f5f3ff;
    --bg:            #f0f2f8;
    --card:          #ffffff;
    --text:          #1e293b;
    --muted:         #64748b;
    --border:        #e2e8f0;
    --radius:        14px;
    --shadow:        0 1px 3px rgba(0,0,0,.06), 0 4px 16px rgba(0,0,0,.04);
    --shadow-hover:  0 8px 30px rgba(67,97,238,.15);
}

body {
    background: var(--bg) !important;
    font-family: 'Plus Jakarta Sans', sans-serif !important;
    color: var(--text) !important;
}

/* ── Page header ─────────────────────────────────── */
.dash-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 28px;
    flex-wrap: wrap;
    gap: 12px;
}
.dash-header h4 {
    font-size: 1.35rem;
    font-weight: 800;
    color: var(--text);
    margin: 0;
    letter-spacing: -.3px;
}
.breadcrumb {
    background: transparent;
    padding: 0;
    margin: 0;
    font-size: .8rem;
}
.breadcrumb-item a { color: var(--primary); text-decoration: none; }
.breadcrumb-item.active { color: var(--muted); }
.breadcrumb-item + .breadcrumb-item::before { color: var(--border); }

/* ── Base card ───────────────────────────────────── */
.card {
    border: 1px solid var(--border) !important;
    border-radius: var(--radius) !important;
    box-shadow: var(--shadow) !important;
    background: var(--card) !important;
    transition: box-shadow .25s, transform .25s;
    margin-bottom: 0 !important;
}
.card:hover {
    box-shadow: var(--shadow-hover) !important;
    transform: translateY(-3px);
}
.card-header {
    border-bottom: 1px solid var(--border) !important;
    background: transparent !important;
    padding: 18px 20px !important;
    border-radius: var(--radius) var(--radius) 0 0 !important;
}
.card-title {
    font-size: .95rem !important;
    font-weight: 700 !important;
    color: var(--text) !important;
    margin: 0 !important;
}
.card-body { padding: 20px !important; }

/* ── Stat card ───────────────────────────────────── */
.stat-card .card-body {
    padding: 22px 20px !important;
}
.stat-icon {
    width: 52px; height: 52px;
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.3rem;
    flex-shrink: 0;
}
.stat-label {
    font-size: .72rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .6px;
    color: var(--muted);
    margin-bottom: 4px;
}
.stat-value {
    font-size: 1.9rem;
    font-weight: 800;
    line-height: 1;
    color: var(--text);
    letter-spacing: -1px;
}
.stat-sub {
    font-size: .75rem;
    color: var(--muted);
    margin-top: 5px;
}

/* icon color variants */
.icon-primary   { background: var(--primary-light);  color: var(--primary); }
.icon-success   { background: var(--success-light);  color: var(--success); }
.icon-danger    { background: var(--danger-light);   color: var(--danger);  }
.icon-warning   { background: var(--warning-light);  color: var(--warning); }
.icon-info      { background: var(--info-light);     color: var(--info);    }
.icon-purple    { background: var(--purple-light);   color: var(--purple);  }

/* border-top accent */
.stat-card.accent-primary { border-top: 3px solid var(--primary) !important; }
.stat-card.accent-success { border-top: 3px solid var(--success) !important; }
.stat-card.accent-warning { border-top: 3px solid var(--warning) !important; }
.stat-card.accent-danger  { border-top: 3px solid var(--danger)  !important; }
.stat-card.accent-info    { border-top: 3px solid var(--info)    !important; }
.stat-card.accent-purple  { border-top: 3px solid var(--purple)  !important; }

/* ── Section label ───────────────────────────────── */
.section-label {
    font-size: .7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1.2px;
    color: var(--muted);
    margin-bottom: 14px;
    margin-top: 6px;
}

/* ── KPI list ────────────────────────────────────── */
.kpi-list { padding: 0 !important; }
.kpi-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 20px;
    border-bottom: 1px solid var(--border);
    gap: 10px;
}
.kpi-item:last-child { border-bottom: none; }
.kpi-item .kpi-label {
    font-size: .875rem;
    color: var(--text);
    display: flex;
    align-items: center;
    gap: 8px;
}
.kpi-badge {
    font-size: .75rem;
    font-weight: 700;
    padding: 4px 12px;
    border-radius: 20px;
    white-space: nowrap;
}
.kpi-badge.kb-primary { background: var(--primary-light); color: var(--primary); }
.kpi-badge.kb-success { background: var(--success-light); color: var(--success); }
.kpi-badge.kb-danger  { background: var(--danger-light);  color: var(--danger);  }
.kpi-badge.kb-warning { background: var(--warning-light); color: var(--warning); }
.kpi-badge.kb-info    { background: var(--info-light);    color: var(--info);    }

.kpi-rate-bar {
    width: 90px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.kpi-rate-bar .progress {
    flex: 1; height: 6px; border-radius: 4px; background: var(--border);
}

/* ── Tables ──────────────────────────────────────── */
.dash-table { width: 100%; font-size: .85rem; }
.dash-table thead th {
    font-size: .7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .5px;
    color: var(--muted);
    background: #f8fafc;
    border-bottom: 1px solid var(--border) !important;
    border-top: none !important;
    padding: 11px 14px;
    white-space: nowrap;
}
.dash-table tbody td {
    padding: 13px 14px;
    border-color: var(--border) !important;
    vertical-align: middle;
    color: var(--text);
}
.dash-table tbody tr:hover td { background: #f8fafc; }

.avatar-circle {
    width: 34px; height: 34px;
    border-radius: 10px;
    background: var(--primary-light);
    color: var(--primary);
    font-weight: 700;
    font-size: .8rem;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}

/* ── Status badge overrides ──────────────────────── */
.badge {
    font-weight: 600 !important;
    font-size: .72rem !important;
    letter-spacing: .2px;
}

/* ── Period select ───────────────────────────────── */
.period-select {
    font-size: .8rem;
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 5px 10px;
    color: var(--text);
    background: #fff;
    outline: none;
    cursor: pointer;
}

/* ── Quick action banner ─────────────────────────── */
.quick-action-banner {
    background: linear-gradient(135deg, var(--primary) 0%, #6366f1 100%);
    border-radius: var(--radius) !important;
    border: none !important;
    padding: 26px 28px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 16px;
    box-shadow: 0 8px 30px rgba(67,97,238,.35) !important;
}
.quick-action-banner h5 {
    font-size: 1.05rem;
    font-weight: 700;
    color: #fff;
    margin: 0 0 4px;
}
.quick-action-banner p { color: rgba(255,255,255,.72); font-size: .875rem; margin: 0; }
.btn-action {
    background: #fff;
    color: var(--primary);
    border: none;
    border-radius: 10px;
    font-weight: 700;
    font-size: .875rem;
    padding: 11px 22px;
    display: inline-flex;
    align-items: center;
    gap: 7px;
    transition: transform .2s, box-shadow .2s;
    text-decoration: none;
    white-space: nowrap;
}
.btn-action:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 14px rgba(0,0,0,.2);
    color: var(--primary);
    text-decoration: none;
}

/* ── Monthly summary mini stat ───────────────────── */
.mini-stat {
    background: #f8fafc;
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 16px;
    text-align: center;
}
.mini-stat-icon { font-size: 1.5rem; margin-bottom: 6px; }
.mini-stat-label { font-size: .72rem; font-weight: 600; text-transform: uppercase; letter-spacing: .5px; color: var(--muted); }
.mini-stat-val { font-size: 1.5rem; font-weight: 800; color: var(--text); letter-spacing: -.5px; }

/* ── Chart containers ────────────────────────────── */
.chart-wrap { position: relative; }

/* ── Btn small ───────────────────────────────────── */
.btn-outline-primary {
    border-color: var(--primary) !important;
    color: var(--primary) !important;
    font-size: .8rem !important;
    font-weight: 600 !important;
    border-radius: 8px !important;
    padding: 6px 14px !important;
}
.btn-outline-primary:hover {
    background: var(--primary) !important;
    color: #fff !important;
}
.btn-info {
    background: var(--info-light) !important;
    border: none !important;
    color: var(--info) !important;
    font-size: .75rem !important;
    font-weight: 600 !important;
    border-radius: 7px !important;
    padding: 5px 12px !important;
}
.btn-info:hover { background: var(--info) !important; color: #fff !important; }

/* ── Responsive ──────────────────────────────────── */
@media (max-width: 575px) {
    .stat-value { font-size: 1.5rem; }
    .quick-action-banner { flex-direction: column; }
}

/* ── Counter animation ───────────────────────────── */
@keyframes fadeUp {
    from { opacity:0; transform:translateY(16px); }
    to   { opacity:1; transform:translateY(0); }
}
.card { animation: fadeUp .45s ease both; }
.row .col-xl-3:nth-child(1) .card,
.row .col-xl-3:nth-child(1) .stat-card { animation-delay: .05s; }
.row .col-xl-3:nth-child(2) .card { animation-delay: .1s; }
.row .col-xl-3:nth-child(3) .card { animation-delay: .15s; }
.row .col-xl-3:nth-child(4) .card { animation-delay: .2s; }
</style>
@endpush

@section('content')
<div class="container-fluid px-3 py-2">

    {{-- ── Page Header ─────────────────────────────── --}}
    <div class="dash-header">
        <h4><i class="dw dw-house-1" style="color:var(--primary)"></i> Dashboard Marketing KPR</h4>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Marketing</a></li>
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
        </nav>
    </div>

    {{-- ── Section: Antrian Verifikasi ────────────── --}}
    <p class="section-label">Antrian Verifikasi</p>
    <div class="row mb-4" style="row-gap:16px">

        {{-- Pengajuan Baru --}}
        <div class="col-xl-3 col-sm-6">
            <div class="card stat-card accent-primary h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <p class="stat-label mb-1">Pengajuan KPR Baru</p>
                            <div class="stat-value count-number" data-target="{{ $queueCounts['submitted'] ?? 0 }}">0</div>
                            <p class="stat-sub mt-2 mb-0"><i class="dw dw-wall-clock"></i> Menunggu verifikasi awal</p>
                        </div>
                        <div class="stat-icon icon-primary ml-3">
                            <i class="dw dw-add-file-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Verifikasi Dokumen --}}
        <div class="col-xl-3 col-sm-6">
            <div class="card stat-card accent-success h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <p class="stat-label mb-1">Verifikasi Dokumen</p>
                            <div class="stat-value count-number" data-target="{{ $queueCounts['document_verification'] ?? 0 }}">0</div>
                            <p class="stat-sub mt-2 mb-0"><i class="dw dw-certificate"></i> Sedang diverifikasi</p>
                        </div>
                        <div class="stat-icon icon-success ml-3">
                            <i class="dw dw-file-31"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Verifikasi Lapangan --}}
        <div class="col-xl-3 col-sm-6">
            <div class="card stat-card accent-warning h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <p class="stat-label mb-1">Verifikasi Lapangan</p>
                            <div class="stat-value count-number" data-target="{{ $queueCounts['field_verification'] ?? 0 }}">0</div>
                            <p class="stat-sub mt-2 mb-0"><i class="dw dw-pin"></i> Menunggu kunjungan</p>
                        </div>
                        <div class="stat-icon icon-warning ml-3">
                            <i class="icon-copy ion-ios-location"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Perlu Revisi --}}
        <div class="col-xl-3 col-sm-6">
            <div class="card stat-card accent-danger h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <p class="stat-label mb-1">Perlu Revisi</p>
                            <div class="stat-value count-number" data-target="{{ $queueCounts['revision'] ?? 0 }}">0</div>
                            <p class="stat-sub mt-2 mb-0"><i class="icon-copy ion-ios-refresh"></i> Menunggu perbaikan</p>
                        </div>
                        <div class="stat-icon icon-danger ml-3">
                            <i class="icon-copy ion-ios-loop-strong"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- ── Section: Hasil KPR ───────────────────── --}}
    <p class="section-label">Hasil & Keputusan KPR</p>
    <div class="row mb-4" style="row-gap:16px">

        {{-- Diteruskan ke Admin --}}
        <div class="col-xl-3 col-sm-6">
            <div class="card stat-card accent-info h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <p class="stat-label mb-1">Diteruskan ke Admin</p>
                            <div class="stat-value count-number" data-target="{{ $queueCounts['sent_to_admin'] ?? 0 }}">0</div>
                            <p class="stat-sub mt-2 mb-0"><i class="icon-copy ion-stats-bars"></i> Proses penilaian</p>
                        </div>
                        <div class="stat-icon icon-info ml-3">
                            <i class="icon-copy ion-navigate"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- KPR Disetujui --}}
        <div class="col-xl-3 col-sm-6">
            <div class="card stat-card accent-success h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <p class="stat-label mb-1">KPR Disetujui</p>
                            <div class="stat-value count-number" data-target="{{ $stats['total_approved'] ?? 0 }}">0</div>
                            <p class="stat-sub mt-2 mb-0"><i class="icon-copy ion-checkmark-circled"></i> KPR siap diproses</p>
                        </div>
                        <div class="stat-icon icon-success ml-3">
                            <i class="icon-copy ion-checkmark-circled"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- KPR Ditolak --}}
        <div class="col-xl-3 col-sm-6">
            <div class="card stat-card accent-danger h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <p class="stat-label mb-1">KPR Ditolak</p>
                            <div class="stat-value count-number" data-target="{{ $stats['total_rejected'] ?? 0 }}">0</div>
                            <p class="stat-sub mt-2 mb-0"><i class="icon-copy ion-close-circled"></i> Tidak memenuhi kriteria</p>
                        </div>
                        <div class="stat-icon icon-danger ml-3">
                            <i class="icon-copy ion-close-circled"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Properti Terjual --}}
        <div class="col-xl-3 col-sm-6">
            <div class="card stat-card accent-purple h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <p class="stat-label mb-1">Properti Terjual</p>
                            <div class="stat-value count-number" data-target="{{ $stats['property_sold'] ?? 0 }}">0</div>
                            <p class="stat-sub mt-2 mb-0"><i class="icon-copy dw dw-building"></i> Melalui pengajuan KPR</p>
                        </div>
                        <div class="stat-icon icon-purple ml-3">
                           <i class="icon-copy dw dw-building"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- ── Chart + KPI ──────────────────────────── --}}
    <div class="row mb-4" style="row-gap:16px">

        {{-- Trend Chart --}}
        <div class="col-xl-8">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h6 class="card-title">
                        <i class="dw dw-bar-chart-1 mr-1" style="color:var(--primary)"></i>
                        Trend Verifikasi KPR
                    </h6>
                    <select id="chartPeriod" class="period-select">
                        <option value="6">6 Bulan Terakhir</option>
                        <option value="12" selected>12 Bulan Terakhir</option>
                        <option value="24">24 Bulan Terakhir</option>
                    </select>
                </div>
                <div class="card-body">
                    <div class="chart-wrap" style="height:280px">
                        <canvas id="performanceChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- KPI Summary --}}
        <div class="col-xl-4">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="card-title">
                        <i class="dw dw-clipboard1" style="color:var(--primary)"></i>
                        Ringkasan Kinerja
                    </h6>
                </div>
                <div class="kpi-list">
                    <div class="kpi-item">
                        <span class="kpi-label">
                            <span class="stat-icon icon-primary" style="width:28px;height:28px;border-radius:8px;font-size:.8rem">
                                <i class="dw dw-add-file"></i>
                            </span>
                            Total Pengajuan KPR
                        </span>
                        <span class="kpi-badge kb-primary">{{ number_format($stats['total_processed'] ?? 0) }}</span>
                    </div>
                    <div class="kpi-item">
                        <span class="kpi-label">
                            <span class="stat-icon icon-success" style="width:28px;height:28px;border-radius:8px;font-size:.8rem">
                                <i class="dw dw-like"></i>
                            </span>
                            KPR Disetujui
                        </span>
                        <span class="kpi-badge kb-success">{{ number_format($stats['total_approved'] ?? 0) }}</span>
                    </div>
                    <div class="kpi-item">
                        <span class="kpi-label">
                            <span class="stat-icon icon-danger" style="width:28px;height:28px;border-radius:8px;font-size:.8rem">
                                <i class="dw dw-cancel"></i>
                            </span>
                            KPR Ditolak
                        </span>
                        <span class="kpi-badge kb-danger">{{ number_format($stats['total_rejected'] ?? 0) }}</span>
                    </div>
                    <div class="kpi-item">
                        <span class="kpi-label">
                            <span class="stat-icon icon-warning" style="width:28px;height:28px;border-radius:8px;font-size:.8rem">
                                <i class="dw dw-refresh"></i>
                            </span>
                            Perlu Revisi
                        </span>
                        <span class="kpi-badge kb-warning">{{ number_format($stats['total_revision'] ?? 0) }}</span>
                    </div>
                    <div class="kpi-item">
                        <span class="kpi-label">
                            <span class="stat-icon icon-info" style="width:28px;height:28px;border-radius:8px;font-size:.8rem">
                                <i class="dw dw-money-2"></i>
                            </span>
                            Total Plafon KPR
                        </span>
                        <span class="kpi-badge kb-info" style="font-size:.68rem">
                            {{ App\Helpers\MarketingHelper::formatRupiah($stats['total_plafon'] ?? 0) }}
                        </span>
                    </div>
                    <div class="kpi-item" style="background:#f8fafc;border-radius:0 0 var(--radius) var(--radius)">
                        <span class="kpi-label" style="font-weight:700">
                            <span class="stat-icon icon-primary" style="width:28px;height:28px;border-radius:8px;font-size:.8rem">
                                <i class="dw dw-target"></i>
                            </span>
                            Approval Rate
                        </span>
                        <div class="kpi-rate-bar">
                            <div class="progress">
                                <div class="progress-bar {{ ($stats['approval_rate'] ?? 0) >= 70 ? 'bg-success' : (($stats['approval_rate'] ?? 0) >= 50 ? 'bg-warning' : 'bg-danger') }}"
                                     style="width:{{ $stats['approval_rate'] ?? 0 }}%"></div>
                            </div>
                            <strong style="font-size:.78rem;color:var(--text);white-space:nowrap">
                                {{ number_format($stats['approval_rate'] ?? 0, 1) }}%
                            </strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- ── Document + Decision Charts ───────────── --}}
    <div class="row mb-4" style="row-gap:16px">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="card-title">
                        <i class="dw dw-folder-11 mr-2" style="color:var(--primary)"></i>
                        Statistik Verifikasi Dokumen
                    </h6>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    <div class="chart-wrap" style="height:240px;width:100%">
                        <canvas id="documentChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="card-title">
                        <i class="dw dw-bar-chart mr-2" style="color:var(--primary)"></i>
                        Distribusi Keputusan Verifikasi
                    </h6>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    <div class="chart-wrap" style="height:240px;width:100%">
                        <canvas id="decisionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Monthly Stats ─────────────────────────── --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title">
                        <i class="dw dw-analytics-21" style="color:var(--primary)"></i>
                        Statistik Bulanan Pengajuan KPR
                    </h6>
                </div>
                <div class="card-body">

                    {{-- Mini stat row --}}
                    <div class="row mb-4" style="row-gap:12px">
                        <div class="col-6 col-md-3">
                            <div class="mini-stat">
                                <div class="mini-stat-icon" style="color:var(--primary)"><i class="dw dw-file"></i></div>
                                <div class="mini-stat-label">Total Pengajuan</div>
                                <div class="mini-stat-val">{{ number_format($summaryStats['total_pengajuan'] ?? 0) }}</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="mini-stat">
                                <div class="mini-stat-icon" style="color:var(--success)"><i class="dw dw-checked"></i></div>
                                <div class="mini-stat-label">KPR Disetujui</div>
                                <div class="mini-stat-val">{{ number_format($summaryStats['total_disetujui'] ?? 0) }}</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="mini-stat">
                                <div class="mini-stat-icon" style="color:var(--danger)"><i class="dw dw-cancel"></i></div>
                                <div class="mini-stat-label">KPR Ditolak</div>
                                <div class="mini-stat-val">{{ number_format($summaryStats['total_ditolak'] ?? 0) }}</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="mini-stat">
                                <div class="mini-stat-icon" style="color:var(--info)"><i class="dw dw-analytics-11"></i></div>
                                <div class="mini-stat-label">Approval Rate</div>
                                <div class="mini-stat-val">{{ number_format($summaryStats['overall_approval_rate'] ?? 0, 1) }}%</div>
                            </div>
                        </div>
                    </div>

                    {{-- Table --}}
                    <div class="table-responsive">
                        <table class="table dash-table">
                            <thead>
                                <tr>
                                    <th>Bulan</th>
                                    <th>Total Pengajuan</th>
                                    <th>Disetujui</th>
                                    <th>Ditolak</th>
                                    <th>Approval Rate</th>
                                    <th>Rata-rata Skor SMART</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($monthlyStats as $stat)
                                <tr>
                                    <td><strong>{{ $stat->formatted_periode }}</strong></td>
                                    <td>{{ number_format($stat->total_pengajuan) }}</td>
                                    <td>
                                        <span style="color:var(--success);font-weight:600">
                                            <i class="dw dw-checked mr-1"></i>{{ number_format($stat->jumlah_disetujui) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span style="color:var(--danger);font-weight:600">
                                            <i class="dw dw-checked mr-1"></i>{{ number_format($stat->jumlah_ditolak) }}
                                        </span>
                                    </td>
                                    <td>{!! $stat->approval_rate_badge !!}</td>
                                    <td>
                                        @if($stat->rata_skor_smart)
                                            <div class="d-flex align-items-center" style="gap:8px">
                                                <div class="progress flex-grow-1" style="height:5px;border-radius:4px;max-width:90px">
                                                    <div class="progress-bar bg-info" style="width:{{ $stat->rata_skor_smart }}%"></div>
                                                </div>
                                                <small style="color:var(--muted);font-weight:600">{{ number_format($stat->rata_skor_smart, 1) }}%</small>
                                            </div>
                                        @else
                                            <span style="color:var(--muted)">—</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5" style="color:var(--muted)">
                                        <i class="dw dw-inbox fa-2x mb-2 d-block" style="opacity:.4"></i>
                                        Belum ada data
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Recent Pengajuan ─────────────────────── --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h6 class="card-title">
                        <i class="dw dw-wall-clock mr-2" style="color:var(--primary)"></i>
                        Pengajuan KPR Terbaru
                    </h6>
                    <a href="{{ route('marketing.pengajuan.semua') }}" class="btn btn-outline-primary">
                        Lihat Semua
                    </a>
                </div>
                <div class="card-body p-0 !important">
                    <div class="table-responsive">
                        <table class="table dash-table mb-0">
                            <thead>
                                <tr>
                                    <th>Kode Pengajuan</th>
                                    <th>Debitur</th>
                                    <th>Properti</th>
                                    <th>Harga Properti</th>
                                    <th>Plafon KPR</th>
                                    <th>Tenor</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentPengajuan as $pengajuan)
                                <tr>
                                    <td>
                                        <strong style="color:var(--primary)">{{ $pengajuan->kode_pengajuan }}</strong>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center" style="gap:10px">
                                            <div class="avatar-circle">
                                                {{ substr($pengajuan->user->nama_lengkap ?? 'N', 0, 1) }}
                                            </div>
                                            <div>
                                                <div style="font-weight:600;font-size:.85rem">{{ $pengajuan->user->nama_lengkap ?? 'N/A' }}</div>
                                                <div style="font-size:.75rem;color:var(--muted)">{{ $pengajuan->user->no_hp ?? '-' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-weight:600;font-size:.85rem">{{ $pengajuan->unit->tipeUnit->proyek->nama_proyek ?? 'N/A' }}</div>
                                        <div style="font-size:.75rem;color:var(--muted)">
                                            {{ $pengajuan->unit->tipeUnit->nama_tipe ?? '' }} · {{ $pengajuan->unit->kode_unit ?? '' }}
                                        </div>
                                    </td>
                                    <td style="font-weight:600;font-size:.82rem">
                                        {{ App\Helpers\MarketingHelper::formatRupiah($pengajuan->harga_properti) }}
                                    </td>
                                    <td style="font-weight:600;font-size:.82rem">
                                        {{ App\Helpers\MarketingHelper::formatRupiah($pengajuan->jumlah_pinjaman) }}
                                    </td>
                                    <td>
                                        <div style="font-weight:600">{{ $pengajuan->tenor_tahun }} Thn</div>
                                        <div style="font-size:.72rem;color:var(--muted)">
                                            {{ App\Helpers\MarketingHelper::formatRupiah($pengajuan->estimasi_angsuran) }}/bln
                                        </div>
                                    </td>
                                    <td>{!! App\Helpers\MarketingHelper::getStatusBadge($pengajuan->status) !!}</td>
                                    <td>
                                        <div style="font-weight:600;font-size:.82rem">
                                            {{ optional($pengajuan->created_at)->format('d/m/Y') ?? '-' }}
                                        </div>
                                        <div style="font-size:.72rem;color:var(--muted)">
                                            {{ optional($pengajuan->created_at)->diffForHumans() ?? '-' }}
                                        </div>
                                    </td>
                                    <td>
                                        <a href="{{ route('marketing.pengajuan.show', $pengajuan) }}" class="btn btn-info btn-sm">
                                            <i class="icon-copy dw dw-eye"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center py-5" style="color:var(--muted)">
                                        <i class="icon-copy dw dw-inbox d-block mb-2" style="opacity:.4"></i>
                                        Belum ada pengajuan KPR
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Quick Action Banner ───────────────────── --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="quick-action-banner">
                <div>
                    <h5><i class="dw dw-pencil"></i> Verifikasi Pengajuan KPR Baru</h5>
                    <p>Terdapat <strong>{{ $queueCounts['submitted'] ?? 0 }} pengajuan</strong> yang menunggu verifikasi awal.
                        Segera lakukan verifikasi untuk mempercepat proses persetujuan KPR.</p>
                </div>
                <a href="{{ route('marketing.pengajuan.masuk') }}" class="btn-action">
                    <i class="dw dw-inbox"></i> Proses Verifikasi
                </a>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function () {

    /* ── Counter animation ──────────────────────── */
    $('.count-number').each(function () {
        var $el = $(this);
        var target = parseInt($el.data('target')) || 0;
        if (target === 0) { $el.text('0'); return; }
        var current = 0, steps = 50, increment = target / steps;
        var timer = setInterval(function () {
            current = Math.min(current + increment, target);
            $el.text(new Intl.NumberFormat('id-ID').format(Math.floor(current)));
            if (current >= target) { clearInterval(timer); }
        }, 18);
    });

    /* ── Chart defaults ─────────────────────────── */
    Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
    Chart.defaults.font.size   = 12;
    Chart.defaults.color       = '#64748b';

    /* ── Performance Line Chart ─────────────────── */
    var perfCtx = document.getElementById('performanceChart').getContext('2d');
    var perfChart = new Chart(perfCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartData['labels'] ?? []) !!},
            datasets: [
                {
                    label: 'Pengajuan Diproses',
                    data: {!! json_encode($chartData['processed'] ?? []) !!},
                    borderColor: '#4361ee',
                    backgroundColor: 'rgba(67,97,238,.08)',
                    fill: true, tension: 0.4,
                    pointBackgroundColor: '#4361ee',
                    pointBorderColor: '#fff', pointBorderWidth: 2,
                    pointRadius: 4, pointHoverRadius: 6
                },
                {
                    label: 'KPR Disetujui',
                    data: {!! json_encode($chartData['approved'] ?? []) !!},
                    borderColor: '#17c964',
                    backgroundColor: 'rgba(23,201,100,.05)',
                    fill: true, tension: 0.4,
                    pointBackgroundColor: '#17c964',
                    pointBorderColor: '#fff', pointBorderWidth: 2,
                    pointRadius: 4, pointHoverRadius: 6
                },
                {
                    label: 'KPR Ditolak',
                    data: {!! json_encode($chartData['rejected'] ?? []) !!},
                    borderColor: '#f5222d',
                    backgroundColor: 'rgba(245,34,45,.05)',
                    fill: true, tension: 0.4,
                    pointBackgroundColor: '#f5222d',
                    pointBorderColor: '#fff', pointBorderWidth: 2,
                    pointRadius: 4, pointHoverRadius: 6
                }
            ]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            interaction: { intersect: false, mode: 'index' },
            plugins: {
                legend: {
                    position: 'top',
                    labels: { usePointStyle: true, boxWidth: 8, padding: 20 }
                },
                tooltip: {
                    backgroundColor: '#1e293b',
                    padding: 12,
                    callbacks: {
                        label: ctx => ' ' + ctx.dataset.label + ': ' +
                            new Intl.NumberFormat('id-ID').format(ctx.raw)
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,.04)', drawBorder: false },
                    ticks: { callback: v => new Intl.NumberFormat('id-ID').format(v) }
                },
                x: { grid: { display: false }, ticks: { padding: 6 } }
            }
        }
    });

    /* ── Doughnut Chart ─────────────────────────── */
    var docCtx = document.getElementById('documentChart').getContext('2d');
    new Chart(docCtx, {
        type: 'doughnut',
        data: {
            labels: ['Valid', 'Tidak Valid', 'Belum Diperiksa'],
            datasets: [{
                data: [
                    {{ $documentStats['valid'] ?? 0 }},
                    {{ $documentStats['invalid'] ?? 0 }},
                    {{ $documentStats['unchecked'] ?? 0 }}
                ],
                backgroundColor: ['#17c964', '#f5222d', '#f59e0b'],
                borderWidth: 0, hoverOffset: 8
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false, cutout: '65%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { usePointStyle: true, padding: 18 }
                },
                tooltip: {
                    backgroundColor: '#1e293b',
                    callbacks: {
                        label: ctx => {
                            let total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                            let pct   = total ? ((ctx.raw / total) * 100).toFixed(1) : 0;
                            return ' ' + ctx.label + ': ' +
                                new Intl.NumberFormat('id-ID').format(ctx.raw) + ' (' + pct + '%)';
                        }
                    }
                }
            }
        }
    });

    /* ── Bar Chart ──────────────────────────────── */
    var decCtx = document.getElementById('decisionChart').getContext('2d');
    new Chart(decCtx, {
        type: 'bar',
        data: {
            labels: ['Diteruskan ke Admin', 'Minta Revisi', 'Ditolak'],
            datasets: [{
                label: 'Jumlah Pengajuan',
                data: [
                    {{ $decisionStats['ajukan_ke_admin'] ?? 0 }},
                    {{ $decisionStats['minta_revisi'] ?? 0 }},
                    {{ $decisionStats['tolak'] ?? 0 }}
                ],
                backgroundColor: ['rgba(67,97,238,.18)', 'rgba(245,158,11,.18)', 'rgba(245,34,45,.18)'],
                borderColor:      ['#4361ee', '#f59e0b', '#f5222d'],
                borderWidth: 2,
                borderRadius: 8,
                barPercentage: 0.55, categoryPercentage: 0.75
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e293b',
                    callbacks: {
                        label: ctx => ' Jumlah: ' + new Intl.NumberFormat('id-ID').format(ctx.raw)
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,.04)', drawBorder: false },
                    ticks: { stepSize: 1, precision: 0 }
                },
                x: { grid: { display: false } }
            }
        }
    });

    /* ── Period AJAX ────────────────────────────── */
    $('#chartPeriod').on('change', function () {
        var months = $(this).val();
        $.ajax({
            url: '{{ route("marketing.chart-data") }}',
            data: { months: months },
            success: function (data) {
                perfChart.data.labels            = data.labels;
                perfChart.data.datasets[0].data  = data.processed;
                perfChart.data.datasets[1].data  = data.approved;
                perfChart.data.datasets[2].data  = data.rejected;
                perfChart.update();
            }
        });
    });

});
</script>
@endpush

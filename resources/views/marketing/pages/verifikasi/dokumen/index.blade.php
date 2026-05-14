@extends('marketing.layouts.app')

@section('title', 'Verifikasi Dokumen KPR')

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

/* ── Page header ────────────────────────────── */
.vd-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    margin-bottom: 26px;
    flex-wrap: wrap;
    gap: 12px;
}
.vd-header-left h4 {
    font-size: 1.3rem;
    font-weight: 800;
    color: var(--text);
    margin: 0 0 4px;
    letter-spacing: -.3px;
}
.vd-header-left p { font-size: .85rem; color: var(--muted); margin: 0; }

/* ── Card base ──────────────────────────────── */
.card {
    border: 1px solid var(--border) !important;
    border-radius: var(--radius) !important;
    box-shadow: var(--shadow) !important;
    background: var(--card) !important;
    transition: box-shadow .25s, transform .25s;
}
.card:hover { box-shadow: var(--shadow-hover) !important; }
.card-header {
    border-bottom: 1px solid var(--border) !important;
    background: transparent !important;
    padding: 16px 20px !important;
    border-radius: var(--radius) var(--radius) 0 0 !important;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 10px;
}
.card-title {
    font-size: .95rem !important;
    font-weight: 700 !important;
    color: var(--text) !important;
    margin: 0 !important;
    display: flex;
    align-items: center;
    gap: 8px;
}
.card-body { padding: 20px !important; }

/* ── Back button ────────────────────────────── */
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
.btn-back:hover { border-color: var(--primary); color: var(--primary); text-decoration: none; }

/* ── Stat cards ─────────────────────────────── */
.stat-card {
    border-radius: var(--radius) !important;
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 12px;
}
.stat-card .sc-top { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; }
.stat-card .sc-icon {
    width: 48px; height: 48px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.2rem; flex-shrink: 0;
}
.stat-card .sc-label { font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .6px; color: var(--muted); }
.stat-card .sc-value { font-size: 2rem; font-weight: 800; color: var(--text); line-height: 1.1; letter-spacing: -1px; }

.sc-primary  { border-top: 3px solid var(--primary) !important; }
.sc-warning  { border-top: 3px solid var(--warning) !important; }
.sc-success  { border-top: 3px solid var(--success) !important; }
.icon-primary { background: var(--primary-light); color: var(--primary); }
.icon-warning { background: var(--warning-light); color: var(--warning); }
.icon-success { background: var(--success-light); color: var(--success); }

/* ── Filter bar ─────────────────────────────── */
.filter-bar { padding: 18px 20px !important; }
.filter-bar .form-control {
    border: 1px solid var(--border);
    border-radius: 9px;
    font-size: .85rem;
    color: var(--text);
    padding: 8px 12px;
    height: auto;
    transition: border-color .2s;
}
.filter-bar .form-control:focus { border-color: var(--primary); outline: none; box-shadow: none; }
.filter-bar label { font-size: .78rem; font-weight: 600; color: var(--muted); margin-bottom: 5px; }
.btn-filter {
    display: inline-flex; align-items: center; gap: 6px;
    font-size: .82rem; font-weight: 600; border-radius: 9px;
    padding: 8px 16px; border: none; cursor: pointer; transition: all .2s;
}
.btn-filter.primary { background: var(--primary); color: #fff; }
.btn-filter.primary:hover { background: #3451d1; }
.btn-filter.reset { background: #f1f5f9; color: var(--muted); border: 1px solid var(--border); }
.btn-filter.reset:hover { background: #e2e8f0; }

/* ── Main table ─────────────────────────────── */
.vd-table { width: 100%; font-size: .84rem; }
.vd-table thead th {
    font-size: .7rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: .5px;
    color: var(--muted); background: #f8fafc;
    border-bottom: 1px solid var(--border) !important;
    border-top: none !important;
    padding: 12px 14px; white-space: nowrap;
}
.vd-table tbody td {
    padding: 14px 14px;
    border-color: var(--border) !important;
    vertical-align: middle;
}
.vd-table tbody tr:hover td { background: #fafbfe; }

/* avatar */
.deb-avatar {
    width: 36px; height: 36px; border-radius: 10px;
    background: var(--primary-light); color: var(--primary);
    font-weight: 700; font-size: .85rem;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}

/* doc checklist pills */
.doc-pills { display: flex; flex-wrap: wrap; gap: 4px; margin-top: 10px; }
.doc-pill {
    display: inline-flex; align-items: center; gap: 4px;
    font-size: .68rem; font-weight: 600;
    padding: 3px 8px; border-radius: 20px;
}
.dp-ok      { background: var(--success-light); color: var(--success); }
.dp-bad     { background: var(--danger-light);  color: var(--danger);  }
.dp-pending { background: #f1f5f9; color: var(--muted); }

/* progress */
.doc-progress-wrap { margin-bottom: 6px; }
.doc-progress-wrap .dp-label {
    display: flex; justify-content: space-between;
    font-size: .72rem; font-weight: 600; color: var(--muted); margin-bottom: 4px;
}
.doc-progress-wrap .progress { height: 6px; border-radius: 4px; background: var(--border); }
.doc-progress-wrap .progress-bar { border-radius: 4px; }

/* status badge */
.status-pill {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: .72rem; font-weight: 700; padding: 4px 12px;
    border-radius: 20px; white-space: nowrap;
}
.sp-lengkap  { background: var(--success-light); color: var(--success); }
.sp-sebagian { background: var(--warning-light); color: var(--warning); }
.sp-invalid  { background: var(--danger-light);  color: var(--danger);  }
.sp-pending  { background: #f1f5f9; color: var(--muted); }

/* action buttons */
.btn-vd {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: .75rem; font-weight: 600;
    padding: 6px 12px; border-radius: 8px;
    border: none; cursor: pointer; transition: all .2s;
    text-decoration: none; white-space: nowrap;
}
.btn-vd.verify { background: var(--info-light); color: var(--info); }
.btn-vd.verify:hover { background: var(--info); color: #fff; }
.btn-vd.detail { background: #f1f5f9; color: var(--muted); }
.btn-vd.detail:hover { background: #e2e8f0; color: var(--text); }

/* expand all btn */
.btn-expand {
    display: inline-flex; align-items: center; gap: 6px;
    font-size: .78rem; font-weight: 600; padding: 6px 14px;
    border-radius: 8px; background: var(--primary-light);
    color: var(--primary); border: none; cursor: pointer;
}

/* ── Pagination ─────────────────────────────── */
.pagination .page-link {
    border: 1px solid var(--border); color: var(--text);
    font-size: .82rem; font-weight: 500;
    border-radius: 8px !important; margin: 0 2px;
    padding: 6px 12px;
}
.pagination .page-item.active .page-link { background: var(--primary); border-color: var(--primary); color: #fff; }
.pagination .page-link:hover { background: var(--primary-light); color: var(--primary); border-color: var(--border); }

/* ── Panduan card ───────────────────────────── */
.panduan-title { font-size: .85rem; font-weight: 700; color: var(--text); margin-bottom: 12px; display: flex; align-items: center; gap: 7px; }
.panduan-table { width: 100%; font-size: .8rem; }
.panduan-table td { padding: 5px 8px; vertical-align: top; color: var(--text); border: none; }
.panduan-table td:first-child { font-weight: 600; white-space: nowrap; color: var(--text); }
.panduan-table td:last-child { color: var(--muted); }
.panduan-divider { border: none; border-top: 1px solid var(--border); margin: 18px 0; }

.info-tip {
    background: var(--info-light);
    border-left: 3px solid var(--info);
    border-radius: 0 9px 9px 0;
    padding: 12px 16px;
    font-size: .82rem;
    color: var(--text);
}
.info-tip strong { color: var(--info); }

/* ── Empty state ────────────────────────────── */
.empty-state { text-align: center; padding: 48px 20px; color: var(--muted); }
.empty-state i { font-size: 2.5rem; opacity: .3; display: block; margin-bottom: 12px; }
.empty-state h6 { font-weight: 700; color: var(--text); margin-bottom: 6px; }

/* badge count */
.count-badge {
    font-size: .72rem; font-weight: 700; padding: 3px 10px;
    border-radius: 20px; background: var(--primary-light);
    color: var(--primary); margin-left: 6px;
}

@keyframes fadeUp {
    from { opacity:0; transform:translateY(14px); }
    to   { opacity:1; transform:translateY(0); }
}
.card { animation: fadeUp .4s ease both; }
.row .col-md-4:nth-child(2) .card { animation-delay: .07s; }
.row .col-md-4:nth-child(3) .card { animation-delay: .14s; }
</style>
@endpush

@section('content')
<div class="container-fluid px-3 py-2">

    {{-- ── Page Header ─────────────────────────────── --}}
    <div class="vd-header">
        <div class="vd-header-left">
            <h4><i class="dw dw-file-31 mr-2" style="color:var(--primary)"></i>Verifikasi Dokumen KPR</h4>
            <p>Verifikasi kelengkapan dan keabsahan dokumen pengajuan KPR debitur</p>
        </div>
        <a href="{{ route('marketing.dashboard') }}" class="btn-back">
            <i class="dw dw-home"></i> Kembali ke Dashboard
        </a>
    </div>

    {{-- ── Filter ───────────────────────────────────── --}}
    <div class="card mb-4">
        <div class="filter-bar">
            <form method="GET" action="{{ route('marketing.verifikasi.dokumen') }}">
                <div class="row align-items-end" style="row-gap:12px">
                    <div class="col-md-4">
                        <label><i class="dw dw-search mr-1"></i> Cari</label>
                        <input type="text" name="search" class="form-control"
                               placeholder="Kode pengajuan / nama debitur"
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <label><i class="dw dw-calendar mr-1"></i> Dari Tanggal</label>
                        <input type="date" name="start_date" class="form-control"
                               value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-3">
                        <label><i class="dw dw-calendar mr-1"></i> Sampai Tanggal</label>
                        <input type="date" name="end_date" class="form-control"
                               value="{{ request('end_date') }}">
                    </div>
                    <div class="col-md-2">
                        <div class="d-flex" style="gap:8px">
                            <button type="submit" class="btn-filter primary flex-grow-1">
                                <i class="dw dw-filter"></i> Filter
                            </button>
                            <a href="{{ route('marketing.verifikasi.dokumen') }}" class="btn-filter reset">
                                <i class="dw dw-refresh"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Stats Row ────────────────────────────────── --}}
    <div class="row mb-4" style="row-gap:14px">

        <div class="col-md-4">
            <div class="card stat-card sc-primary">
                <div class="sc-top">
                    <div>
                        <p class="sc-label mb-1">Total Verifikasi Dokumen</p>
                        <div class="sc-value">{{ $pengajuan->total() ?? 0 }}</div>
                    </div>
                    <div class="sc-icon icon-primary">
                        <i class="dw dw-folder"></i>
                    </div>
                </div>
                <p style="font-size:.75rem;color:var(--muted);margin:0">
                    <i class="dw dw-analytics mr-1"></i> Total data dalam antrian verifikasi
                </p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card stat-card sc-warning">
                <div class="sc-top">
                    <div>
                        <p class="sc-label mb-1">Belum Diverifikasi</p>
                        <div class="sc-value">{{ $pendingCount ?? 0 }}</div>
                    </div>
                    <div class="sc-icon icon-warning">
                        <i class="dw dw-hourglass"></i>
                    </div>
                </div>
                <p style="font-size:.75rem;color:var(--muted);margin:0">
                    <i class="dw dw-clock mr-1"></i> Menunggu tindakan petugas
                </p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card stat-card sc-success">
                <div class="sc-top">
                    <div>
                        <p class="sc-label mb-1">Rata-rata Kelengkapan</p>
                        <div class="sc-value">{{ number_format($avgCompletion ?? 0, 1) }}%</div>
                    </div>
                    <div class="sc-icon icon-success">
                        <i class="dw dw-analytics-21"></i>
                    </div>
                </div>
                <div>
                    <div class="progress" style="height:6px;border-radius:4px;background:var(--border)">
                        <div class="progress-bar bg-success" style="width:{{ $avgCompletion ?? 0 }}%;border-radius:4px"></div>
                    </div>
                    <p style="font-size:.73rem;color:var(--success);font-weight:600;margin:5px 0 0">
                        @php
                            $avg = $avgCompletion ?? 0;
                            if ($avg >= 80) echo '<i class="dw dw-like mr-1"></i> Sangat Baik';
                            elseif ($avg >= 60) echo '<i class="dw dw-checked mr-1"></i> Cukup Baik';
                            elseif ($avg >= 40) echo '<i class="dw dw-hourglass mr-1"></i> Perlu Peningkatan';
                            else echo '<i class="dw dw-sad mr-1"></i> Kurang';
                        @endphp
                    </p>
                </div>
            </div>
        </div>

    </div>

    {{-- ── Main Table Card ──────────────────────────── --}}
    <div class="card mb-4">
        <div class="card-header">
            <h6 class="card-title">
                <i class="dw dw-table" style="color:var(--primary)"></i>
                Daftar Pengajuan KPR — Verifikasi Dokumen
                <span class="count-badge">{{ $pengajuan->total() }} Pengajuan</span>
            </h6>
            <button type="button" class="btn-expand" id="expandAll">
                <i class="dw dw-maximize"></i> Expand All
            </button>
        </div>
        <div class="card-body p-0 !important">
            <div class="table-responsive">
                <table class="table vd-table mb-0">
                    <thead>
                        <tr>
                            <th style="width:3%;text-align:center">No</th>
                            <th style="width:11%">Kode Pengajuan</th>
                            <th style="width:16%">Data Debitur</th>
                            <th style="width:16%">Properti &amp; KPR</th>
                            <th style="width:24%">Progress Verifikasi</th>
                            <th style="width:11%;text-align:center">Status</th>
                            <th style="width:10%">Tanggal</th>
                            <th style="width:9%;text-align:center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($pengajuan as $index => $item)
                    @php
                        $verifikasi  = $item->verifikasiMarketing;
                        $validCount  = $verifikasi ? $verifikasi->getTotalValidDocuments() : 0;
                        $totalDocs   = 7;
                        $progress    = $totalDocs > 0 ? round(($validCount / $totalDocs) * 100) : 0;
                        $progColor   = $progress >= 80 ? 'success' : ($progress >= 50 ? 'warning' : 'danger');

                        $dokumenStatus = [
                            'ktp'        => $verifikasi && $verifikasi->dok_ktp_valid,
                            'kk'         => $verifikasi && $verifikasi->dok_kk_valid,
                            'slip_gaji'  => $verifikasi && $verifikasi->dok_slip_gaji_valid,
                            'rek_koran'  => $verifikasi && $verifikasi->dok_rek_koran_valid,
                            'slik'       => $verifikasi && $verifikasi->dok_slik_valid,
                            'surat_kerja'=> $verifikasi && $verifikasi->dok_surat_kerja_valid,
                            'npwp'       => $verifikasi && $verifikasi->dok_npwp_valid,
                        ];

                        $docList = [
                            'ktp'        => 'KTP',
                            'kk'         => 'KK',
                            'slip_gaji'  => 'Slip Gaji',
                            'rek_koran'  => 'Rek Koran',
                            'slik'       => 'SLIK OJK',
                            'surat_kerja'=> 'Srt Kerja',
                            'npwp'       => 'NPWP',
                        ];
                    @endphp
                    <tr>
                        {{-- No --}}
                        <td style="text-align:center;color:var(--muted);font-weight:600">
                            {{ $pengajuan->firstItem() + $index }}
                        </td>

                        {{-- Kode --}}
                        <td>
                            <strong style="color:var(--primary)">{{ $item->kode_pengajuan }}</strong>
                            <div style="font-size:.72rem;color:var(--muted);margin-top:2px">#{{ $item->id }}</div>
                        </td>

                        {{-- Debitur --}}
                        <td>
                            <div class="d-flex align-items-start" style="gap:10px">
                                <div class="deb-avatar">{{ substr($item->user->nama_lengkap ?? 'N', 0, 1) }}</div>
                                <div>
                                    <div style="font-weight:600;font-size:.85rem">{{ $item->user->nama_lengkap ?? 'N/A' }}</div>
                                    <div style="font-size:.73rem;color:var(--muted);margin-top:2px">
                                        <i class="dw dw-phone"></i> {{ $item->debiturPribadi->no_hp ?? '-' }}
                                    </div>
                                    <div style="font-size:.73rem;color:var(--muted)">
                                        <i class="dw dw-id-card"></i> {{ substr($item->debiturPribadi->nik ?? '-', 0, 16) }}
                                    </div>
                                </div>
                            </div>
                        </td>

                        {{-- Properti --}}
                        <td>
                            <div style="font-weight:600;font-size:.85rem">{{ $item->unit->tipeUnit->proyek->nama_proyek ?? 'N/A' }}</div>
                            <div style="font-size:.73rem;color:var(--muted);margin-top:2px">
                                <i class="dw dw-building"></i> {{ $item->unit->tipeUnit->nama_tipe ?? '-' }}
                                &nbsp;·&nbsp;
                                <i class="dw dw-tag"></i> {{ $item->unit->kode_unit ?? '-' }}
                            </div>
                            <div style="margin-top:6px;padding-top:6px;border-top:1px dashed var(--border)">
                                <div style="font-size:.73rem;color:var(--muted)">
                                    <i class="dw dw-money"></i>
                                    <strong>{{ App\Helpers\MarketingHelper::formatRupiah($item->jumlah_pinjaman) }}</strong>
                                </div>
                                <div style="font-size:.73rem;color:var(--muted)">
                                    <i class="dw dw-clock"></i> Tenor {{ $item->tenor_tahun }} Tahun
                                </div>
                            </div>
                        </td>

                        {{-- Progress --}}
                        <td>
                            <div class="doc-progress-wrap">
                                <div class="dp-label">
                                    <span>Kelengkapan Dokumen</span>
                                    <span style="color:var(--{{ $progColor }})">{{ $validCount }}/{{ $totalDocs }} · {{ $progress }}%</span>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-{{ $progColor }}" style="width:{{ $progress }}%"></div>
                                </div>
                            </div>
                            <div class="doc-pills">
                                @foreach($docList as $key => $label)
                                    @if($dokumenStatus[$key])
                                        <span class="doc-pill dp-ok"><i class="dw dw-checked-circle"></i>{{ $label }}</span>
                                    @elseif($verifikasi && isset($verifikasi->{'dok_'.$key.'_valid'}) && $verifikasi->{'dok_'.$key.'_valid'} === false)
                                        <span class="doc-pill dp-bad"><i class="dw dw-close-circle"></i>{{ $label }}</span>
                                    @else
                                        <span class="doc-pill dp-pending"><i class="dw dw-hourglass"></i>{{ $label }}</span>
                                    @endif
                                @endforeach
                            </div>
                        </td>

                        {{-- Status --}}
                        <td style="text-align:center">
                            @if($verifikasi && $dokumenStatus['ktp'] && $dokumenStatus['kk'] && $dokumenStatus['slip_gaji'])
                                <span class="status-pill sp-lengkap"><i class="dw dw-checked"></i> Lengkap</span>
                            @elseif($verifikasi && ($dokumenStatus['ktp'] || $dokumenStatus['kk']))
                                <span class="status-pill sp-sebagian"><i class="dw dw-hourglass"></i> Sebagian</span>
                            @elseif($verifikasi && $verifikasi->dok_ktp_valid === false)
                                <span class="status-pill sp-invalid"><i class="dw dw-close"></i> Tidak Valid</span>
                            @else
                                <span class="status-pill sp-pending"><i class="dw dw-hourglass"></i> Belum Diperiksa</span>
                            @endif

                            @if($verifikasi && $verifikasi->rekomendasi_marketing)
                                <div class="mt-2">
                                    {!! App\Helpers\MarketingHelper::getRekomendasiBadge($verifikasi->rekomendasi_marketing) !!}
                                </div>
                            @endif
                        </td>

                        {{-- Tanggal --}}
                        <td>
                            <div style="font-weight:600;font-size:.82rem">
                                {{ \Carbon\Carbon::parse($item->tgl_marketing_proses ?? $item->created_at)->format('d/m/Y') }}
                            </div>
                            <div style="font-size:.75rem;color:var(--muted)">
                                {{ \Carbon\Carbon::parse($item->tgl_marketing_proses ?? $item->created_at)->format('H:i') }}
                            </div>
                            @if($item->tgl_marketing_proses)
                                <div style="font-size:.72rem;color:var(--success);margin-top:3px">
                                    <i class="dw dw-time"></i> {{ $item->tgl_marketing_proses->diffForHumans() }}
                                </div>
                            @else
                                <div style="font-size:.72rem;color:var(--muted);margin-top:3px">
                                    <i class="dw dw-hourglass"></i> Belum dimulai
                                </div>
                            @endif
                        </td>

                        {{-- Aksi --}}
                        <td style="text-align:center">
                            <div class="d-flex flex-column" style="gap:6px;align-items:center">
                                <a href="{{ route('marketing.verifikasi.dokumen.show', $item->id) }}" class="btn-vd verify">
                                    <i class="dw dw-edit-2"></i> Verifikasi
                                </a>
                                <a href="{{ route('marketing.pengajuan.show', $item->id) }}" class="btn-vd detail">
                                    <i class="dw dw-eye"></i> Detail
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8">
                            <div class="empty-state">
                                <i class="dw dw-folder"></i>
                                <h6>Tidak ada pengajuan yang perlu verifikasi dokumen</h6>
                                <p style="font-size:.82rem">Semua pengajuan sudah diverifikasi atau belum ada pengajuan baru</p>
                                <a href="{{ route('marketing.pengajuan.masuk') }}" class="btn-filter primary mt-2">
                                    <i class="dw dw-inbox"></i> Lihat Antrian Pengajuan
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($pengajuan->hasPages())
            <div class="d-flex align-items-center justify-content-between px-4 py-3"
                 style="border-top:1px solid var(--border)">
                <small style="color:var(--muted)">
                    Menampilkan <strong>{{ $pengajuan->firstItem() ?? 0 }}</strong>
                    – <strong>{{ $pengajuan->lastItem() ?? 0 }}</strong>
                    dari <strong>{{ $pengajuan->total() }}</strong> data
                </small>
                {{ $pengajuan->withQueryString()->links('pagination::bootstrap-4') }}
            </div>
            @endif
        </div>
    </div>

    {{-- ── Panduan Verifikasi ───────────────────────── --}}
    <div class="card mb-4">
        <div class="card-header">
            <h6 class="card-title">
                <i class="dw dw-info-circle" style="color:var(--primary)"></i>
                Panduan Verifikasi Dokumen KPR
            </h6>
        </div>
        <div class="card-body">
            <div class="row" style="row-gap:20px">

                {{-- Jenis Dokumen --}}
                <div class="col-md-6">
                    <p class="panduan-title">
    <span class="sc-icon icon-primary d-inline-flex align-items-center justify-content-center"
          style="width:28px;height:28px;border-radius:8px;font-size:.85rem">
        <i class="dw dw-file"></i>
    </span>
    Dokumen Wajib Diverifikasi
</p>
                    <table class="panduan-table">
                        <tr><td>1. KTP Debitur</td><td>Sesuai identitas, jelas, tidak expired</td></tr>
                        <tr><td>2. Kartu Keluarga (KK)</td><td>Sesuai KTP, data lengkap</td></tr>
                        <tr><td>3. Slip Gaji</td><td>3 bulan terakhir, sesuai penghasilan</td></tr>
                        <tr><td>4. Rekening Koran</td><td>3 bulan, mutasi sesuai penghasilan</td></tr>
                        <tr><td>5. Laporan SLIK OJK</td><td>Riwayat kredit lancar</td></tr>
                        <tr><td>6. Surat Keterangan Kerja</td><td>Legal dari perusahaan</td></tr>
                        <tr><td>7. NPWP</td><td>Wajib jika penghasilan &gt; Rp 4,5 jt/bulan</td></tr>
                    </table>
                </div>

                {{-- Status & Rekomendasi --}}
                <div class="col-md-6">
    <p class="panduan-title">
        <span class="sc-icon icon-success d-inline-flex align-items-center justify-content-center"
              style="width:28px;height:28px;border-radius:8px;font-size:.85rem">
            <i class="dw dw-file-135"></i>
        </span>
        Status Verifikasi
    </p>

    <table class="panduan-table">
        <tr>
            <td><span class="status-pill sp-lengkap" style="font-size:.68rem;padding:2px 10px">Valid</span></td>
            <td>Dokumen sesuai, jelas, dan masih berlaku</td>
        </tr>
        <tr>
            <td><span class="status-pill sp-invalid" style="font-size:.68rem;padding:2px 10px">Tidak Valid</span></td>
            <td>Tidak sesuai / blur / expired / palsu</td>
        </tr>
        <tr>
            <td><span class="status-pill sp-sebagian" style="font-size:.68rem;padding:2px 10px">Perlu Revisi</span></td>
            <td>Kurang lengkap / perlu perbaikan</td>
        </tr>
        <tr>
            <td><span class="status-pill sp-pending" style="font-size:.68rem;padding:2px 10px">Belum Diperiksa</span></td>
            <td>Dokumen belum diverifikasi</td>
        </tr>
    </table>

    <hr class="panduan-divider">

    <p class="panduan-title">
        <span class="sc-icon icon-primary d-inline-flex align-items-center justify-content-center"
              style="width:28px;height:28px;border-radius:8px;font-size:.85rem">
            <i class="dw dw-file-210"></i>
        </span>
        Rekomendasi Kelayakan
    </p>

    <table class="panduan-table">
        <tr>
            <td><span class="status-pill sp-lengkap" style="font-size:.68rem;padding:2px 10px">Layak</span></td>
            <td>Minimal 85% dokumen valid</td>
        </tr>
        <tr>
            <td><span class="status-pill sp-sebagian" style="font-size:.68rem;padding:2px 10px">Perlu Pertimbangan</span></td>
            <td>60–85% dokumen valid</td>
        </tr>
        <tr>
            <td><span class="status-pill sp-invalid" style="font-size:.68rem;padding:2px 10px">Tidak Layak</span></td>
            <td>Kurang dari 60% dokumen valid</td>
        </tr>
    </table>
</div>
            </div>

            <hr class="panduan-divider">
            <div class="info-tip">
                <i class="dw dw-bulb mr-2" style="color:var(--info)"></i>
                <strong>Tips:</strong> Prioritaskan verifikasi untuk pengajuan dengan tanggal <strong>tertua</strong> terlebih dahulu.
                Setelah verifikasi dokumen selesai, lanjutkan ke <strong>Verifikasi Lapangan</strong>.
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
$(document).ready(function () {
    $('#expandAll').on('click', function () {
        $('[data-toggle="tooltip"]').tooltip('show');
        setTimeout(function () {
            $('[data-toggle="tooltip"]').tooltip('hide');
        }, 2000);
    });
});
</script>
@endpush

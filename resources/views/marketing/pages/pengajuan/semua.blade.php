@extends('marketing.layouts.app')

@section('title', 'Antrian Pengajuan KPR Baru')

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
.page-top {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    margin-bottom: 26px;
}
.page-top-left h4 {
    font-size: 1.3rem;
    font-weight: 800;
    color: var(--text);
    margin: 0 0 4px;
    letter-spacing: -.3px;
}
.page-top-left p { font-size: .85rem; color: var(--muted); margin: 0; }

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
    white-space: nowrap;
}
.btn-back:hover { border-color: var(--primary); color: var(--primary); text-decoration: none; }

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

/* ── Stat cards ─────────────────────────────── */
.stat-card {
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 12px;
}
.stat-top { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; }
.stat-icon {
    width: 48px; height: 48px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.2rem; flex-shrink: 0;
}
.stat-label { font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .6px; color: var(--muted); margin-bottom: 3px; }
.stat-value { font-size: 2rem; font-weight: 800; color: var(--text); line-height: 1.1; letter-spacing: -1px; }
.stat-value.sm { font-size: 1.15rem; letter-spacing: -.3px; }
.stat-sub { font-size: .75rem; color: var(--muted); margin: 0; }

.sc-primary { border-top: 3px solid var(--primary) !important; }
.sc-info    { border-top: 3px solid var(--info)    !important; }
.sc-warning { border-top: 3px solid var(--warning) !important; }

.icon-primary { background: var(--primary-light); color: var(--primary); }
.icon-info    { background: var(--info-light);    color: var(--info);    }
.icon-warning { background: var(--warning-light); color: var(--warning); }

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
.filter-bar label { font-size: .78rem; font-weight: 600; color: var(--muted); margin-bottom: 5px; display: block; }
.btn-filter {
    display: inline-flex; align-items: center; gap: 6px;
    font-size: .82rem; font-weight: 600; border-radius: 9px;
    padding: 8px 16px; border: none; cursor: pointer; transition: all .2s;
    text-decoration: none;
}
.btn-filter.primary { background: var(--primary); color: #fff; }
.btn-filter.primary:hover { background: #3451d1; color: #fff; }
.btn-filter.reset { background: #f1f5f9; color: var(--muted); border: 1px solid var(--border); }
.btn-filter.reset:hover { background: #e2e8f0; }

/* ── Table ──────────────────────────────────── */
.aq-table { width: 100%; font-size: .84rem; }
.aq-table thead th {
    font-size: .7rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: .5px;
    color: var(--muted); background: #f8fafc;
    border-bottom: 1px solid var(--border) !important;
    border-top: none !important;
    padding: 12px 14px; white-space: nowrap;
}
.aq-table tbody td {
    padding: 14px 14px;
    border-color: var(--border) !important;
    vertical-align: middle;
}
.aq-table tbody tr:hover td { background: #fafbfe; }

/* avatar */
.deb-avatar {
    width: 36px; height: 36px; border-radius: 10px;
    background: var(--primary-light); color: var(--primary);
    font-weight: 700; font-size: .85rem;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}

/* tenor tag */
.tenor-tag {
    display: inline-block;
    font-size: .7rem; font-weight: 700;
    padding: 3px 9px; border-radius: 20px;
    background: var(--info-light); color: var(--info);
    margin-top: 3px;
}

/* action buttons */
.btn-act {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: .75rem; font-weight: 600;
    padding: 6px 12px; border-radius: 8px;
    border: none; cursor: pointer; transition: all .2s;
    text-decoration: none; white-space: nowrap;
}
.btn-act.detail   { background: #f1f5f9; color: var(--muted); }
.btn-act.detail:hover { background: #e2e8f0; color: var(--text); text-decoration: none; }
.btn-act.verify   { background: var(--success-light); color: var(--success); }
.btn-act.verify:hover { background: var(--success); color: #fff; text-decoration: none; }

/* export btn */
.btn-export {
    display: inline-flex; align-items: center; gap: 6px;
    font-size: .78rem; font-weight: 600;
    padding: 7px 14px; border-radius: 9px;
    background: var(--success-light); color: var(--success);
    border: 1px solid rgba(23,201,100,.25);
    text-decoration: none; transition: all .2s;
}
.btn-export:hover { background: var(--success); color: #fff; text-decoration: none; }

/* count badge */
.count-badge {
    font-size: .72rem; font-weight: 700; padding: 3px 10px;
    border-radius: 20px; background: var(--primary-light);
    color: var(--primary); margin-left: 4px;
}

/* ── Info banner ────────────────────────────── */
.info-banner {
    background: var(--primary-light);
    border: 1px solid rgba(67,97,238,.2);
    border-radius: var(--radius);
    padding: 16px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    font-size: .84rem;
    color: var(--text);
}
.info-banner strong { color: var(--primary); }
.priority-tag {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: .72rem; font-weight: 700; padding: 5px 12px;
    border-radius: 20px; background: var(--primary); color: #fff;
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

/* ── Empty state ────────────────────────────── */
.empty-state { text-align: center; padding: 52px 20px; color: var(--muted); }
.empty-state i { font-size: 2.5rem; opacity: .3; display: block; margin-bottom: 12px; }
.empty-state h6 { font-weight: 700; color: var(--text); margin-bottom: 6px; }

/* ── Animations ─────────────────────────────── */
@keyframes fadeUp {
    from { opacity:0; transform:translateY(14px); }
    to   { opacity:1; transform:translateY(0); }
}
.card { animation: fadeUp .4s ease both; }
.col-md-4:nth-child(2) .card { animation-delay: .07s; }
.col-md-4:nth-child(3) .card { animation-delay: .14s; }
</style>
@endpush

@section('content')
<div class="container-fluid px-3 py-2">

    {{-- ── Page Header ─────────────────────────────── --}}
    <div class="page-top">
        <div class="page-top-left">
            <h4><i class="dw dw-inbox mr-2" style="color:var(--primary)"></i>Antrian Pengajuan KPR Baru</h4>
            <p>Daftar pengajuan KPR yang menunggu verifikasi awal dari marketing</p>
        </div>
        <a href="{{ route('marketing.dashboard') }}" class="btn-back">
            <i class="dw dw-home"></i> Kembali ke Dashboard
        </a>
    </div>

    {{-- ── Filter Bar ───────────────────────────────── --}}
    <div class="card mb-4">
        <div class="filter-bar">
            <form method="GET" action="{{ route('marketing.pengajuan.masuk') }}">
                <div class="row align-items-end" style="row-gap:12px">
                    <div class="col-md-4">
                        <label><i class="dw dw-search mr-1"></i> Cari</label>
                        <input type="text" name="search" class="form-control"
                               placeholder="Kode pengajuan / nama debitur / NIK"
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
                            <a href="{{ route('marketing.pengajuan.masuk') }}" class="btn-filter reset">
                                <i class="dw dw-refresh"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Stat Cards ───────────────────────────────── --}}
    <div class="row mb-4" style="row-gap:14px">

        <div class="col-md-4">
            <div class="card stat-card sc-primary">
                <div class="stat-top">
                    <div>
                        <p class="stat-label mb-1">Total Antrian</p>
                        <div class="stat-value">{{ $antrian->total() }}</div>
                    </div>
                    <div class="stat-icon icon-primary">
                        <i class="dw dw-inbox"></i>
                    </div>
                </div>
                <p class="stat-sub"><i class="dw dw-analytics mr-1"></i> Pengajuan menunggu verifikasi</p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card stat-card sc-info">
                <div class="stat-top">
                    <div>
                        <p class="stat-label mb-1">Total Plafon KPR</p>
                        <div class="stat-value sm">{{ App\Helpers\MarketingHelper::formatRupiah($totalPlafon ?? 0) }}</div>
                    </div>
                    <div class="stat-icon icon-info">
                        <i class="dw dw-building"></i>
                    </div>
                </div>
                <p class="stat-sub"><i class="dw dw-money mr-1"></i> Akumulasi plafon dalam antrian</p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card stat-card sc-warning">
                <div class="stat-top">
                    <div>
                        <p class="stat-label mb-1">Pengajuan Hari Ini</p>
                        <div class="stat-value">{{ $todayCount ?? 0 }}</div>
                    </div>
                    <div class="stat-icon icon-warning">
                        <i class="dw dw-calendar"></i>
                    </div>
                </div>
                <p class="stat-sub"><i class="dw dw-clock mr-1"></i> Masuk pada hari ini</p>
            </div>
        </div>

    </div>

    {{-- ── Table Card ───────────────────────────────── --}}
    <div class="card mb-4">
        <div class="card-header">
            <h6 class="card-title">
                <i class="dw dw-table" style="color:var(--primary)"></i>
                Daftar Pengajuan KPR
                <span class="count-badge">{{ $antrian->total() }} Pengajuan</span>
            </h6>
            <a href="{{ route('marketing.pengajuan.masuk.export') }}?{{ http_build_query(request()->all()) }}"
               class="btn-export">
                <i class="dw dw-download"></i> Export Excel
            </a>
        </div>
        <div class="card-body p-0 !important">
            <div class="table-responsive">
                <table class="table aq-table mb-0">
                    <thead>
                        <tr>
                            <th style="width:4%;text-align:center">No</th>
                            <th style="width:11%">Kode Pengajuan</th>
                            <th style="width:17%">Data Debitur</th>
                            <th style="width:16%">Properti</th>
                            <th style="width:14%">Detail KPR</th>
                            <th style="width:11%">Tanggal</th>
                            <th style="width:11%;text-align:center">Status</th>
                            <th style="width:10%;text-align:center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($antrian as $index => $item)
                    <tr>
                        {{-- No --}}
                        <td style="text-align:center;color:var(--muted);font-weight:600">
                            {{ $antrian->firstItem() + $index }}
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
                                        <i class="dw dw-id-card"></i> {{ $item->debiturPribadi->nik ?? '-' }}
                                    </div>
                                </div>
                            </div>
                        </td>

                        {{-- Properti --}}
                        <td>
                            <div style="font-weight:600;font-size:.85rem">{{ $item->unit->tipeUnit->proyek->nama_proyek ?? 'N/A' }}</div>
                            <div style="font-size:.73rem;color:var(--muted);margin-top:2px">
                                <i class="dw dw-building"></i> {{ $item->unit->tipeUnit->nama_tipe ?? '-' }}
                            </div>
                            <div style="font-size:.73rem;color:var(--muted)">
                                <i class="dw dw-tag"></i> Unit: {{ $item->unit->kode_unit ?? '-' }}
                            </div>
                        </td>

                        {{-- Detail KPR --}}
                        <td>
                            <div style="font-size:.73rem;color:var(--muted)">Harga Properti</div>
                            <div style="font-weight:600;font-size:.82rem">
                                {{ App\Helpers\MarketingHelper::formatRupiah($item->harga_properti) }}
                            </div>
                            <div style="font-size:.73rem;color:var(--muted);margin-top:6px">Plafon KPR</div>
                            <div style="font-weight:700;font-size:.82rem;color:var(--primary)">
                                {{ App\Helpers\MarketingHelper::formatRupiah($item->jumlah_pinjaman) }}
                            </div>
                            <span class="tenor-tag">{{ $item->tenor_tahun }} Tahun</span>
                        </td>

                        {{-- Tanggal --}}
                        <td>
                            <div style="font-weight:600;font-size:.82rem">
                                {{ \Carbon\Carbon::parse($item->tgl_submitted)->format('d/m/Y') }}
                            </div>
                            <div style="font-size:.75rem;color:var(--muted)">
                                {{ \Carbon\Carbon::parse($item->tgl_submitted)->format('H:i') }}
                            </div>
                            <div style="font-size:.72rem;color:var(--muted);margin-top:3px">
                                <i class="dw dw-time"></i>
                                {{ \Carbon\Carbon::parse($item->tgl_submitted)->diffForHumans() }}
                            </div>
                        </td>

                        {{-- Status --}}
                        <td style="text-align:center">
                            {!! App\Helpers\MarketingHelper::getStatusBadge($item->status) !!}
                            <div style="font-size:.7rem;color:var(--muted);margin-top:5px">
                                <i class="dw dw-user"></i> Menunggu Verifikasi
                            </div>
                        </td>

                        {{-- Aksi --}}
                        <td style="text-align:center">
                            <div class="d-flex flex-column" style="gap:6px;align-items:center">
                                <a href="{{ route('marketing.pengajuan.show', $item->id) }}"
                                   class="btn-act detail">
                                    <i class="dw dw-eye"></i> Detail
                                </a>
                                <a href="{{ route('marketing.pengajuan.start-verifikasi', $item->id) }}"
                                   class="btn-act verify"
                                   onclick="return confirm('Mulai verifikasi pengajuan KPR ini?')">
                                    <i class="dw dw-edit-2"></i> Verifikasi
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8">
                            <div class="empty-state">
                                <i class="dw dw-inbox"></i>
                                <h6>Tidak ada antrian pengajuan KPR</h6>
                                <p style="font-size:.82rem">Semua pengajuan sudah diproses atau belum ada pengajuan baru</p>
                                <a href="{{ route('marketing.dashboard') }}" class="btn-filter primary mt-2">
                                    <i class="dw dw-home"></i> Kembali ke Dashboard
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($antrian->hasPages())
            <div class="d-flex align-items-center justify-content-between px-4 py-3"
                 style="border-top:1px solid var(--border)">
                <small style="color:var(--muted)">
                    Menampilkan <strong>{{ $antrian->firstItem() ?? 0 }}</strong>
                    – <strong>{{ $antrian->lastItem() ?? 0 }}</strong>
                    dari <strong>{{ $antrian->total() }}</strong> data
                </small>
                {{ $antrian->withQueryString()->links('pagination::bootstrap-4') }}
            </div>
            @endif
        </div>
    </div>

    {{-- ── Info Banner ──────────────────────────────── --}}
    @if($antrian->total() > 0)
    <div class="info-banner mb-4">
        <div>
            <i class="dw dw-info-circle mr-2" style="color:var(--primary)"></i>
            Terdapat <strong>{{ $antrian->total() }} pengajuan KPR</strong> yang perlu diverifikasi.
            Klik tombol <strong style="color:var(--success)">"Verifikasi"</strong> untuk memulai proses verifikasi dokumen dan lapangan.
        </div>
        <span class="priority-tag">
            <i class="dw dw-file"></i> Prioritaskan berdasarkan tanggal
        </span>
    </div>
    @endif

</div>
@endsection

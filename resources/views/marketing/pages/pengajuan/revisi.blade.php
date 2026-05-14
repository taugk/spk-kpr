@extends('marketing.layouts.app')

@section('title', 'Pengajuan Perlu Revisi')

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
    --revision:     #f97316;
    --revision-light:#fff7ed;
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

.sc-revision { border-top: 3px solid var(--revision) !important; }
.icon-revision { background: var(--revision-light); color: var(--revision); }

.filter-bar { padding: 18px 20px !important; }
.filter-bar .form-control {
    border: 1px solid var(--border);
    border-radius: 9px;
    font-size: .85rem;
    padding: 8px 12px;
    height: auto;
}
.filter-bar .form-control:focus { border-color: var(--primary); outline: none; box-shadow: none; }
.filter-bar label { font-size: .78rem; font-weight: 600; color: var(--muted); margin-bottom: 5px; display: block; }

.btn-filter {
    display: inline-flex; align-items: center; gap: 6px;
    font-size: .82rem; font-weight: 600; border-radius: 9px;
    padding: 8px 16px; border: none; cursor: pointer;
    text-decoration: none;
}
.btn-filter.primary { background: var(--primary); color: #fff; }
.btn-filter.primary:hover { background: #3451d1; color: #fff; }
.btn-filter.reset { background: #f1f5f9; color: var(--muted); border: 1px solid var(--border); }
.btn-filter.reset:hover { background: #e2e8f0; }

.aq-table { width: 100%; font-size: .84rem; }
.aq-table thead th {
    font-size: .7rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: .5px;
    color: var(--muted); background: #f8fafc;
    border-bottom: 1px solid var(--border) !important;
    padding: 12px 14px; white-space: nowrap;
}
.aq-table tbody td { padding: 14px 14px; border-color: var(--border) !important; vertical-align: middle; }
.aq-table tbody tr:hover td { background: #fafbfe; }

.deb-avatar {
    width: 36px; height: 36px; border-radius: 10px;
    background: var(--primary-light); color: var(--primary);
    font-weight: 700; font-size: .85rem;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}

.btn-act {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: .75rem; font-weight: 600;
    padding: 6px 12px; border-radius: 8px;
    text-decoration: none; white-space: nowrap;
}
.btn-act.detail { background: #f1f5f9; color: var(--muted); }
.btn-act.detail:hover { background: #e2e8f0; color: var(--text); text-decoration: none; }
.btn-act.revise { background: var(--revision-light); color: var(--revision); }
.btn-act.revise:hover { background: var(--revision); color: #fff; text-decoration: none; }

.count-badge {
    font-size: .72rem; font-weight: 700; padding: 3px 10px;
    border-radius: 20px; background: var(--revision-light);
    color: var(--revision); margin-left: 4px;
}

.empty-state { text-align: center; padding: 52px 20px; color: var(--muted); }
.empty-state i { font-size: 2.5rem; opacity: .3; display: block; margin-bottom: 12px; }
.empty-state h6 { font-weight: 700; color: var(--text); margin-bottom: 6px; }

@keyframes fadeUp {
    from { opacity:0; transform:translateY(14px); }
    to   { opacity:1; transform:translateY(0); }
}
.card { animation: fadeUp .4s ease both; }
</style>
@endpush

@section('content')
<div class="container-fluid px-3 py-2">

    <div class="page-top">
        <div class="page-top-left">
            <h4><i class="dw dw-refresh mr-2" style="color:var(--revision)"></i>Pengajuan Perlu Revisi</h4>
            <p>Daftar pengajuan KPR yang memerlukan revisi dari debitur</p>
        </div>
        <a href="{{ route('marketing.dashboard') }}" class="btn-back">
            <i class="dw dw-home"></i> Kembali ke Dashboard
        </a>
    </div>

    {{-- Filter Bar --}}
    <div class="card mb-4">
        <div class="filter-bar">
            <form method="GET" action="{{ route('marketing.pengajuan.data.revisi') }}">
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
                            <a href="{{ route('marketing.pengajuan.data.revisi') }}" class="btn-filter reset">
                                <i class="dw dw-refresh"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Stat Cards --}}
    <div class="row mb-4" style="row-gap:14px">
        <div class="col-md-4">
            <div class="card stat-card sc-revision">
                <div class="stat-top">
                    <div>
                        <p class="stat-label mb-1">Total Perlu Revisi</p>
                        <div class="stat-value">{{ $pengajuan->total() ?? 0 }}</div>
                    </div>
                    <div class="stat-icon icon-revision">
                        <i class="dw dw-refresh"></i>
                    </div>
                </div>
                <p class="stat-sub"><i class="dw dw-clock mr-1"></i> Menunggu revisi dari debitur</p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card stat-card">
                <div class="stat-top">
                    <div>
                        <p class="stat-label mb-1">Total Plafon KPR</p>
                        <div class="stat-value sm">{{ App\Helpers\MarketingHelper::formatRupiah($totalPlafon ?? 0) }}</div>
                    </div>
                    <div class="stat-icon icon-info">
                        <i class="dw dw-money"></i>
                    </div>
                </div>
                <p class="stat-sub"><i class="dw dw-building mr-1"></i> Akumulasi plafon revisi</p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card stat-card">
                <div class="stat-top">
                    <div>
                        <p class="stat-label mb-1">Rata-rata Tenor</p>
                        <div class="stat-value sm">{{ $avgTenor ?? 0 }} <small style="font-size:.8rem">Tahun</small></div>
                    </div>
                    <div class="stat-icon icon-primary">
                        <i class="dw dw-time"></i>
                    </div>
                </div>
                <p class="stat-sub"><i class="dw dw-analytics mr-1"></i> Rata-rata jangka waktu KPR</p>
            </div>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="card mb-4">
        <div class="card-header">
            <h6 class="card-title">
                <i class="dw dw-refresh" style="color:var(--revision)"></i>
                Daftar Pengajuan Perlu Revisi
                <span class="count-badge">{{ $pengajuan->total() ?? 0 }} Pengajuan</span>
            </h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table aq-table mb-0">
                    <thead>
                        <tr>
                            <th style="width:4%">No</th>
                            <th style="width:12%">Kode Pengajuan</th>
                            <th style="width:18%">Data Debitur</th>
                            <th style="width:16%">Properti</th>
                            <th style="width:14%">Detail KPR</th>
                            <th style="width:10%">Tgl Revisi</th>
                            <th style="width:10%">Status</th>
                            <th style="width:10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($pengajuan as $index => $item)
                    <tr>
                        <td style="color:var(--muted);font-weight:600">
                            {{ $pengajuan->firstItem() + $index }}
                        </td>
                        <td>
                            <strong style="color:var(--revision)">{{ $item->kode_pengajuan }}</strong>
                            <div style="font-size:.72rem;color:var(--muted);margin-top:2px">#{{ $item->id }}</div>
                        </td>
                        <td>
                            <div class="d-flex align-items-start" style="gap:10px">
                                <div class="deb-avatar">{{ substr($item->user->nama_lengkap ?? 'N', 0, 1) }}</div>
                                <div>
                                    <div style="font-weight:600;font-size:.85rem">{{ $item->user->nama_lengkap ?? 'N/A' }}</div>
                                    <div style="font-size:.73rem;color:var(--muted)">NIK: {{ $item->debiturPribadi->nik ?? '-' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div style="font-weight:600;font-size:.85rem">{{ $item->unit->tipeUnit->proyek->nama_proyek ?? 'N/A' }}</div>
                            <div style="font-size:.73rem;color:var(--muted)">Tipe: {{ $item->unit->tipeUnit->nama_tipe ?? '-' }}</div>
                            <div style="font-size:.73rem;color:var(--muted)">Unit: {{ $item->unit->kode_unit ?? '-' }}</div>
                        </td>
                        <td>
                            <div style="font-weight:700;font-size:.82rem;color:var(--primary)">
                                {{ App\Helpers\MarketingHelper::formatRupiah($item->jumlah_pinjaman) }}
                            </div>
                            <span class="tenor-tag" style="background:var(--revision-light);color:var(--revision)">{{ $item->tenor_tahun }} Tahun</span>
                        </td>
                        <td>
                            <div style="font-weight:600;font-size:.82rem">
                                {{ \Carbon\Carbon::parse($item->updated_at)->format('d/m/Y') }}
                            </div>
                            <div style="font-size:.75rem;color:var(--muted)">{{ \Carbon\Carbon::parse($item->updated_at)->format('H:i') }}</div>
                        </td>
                        <td>
                            <span class="badge badge-warning" style="background:var(--revision-light);color:var(--revision);padding:5px 10px;border-radius:20px">
                                <i class="dw dw-refresh"></i> Perlu Revisi
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('marketing.pengajuan.show', $item->id) }}" class="btn-act detail">
                                <i class="dw dw-eye"></i> Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="empty-state">
                                    <i class="dw dw-refresh"></i>
                                    <h6>Tidak ada pengajuan yang perlu revisi</h6>
                                    <p>Semua pengajuan dalam status baik</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            @if($pengajuan->hasPages())
            <div class="d-flex align-items-center justify-content-between px-4 py-3" style="border-top:1px solid var(--border)">
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

</div>
@endsection

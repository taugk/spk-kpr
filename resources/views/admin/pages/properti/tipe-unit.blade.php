@extends('admin.layouts.app')

@section('title', 'Manajemen Tipe Unit' . ($proyek ? ' — ' . $proyek->nama_proyek : ''))

@section('page_action')
    <div class="d-flex flex-wrap align-items-center">
        @if($proyek)
            <a href="{{ route('admin.properti.index') }}" class="btn btn-light btn-back mr-2">
                <i class="dw dw-left-arrow mr-1"></i> Kembali
            </a>
        @endif
        <a href="{{ route('admin.properti.tipe-unit.create', $proyekId ? ['proyek_id' => $proyekId] : []) }}"
           class="btn btn-primary btn-action">
            <i class="dw dw-add mr-1"></i> Tambah Tipe Unit
        </a>
    </div>
@endsection

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    * { font-family: 'Plus Jakarta Sans', sans-serif; }

    /* ── Page Header Context ── */
    .context-bar {
        background: #fff;
        border: 1px solid #f0f0f5;
        border-radius: 16px;
        padding: 18px 24px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        box-shadow: 0 2px 12px rgba(0,0,0,.05);
    }
    .context-bar-left { display: flex; align-items: center; gap: 14px; }
    .context-icon {
        width: 48px; height: 48px; border-radius: 14px;
        background: #eff2ff; color: #4361ee;
        display: flex; align-items: center; justify-content: center;
        font-size: 22px; flex-shrink: 0;
    }
    .context-title { font-size: 16px; font-weight: 800; color: #1a1d2e; margin: 0; line-height: 1.3; }
    .context-sub   { font-size: 12px; color: #9ca3b0; margin: 3px 0 0; }
    .context-meta  { display: flex; align-items: center; gap: 16px; flex-wrap: wrap; }
    .context-meta-item { font-size: 12px; color: #9ca3b0; display: flex; align-items: center; gap: 5px; }
    .context-meta-item strong { font-size: 15px; font-weight: 800; color: #1a1d2e; }

    /* ── Filter Bar ── */
    .filter-bar {
        background: #fff;
        border: 1px solid #f0f0f5;
        border-radius: 14px;
        padding: 14px 18px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        box-shadow: 0 2px 8px rgba(0,0,0,.04);
    }
    .filter-search {
        flex: 1; min-width: 200px;
        position: relative;
    }
    .filter-search input {
        width: 100%;
        border: 1px solid #e8eaf0;
        border-radius: 10px;
        padding: 9px 14px 9px 38px;
        font-size: 13px; font-weight: 500;
        color: #374151;
        background: #fafbff;
        outline: none;
        transition: border .2s, box-shadow .2s;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }
    .filter-search input:focus {
        border-color: #4361ee;
        box-shadow: 0 0 0 3px rgba(67,97,238,.12);
        background: #fff;
    }
    .filter-search .search-icon {
        position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
        color: #9ca3b0; font-size: 15px; pointer-events: none;
    }
    .filter-count {
        font-size: 12px; color: #9ca3b0; font-weight: 500; white-space: nowrap;
    }
    .filter-count strong { color: #1a1d2e; font-weight: 700; }

    /* ── Grid View Toggle ── */
    .view-toggle { display: flex; gap: 4px; }
    .view-btn {
        width: 34px; height: 34px; border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        border: 1px solid #e8eaf0; background: #fff; color: #9ca3b0;
        cursor: pointer; transition: all .16s; font-size: 14px;
    }
    .view-btn.active { background: #4361ee; color: #fff; border-color: #4361ee; }
    .view-btn:hover:not(.active) { background: #f5f5f7; color: #374151; }

    /* ── Card Grid ── */
    .tipe-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
        margin-bottom: 24px;
    }

    .tipe-card {
        background: #fff;
        border: 1px solid #f0f0f5;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,.05);
        transition: all .22s;
        display: flex;
        flex-direction: column;
    }
    .tipe-card:hover {
        box-shadow: 0 10px 30px rgba(0,0,0,.10);
        transform: translateY(-3px);
        border-color: #dde1f5;
    }

    .tipe-card-img {
        position: relative;
        height: 175px;
        overflow: hidden;
        background: #f3f4f8;
        flex-shrink: 0;
    }
    .tipe-card-img img {
        width: 100%; height: 100%;
        object-fit: cover;
        display: block;
        transition: transform .4s;
    }
    .tipe-card:hover .tipe-card-img img { transform: scale(1.06); }
    .tipe-card-img-overlay {
        position: absolute; inset: 0;
        background: linear-gradient(to top, rgba(10,12,40,.5) 0%, transparent 55%);
    }
    .tipe-card-price {
        position: absolute; bottom: 12px; left: 12px;
        font-size: 14px; font-weight: 800; color: #fff;
        text-shadow: 0 1px 4px rgba(0,0,0,.4);
    }
    .tipe-card-stok {
        position: absolute; top: 10px; right: 10px;
        font-size: 11px; font-weight: 700;
        padding: 3px 10px; border-radius: 20px;
        backdrop-filter: blur(6px);
    }
    .tipe-card-stok.ok    { background: rgba(12,166,120,.85); color: #fff; }
    .tipe-card-stok.empty { background: rgba(224,49,49,.85);  color: #fff; }

    .tipe-card-body { padding: 16px; flex: 1; display: flex; flex-direction: column; }
    .tipe-card-name { font-size: 15px; font-weight: 800; color: #1a1d2e; margin: 0 0 3px; line-height: 1.3; }
    .tipe-card-code {
        display: inline-block; font-size: 11px; font-weight: 600;
        color: #6366f1; background: #eef2ff;
        padding: 2px 8px; border-radius: 6px; letter-spacing: .4px; margin-bottom: 12px;
    }

    .tipe-card-specs {
        display: grid; grid-template-columns: 1fr 1fr;
        gap: 8px; margin-bottom: 14px;
    }
    .spec-item {
        background: #fafbff; border: 1px solid #f0f0f5;
        border-radius: 9px; padding: 8px 10px;
    }
    .spec-label { font-size: 10px; font-weight: 700; color: #9ca3b0; text-transform: uppercase; letter-spacing: .4px; display: block; margin-bottom: 2px; }
    .spec-value { font-size: 13px; font-weight: 700; color: #374151; }

    .tipe-card-proyek {
        font-size: 12px; color: #9ca3b0; margin-bottom: 14px;
        display: flex; align-items: center; gap: 5px;
    }
    .tipe-card-proyek i { color: #4361ee; }

    .tipe-card-actions {
        display: flex; gap: 8px; margin-top: auto;
    }
    .btn-card-action {
        flex: 1; padding: 8px 0; border-radius: 10px;
        font-size: 12px; font-weight: 700; text-align: center;
        border: 1px solid; cursor: pointer; transition: all .16s;
        display: flex; align-items: center; justify-content: center; gap: 5px;
        text-decoration: none;
    }
    .btn-card-action.view   { background: #eff2ff; border-color: #dde3fb; color: #4361ee; }
    .btn-card-action.edit   { background: #fff8e1; border-color: #fde68a; color: #d97706; }
    .btn-card-action.delete { background: #fff0f0; border-color: #fecaca; color: #e03131; }
    .btn-card-action:hover  { filter: brightness(.94); text-decoration: none; }

    /* ── Table View ── */
    .table-card {
        background: #fff;
        border: 1px solid #f0f0f5;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 2px 12px rgba(0,0,0,.05);
        margin-bottom: 24px;
    }
    .tipe-table { width: 100%; border-collapse: collapse; }
    .tipe-table thead tr th {
        font-size: 11px; font-weight: 700; text-transform: uppercase;
        letter-spacing: .5px; color: #9ca3b0; background: #fafbff;
        padding: 12px 14px; border-bottom: 1px solid #f0f0f5; white-space: nowrap;
    }
    .tipe-table tbody tr td {
        padding: 13px 14px; font-size: 13px; border-bottom: 1px solid #f8f9fb;
        vertical-align: middle; color: #374151;
    }
    .tipe-table tbody tr:last-child td { border-bottom: none; }
    .tipe-table tbody tr:hover td { background: #fafbff; }
    .tipe-table-thumb { width: 44px; height: 44px; border-radius: 10px; object-fit: cover; border: 2px solid #f0f0f5; }

    .tbl-name  { font-weight: 700; color: #1a1d2e; font-size: 13px; }
    .tbl-code  { font-size: 11px; color: #9ca3b0; margin-top: 1px; }
    .tbl-price { font-weight: 700; color: #4361ee; }
    .tbl-size  { color: #6b7280; }

    .stok-badge {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600;
    }
    .stok-badge .dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; display: inline-block; }
    .stok-badge.ok    { background: #edfff4; color: #0ca678; }
    .stok-badge.empty { background: #fff0f0; color: #e03131; }

    .tbl-actions { display: flex; gap: 5px; }
    .tbl-btn {
        width: 30px; height: 30px; border-radius: 8px;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 13px; border: none; cursor: pointer; transition: all .16s;
        background: #f5f5f7; color: #6b7280; text-decoration: none;
    }
    .tbl-btn.view:hover   { background: #eff2ff; color: #4361ee; }
    .tbl-btn.edit:hover   { background: #fff8e1; color: #d97706; }
    .tbl-btn.delete:hover { background: #fff0f0; color: #e03131; }

    /* ── Pagination ── */
    .pagination-wrap { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px; }
    .pagination-info { font-size: 13px; color: #9ca3b0; }
    .pagination .page-link {
        border-radius: 8px !important; margin: 0 2px;
        font-size: 13px; font-weight: 600; color: #374151;
        border: 1px solid #e8eaf0; padding: 6px 12px;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }
    .pagination .page-item.active .page-link { background: #4361ee; border-color: #4361ee; color: #fff; }
    .pagination .page-link:hover { background: #f5f5f7; color: #1a1d2e; }

    /* ── Empty State ── */
    .empty-state {
        text-align: center; padding: 60px 24px; background: #fff;
        border-radius: 16px; border: 2px dashed #e8eaf0;
    }
    .empty-state-icon {
        width: 72px; height: 72px; background: #f3f4f8; border-radius: 20px;
        display: flex; align-items: center; justify-content: center;
        font-size: 30px; color: #9ca3b0; margin: 0 auto 16px;
    }
    .empty-state h5 { font-weight: 800; color: #1a1d2e; margin-bottom: 6px; }
    .empty-state p  { color: #9ca3b0; font-size: 14px; }

    /* ── Modal ── */
    .detail-chip { background: #fafbff; border: 1px solid #f0f0f5; border-radius: 10px; padding: 10px 14px; }
    .detail-chip .chip-label {
        font-size: 11px; font-weight: 700; color: #9ca3b0;
        text-transform: uppercase; letter-spacing: .4px; display: block; margin-bottom: 3px;
    }
    .detail-chip .chip-value { font-size: 14px; font-weight: 700; color: #1a1d2e; }

    .btn-back {
        border-radius: 10px; font-size: 13px; font-weight: 600;
        border: 1px solid #e8eaf0; background: #fff; color: #374151;
        padding: 7px 16px;
    }
    .btn-back:hover { background: #f5f5f7; color: #1a1d2e; }

    /* ── Responsive ── */
    @media (max-width: 768px) {
        .tipe-grid { grid-template-columns: 1fr; }
        .context-bar { flex-direction: column; align-items: flex-start; }
        .filter-bar { flex-direction: column; align-items: stretch; }
        .filter-search { min-width: unset; }
        .tipe-table thead { display: none; }
        .tipe-table tbody tr { display: block; padding: 14px 16px; border-bottom: 1px solid #f3f4f8; }
        .tipe-table tbody tr td {
            display: flex; justify-content: space-between; align-items: center;
            padding: 4px 0; border: none; font-size: 13px;
        }
        .tipe-table tbody tr td::before {
            content: attr(data-label);
            font-size: 11px; font-weight: 700; color: #9ca3b0;
            text-transform: uppercase; letter-spacing: .4px; flex-shrink: 0; margin-right: 8px;
        }
    }
</style>
@endpush

@section('content')
<div class="min-height-200px">

    {{-- Context Bar (tampil jika dari proyek tertentu) --}}
    @if($proyek)
    <div class="context-bar">
        <div class="context-bar-left">
            <div class="context-icon"><i class="dw dw-building"></i></div>
            <div>
                <p class="context-title">{{ $proyek->nama_proyek }}</p>
                <p class="context-sub">
                    <i class="dw dw-pin" style="color:#e03131; margin-right:3px;"></i>
                    {{ $proyek->lokasi }}
                    &nbsp;·&nbsp;
                    <code style="font-size:11px; color:#6366f1; background:#eef2ff; padding:1px 6px; border-radius:4px;">
                        {{ $proyek->kode_proyek }}
                    </code>
                    &nbsp;·&nbsp;
                    <span class="badge badge-pill
                        {{ $proyek->status === 'aktif' ? 'badge-success' : ($proyek->status === 'tutup' ? 'badge-warning' : 'badge-secondary') }}"
                        style="font-size:11px;">
                        {{ ucfirst($proyek->status) }}
                    </span>
                </p>
            </div>
        </div>
        <div class="context-meta">
            <div class="context-meta-item">
                <strong>{{ $tipeUnit->total() }}</strong> Tipe Unit
            </div>
            <div class="context-meta-item">
                <strong>{{ $tipeUnit->sum('stok_tersedia') }}</strong> Total Stok
            </div>
        </div>
    </div>
    @endif

    {{-- Filter Bar --}}
    <div class="filter-bar">
        <form method="GET" action="{{ route('admin.properti.tipe-unit') }}"
              class="d-flex align-items-center flex-wrap" style="flex:1; gap:10px;">
            @if($proyekId)
                <input type="hidden" name="proyek_id" value="{{ $proyekId }}">
            @endif

            <div class="filter-search">
                <i class="dw dw-search search-icon"></i>
                <input type="text" name="search"
                       value="{{ request('search') }}"
                       placeholder="Cari nama tipe atau kode…">
            </div>

            @if(!$proyekId)
            <select name="proyek_id_filter"
                    onchange="this.form.submit()"
                    style="border:1px solid #e8eaf0; border-radius:10px; padding:9px 14px;
                           font-size:13px; font-weight:500; color:#374151; background:#fafbff;
                           outline:none; font-family:'Plus Jakarta Sans',sans-serif; cursor:pointer;">
                <option value="">Semua Proyek</option>
                @foreach(DB::table('proyek')->orderBy('nama_proyek')->get() as $p)
                    <option value="{{ $p->id }}" {{ request('proyek_id_filter') == $p->id ? 'selected' : '' }}>
                        {{ $p->nama_proyek }}
                    </option>
                @endforeach
            </select>
            @endif

            <button type="submit" class="btn btn-primary" style="border-radius:10px; font-size:13px; font-weight:700; padding:9px 18px;">
                <i class="dw dw-search mr-1"></i> Cari
            </button>
            @if(request('search') || request('proyek_id_filter'))
                <a href="{{ route('admin.properti.tipe-unit', $proyekId ? ['proyek_id' => $proyekId] : []) }}"
                   class="btn btn-light" style="border-radius:10px; font-size:13px; font-weight:600; border:1px solid #e8eaf0;">
                    Reset
                </a>
            @endif
        </form>

        <div class="d-flex align-items-center" style="gap:10px;">
            <span class="filter-count">
                <strong>{{ $tipeUnit->total() }}</strong> tipe unit
            </span>
            <div class="view-toggle">
                <button class="view-btn active" id="btnGrid" onclick="setView('grid')" title="Grid View">
                    <i class="dw dw-grid"></i>
                </button>
                <button class="view-btn" id="btnTable" onclick="setView('table')" title="Table View">
                    <i class="dw dw-menu"></i>
                </button>
            </div>
        </div>
    </div>

    {{-- Content --}}
    @if($tipeUnit->isEmpty())
        <div class="empty-state">
            <div class="empty-state-icon"><i class="dw dw-building"></i></div>
            <h5>Belum Ada Tipe Unit</h5>
            <p>{{ $proyek ? 'Proyek ini belum memiliki tipe unit.' : 'Belum ada tipe unit yang ditambahkan.' }}</p>
            <a href="{{ route('admin.properti.tipe-unit.create', $proyekId ? ['proyek_id' => $proyekId] : []) }}"
               class="btn btn-primary mt-2">
                <i class="dw dw-add mr-1"></i> Tambah Tipe Unit
            </a>
        </div>
    @else

        {{-- ── GRID VIEW ── --}}
        <div id="viewGrid" class="tipe-grid">
            @foreach($tipeUnit as $tipe)
                @php
                    $gambarArr = json_decode($tipe->gambar ?? '[]', true) ?? [];
                    $gambarUrl = !empty($gambarArr[0]) && Storage::disk('public')->exists($gambarArr[0])
                                 ? asset('storage/' . $gambarArr[0])
                                 : asset('deskapp/vendors/images/img2.jpg');
                @endphp
                <div class="tipe-card">
                    <div class="tipe-card-img">
                        <img src="{{ $gambarUrl }}"
                             alt="{{ $tipe->nama_tipe }}"
                             onerror="this.src='{{ asset('deskapp/vendors/images/img2.jpg') }}'">
                        <div class="tipe-card-img-overlay"></div>
                        <div class="tipe-card-price">Rp {{ number_format($tipe->harga, 0, ',', '.') }}</div>
                        <span class="tipe-card-stok {{ $tipe->stok_tersedia > 0 ? 'ok' : 'empty' }}">
                            {{ $tipe->stok_tersedia > 0 ? $tipe->stok_tersedia . ' unit' : 'Habis' }}
                        </span>
                    </div>
                    <div class="tipe-card-body">
                        <h6 class="tipe-card-name">{{ $tipe->nama_tipe }}</h6>
                        <span class="tipe-card-code">{{ $tipe->kode_tipe }}</span>

                        @if(!$proyek)
                        <div class="tipe-card-proyek">
                            <i class="dw dw-building"></i> {{ $tipe->nama_proyek }}
                        </div>
                        @endif

                        <div class="tipe-card-specs">
                            <div class="spec-item">
                                <span class="spec-label">Luas Tanah</span>
                                <span class="spec-value">{{ $tipe->luas_tanah }} m²</span>
                            </div>
                            <div class="spec-item">
                                <span class="spec-label">Luas Bangunan</span>
                                <span class="spec-value">{{ $tipe->luas_bangunan }} m²</span>
                            </div>
                            @if($tipe->jumlah_kamar !== null)
                            <div class="spec-item">
                                <span class="spec-label">Kamar Tidur</span>
                                <span class="spec-value">{{ $tipe->jumlah_kamar }} KT</span>
                            </div>
                            @endif
                            @if($tipe->jumlah_wc !== null)
                            <div class="spec-item">
                                <span class="spec-label">Kamar Mandi</span>
                                <span class="spec-value">{{ $tipe->jumlah_wc }} KM</span>
                            </div>
                            @endif
                        </div>

                        <div class="tipe-card-actions">
                            <button class="btn-card-action view" onclick="detailTipe({{ $tipe->id }})">
                                <i class="dw dw-eye"></i> Detail
                            </button>
                            <a href="{{ route('admin.properti.tipe-unit.edit', $tipe->id) }}"
                               class="btn-card-action edit">
                                <i class="dw dw-edit2"></i> Edit
                            </a>
                            <button class="btn-card-action delete delete-tipe-btn" data-id="{{ $tipe->id }}">
                                <i class="dw dw-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- ── TABLE VIEW ── --}}
        <div id="viewTable" class="table-card" style="display:none;">
            <div class="table-responsive">
                <table class="tipe-table">
                    <thead>
                        <tr>
                            <th style="width:54px; padding-left:18px;">Foto</th>
                            <th>Nama Tipe</th>
                            @if(!$proyek)<th>Proyek</th>@endif
                            <th>LT / LB</th>
                            <th>Kamar</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th style="text-align:right; padding-right:18px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tipeUnit as $tipe)
                            @php
                                $gambarArr = json_decode($tipe->gambar ?? '[]', true) ?? [];
                                $gambarUrl = !empty($gambarArr[0]) && Storage::disk('public')->exists($gambarArr[0])
                                             ? asset('storage/' . $gambarArr[0])
                                             : asset('deskapp/vendors/images/img2.jpg');
                            @endphp
                            <tr>
                                <td data-label="Foto" style="padding-left:18px;">
                                    <img src="{{ $gambarUrl }}"
                                         class="tipe-table-thumb"
                                         alt="{{ $tipe->nama_tipe }}"
                                         onerror="this.src='{{ asset('deskapp/vendors/images/img2.jpg') }}'">
                                </td>
                                <td data-label="Nama Tipe">
                                    <div class="tbl-name">{{ $tipe->nama_tipe }}</div>
                                    <div class="tbl-code">{{ $tipe->kode_tipe }}</div>
                                </td>
                                @if(!$proyek)
                                <td data-label="Proyek" style="color:#6b7280; font-size:13px;">
                                    {{ $tipe->nama_proyek }}
                                </td>
                                @endif
                                <td data-label="LT/LB" class="tbl-size">
                                    {{ $tipe->luas_tanah }} / {{ $tipe->luas_bangunan }} m²
                                </td>
                                <td data-label="Kamar" style="color:#6b7280;">
                                    @if($tipe->jumlah_kamar !== null || $tipe->jumlah_wc !== null)
                                        {{ $tipe->jumlah_kamar ?? '—' }} KT &nbsp;·&nbsp; {{ $tipe->jumlah_wc ?? '—' }} KM
                                    @else
                                        <span style="color:#d1d5db;">—</span>
                                    @endif
                                </td>
                                <td data-label="Harga" class="tbl-price">
                                    Rp {{ number_format($tipe->harga, 0, ',', '.') }}
                                </td>
                                <td data-label="Stok">
                                    <span class="stok-badge {{ $tipe->stok_tersedia > 0 ? 'ok' : 'empty' }}">
                                        <span class="dot"></span>
                                        {{ $tipe->stok_tersedia }} unit
                                    </span>
                                </td>
                                <td data-label="Aksi" style="padding-right:18px;">
                                    <div class="tbl-actions justify-content-end">
                                        <button class="tbl-btn view"
                                                onclick="detailTipe({{ $tipe->id }})" title="Detail">
                                            <i class="dw dw-eye"></i>
                                        </button>
                                        <a href="{{ route('admin.properti.tipe-unit.edit', $tipe->id) }}"
                                           class="tbl-btn edit" title="Edit">
                                            <i class="dw dw-edit2"></i>
                                        </a>
                                        <button class="tbl-btn delete delete-tipe-btn"
                                                data-id="{{ $tipe->id }}" title="Hapus">
                                            <i class="dw dw-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        @if($tipeUnit->hasPages())
        <div class="pagination-wrap">
            <span class="pagination-info">
                Menampilkan {{ $tipeUnit->firstItem() }}–{{ $tipeUnit->lastItem() }}
                dari {{ $tipeUnit->total() }} tipe unit
            </span>
            {{ $tipeUnit->links() }}
        </div>
        @endif

    @endif
</div>

{{-- Modal Detail Tipe Unit --}}
<div class="modal fade" id="detailTipeUnitModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius:16px; border:none; overflow:hidden; font-family:'Plus Jakarta Sans',sans-serif;">
            <div class="modal-header" style="border-bottom:1px solid #f0f0f5; padding:20px 24px;">
                <h5 class="modal-title" style="font-weight:800; font-size:16px; color:#1a1d2e;">Detail Tipe Unit</h5>
                <button type="button" class="close" data-dismiss="modal" style="color:#9ca3b0;">&times;</button>
            </div>
            <div class="modal-body" style="padding:24px;">
                <div class="row">
                    <div class="col-md-5 mb-3 mb-md-0">
                        <img id="modalGambar" src=""
                             style="width:100%; height:220px; object-fit:cover; border-radius:12px; background:#f3f4f8;"
                             onerror="this.src='{{ asset('deskapp/vendors/images/img2.jpg') }}'">
                        {{-- Thumbnail strip jika ada multiple gambar --}}
                        <div id="modalThumbStrip" class="d-flex mt-2" style="gap:6px; overflow-x:auto;"></div>
                    </div>
                    <div class="col-md-7">
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                            <div class="detail-chip" id="chipProyek"></div>
                            <div class="detail-chip" id="chipKode"></div>
                            <div class="detail-chip" id="chipNama"></div>
                            <div class="detail-chip" id="chipHarga"></div>
                            <div class="detail-chip" id="chipLT"></div>
                            <div class="detail-chip" id="chipLB"></div>
                            <div class="detail-chip" id="chipKamar"></div>
                            <div class="detail-chip" id="chipWC"></div>
                            <div class="detail-chip" id="chipStok"></div>
                            <div class="detail-chip" id="chipTerjual"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="border-top:1px solid #f0f0f5; padding:16px 24px;">
                <a href="{{ route('admin.properti.unit', []) }}" id="modalUnitLink"
                   class="btn btn-outline-primary" style="border-radius:10px; font-weight:700; font-size:13px;">
                    <i class="dw dw-home mr-1"></i> Lihat Unit
                </a>
                <a href="#" id="modalEditLink"
                   class="btn btn-warning" style="border-radius:10px; font-weight:700; font-size:13px;">
                    <i class="dw dw-edit2 mr-1"></i> Edit
                </a>
                <button type="button" class="btn btn-light" data-dismiss="modal"
                        style="border-radius:10px; font-weight:600; font-size:13px; border:1px solid #e8eaf0;">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// ── View Toggle (Grid / Table) ──
const VIEW_KEY = 'tipeUnitView';

function setView(mode) {
    localStorage.setItem(VIEW_KEY, mode);
    document.getElementById('viewGrid').style.display  = mode === 'grid'  ? '' : 'none';
    document.getElementById('viewTable').style.display = mode === 'table' ? '' : 'none';
    document.getElementById('btnGrid').classList.toggle('active',  mode === 'grid');
    document.getElementById('btnTable').classList.toggle('active', mode === 'table');
}

// Restore last view preference
(function () {
    const saved = localStorage.getItem(VIEW_KEY) || 'grid';
    setView(saved);
})();

// ── Chip Helper ──
function setChip(id, label, value) {
    document.getElementById(id).innerHTML =
        `<span class="chip-label">${label}</span><span class="chip-value">${value ?? '—'}</span>`;
}

$(document).ready(function () {

    // ── Delete Tipe Unit ──
    $(document).on('click', '.delete-tipe-btn', function (e) {
        e.preventDefault();
        const id  = $(this).data('id');
        const btn = $(this);

        Swal.fire({
            title: 'Hapus Tipe Unit?',
            text: 'Semua unit yang terkait dengan tipe ini juga akan ikut terhapus.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#e03131'
        }).then(result => {
            if (!result.isConfirmed) return;
            btn.prop('disabled', true);

            $.ajax({
                url: '/admin/properti/tipe-unit/' + id,
                method: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: res => {
                    if (res.success) {
                        Swal.fire({ title: 'Terhapus!', icon: 'success', timer: 1500, showConfirmButton: false })
                            .then(() => location.reload());
                    } else {
                        Swal.fire('Gagal', res.message || 'Gagal menghapus tipe unit.', 'error');
                        btn.prop('disabled', false);
                    }
                },
                error: xhr => {
                    Swal.fire('Error', xhr.responseJSON?.message || 'Terjadi kesalahan.', 'error');
                    btn.prop('disabled', false);
                }
            });
        });
    });

    // Flash message
    @if(session('success'))
        Swal.fire({ title: 'Berhasil!', text: '{{ session('success') }}', icon: 'success', timer: 2000, showConfirmButton: false });
    @endif
    @if(session('error'))
        Swal.fire({ title: 'Gagal!', text: '{{ session('error') }}', icon: 'error' });
    @endif
});

// ── Detail Tipe Unit ──
function detailTipe(id) {
    Swal.fire({ title: 'Memuat...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

    $.ajax({
        url: '/admin/properti/tipe-unit/' + id + '/detail',
        method: 'GET',
        success: function (r) {
            Swal.close();

            setChip('chipProyek',  'Proyek',        r.nama_proyek);
            setChip('chipKode',    'Kode Tipe',      r.kode_tipe);
            setChip('chipNama',    'Nama Tipe',      r.nama_tipe);
            setChip('chipHarga',   'Harga',          r.harga ? 'Rp ' + new Intl.NumberFormat('id-ID').format(r.harga) : null);
            setChip('chipLT',      'Luas Tanah',     r.luas_tanah ? r.luas_tanah + ' m²' : null);
            setChip('chipLB',      'Luas Bangunan',  r.luas_bangunan ? r.luas_bangunan + ' m²' : null);
            setChip('chipKamar',   'Kamar Tidur',    r.jumlah_kamar ?? '—');
            setChip('chipWC',      'Kamar Mandi',    r.jumlah_wc ?? '—');
            setChip('chipStok',    'Stok Tersedia',  r.stok_tersedia ?? 0);
            setChip('chipTerjual', 'Terjual',        r.terjual ?? 0);

            // Gambar utama (sudah full URL dari controller)
            const defaultImg = '{{ asset("deskapp/vendors/images/img2.jpg") }}';
            $('#modalGambar').attr('src', r.gambar || defaultImg);

            // Link aksi
            $('#modalEditLink').attr('href', '/admin/properti/tipe-unit/' + id + '/edit');
            $('#modalUnitLink').attr('href', '/admin/properti/unit?proyek_id=' + r.proyek_id);

            $('#detailTipeUnitModal').modal('show');
        },
        error: xhr => {
            Swal.close();
            Swal.fire('Error', xhr.responseJSON?.message || 'Gagal mengambil data.', 'error');
        }
    });
}
</script>
@endpush
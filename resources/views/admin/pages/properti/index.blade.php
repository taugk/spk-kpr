@extends('admin.layouts.app')

@section('title', 'Katalog Properti')

@section('page_action')
    <div class="btn-group btn-group-sm" role="group">
        <a href="{{ route('admin.properti.proyek.create') }}" class="btn btn-primary">
            <i class="dw dw-add"></i> Proyek
        </a>
        <a href="{{ route('admin.properti.tipe-unit.create') }}" class="btn btn-outline-primary">
            <i class="dw dw-building"></i> Tipe Unit
        </a>
        <a href="{{ route('admin.properti.unit.create') }}" class="btn btn-outline-success">
            <i class="dw dw-home"></i> Unit
        </a>
    </div>
@endsection

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    * { font-family: 'Plus Jakarta Sans', sans-serif; }

    /* ── Stats Bar ── */
    .stats-bar {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 2px 12px rgba(0,0,0,.06);
        margin-bottom: 28px;
        overflow: hidden;
        border: 1px solid #f0f0f5;
    }
    .stat-item {
        padding: 20px 24px;
        display: flex; align-items: center; gap: 14px;
        border-right: 1px solid #f0f0f5; transition: background .2s;
    }
    .stat-item:last-child { border-right: none; }
    .stat-item:hover { background: #fafbff; }
    .stat-icon {
        width: 46px; height: 46px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 20px; flex-shrink: 0;
    }
    .stat-icon.blue   { background: #eff2ff; color: #4361ee; }
    .stat-icon.green  { background: #edfff4; color: #0ca678; }
    .stat-icon.orange { background: #fff4e6; color: #e8720c; }
    .stat-icon.red    { background: #fff0f0; color: #e03131; }
    .stat-label { font-size: 12px; color: #9ca3b0; font-weight: 500; letter-spacing: .3px; margin-bottom: 3px; }
    .stat-value { font-size: 22px; font-weight: 800; color: #1a1d2e; line-height: 1; }

    /* ── Project Card ── */
    .project-card {
        background: #fff; border-radius: 16px;
        box-shadow: 0 2px 12px rgba(0,0,0,.06);
        border: 1px solid #f0f0f5; overflow: hidden;
        margin-bottom: 24px; transition: box-shadow .2s, transform .2s;
    }
    .project-card:hover { box-shadow: 0 8px 28px rgba(0,0,0,.10); transform: translateY(-2px); }

    .project-header { display: flex; }
    .project-thumb { width: 220px; min-width: 220px; position: relative; overflow: hidden; flex-shrink: 0; }
    .project-thumb img { width: 100%; height: 100%; object-fit: cover; display: block; transition: transform .4s; }
    .project-card:hover .project-thumb img { transform: scale(1.04); }
    .project-thumb-overlay {
        position: absolute; inset: 0;
        background: linear-gradient(to top, rgba(0,0,0,.45) 0%, transparent 60%);
    }
    .project-thumb-badge {
        position: absolute; bottom: 12px; left: 12px;
        font-size: 11px; font-weight: 700; letter-spacing: .5px; padding: 4px 10px; border-radius: 20px;
    }

    .project-info { flex: 1; padding: 20px 22px 0; display: flex; flex-direction: column; }
    .project-info-top { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 6px; }
    .project-name { font-size: 17px; font-weight: 800; color: #1a1d2e; margin: 0; line-height: 1.3; }
    .project-location { font-size: 13px; color: #6b7280; margin: 4px 0 0; }
    .project-location i { color: #e03131; margin-right: 3px; }
    .project-code {
        display: inline-block; font-size: 11px; font-weight: 600;
        color: #6366f1; background: #eef2ff;
        padding: 2px 8px; border-radius: 6px; margin-top: 6px; letter-spacing: .5px;
    }
    .project-desc {
        font-size: 13px; color: #9ca3b0; margin: 10px 0 0; line-height: 1.6;
        display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
    }
    .project-actions { display: flex; align-items: center; gap: 6px; flex-shrink: 0; }

    .btn-action-icon {
        width: 34px; height: 34px; border-radius: 10px;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 14px; border: 1px solid #e8eaf0; background: #fff; color: #6b7280;
        transition: all .18s; cursor: pointer; text-decoration: none;
    }
    .btn-action-icon:hover { background: #f5f5f5; color: #1a1d2e; text-decoration: none; }
    .btn-action-icon.edit:hover   { background: #fff8e1; border-color: #f59e0b; color: #d97706; }
    .btn-action-icon.delete:hover { background: #fff0f0; border-color: #fca5a5; color: #e03131; }

    /* ── Units Section ── */
    .units-section { border-top: 1px solid #f3f4f8; margin-top: 14px; }
    .units-section-header {
        display: flex; align-items: center; justify-content: space-between;
        padding: 12px 22px 8px; background: #fafbff;
    }
    .units-section-title { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: .8px; color: #9ca3b0; }
    .units-section-add {
        font-size: 12px; font-weight: 600; color: #4361ee;
        text-decoration: none; display: flex; align-items: center; gap: 4px;
    }
    .units-section-add:hover { color: #2d47d0; text-decoration: none; }

    .units-table { width: 100%; border-collapse: collapse; }
    .units-table thead tr th {
        font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; color: #9ca3b0;
        padding: 8px 12px; background: #fafbff; border-bottom: 1px solid #f3f4f8; white-space: nowrap;
    }
    .units-table tbody tr td {
        padding: 11px 12px; font-size: 13px; border-bottom: 1px solid #f8f9fb; vertical-align: middle; color: #374151;
    }
    .units-table tbody tr:last-child td { border-bottom: none; }
    .units-table tbody tr:hover td { background: #fafbff; }

    .unit-thumb { width: 38px; height: 38px; border-radius: 10px; object-fit: cover; border: 2px solid #f0f0f5; }
    .unit-name  { font-weight: 700; color: #1a1d2e; font-size: 13px; }
    .unit-code  { font-size: 11px; color: #9ca3b0; margin-top: 2px; }
    .unit-size  { color: #6b7280; font-size: 13px; }
    .unit-price { font-weight: 700; color: #4361ee; font-size: 14px; }

    .badge-stock {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600;
    }
    .badge-stock.available { background: #edfff4; color: #0ca678; }
    .badge-stock.sold-out  { background: #fff0f0; color: #e03131; }

    .unit-btns { display: flex; gap: 5px; }
    .unit-btn {
        width: 30px; height: 30px; border-radius: 8px;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 13px; border: none; cursor: pointer; transition: all .16s;
        background: #f5f5f7; color: #6b7280; text-decoration: none;
    }
    .unit-btn.view:hover   { background: #eff2ff; color: #4361ee; }
    .unit-btn.edit:hover   { background: #fff8e1; color: #d97706; }
    .unit-btn.delete:hover { background: #fff0f0; color: #e03131; }

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

    .detail-chip { background: #fafbff; border: 1px solid #f0f0f5; border-radius: 10px; padding: 10px 14px; }
    .detail-chip .chip-label {
        font-size: 11px; font-weight: 700; color: #9ca3b0;
        text-transform: uppercase; letter-spacing: .4px; display: block; margin-bottom: 3px;
    }
    .detail-chip .chip-value { font-size: 14px; font-weight: 700; color: #1a1d2e; }

    @media (max-width: 768px) {
        .stats-bar { grid-template-columns: repeat(2, 1fr); }
        .stat-item { border-bottom: 1px solid #f0f0f5; }
        .stat-item:nth-child(2n) { border-right: none; }
        .project-header { flex-direction: column; }
        .project-thumb { width: 100%; min-width: unset; height: 200px; }
        .units-table thead { display: none; }
        .units-table tbody tr { display: block; padding: 12px; border-bottom: 1px solid #f3f4f8; }
        .units-table tbody tr td {
            display: flex; justify-content: space-between; align-items: center;
            padding: 4px 0; border: none; font-size: 13px;
        }
        .units-table tbody tr td::before {
            content: attr(data-label);
            font-size: 11px; font-weight: 700; color: #9ca3b0;
            text-transform: uppercase; letter-spacing: .4px; flex-shrink: 0; margin-right: 8px;
        }
    }
</style>
@endpush

@section('content')
<div class="min-height-200px">

    {{-- Stats Bar --}}
    <div class="stats-bar">
        <div class="stat-item">
            <div class="stat-icon blue"><i class="dw dw-building"></i></div>
            <div>
                <div class="stat-label">Total Proyek</div>
                <div class="stat-value">{{ $proyek->count() }}</div>
            </div>
        </div>
        <div class="stat-item">
            <div class="stat-icon green"><i class="dw dw-house-1"></i></div>
            <div>
                <div class="stat-label">Tipe Unit</div>
                <div class="stat-value">{{ $proyek->sum(fn($p) => $p->tipeUnit->count()) }}</div>
            </div>
        </div>
        <div class="stat-item">
            <div class="stat-icon orange"><i class="icon-copy dw dw-package"></i></div>
            <div>
                <div class="stat-label">Stok Unit</div>
                <div class="stat-value">{{ $proyek->sum(fn($p) => $p->tipeUnit->sum('stok_tersedia')) }}</div>
            </div>
        </div>
        <div class="stat-item">
            <div class="stat-icon red"><i class="dw dw-pin"></i></div>
            <div>
                <div class="stat-label">Kota</div>
                <div class="stat-value">{{ $proyek->unique('kota')->count() }}</div>
            </div>
        </div>
    </div>

    {{-- Project List --}}
    @forelse($proyek as $item)
        @php
            // foto_proyek adalah JSON array — ambil foto pertama
            $fotos      = json_decode($item->foto_proyek ?? '[]', true) ?? [];
            $fotoProyek = !empty($fotos[0]) && Storage::disk('public')->exists($fotos[0])
                          ? asset('storage/' . $fotos[0])
                          : asset('deskapp/vendors/images/product-img1.jpg');
        @endphp

        <div class="project-card">
            <div class="project-header">

                {{-- Thumbnail --}}
                <div class="project-thumb">
                    <img src="{{ $fotoProyek }}"
                         alt="{{ $item->nama_proyek }}"
                         onerror="this.src='{{ asset('deskapp/vendors/images/product-img1.jpg') }}'">
                    <div class="project-thumb-overlay"></div>
                    <span class="project-thumb-badge badge
                        {{ $item->status === 'aktif' ? 'badge-success' : ($item->status === 'tutup' ? 'badge-warning' : 'badge-secondary') }}">
                        {{ ucfirst($item->status) }}
                    </span>
                </div>

                {{-- Info --}}
                <div class="project-info">
                    <div class="project-info-top">
                        <div>
                            <h5 class="project-name">{{ $item->nama_proyek }}</h5>
                            <p class="project-location">
                                <i class="dw dw-pin"></i> {{ $item->lokasi }}
                            </p>
                            <span class="project-code">{{ $item->kode_proyek }}</span>
                        </div>
                        <div class="project-actions ml-3">
                            <a href="{{ route('admin.properti.tipe-unit', ['proyek_id' => $item->id]) }}"
                               class="btn-action-icon" title="Kelola Tipe Unit">
                                <i class="dw dw-building"></i>
                            </a>
                            <a href="{{ route('admin.properti.unit', ['proyek_id' => $item->id]) }}"
                               class="btn-action-icon" title="Kelola Unit">
                                <i class="dw dw-home"></i>
                            </a>
                            <a href="{{ route('admin.properti.proyek.edit', $item->id) }}"
                               class="btn-action-icon edit" title="Edit Proyek">
                                <i class="dw dw-edit2"></i>
                            </a>
                            <button class="btn-action-icon delete delete-proyek-btn"
                                    data-id="{{ $item->id }}" title="Hapus Proyek">
                                <i class="dw dw-trash"></i>
                            </button>
                        </div>
                    </div>

                    @if($item->deskripsi)
                        <p class="project-desc">{{ $item->deskripsi }}</p>
                    @endif

                    {{-- Quick Metrics --}}
                    <div class="d-flex mt-auto pb-3 pt-2" style="gap:20px;">
                        <div style="font-size:12px; color:#9ca3b0;">
                            <span style="font-weight:800; font-size:15px; color:#1a1d2e;">
                                {{ $item->tipeUnit->count() }}
                            </span> Tipe Unit
                        </div>
                        <div style="font-size:12px; color:#9ca3b0;">
                            <span style="font-weight:800; font-size:15px; color:#0ca678;">
                                {{ $item->tipeUnit->sum('stok_tersedia') }}
                            </span> Stok Tersedia
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tipe Unit Table --}}
            <div class="units-section">
                <div class="units-section-header">
                    <span class="units-section-title">Tipe Unit</span>
                    <a href="{{ route('admin.properti.tipe-unit.create', ['proyek_id' => $item->id]) }}"
                       class="units-section-add">
                        <i class="dw dw-add" style="font-size:13px;"></i> Tambah Tipe
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="units-table">
                        <thead>
                            <tr>
                                <th style="width:50px; padding-left:16px;">Foto</th>
                                <th>Nama Tipe</th>
                                <th>LT / LB</th>
                                <th>Harga</th>
                                <th>Stok</th>
                                <th style="text-align:right; padding-right:16px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($item->tipeUnit as $tipe)
                                @php
                                    // gambar adalah JSON array — ambil gambar pertama
                                    $gambarArr  = json_decode($tipe->gambar ?? '[]', true) ?? [];
                                    $gambarTipe = !empty($gambarArr[0]) && Storage::disk('public')->exists($gambarArr[0])
                                                  ? asset('storage/' . $gambarArr[0])
                                                  : asset('deskapp/vendors/images/img2.jpg');
                                @endphp
                                <tr>
                                    <td data-label="Foto" style="padding-left:16px;">
                                        <img src="{{ $gambarTipe }}"
                                             class="unit-thumb"
                                             alt="{{ $tipe->nama_tipe }}"
                                             onerror="this.src='{{ asset('deskapp/vendors/images/img2.jpg') }}'">
                                    </td>
                                    <td data-label="Nama Tipe">
                                        <div class="unit-name">{{ $tipe->nama_tipe }}</div>
                                        <div class="unit-code">{{ $tipe->kode_tipe }}</div>
                                    </td>
                                    <td data-label="LT/LB" class="unit-size">
                                        {{ $tipe->luas_tanah }} / {{ $tipe->luas_bangunan }} m²
                                    </td>
                                    <td data-label="Harga" class="unit-price">
                                        Rp {{ number_format($tipe->harga, 0, ',', '.') }}
                                    </td>
                                    <td data-label="Stok">
                                        <span class="badge-stock {{ $tipe->stok_tersedia > 0 ? 'available' : 'sold-out' }}">
                                            <span style="width:6px;height:6px;border-radius:50%;background:currentColor;display:inline-block;"></span>
                                            {{ $tipe->stok_tersedia }} unit
                                        </span>
                                    </td>
                                    <td data-label="Aksi" style="padding-right:16px;">
                                        <div class="unit-btns justify-content-end">
                                            <button class="unit-btn view"
                                                    onclick="detailTipe({{ $tipe->id }})" title="Detail">
                                                <i class="dw dw-eye"></i>
                                            </button>
                                            <a href="{{ route('admin.properti.tipe-unit.edit', $tipe->id) }}"
                                               class="unit-btn edit" title="Edit">
                                                <i class="dw dw-edit2"></i>
                                            </a>
                                            <button class="unit-btn delete delete-tipe-btn"
                                                    data-id="{{ $tipe->id }}" title="Hapus">
                                                <i class="dw dw-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="text-align:center; padding:28px; color:#9ca3b0; font-size:13px;">
                                        Belum ada tipe unit.
                                        <a href="{{ route('admin.properti.tipe-unit.create', ['proyek_id' => $item->id]) }}"
                                           style="color:#4361ee; font-weight:600; margin-left:4px;">+ Tambah sekarang</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    @empty
        <div class="empty-state">
            <div class="empty-state-icon"><i class="dw dw-building"></i></div>
            <h5>Belum Ada Proyek</h5>
            <p>Mulai dengan menambahkan proyek properti pertama Anda.</p>
            <a href="{{ route('admin.properti.proyek.create') }}" class="btn btn-primary mt-2">
                <i class="dw dw-add mr-1"></i> Tambah Proyek
            </a>
        </div>
    @endforelse

</div>

{{-- Modal Detail Tipe Unit --}}
<div class="modal fade" id="detailTipeUnitModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius:16px; border:none; overflow:hidden;">
            <div class="modal-header" style="border-bottom:1px solid #f0f0f5; padding:20px 24px;">
                <h5 class="modal-title" style="font-weight:800; font-size:16px; color:#1a1d2e;">Detail Tipe Unit</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" style="padding:24px;">
                <div class="row">
                    <div class="col-md-5 mb-3 mb-md-0">
                        <img id="modalGambar" src=""
                             style="width:100%; height:220px; object-fit:cover; border-radius:12px; background:#f3f4f8;"
                             onerror="this.src='{{ asset('deskapp/vendors/images/img2.jpg') }}'">
                    </div>
                    <div class="col-md-7">
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
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
                <a href="#" id="modalEditLink" class="btn btn-warning" style="border-radius:10px; font-weight:700;">
                    <i class="dw dw-edit2 mr-1"></i> Edit Tipe Unit
                </a>
                <button type="button" class="btn btn-light" data-dismiss="modal"
                        style="border-radius:10px; font-weight:600; border:1px solid #e8eaf0;">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function setChip(id, label, value) {
    document.getElementById(id).innerHTML =
        `<span class="chip-label">${label}</span><span class="chip-value">${value ?? '—'}</span>`;
}

$(document).ready(function () {

    // ── Delete Proyek ──
    $(document).on('click', '.delete-proyek-btn', function (e) {
        e.preventDefault();
        const id  = $(this).data('id');
        const btn = $(this);

        Swal.fire({
            title: 'Hapus Proyek?',
            text: 'Semua tipe unit dan unit terkait akan ikut terhapus.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#e03131'
        }).then(result => {
            if (!result.isConfirmed) return;
            btn.prop('disabled', true);

            $.ajax({
                url: '/admin/properti/proyek/' + id,
                method: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: res => {
                    if (res.success) {
                        Swal.fire({ title: 'Terhapus!', icon: 'success', timer: 1500, showConfirmButton: false })
                            .then(() => location.reload());
                    } else {
                        Swal.fire('Gagal', res.message || 'Gagal menghapus proyek.', 'error');
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

    // ── Delete Tipe Unit ──
    $(document).on('click', '.delete-tipe-btn', function (e) {
        e.preventDefault();
        const id  = $(this).data('id');
        const btn = $(this);

        Swal.fire({
            title: 'Hapus Tipe Unit?',
            text: 'Semua unit terkait akan ikut terhapus.',
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
});

// ── Detail Tipe Unit ──
function detailTipe(id) {
    Swal.fire({ title: 'Memuat...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

    $.ajax({
        url: '/admin/properti/tipe-unit/' + id + '/detail',
        method: 'GET',
        success: function (r) {
            Swal.close();

            setChip('chipProyek',  'Proyek',       r.nama_proyek);
            setChip('chipKode',    'Kode Tipe',     r.kode_tipe);
            setChip('chipNama',    'Nama Tipe',     r.nama_tipe);
            setChip('chipHarga',   'Harga',         r.harga ? 'Rp ' + new Intl.NumberFormat('id-ID').format(r.harga) : null);
            setChip('chipLT',      'Luas Tanah',    r.luas_tanah ? r.luas_tanah + ' m²' : null);
            setChip('chipLB',      'Luas Bangunan', r.luas_bangunan ? r.luas_bangunan + ' m²' : null);
            setChip('chipKamar',   'Kamar Tidur',   r.jumlah_kamar ?? '—');
            setChip('chipWC',      'Kamar Mandi',   r.jumlah_wc ?? '—');
            setChip('chipStok',    'Stok Tersedia', r.stok_tersedia ?? 0);
            setChip('chipTerjual', 'Terjual',       r.terjual ?? 0);

            // gambar sudah berupa full URL dari controller
            $('#modalGambar').attr('src', r.gambar || '{{ asset("deskapp/vendors/images/img2.jpg") }}');
            $('#modalEditLink').attr('href', '/admin/properti/tipe-unit/' + id + '/edit');
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
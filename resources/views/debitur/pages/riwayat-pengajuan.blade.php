@extends('debitur.layouts.app')

@section('title', 'Riwayat Pengajuan KPR | Debitur Citra Persada')

@section('content')
<style>
    :root {
        --rp-primary: #0C447C;
        --rp-primary-soft: #E6F1FB;
        --rp-success: #0F7B4F;
        --rp-success-soft: #E1F5EE;
        --rp-warning: #B7791F;
        --rp-warning-soft: #FAEEDA;
        --rp-danger: #B42318;
        --rp-danger-soft: #FAECE7;
        --rp-purple: #5B4FC7;
        --rp-purple-soft: #EEEDFE;
        --rp-bg: #F6F8FB;
        --rp-card: #FFFFFF;
        --rp-border: #E4E8EE;
        --rp-text: #1F2937;
        --rp-muted: #6B7280;
        --rp-radius: 18px;
        --rp-shadow: 0 16px 40px rgba(15, 23, 42, .07);
    }

    .page-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 14px;
        margin-bottom: 18px;
    }

    .page-title h5 {
        margin: 0;
        font-size: 21px;
        font-weight: 800;
        color: var(--rp-text);
    }

    .page-title p {
        margin: 5px 0 0;
        font-size: 13px;
        color: var(--rp-muted);
    }

    .btn-primary-soft {
        border: 1px solid #85B7EB;
        background: var(--rp-primary-soft);
        color: var(--rp-primary);
        border-radius: 13px;
        padding: 10px 15px;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 7px;
        transition: .2s ease;
    }

    .btn-primary-soft:hover {
        color: var(--rp-primary);
        transform: translateY(-1px);
        box-shadow: 0 10px 22px rgba(12, 68, 124, .12);
    }

    .summary-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 18px;
    }

    .summary-card {
        background: var(--rp-card);
        border: 1px solid var(--rp-border);
        border-radius: var(--rp-radius);
        box-shadow: var(--rp-shadow);
        padding: 17px;
        display: flex;
        gap: 13px;
        align-items: center;
    }

    .summary-icon {
        width: 46px;
        height: 46px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        flex: 0 0 auto;
    }

    .summary-card span {
        font-size: 12px;
        color: var(--rp-muted);
        display: block;
    }

    .summary-card strong {
        font-size: 24px;
        color: var(--rp-text);
        line-height: 1.1;
        display: block;
        margin-top: 2px;
    }

    .main-card {
        background: var(--rp-card);
        border: 1px solid var(--rp-border);
        border-radius: var(--rp-radius);
        box-shadow: var(--rp-shadow);
        overflow: hidden;
    }

    .filter-bar {
        padding: 18px;
        border-bottom: 1px solid var(--rp-border);
        display: grid;
        grid-template-columns: minmax(0, 1fr) 190px 190px;
        gap: 12px;
        background: linear-gradient(135deg, #FFFFFF 0%, #F2F7FC 100%);
    }

    .search-box {
        position: relative;
    }

    .search-box i {
        position: absolute;
        top: 50%;
        left: 13px;
        transform: translateY(-50%);
        color: var(--rp-muted);
        font-size: 15px;
    }

    .filter-input,
    .filter-select {
        width: 100%;
        border: 1px solid #D5DCE5;
        background: #FFFFFF;
        border-radius: 13px;
        padding: 10px 12px;
        font-size: 13px;
        color: var(--rp-text);
        outline: none;
        transition: .18s ease;
    }

    .search-box .filter-input {
        padding-left: 38px;
    }

    .filter-input:focus,
    .filter-select:focus {
        border-color: #3B82F6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, .10);
    }

    .table-wrap {
        overflow-x: auto;
    }

    .riwayat-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 920px;
    }

    .riwayat-table thead th {
        background: #FBFCFE;
        color: #64748B;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .04em;
        font-weight: 800;
        padding: 14px 16px;
        border-bottom: 1px solid var(--rp-border);
        white-space: nowrap;
    }

    .riwayat-table tbody td {
        padding: 15px 16px;
        border-bottom: 1px solid #EEF2F7;
        vertical-align: middle;
        font-size: 13px;
        color: var(--rp-text);
    }

    .riwayat-table tbody tr {
        transition: .16s ease;
    }

    .riwayat-table tbody tr:hover {
        background: #F8FAFC;
    }

    .code-text {
        font-weight: 800;
        color: var(--rp-primary);
    }

    .muted-small {
        font-size: 11px;
        color: var(--rp-muted);
        margin-top: 2px;
    }

    .property-name {
        font-weight: 700;
        color: var(--rp-text);
    }

    .badge-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 800;
        white-space: nowrap;
    }

    .badge-draft {
        background: #F3F4F6;
        color: #4B5563;
    }

    .badge-pending,
    .badge-diproses,
    .badge-menunggu {
        background: var(--rp-warning-soft);
        color: var(--rp-warning);
    }

    .badge-disetujui,
    .badge-approved,
    .badge-layak {
        background: var(--rp-success-soft);
        color: var(--rp-success);
    }

    .badge-ditolak,
    .badge-rejected,
    .badge-tidak-layak {
        background: var(--rp-danger-soft);
        color: var(--rp-danger);
    }

    .badge-survey,
    .badge-analisis {
        background: var(--rp-purple-soft);
        color: var(--rp-purple);
    }

    .score-box {
        min-width: 88px;
    }

    .score-value {
        font-weight: 800;
        color: var(--rp-text);
    }

    .score-track {
        margin-top: 5px;
        width: 78px;
        height: 7px;
        background: #E8EEF5;
        border-radius: 999px;
        overflow: hidden;
    }

    .score-fill {
        height: 100%;
        width: 0;
        border-radius: 999px;
        background: var(--rp-primary);
    }

    .action-group {
        display: flex;
        align-items: center;
        gap: 7px;
    }

    .action-btn {
        width: 34px;
        height: 34px;
        border-radius: 11px;
        border: 1px solid var(--rp-border);
        background: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #4B5563;
        text-decoration: none;
        transition: .18s ease;
        cursor: pointer;
    }

    .action-btn:hover {
        transform: translateY(-1px);
    }

    .action-btn.detail:hover {
        background: var(--rp-primary-soft);
        color: var(--rp-primary);
        border-color: #85B7EB;
    }

    .action-btn.edit:hover {
        background: var(--rp-warning-soft);
        color: var(--rp-warning);
        border-color: #F1C27D;
    }

    .action-btn.delete:hover {
        background: var(--rp-danger-soft);
        color: var(--rp-danger);
        border-color: #F4AAA3;
    }

    .empty-state {
        padding: 50px 18px;
        text-align: center;
        display: none;
    }

    .empty-icon {
        width: 72px;
        height: 72px;
        border-radius: 24px;
        background: var(--rp-primary-soft);
        color: var(--rp-primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 34px;
        margin: 0 auto 14px;
    }

    .empty-state h6 {
        font-size: 16px;
        font-weight: 800;
        margin-bottom: 5px;
    }

    .empty-state p {
        font-size: 13px;
        color: var(--rp-muted);
        margin-bottom: 15px;
    }

    .pagination-area {
        padding: 14px 18px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        border-top: 1px solid var(--rp-border);
        background: #FBFCFE;
    }

    .pagination-info {
        font-size: 12px;
        color: var(--rp-muted);
    }

    .pagination-buttons {
        display: flex;
        gap: 7px;
    }

    .page-btn {
        min-width: 34px;
        height: 34px;
        border-radius: 10px;
        border: 1px solid var(--rp-border);
        background: #fff;
        color: #4B5563;
        font-size: 12px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0 10px;
    }

    .page-btn.active {
        background: var(--rp-primary);
        color: #fff;
        border-color: var(--rp-primary);
    }

    .page-btn:disabled {
        opacity: .45;
        cursor: not-allowed;
    }

    @media (max-width: 992px) {
        .summary-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .filter-bar {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 576px) {
        .page-head {
            flex-direction: column;
        }

        .btn-primary-soft {
            width: 100%;
            justify-content: center;
        }

        .summary-grid {
            grid-template-columns: 1fr;
        }

        .summary-card {
            padding: 14px;
        }

        .pagination-area {
            flex-direction: column;
            align-items: stretch;
        }

        .pagination-buttons {
            justify-content: center;
            flex-wrap: wrap;
        }
    }
</style>

<div class="row">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('debitur.dashboard') }}" class="text-decoration-none">Dashboard</a>
                </li>
                <li class="breadcrumb-item active">Riwayat Pengajuan</li>
            </ol>
        </nav>
    </div>
</div>

<div class="page-head">
    <div class="page-title">
        <h5>Riwayat Pengajuan KPR</h5>
        <p>Pantau status pengajuan, skor kelayakan, dan kelengkapan data KPR kamu.</p>
    </div>

    <a href="{{ route('debitur.pengajuan-kpr') }}" class="btn-primary-soft">
        <i class="bi bi-plus-circle"></i>
        Ajukan KPR Baru
    </a>
</div>



<div class="summary-grid">
    <div class="summary-card">
        <div class="summary-icon" style="background:var(--rp-primary-soft);color:var(--rp-primary);">
            <i class="bi bi-folder-check"></i>
        </div>
        <div>
            <span>Total Pengajuan</span>
            <strong>{{ $total }}</strong>
        </div>
    </div>

    <div class="summary-card">
        <div class="summary-icon" style="background:#F3F4F6;color:#4B5563;">
            <i class="bi bi-pencil-square"></i>
        </div>
        <div>
            <span>Draft</span>
            <strong>{{ $draft }}</strong>
        </div>
    </div>

    <div class="summary-card">
        <div class="summary-icon" style="background:var(--rp-warning-soft);color:var(--rp-warning);">
            <i class="bi bi-hourglass-split"></i>
        </div>
        <div>
            <span>Diproses</span>
            <strong>{{ $diproses }}</strong>
        </div>
    </div>

    <div class="summary-card">
        <div class="summary-icon" style="background:var(--rp-success-soft);color:var(--rp-success);">
            <i class="bi bi-check2-circle"></i>
        </div>
        <div>
            <span>Disetujui</span>
            <strong>{{ $disetujui }}</strong>
        </div>
    </div>
</div>

<div class="main-card">
    <div class="filter-bar">
        <div class="search-box">
            <i class="bi bi-search"></i>
            <input type="text" id="searchInput" class="filter-input" placeholder="Cari kode, properti, tipe, atau status...">
        </div>

        <select id="statusFilter" class="filter-select">
            <option value="all">Semua Status</option>
            <option value="draft">Draft</option>
            <option value="pending">Pending</option>
            <option value="diproses">Diproses</option>
            <option value="survey">Survey</option>
            <option value="analisis">Analisis</option>
            <option value="disetujui">Disetujui</option>
            <option value="ditolak">Ditolak</option>
        </select>

        <select id="sortFilter" class="filter-select">
            <option value="newest">Terbaru</option>
            <option value="oldest">Terlama</option>
            <option value="score_high">Skor Tertinggi</option>
            <option value="score_low">Skor Terendah</option>
            <option value="price_high">Harga Tertinggi</option>
            <option value="price_low">Harga Terendah</option>
        </select>
    </div>

    <div class="table-wrap" id="tableWrap">
        <table class="riwayat-table">
            <thead>
                <tr>
                    <th>Kode Pengajuan</th>
                    <th>Properti</th>
                    <th>Harga & DP</th>
                    <th>Tenor</th>
                    <th>Status</th>
                    <th>Skor</th>
                    <th>Catatan</th>
                    <th width="120">Aksi</th>
                </tr>
            </thead>
            <tbody id="riwayatBody"></tbody>
        </table>
    </div>

    <div class="empty-state" id="emptyState">
        <div class="empty-icon">
            <i class="bi bi-folder-x"></i>
        </div>
        <h6>Belum ada data pengajuan</h6>
        <p>Data pengajuan tidak ditemukan atau tidak sesuai filter.</p>
        <a href="{{ route('debitur.pengajuan-kpr') }}" class="btn-primary-soft">
            <i class="bi bi-plus-circle"></i>
            Buat Pengajuan Baru
        </a>
    </div>

    <div class="pagination-area" id="paginationArea">
        <div class="pagination-info" id="paginationInfo">Menampilkan 0 data</div>
        <div class="pagination-buttons" id="paginationButtons"></div>
    </div>
</div>

{{-- Modal Hapus Draft --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold">Hapus Draft Pengajuan?</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-muted small">
                Draft yang dihapus tidak bisa dikembalikan. Pengajuan yang sudah dikirim tidak dapat dihapus dari halaman ini.
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Batal</button>

                <form id="deleteForm" method="POST" action="#">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger rounded-3">Hapus Draft</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection


@push('scripts')
<script>
    const pengajuanData = @json($dataPengajuan);
    
    // FIXED: Added trailing slashes to URLs
    const routeBase = {
        detail: `{{ url('/debitur/pengajuan/show') }}/`,
        edit: `{{ url('/debitur/pengajuan-kpr/edit') }}/`,
        delete: `{{ url('/debitur/pengajuan-kpr/delete') }}/`
    };

    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const sortFilter = document.getElementById('sortFilter');
    const riwayatBody = document.getElementById('riwayatBody');
    const tableWrap = document.getElementById('tableWrap');
    const emptyState = document.getElementById('emptyState');
    const paginationArea = document.getElementById('paginationArea');
    const paginationInfo = document.getElementById('paginationInfo');
    const paginationButtons = document.getElementById('paginationButtons');
    const deleteForm = document.getElementById('deleteForm');

    let currentPage = 1;
    const perPage = 8;

    function rupiah(value) {
        return 'Rp ' + (parseInt(value) || 0).toLocaleString('id-ID');
    }

    function formatDate(dateString) {
        if (!dateString || dateString === '-') return '-';

        const date = new Date(dateString);
        if (isNaN(date.getTime())) return dateString;

        return date.toLocaleDateString('id-ID', {
            day: '2-digit',
            month: 'short',
            year: 'numeric'
        });
    }

    function normalizeStatus(status) {
        return String(status || '').toLowerCase().replace(/_/g, '-');
    }

    function statusLabel(status) {
        const normalized = normalizeStatus(status);

        const labels = {
            'draft': 'Draft',
            'pending': 'Pending',
            'diproses': 'Diproses',
            'menunggu': 'Menunggu',
            'survey': 'Survey',
            'analisis': 'Analisis',
            'disetujui': 'Disetujui',
            'approved': 'Disetujui',
            'layak': 'Layak',
            'ditolak': 'Ditolak',
            'rejected': 'Ditolak',
            'tidak-layak': 'Tidak Layak'
        };

        return labels[normalized] || status || '-';
    }

    function statusIcon(status) {
        const normalized = normalizeStatus(status);

        if (normalized === 'draft') return 'bi-pencil-square';
        if (['disetujui', 'approved', 'layak'].includes(normalized)) return 'bi-check-circle';
        if (['ditolak', 'rejected', 'tidak-layak'].includes(normalized)) return 'bi-x-circle';
        if (['survey', 'analisis'].includes(normalized)) return 'bi-clipboard-data';

        return 'bi-hourglass-split';
    }

    function scoreColor(score) {
        score = parseInt(score) || 0;
        if (score >= 80) return '#0F7B4F';
        if (score >= 60) return '#F59E0B';
        if (score > 0) return '#E24B4A';
        return '#94A3B8';
    }

    function filteredData() {
        const keyword = searchInput.value.toLowerCase().trim();
        const status = statusFilter.value;
        const sort = sortFilter.value;

        let data = [...pengajuanData];

        if (keyword) {
            data = data.filter(item => {
                return String(item.kode || '').toLowerCase().includes(keyword)
                    || String(item.properti || '').toLowerCase().includes(keyword)
                    || String(item.tipe || '').toLowerCase().includes(keyword)
                    || String(item.status || '').toLowerCase().includes(keyword)
                    || String(item.catatan || '').toLowerCase().includes(keyword);
            });
        }

        if (status !== 'all') {
            data = data.filter(item => normalizeStatus(item.status) === status);
        }

        data.sort((a, b) => {
            if (sort === 'newest') return new Date(b.tanggal) - new Date(a.tanggal);
            if (sort === 'oldest') return new Date(a.tanggal) - new Date(b.tanggal);
            if (sort === 'score_high') return (parseInt(b.skor) || 0) - (parseInt(a.skor) || 0);
            if (sort === 'score_low') return (parseInt(a.skor) || 0) - (parseInt(b.skor) || 0);
            if (sort === 'price_high') return (parseInt(b.harga) || 0) - (parseInt(a.harga) || 0);
            if (sort === 'price_low') return (parseInt(a.harga) || 0) - (parseInt(b.harga) || 0);
            return 0;
        });

        return data;
    }

    function renderTable() {
        const data = filteredData();
        const totalPage = Math.max(Math.ceil(data.length / perPage), 1);

        if (currentPage > totalPage) currentPage = totalPage;

        const start = (currentPage - 1) * perPage;
        const end = start + perPage;
        const pageData = data.slice(start, end);

        riwayatBody.innerHTML = '';

        if (data.length === 0) {
            tableWrap.style.display = 'none';
            paginationArea.style.display = 'none';
            emptyState.style.display = 'block';
            return;
        }

        tableWrap.style.display = 'block';
        paginationArea.style.display = 'flex';
        emptyState.style.display = 'none';

        pageData.forEach(item => {
            const status = normalizeStatus(item.status);
            const score = parseInt(item.skor) || 0;
            const canEdit = status === 'draft';
            const canDelete = status === 'draft';

            const tr = document.createElement('tr');
            tr.innerHTML = `
                <tr>
                    <div class="code-text">${item.kode || '-'}</div>
                    <div class="muted-small">${formatDate(item.tanggal)}</div>
                </td>
                <td>
                    <div class="property-name">${item.properti || '-'}</div>
                    <div class="muted-small">${item.tipe || '-'}</div>
                </td>
                <td>
                    <div style="font-weight:700;">${rupiah(item.harga)}</div>
                    <div class="muted-small">DP: ${rupiah(item.dp)}</div>
                </td>
                <td>
                    <div style="font-weight:700;">${item.tenor || 0} Tahun</div>
                </td>
                <td>
                    <span class="badge-status badge-${status}">
                        <i class="bi ${statusIcon(status)}"></i>
                        ${statusLabel(status)}
                    </span>
                </td>
                <td>
                    <div class="score-box">
                        <div class="score-value" style="color:${scoreColor(score)};">${score > 0 ? score : '-'}</div>
                        <div class="score-track">
                            <div class="score-fill" style="width:${Math.min(score, 100)}%;background:${scoreColor(score)};"></div>
                        </div>
                    </div>
                </td>
                <td>
                    <div style="max-width:190px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" title="${item.catatan || '-'}">
                        ${item.catatan || '-'}
                    </div>
                </td>
                <td>
                    <div class="action-group">
                        <a href="${routeBase.detail}${item.id}" class="action-btn detail" title="Detail">
                            <i class="bi bi-eye"></i>
                        </a>

                        ${canEdit ? `
                            <a href="${routeBase.edit}${item.id}" class="action-btn edit" title="Edit Draft">
                                <i class="bi bi-pencil"></i>
                            </a>
                        ` : ''}

                        ${canDelete ? `
                            <button type="button" class="action-btn delete" title="Hapus Draft" onclick="confirmDelete(${item.id})">
                                <i class="bi bi-trash"></i>
                            </button>
                        ` : ''}
                    </div>
                </td>
            `;

            riwayatBody.appendChild(tr);
        });

        renderPagination(data.length, totalPage, start, Math.min(end, data.length));
    }

    function renderPagination(totalData, totalPage, start, end) {
        paginationInfo.textContent = `Menampilkan ${start + 1} - ${end} dari ${totalData} data`;
        paginationButtons.innerHTML = '';

        const prevBtn = document.createElement('button');
        prevBtn.className = 'page-btn';
        prevBtn.innerHTML = '<i class="bi bi-chevron-left"></i>';
        prevBtn.disabled = currentPage === 1;
        prevBtn.onclick = () => {
            if (currentPage > 1) {
                currentPage--;
                renderTable();
            }
        };
        paginationButtons.appendChild(prevBtn);

        // Show limited page numbers
        let startPage = Math.max(1, currentPage - 2);
        let endPage = Math.min(totalPage, startPage + 4);
        
        if (endPage - startPage < 4 && startPage > 1) {
            startPage = Math.max(1, endPage - 4);
        }

        for (let i = startPage; i <= endPage; i++) {
            const btn = document.createElement('button');
            btn.className = `page-btn ${i === currentPage ? 'active' : ''}`;
            btn.textContent = i;
            btn.onclick = () => {
                currentPage = i;
                renderTable();
            };
            paginationButtons.appendChild(btn);
        }

        const nextBtn = document.createElement('button');
        nextBtn.className = 'page-btn';
        nextBtn.innerHTML = '<i class="bi bi-chevron-right"></i>';
        nextBtn.disabled = currentPage === totalPage;
        nextBtn.onclick = () => {
            if (currentPage < totalPage) {
                currentPage++;
                renderTable();
            }
        };
        paginationButtons.appendChild(nextBtn);
    }

    window.confirmDelete = function(id) {
        deleteForm.action = `${routeBase.delete}${id}`;
        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    }

    // Event listeners
    searchInput.addEventListener('input', () => {
        currentPage = 1;
        renderTable();
    });

    statusFilter.addEventListener('change', () => {
        currentPage = 1;
        renderTable();
    });

    sortFilter.addEventListener('change', () => {
        currentPage = 1;
        renderTable();
    });

    // Initial render
    renderTable();
</script>
@endpush
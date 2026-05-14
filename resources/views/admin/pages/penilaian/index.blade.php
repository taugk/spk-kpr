@extends('admin.layouts.app')

@section('title', 'Penilaian SMART')

@section('page_action')
<a href="{{ route('admin.pengajuan.index') }}" class="btn btn-secondary">
    <i class="dw dw-back"></i> Kembali ke Pengajuan
</a>
@endsection

@section('content')
<div class="card-box mb-30">
    <div class="pd-20">
        <h4 class="text-blue h4">Daftar Penilaian SMART</h4>
        <p class="mb-0">Hasil penilaian kelayakan pengajuan KPR menggunakan metode SMART</p>
    </div>
    <div class="pb-20">
        <table class="data-table table stripe hover nowrap">
            <thead>
                <tr>
                    <th>Kode Pengajuan</th>
                    <th>Debitur</th>
                    <th>Admin Penilai</th>
                    <th>Skor Akhir</th>
                    <th>Threshold</th>
                    <th>Hasil</th>
                    <th>Tanggal Penilaian</th>
                    <th class="datatable-nosort">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse(($penilaian ?? []) as $item)
                    <tr>
                        <td>{{ $item->kode_pengajuan ?? '-' }}</td>
                        <td>{{ $item->nama_debitur ?? '-' }}</td>
                        <td>{{ $item->nama_admin ?? '-' }}</td>
                        <td>
                            <strong>{{ number_format($item->skor_akhir ?? 0, 2) }}</strong>
                            @if(($item->skor_akhir ?? 0) >= ($item->threshold ?? 65))
                                <span class="badge badge-success">✓</span>
                            @elseif(($item->skor_akhir ?? 0) > 0)
                                <span class="badge badge-danger">✗</span>
                            @endif
                        </td>
                        <td>{{ number_format($item->threshold ?? 65, 2) }}%</td>
                        <td>@include('admin.components.status-badge', ['status' => $item->hasil ?? 'belum_dinilai'])</td>
                        <td>{{ optional($item->tgl_penilaian ?? null)->format('d M Y H:i') ?? '-' }}</td>
                        <td>
                            <a href="{{ route('admin.penilaian.show', $item->id) }}" class="btn btn-sm btn-primary">
                                <i class="dw dw-eye"></i> Detail
                            </a>
                         </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center">
                        <div class="py-4">
                            <i class="dw dw-analytics-21" style="font-size: 48px; color: #ccc;"></i>
                            <p class="mt-2 mb-0">Belum ada data penilaian.</p>
                            <small class="text-muted">Lakukan penilaian dari halaman detail pengajuan</small>
                        </div>
                    </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(method_exists($penilaian, 'links'))
    <div class="pd-20">
        {{ $penilaian->links() }}
    </div>
    @endif
</div>
@endsection
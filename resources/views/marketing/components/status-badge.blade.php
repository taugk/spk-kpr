@php
    $map = [
        'draft' => 'secondary',
        'submitted' => 'primary',
        'verifikasi_marketing' => 'info',
        'revisi_debitur' => 'warning',
        'ditolak_marketing' => 'danger',
        'antrian_admin' => 'primary',
        'penilaian_admin' => 'warning',
        'selesai_dinilai' => 'info',
        'ditolak_sistem' => 'danger',
        'disetujui_sistem' => 'success',
        'aktif' => 'success',
        'nonaktif' => 'secondary',
        'layak' => 'success',
        'tidak_layak' => 'danger',
        'tersedia' => 'success',
        'dipesan' => 'warning',
        'terjual' => 'primary',
        'dibatalkan' => 'danger',
    ];
    $class = $map[$status ?? ''] ?? 'secondary';
@endphp

<span class="badge badge-{{ $class }}">
    {{ Str::headline(str_replace('_', ' ', $status ?? '-')) }}
</span>

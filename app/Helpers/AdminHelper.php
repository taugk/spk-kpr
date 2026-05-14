<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

if (! function_exists('admin_is')) {
    function admin_is(?string $role = 'admin'): bool
    {
        return auth()->check() && auth()->user()->role === $role;
    }
}

if (! function_exists('rupiah')) {
    function rupiah($value): string
    {
        return 'Rp ' . number_format((float) $value, 0, ',', '.');
    }
}

if (! function_exists('status_pengajuan_badge')) {
    function status_pengajuan_badge(?string $status): string
    {
        return match ($status) {
            'draft' => 'secondary',
            'submitted' => 'info',
            'verifikasi_marketing' => 'primary',
            'revisi_debitur' => 'warning',
            'ditolak_marketing', 'ditolak_sistem' => 'danger',
            'antrian_admin', 'penilaian_admin' => 'warning',
            'selesai_dinilai' => 'dark',
            'disetujui_sistem' => 'success',
            default => 'secondary',
        };
    }
}

if (! function_exists('status_pengajuan_label')) {
    function status_pengajuan_label(?string $status): string
    {
        return Str::of($status ?? '-')->replace('_', ' ')->title()->toString();
    }
}

if (! function_exists('kode_otomatis')) {
    function kode_otomatis(string $prefix, string $table, string $column): string
    {
        $tahun = now()->format('Y');
        $latest = DB::table($table)->where($column, 'like', "$prefix-$tahun-%")->orderByDesc('id')->value($column);
        $urut = $latest ? ((int) Str::afterLast($latest, '-') + 1) : 1;

        return sprintf('%s-%s-%05d', $prefix, $tahun, $urut);
    }
}

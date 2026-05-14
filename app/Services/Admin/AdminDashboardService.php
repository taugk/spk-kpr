<?php

namespace App\Services\Admin;

use Illuminate\Support\Facades\DB;

class AdminDashboardService
{
    public function summary(): array
    {
        $pengajuan = DB::table('pengajuan');

        return [
            'total_pengajuan' => (clone $pengajuan)->count(),
            'antrian_admin' => (clone $pengajuan)->whereIn('status', ['antrian_admin', 'penilaian_admin'])->count(),
            'disetujui' => (clone $pengajuan)->where('status', 'disetujui_sistem')->count(),
            'ditolak' => (clone $pengajuan)->whereIn('status', ['ditolak_sistem', 'ditolak_marketing'])->count(),
            'rata_skor' => round((float) DB::table('penilaian')->avg('skor_akhir'), 2),
            'belum_dibaca' => auth()->check()
                ? DB::table('notifikasi')->where('user_id', auth()->id())->where('dibaca', 0)->count()
                : 0,
        ];
    }

    public function latestPengajuan(int $limit = 10)
    {
        return DB::table('v_pengajuan_lengkap')->orderByDesc('pengajuan_id')->limit($limit)->get();
    }

    public function monthlyStats()
    {
        return DB::table('v_statistik_bulanan')->limit(12)->get();
    }
}

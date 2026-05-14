<?php

namespace App\Services\Admin;

use Illuminate\Support\Facades\DB;

class ReportService
{
    public function rekapPengajuan(?string $dari = null, ?string $sampai = null)
    {
        $pengajuan = DB::table('v_pengajuan_lengkap')
            ->when($dari, fn ($q) => $q->whereDate('tgl_submitted', '>=', $dari))
            ->when($sampai, fn ($q) => $q->whereDate('tgl_submitted', '<=', $sampai))
            ->orderByDesc('pengajuan_id')
            ->get();

        // Tambahkan data laporan untuk setiap pengajuan
        foreach ($pengajuan as $item) {
            // Ambil laporan terbaru untuk pengajuan ini
            $laporan = DB::table('laporan')
                ->where('pengajuan_id', $item->pengajuan_id)
                ->orderByDesc('tgl_cetak')
                ->first();
            
            $item->nomor_laporan = $laporan->nomor_laporan ?? '-';
            $item->path_file = $laporan->path_file ?? null;
            $item->tgl_cetak = $laporan->tgl_cetak ?? null;
            $item->jenis_laporan = $laporan->jenis_laporan ?? '-';
        }

        return $pengajuan;
    }

    public function simpanLogLaporan(int $pengajuanId, ?int $penilaianId, string $jenis, string $pathFile): int
    {
        return DB::table('laporan')->insertGetId([
            'pengajuan_id' => $pengajuanId,
            'penilaian_id' => $penilaianId,
            'jenis_laporan' => $jenis,
            'nomor_laporan' => kode_otomatis('LAP', 'laporan', 'nomor_laporan'),
            'dibuat_oleh' => auth()->id(),
            'path_file' => $pathFile,
            'tgl_cetak' => now(),
        ]);
    }

    /**
     * Ambil detail laporan berdasarkan pengajuan_id
     */
    public function getLaporanByPengajuanId(int $pengajuanId)
    {
        return DB::table('laporan')
            ->where('pengajuan_id', $pengajuanId)
            ->orderByDesc('tgl_cetak')
            ->first();
    }

    /**
     * Ambil semua laporan dengan join ke pengajuan
     */
    public function getAllLaporan(?string $dari = null, ?string $sampai = null)
    {
        return DB::table('laporan as l')
            ->join('v_pengajuan_lengkap as p', 'l.pengajuan_id', '=', 'p.pengajuan_id')
            ->when($dari, fn ($q) => $q->whereDate('l.tgl_cetak', '>=', $dari))
            ->when($sampai, fn ($q) => $q->whereDate('l.tgl_cetak', '<=', $sampai))
            ->select(
                'l.*',
                'p.nama_debitur',
                'p.no_ktp',
                'p.no_npwp',
                'p.jenis_pengajuan',
                'p.plafon_pengajuan'
            )
            ->orderByDesc('l.tgl_cetak')
            ->get();
    }
}
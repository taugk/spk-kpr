<?php

namespace App\Services\Admin;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\{DB, Log};

class PengajuanAdminService
{
    public function paginate(array $filter = [], int $perPage = 10): LengthAwarePaginator
    {
        return DB::table('v_pengajuan_lengkap')
            ->select('*')
            ->when($filter['status'] ?? null, fn ($q, $status) => $q->where('status', $status))
            ->when($filter['keyword'] ?? null, function ($q, $keyword) {
                $q->where(function ($sub) use ($keyword) {
                    $sub->where('kode_pengajuan', 'like', "%{$keyword}%")
                        ->orWhere('nama_debitur', 'like', "%{$keyword}%")
                        ->orWhere('nama_proyek', 'like', "%{$keyword}%");
                });
            })
            ->orderByDesc('pengajuan_id')
            ->paginate($perPage)
            ->through(function ($item) {
                if (!isset($item->created_at) && isset($item->tanggal_pengajuan)) {
                    $item->created_at = $item->tanggal_pengajuan;
                } elseif (!isset($item->created_at)) {
                    $item->created_at = null;
                }
                return $item;
            })
            ->withQueryString();
    }

    /**
     * Get complete detail of pengajuan with all related data
     */
    public function detail(int $id): object
    {
        // Get main pengajuan data with relations
        $pengajuan = DB::table('pengajuan as p')
            ->leftJoin('unit as u', 'u.id', '=', 'p.unit_id')
            ->leftJoin('tipe_unit as tu', 'tu.id', '=', 'u.tipe_unit_id')
            ->leftJoin('proyek as pr', 'pr.id', '=', 'tu.proyek_id')
            ->leftJoin('users as debit', 'debit.id', '=', 'p.user_id')
            ->leftJoin('users as marketing', 'marketing.id', '=', 'p.marketing_id')
            ->leftJoin('users as admin', 'admin.id', '=', 'p.admin_id')
            ->select(
                'p.*',
                'u.kode_unit',
                'u.foto_unit',
                'u.fasilitas as unit_fasilitas',
                'tu.kode_tipe',
                'tu.nama_tipe',
                'tu.luas_tanah',
                'tu.luas_bangunan',
                'tu.jumlah_kamar',
                'tu.jumlah_wc',
                'pr.kode_proyek',
                'pr.nama_proyek',
                'pr.lokasi as proyek_lokasi',
                'pr.kota as proyek_kota',
                'pr.provinsi as proyek_provinsi',
                'debit.nama_lengkap as nama_debitur',
                'debit.email as email_debitur',
                'marketing.nama_lengkap as nama_marketing',
                'admin.nama_lengkap as nama_admin'
            )
            ->where('p.id', $id)
            ->firstOrFail();

        // Get debitur personal data
        $pengajuan->debitur_pribadi = DB::table('debitur_pribadi')
            ->where('user_id', $pengajuan->user_id)
            ->first();

        // Get debitur employment data
        $pengajuan->debitur_pekerjaan = DB::table('debitur_pekerjaan')
            ->where('user_id', $pengajuan->user_id)
            ->first();

        // Get debitur financial data
        $pengajuan->debitur_keuangan = DB::table('debitur_keuangan')
            ->where('user_id', $pengajuan->user_id)
            ->first();

        // Get documents
        $pengajuan->dokumen = DB::table('dokumen_pengajuan')
            ->where('pengajuan_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Get status history
        $pengajuan->riwayat = DB::table('riwayat_status')
            ->where('pengajuan_id', $id)
            ->orderBy('created_at', 'asc')
            ->get();

        // Get assessment data if exists
        $pengajuan->penilaian = DB::table('penilaian')
            ->where('pengajuan_id', $id)
            ->first();

        if ($pengajuan->penilaian) {
            $pengajuan->penilaian->details = DB::table('v_penilaian_detail')
                ->where('penilaian_id', $pengajuan->penilaian->id)
                ->get();
        }

        return $pengajuan;
    }

    public function ubahStatus(int $id, string $statusBaru, ?string $keterangan = null): void
    {
        DB::transaction(function () use ($id, $statusBaru, $keterangan) {
            $pengajuan = DB::table('pengajuan')
                ->where('id', $id)
                ->firstOrFail();

            $statusLama = $pengajuan->status;

            DB::table('pengajuan')
                ->where('id', $id)
                ->update([
                    'status' => $statusBaru,
                    'updated_at' => now(),
                ]);

            DB::table('riwayat_status')->insert([
                'pengajuan_id' => $id,
                'status_lama' => $statusLama,
                'status_baru' => $statusBaru,
                'diubah_oleh' => auth()->id(),
                'keterangan' => $keterangan,
                'created_at' => now(),
            ]);

            $this->sendStatusNotification($id, $statusLama, $statusBaru);
        });
    }

    private function sendStatusNotification(int $pengajuanId, string $statusLama, string $statusBaru): void
    {
        $pengajuan = DB::table('pengajuan')
            ->where('id', $pengajuanId)
            ->first();

        if (!$pengajuan) {
            return;
        }

        DB::table('notifikasi')->insert([
            'user_id' => $pengajuan->user_id,
            'pengajuan_id' => $pengajuanId,
            'judul' => 'Status Pengajuan Diperbarui',
            'pesan' => "Status pengajuan {$pengajuan->kode_pengajuan} berubah dari {$statusLama} menjadi {$statusBaru}.",
            'tipe' => in_array($statusBaru, ['disetujui_sistem', 'layak']) ? 'sukses' : 'info',
            'dibaca' => false,
            'created_at' => now(),
        ]);
    }
}
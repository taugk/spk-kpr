<?php

namespace App\Observers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DokumenPengajuanObserver
{
    public function updated(Model $dokumen): void
    {
        if (! $dokumen->wasChanged('status_verifikasi')) {
            return;
        }

        DB::table('riwayat_status')->insert([
            'pengajuan_id' => $dokumen->pengajuan_id,
            'status_lama' => null,
            'status_baru' => 'dokumen_' . $dokumen->status_verifikasi,
            'diubah_oleh' => auth()->id(),
            'keterangan' => 'Dokumen ' . $dokumen->jenis_dokumen . ' diperbarui status verifikasinya.',
            'created_at' => now(),
        ]);
    }
}

<?php

namespace App\Observers;

use App\Services\Admin\NotificationService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PenilaianObserver
{
    public function created(Model $penilaian): void
    {
        DB::table('pengajuan')->where('id', $penilaian->pengajuan_id)->update([
            'status' => 'penilaian_admin',
            'tgl_admin_proses' => now(),
            'updated_at' => now(),
        ]);
    }

    public function updated(Model $penilaian): void
    {
        if (! $penilaian->wasChanged('hasil')) {
            return;
        }

        $pengajuan = DB::table('pengajuan')->where('id', $penilaian->pengajuan_id)->first();

        if ($pengajuan) {
            app(NotificationService::class)->send(
                userId: $pengajuan->user_id,
                judul: 'Hasil penilaian KPR tersedia',
                pesan: 'Pengajuan Anda telah dinilai dengan hasil: ' . str_replace('_', ' ', $penilaian->hasil) . '.',
                tipe: $penilaian->hasil === 'layak' ? 'sukses' : 'peringatan',
                pengajuanId: $pengajuan->id
            );
        }
    }
}

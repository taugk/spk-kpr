<?php

namespace App\Services\Admin;

use Illuminate\Support\Facades\DB;

class NotificationService
{
    public function send(int $userId, string $judul, string $pesan, string $tipe = 'info', ?int $pengajuanId = null): void
    {
        DB::table('notifikasi')->insert([
            'user_id' => $userId,
            'pengajuan_id' => $pengajuanId,
            'judul' => $judul,
            'pesan' => $pesan,
            'tipe' => $tipe,
            'dibaca' => 0,
            'created_at' => now(),
        ]);
    }
}

<?php

namespace App\Observers;

use Illuminate\Support\Facades\Log;

use App\Models\{Pengajuan, Unit};

class DebiturPengajuanObserver
{
    /**
     * Setelah pengajuan baru dibuat.
     * – Log audit
     * – Kirim notifikasi internal ke marketing (jika bukan draft)
     */
    public function created(Pengajuan $pengajuan): void
    {
        Log::info('[Pengajuan] Dibuat', [
            'id'             => $pengajuan->id,
            'kode'           => $pengajuan->kode_pengajuan,
            'user_id'        => $pengajuan->user_id,
            'status'         => $pengajuan->status,
        ]);

        if ($pengajuan->status === 'submitted') {
            $this->notifikasiMarketingBaru($pengajuan);
        }
    }

    /**
     * Setelah pengajuan diperbarui.
     * – Pantau perubahan status
     * – Kirim notifikasi sesuai transisi
     */
    public function updated(Pengajuan $pengajuan): void
    {
        if (!$pengajuan->wasChanged('status')) {
            return;
        }

        $statusLama = $pengajuan->getOriginal('status');
        $statusBaru = $pengajuan->status;

        Log::info('[Pengajuan] Status berubah', [
            'id'   => $pengajuan->id,
            'kode' => $pengajuan->kode_pengajuan,
            'dari' => $statusLama,
            'ke'   => $statusBaru,
        ]);

        match ($statusBaru) {
            'submitted'            => $this->notifikasiMarketingBaru($pengajuan),
            'verifikasi_marketing' => $this->notifikasiDebiturProses($pengajuan, 'Pengajuan Anda sedang diverifikasi oleh marketing.'),
            'revisi_debitur'       => $this->notifikasiDebiturRevisi($pengajuan),
            'ditolak_marketing'    => $this->notifikasiDebiturDitolak($pengajuan, 'marketing'),
            'antrian_admin'        => $this->notifikasiAdminBaru($pengajuan),
            'penilaian_admin'      => $this->notifikasiDebiturProses($pengajuan, 'Pengajuan Anda sedang dinilai oleh admin.'),
            'disetujui_sistem'     => $this->notifikasiDebiturDisetujui($pengajuan),
            'ditolak_sistem'       => $this->notifikasiDebiturDitolak($pengajuan, 'sistem'),
            default                => null,
        };

        // Bebaskan unit kembali jika pengajuan ditolak
        if (in_array($statusBaru, ['ditolak_marketing', 'ditolak_sistem'], true)) {
            $pengajuan->unit?->update(['status' => 'tersedia']);
        }

        // Tandai unit terjual jika disetujui
        if ($statusBaru === 'disetujui_sistem') {
            $pengajuan->unit?->update(['status' => 'terjual']);
        }
    }

    /**
     * Setelah pengajuan dihapus.
     * – Log audit
     * – Bebaskan unit jika masih terkunci
     */
    public function deleted(Pengajuan $pengajuan): void
    {
        Log::info('[Pengajuan] Dihapus', [
            'id'      => $pengajuan->id,
            'kode'    => $pengajuan->kode_pengajuan,
            'user_id' => $pengajuan->user_id,
        ]);

        // Bebaskan unit yang sempat di-booking
        if ($pengajuan->unit && $pengajuan->unit->status === 'dipesan') {
            $pengajuan->unit->update(['status' => 'tersedia']);
        }
    }

    // -----------------------------------------------------------------------
    // Notifikasi helpers (implementasi bisa diarahkan ke Notification / Event)
    // -----------------------------------------------------------------------

    private function notifikasiMarketingBaru(Pengajuan $pengajuan): void
    {
        // TODO: dispatch(new PengajuanBaruEvent($pengajuan));
        // Atau: Marketing::all()->each(fn($m) => $m->notify(new PengajuanBaruNotification($pengajuan)));
        Log::info('[Notif] Marketing: pengajuan baru masuk', ['kode' => $pengajuan->kode_pengajuan]);
    }

    private function notifikasiDebiturProses(Pengajuan $pengajuan, string $pesan): void
    {
        // TODO: $pengajuan->user->notify(new StatusPengajuanNotification($pengajuan, $pesan));
        Log::info('[Notif] Debitur: ' . $pesan, ['kode' => $pengajuan->kode_pengajuan]);
    }

    private function notifikasiDebiturRevisi(Pengajuan $pengajuan): void
    {
        // TODO: $pengajuan->user->notify(new RevisiDiperlukanNotification($pengajuan));
        Log::info('[Notif] Debitur: pengajuan perlu direvisi', ['kode' => $pengajuan->kode_pengajuan]);
    }

    private function notifikasiDebiturDitolak(Pengajuan $pengajuan, string $oleh): void
    {
        // TODO: $pengajuan->user->notify(new PengajuanDitolakNotification($pengajuan, $oleh));
        Log::info('[Notif] Debitur: pengajuan ditolak oleh ' . $oleh, ['kode' => $pengajuan->kode_pengajuan]);
    }

    private function notifikasiAdminBaru(Pengajuan $pengajuan): void
    {
        // TODO: Admin::all()->each(fn($a) => $a->notify(new AntrianAdminNotification($pengajuan)));
        Log::info('[Notif] Admin: pengajuan masuk antrian penilaian', ['kode' => $pengajuan->kode_pengajuan]);
    }

    private function notifikasiDebiturDisetujui(Pengajuan $pengajuan): void
    {
        // TODO: $pengajuan->user->notify(new PengajuanDisetujuiNotification($pengajuan));
        Log::info('[Notif] Debitur: pengajuan DISETUJUI', ['kode' => $pengajuan->kode_pengajuan]);
    }
}
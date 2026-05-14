<?php

namespace App\Observers;

use Illuminate\Support\Facades\Auth;

use App\Models\{Notifikasi, Pengajuan, RiwayatStatus};

class PengajuanKPRObserver
{
    /**
     * Handle the Pengajuan "created" event.
     */
    public function created(Pengajuan $pengajuan): void
    {
        // Create initial status history
        RiwayatStatus::create([
            'pengajuan_id' => $pengajuan->id,
            'status_lama' => null,
            'status_baru' => $pengajuan->status,
            'diubah_oleh' => Auth::id(),
            'keterangan' => 'Pengajuan KPR dibuat oleh debitur',
            'created_at' => now(),
        ]);

        // Notify all marketing users
        Notifikasi::sendToRole(
            'marketing',
            'Pengajuan KPR Baru',
            "Pengajuan KPR baru dengan kode {$pengajuan->kode_pengajuan} memerlukan verifikasi.",
            Notifikasi::TIPE_INFO,
            $pengajuan->id
        );
    }

    /**
     * Handle the Pengajuan "updated" event.
     */
    public function updated(Pengajuan $pengajuan): void
    {
        // If status changed, create history
        if ($pengajuan->isDirty('status')) {
            $oldStatus = $pengajuan->getOriginal('status');
            $newStatus = $pengajuan->status;

            RiwayatStatus::create([
                'pengajuan_id' => $pengajuan->id,
                'status_lama' => $oldStatus,
                'status_baru' => $newStatus,
                'diubah_oleh' => Auth::id(),
                'keterangan' => $this->getStatusChangeDescription($oldStatus, $newStatus),
                'created_at' => now(),
            ]);

            // Send notifications for specific status changes
            $this->sendStatusNotification($pengajuan, $oldStatus, $newStatus);
        }
    }

    /**
     * Get description for status change
     */
    private function getStatusChangeDescription(?string $oldStatus, string $newStatus): string
    {
        $descriptions = [
            Pengajuan::STATUS_SUBMITTED => 'Pengajuan KPR disubmit oleh debitur',
            Pengajuan::STATUS_VERIFIKASI_MARKETING => 'Verifikasi KPR oleh marketing dimulai',
            Pengajuan::STATUS_REVISI_DEBITUR => 'Revisi data/dokumen diminta dari debitur',
            Pengajuan::STATUS_DITOLAK_MARKETING => 'Pengajuan KPR ditolak oleh marketing',
            Pengajuan::STATUS_ANTRIAN_ADMIN => 'Pengajuan KPR diteruskan ke admin untuk penilaian',
            Pengajuan::STATUS_PENILAIAN_ADMIN => 'Penilaian kelayakan KPR dimulai',
            Pengajuan::STATUS_SELESAI_DINILAI => 'Penilaian kelayakan KPR selesai',
            Pengajuan::STATUS_DISETUJUI_SISTEM => 'KPR disetujui - debitur layak mendapatkan KPR',
            Pengajuan::STATUS_DITOLAK_SISTEM => 'KPR ditolak - debitur tidak memenuhi kriteria',
        ];

        return $descriptions[$newStatus] ?? 'Status pengajuan KPR berubah';
    }

    /**
     * Send notification for status change
     */
    private function sendStatusNotification(Pengajuan $pengajuan, ?string $oldStatus, string $newStatus): void
    {
        $notifications = [
            Pengajuan::STATUS_DISETUJUI_SISTEM => [
                'judul' => 'KPR Disetujui!',
                'pesan' => "Selamat! Pengajuan KPR Anda untuk unit {$pengajuan->unit->kode_unit} telah disetujui.",
                'tipe' => Notifikasi::TIPE_SUKSES,
            ],
            Pengajuan::STATUS_DITOLAK_SISTEM => [
                'judul' => 'Pengajuan KPR Ditolak',
                'pesan' => "Maaf, pengajuan KPR Anda belum dapat disetujui karena tidak memenuhi kriteria.",
                'tipe' => Notifikasi::TIPE_ERROR,
            ],
            Pengajuan::STATUS_REVISI_DEBITUR => [
                'judul' => 'Perlu Revisi Data KPR',
                'pesan' => "Pengajuan KPR Anda perlu direvisi. Silakan cek catatan yang diberikan.",
                'tipe' => Notifikasi::TIPE_PERINGATAN,
            ],
        ];

        if (isset($notifications[$newStatus])) {
            $notif = $notifications[$newStatus];
            Notifikasi::send(
                $pengajuan->user_id,
                $notif['judul'],
                $notif['pesan'] . ($pengajuan->catatan_debitur ? " Catatan: {$pengajuan->catatan_debitur}" : ''),
                $notif['tipe'],
                $pengajuan->id
            );
        }
    }
}

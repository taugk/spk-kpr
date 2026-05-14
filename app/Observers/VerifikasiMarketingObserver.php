<?php

namespace App\Observers;

use App\Models\{Notifikasi, VerifikasiMarketing};

class VerifikasiMarketingObserver
{
    /**
     * Handle the VerifikasiMarketing "created" event.
     */
    public function created(VerifikasiMarketing $verifikasiMarketing): void
    {
        Notifikasi::send(
            $verifikasiMarketing->marketing_id,
            'Verifikasi KPR Dimulai',
            "Anda ditugaskan memverifikasi pengajuan KPR #{$verifikasiMarketing->pengajuan->kode_pengajuan}",
            Notifikasi::TIPE_INFO,
            $verifikasiMarketing->pengajuan_id
        );
    }

    /**
     * Handle the VerifikasiMarketing "updated" event.
     */
    public function updated(VerifikasiMarketing $verifikasiMarketing): void
    {
        // Document verification complete
        if ($verifikasiMarketing->wasChanged('dok_ktp_valid') &&
            $this->isDocumentVerificationComplete($verifikasiMarketing)) {

            Notifikasi::send(
                $verifikasiMarketing->marketing_id,
                'Verifikasi Dokumen KPR Selesai',
                "Verifikasi dokumen KPR untuk pengajuan #{$verifikasiMarketing->pengajuan->kode_pengajuan} telah selesai.",
                Notifikasi::TIPE_SUKSES,
                $verifikasiMarketing->pengajuan_id
            );
        }

        // Field verification complete
        if ($verifikasiMarketing->wasChanged('tgl_kunjungan') &&
            $verifikasiMarketing->tgl_kunjungan) {

            Notifikasi::send(
                $verifikasiMarketing->marketing_id,
                'Verifikasi Lapangan KPR Selesai',
                "Verifikasi lapangan KPR untuk pengajuan #{$verifikasiMarketing->pengajuan->kode_pengajuan} telah selesai.",
                Notifikasi::TIPE_SUKSES,
                $verifikasiMarketing->pengajuan_id
            );
        }

        // Decision made
        if ($verifikasiMarketing->wasChanged('keputusan') && $verifikasiMarketing->keputusan) {
            $this->sendDecisionNotification($verifikasiMarketing);
        }
    }

    /**
     * Check if document verification is complete
     */
    private function isDocumentVerificationComplete(VerifikasiMarketing $verifikasi): bool
    {
        return !is_null($verifikasi->dok_ktp_valid) &&
               !is_null($verifikasi->dok_kk_valid) &&
               !is_null($verifikasi->dok_slip_gaji_valid) &&
               !is_null($verifikasi->dok_rek_koran_valid) &&
               !is_null($verifikasi->dok_slik_valid) &&
               !is_null($verifikasi->dok_surat_kerja_valid);
    }

    /**
     * Send notification based on decision
     */
    private function sendDecisionNotification(VerifikasiMarketing $verifikasi): void
    {
        $messages = [
            VerifikasiMarketing::KEPUTUSAN_AJUKAN_KE_ADMIN => [
                'judul' => 'Pengajuan KPR Diteruskan',
                'pesan' => "Pengajuan KPR #{$verifikasi->pengajuan->kode_pengajuan} telah diteruskan ke admin.",
                'tipe' => Notifikasi::TIPE_SUKSES,
            ],
            VerifikasiMarketing::KEPUTUSAN_MINTA_REVISI => [
                'judul' => 'Revisi KPR Diminta',
                'pesan' => "Pengajuan KPR #{$verifikasi->pengajuan->kode_pengajuan} memerlukan revisi dari debitur.",
                'tipe' => Notifikasi::TIPE_PERINGATAN,
            ],
            VerifikasiMarketing::KEPUTUSAN_TOLAK => [
                'judul' => 'Pengajuan KPR Ditolak',
                'pesan' => "Pengajuan KPR #{$verifikasi->pengajuan->kode_pengajuan} ditolak pada tahap verifikasi.",
                'tipe' => Notifikasi::TIPE_ERROR,
            ],
        ];

        if (isset($messages[$verifikasi->keputusan])) {
            $msg = $messages[$verifikasi->keputusan];
            Notifikasi::send(
                $verifikasi->marketing_id,
                $msg['judul'],
                $msg['pesan'],
                $msg['tipe'],
                $verifikasi->pengajuan_id
            );
        }
    }
}

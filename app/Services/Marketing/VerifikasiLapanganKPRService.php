<?php

namespace App\Services\Marketing;

use Illuminate\Support\Facades\DB;

use App\Models\{Notifikasi, Pengajuan, RiwayatStatus, VerifikasiMarketing};

class VerifikasiLapanganKPRService
{
    /**
     * Process field verification for KPR (Developer/Property Company)
     * Verifikasi dilakukan untuk memastikan:
     * 1. Debitur benar-benar ada (domisili sesuai)
     * 2. Pekerjaan debitur valid
     * 3. Kemampuan membayar sesuai
     */
    public function processVerification(
        Pengajuan $pengajuan,
        int $marketingId,
        array $data,
        $fotoDokumentasi = null
    ): array {
        DB::beginTransaction();

        try {
            $verifikasi = $pengajuan->verifikasiMarketing;

            if (!$verifikasi) {
                throw new \Exception('Verification record not found');
            }

            // Upload dokumentasi foto jika ada
            $photoPaths = [];
            if ($fotoDokumentasi) {
                foreach ($fotoDokumentasi as $photo) {
                    $path = $photo->store('verifikasi-lapangan/' . $pengajuan->kode_pengajuan, 'public');
                    $photoPaths[] = $path;
                }
            }

            // Update field survey data
            $verifikasi->update([
                'tgl_kunjungan' => $data['tgl_kunjungan'],
                'alamat_terverifikasi' => $data['alamat_terverifikasi'] ?? null,
                'tempat_kerja_terverifikasi' => $data['tempat_kerja_terverifikasi'] ?? null,
                'verifikasi_alamat' => $data['verifikasi_alamat'] ?? false,
                'verifikasi_pekerjaan' => $data['verifikasi_pekerjaan'] ?? false,
                'penghasilan_terverif' => $data['penghasilan_terverif'] ?? false,
                'foto_dokumentasi' => !empty($photoPaths) ? json_encode($photoPaths) : null,
                'catatan_verifikasi' => $data['catatan_lapangan'] ?? null,
                'rekomendasi_marketing' => $data['rekomendasi'],
            ]);

            // Process decision
            $result = $this->processDecision($pengajuan, $verifikasi, $marketingId, $data);

            if (!$result['success']) {
                throw new \Exception($result['message']);
            }

            DB::commit();

            return [
                'success' => true,
                'decision' => $data['keputusan'],
                'message' => $result['message'],
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('KPR field verification failed: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => $e->getMessage() ?: 'Terjadi kesalahan saat verifikasi lapangan.',
            ];
        }
    }

    /**
     * Process decision based on marketing's choice
     */
    private function processDecision(
        Pengajuan $pengajuan,
        VerifikasiMarketing $verifikasi,
        int $marketingId,
        array $data
    ): array {
        switch ($data['keputusan']) {
            case VerifikasiMarketing::KEPUTUSAN_AJUKAN_KE_ADMIN:
                return $this->ajukanKeAdmin($pengajuan, $verifikasi, $marketingId);

            case VerifikasiMarketing::KEPUTUSAN_MINTA_REVISI:
                return $this->mintaRevisi($pengajuan, $verifikasi, $marketingId, $data['alasan']);

            case VerifikasiMarketing::KEPUTUSAN_TOLAK:
                return $this->tolakPengajuan($pengajuan, $verifikasi, $marketingId, $data['alasan']);

            default:
                return [
                    'success' => false,
                    'message' => 'Keputusan tidak valid.',
                ];
        }
    }

    /**
     * Submit to admin for SMART assessment
     */
    private function ajukanKeAdmin(
        Pengajuan $pengajuan,
        VerifikasiMarketing $verifikasi,
        int $marketingId
    ): array {
        $verifikasi->setAjukanKeAdmin();

        $oldStatus = $pengajuan->status;
        $pengajuan->status = Pengajuan::STATUS_ANTRIAN_ADMIN;
        $pengajuan->save();

        RiwayatStatus::createHistory(
            $pengajuan->id,
            Pengajuan::STATUS_ANTRIAN_ADMIN,
            $oldStatus,
            $marketingId,
            'Verifikasi lapangan selesai, data debitur valid. Pengajuan diteruskan ke admin.'
        );

        // Notify admin
        Notifikasi::sendToRole(
            'admin',
            'Pengajuan KPR Siap Dinilai',
            "Pengajuan KPR {$pengajuan->kode_pengajuan} telah lolos verifikasi lapangan dan siap dinilai.",
            Notifikasi::TIPE_INFO,
            $pengajuan->id
        );

        // Notify debitur
        Notifikasi::send(
            $pengajuan->user_id,
            'Verifikasi Lapangan Selesai',
            "Verifikasi lapangan untuk pengajuan KPR Anda telah selesai. Data Anda valid.",
            Notifikasi::TIPE_SUKSES,
            $pengajuan->id
        );

        return [
            'success' => true,
            'message' => 'Pengajuan KPR diteruskan ke admin untuk penilaian kelayakan.',
        ];
    }

    /**
     * Request revision from debitur
     */
    private function mintaRevisi(
        Pengajuan $pengajuan,
        VerifikasiMarketing $verifikasi,
        int $marketingId,
        string $alasan
    ): array {
        $verifikasi->setMintaRevisi($alasan);

        $oldStatus = $pengajuan->status;
        $pengajuan->status = Pengajuan::STATUS_REVISI_DEBITUR;
        $pengajuan->catatan_debitur = $alasan;
        $pengajuan->save();

        RiwayatStatus::createHistory(
            $pengajuan->id,
            Pengajuan::STATUS_REVISI_DEBITUR,
            $oldStatus,
            $marketingId,
            "Data debitur tidak sesuai saat verifikasi lapangan: {$alasan}"
        );

        Notifikasi::send(
            $pengajuan->user_id,
            'Data Perlu Direvisi',
            "Verifikasi lapangan menemukan ketidaksesuaian data. Silakan perbaiki: {$alasan}",
            Notifikasi::TIPE_PERINGATAN,
            $pengajuan->id
        );

        return [
            'success' => true,
            'message' => 'Revisi data diminta dari debitur.',
        ];
    }

    /**
     * Reject KPR application
     */
    private function tolakPengajuan(
        Pengajuan $pengajuan,
        VerifikasiMarketing $verifikasi,
        int $marketingId,
        string $alasan
    ): array {
        $verifikasi->setTolak($alasan);

        $oldStatus = $pengajuan->status;
        $pengajuan->status = Pengajuan::STATUS_DITOLAK_MARKETING;
        $pengajuan->catatan_debitur = $alasan;
        $pengajuan->tgl_selesai = now();
        $pengajuan->save();

        RiwayatStatus::createHistory(
            $pengajuan->id,
            Pengajuan::STATUS_DITOLAK_MARKETING,
            $oldStatus,
            $marketingId,
            "Pengajuan KPR ditolak karena: {$alasan}"
        );

        Notifikasi::send(
            $pengajuan->user_id,
            'Pengajuan KPR Ditolak',
            "Pengajuan KPR Anda ditolak setelah verifikasi lapangan. Alasan: {$alasan}",
            Notifikasi::TIPE_ERROR,
            $pengajuan->id
        );

        return [
            'success' => true,
            'message' => 'Pengajuan KPR ditolak.',
        ];
    }
}

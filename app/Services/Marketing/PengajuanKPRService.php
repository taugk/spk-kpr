<?php

namespace App\Services\Marketing;

use Illuminate\Support\Facades\{DB, Log};

use App\Models\{Notifikasi, Pengajuan, RiwayatStatus, VerifikasiMarketing};

class PengajuanKPRService
{
    /**
     * Ambil pengajuan dari antrian & mulai proses verifikasi dokumen.
     * Status berubah dari 'submitted' → 'verifikasi_marketing'.
     */
    public function startVerifikasi(Pengajuan $pengajuan, int $marketingId): bool
    {
        if (!$this->canProcess($pengajuan, $marketingId)) {
            return false;
        }

        DB::beginTransaction();
        try {
            $oldStatus = $pengajuan->status;

            $pengajuan->update([
                'status'               => Pengajuan::STATUS_VERIFIKASI_MARKETING,
                'marketing_id'         => $marketingId,
                'tgl_marketing_proses' => now(),
            ]);

            // Buat record verifikasi (jika belum ada)
            VerifikasiMarketing::firstOrCreate(
                ['pengajuan_id' => $pengajuan->id],
                ['marketing_id' => $marketingId]
            );

            RiwayatStatus::createHistory(
                pengajuanId: $pengajuan->id,
                statusLama : $oldStatus,
                statusBaru : Pengajuan::STATUS_VERIFIKASI_MARKETING,
                diubahOleh : $marketingId,
                keterangan : 'Pengajuan KPR mulai diverifikasi oleh marketing.'
            );

            Notifikasi::send(
                userId     : $pengajuan->user_id,
                judul      : 'Pengajuan Sedang Diverifikasi',
                pesan      : "Pengajuan KPR Anda ({$pengajuan->kode_pengajuan}) sedang "
                           . "dalam proses verifikasi dokumen oleh tim marketing.",
                tipe       : Notifikasi::TIPE_INFO,
                pengajuanId: $pengajuan->id
            );

            DB::commit();
            return true;

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Gagal memulai verifikasi KPR', [
                'pengajuan_id' => $pengajuan->id,
                'marketing_id' => $marketingId,
                'error'        => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Cek apakah marketing ini boleh mengambil pengajuan tersebut.
     */
    public function canProcess(Pengajuan $pengajuan, int $marketingId): bool
    {
        // Sudah diambil marketing lain → tidak boleh
        if ($pengajuan->marketing_id !== null && $pengajuan->marketing_id !== $marketingId) {
            return false;
        }
        return $pengajuan->status === Pengajuan::STATUS_SUBMITTED;
    }

    /**
     * Ringkasan jumlah antrian untuk badge di sidebar/dashboard.
     */
    public function getQueueSummary(int $marketingId): array
    {
        return [
            // Pengajuan baru yang belum diambil siapapun
            'antrian_masuk'      => Pengajuan::submitted()->whereNull('marketing_id')->count(),

            // Sedang dalam proses verifikasi dokumen oleh marketing ini
            'sedang_diverifikasi' => Pengajuan::where('marketing_id', $marketingId)
                ->where('status', Pengajuan::STATUS_VERIFIKASI_MARKETING)
                ->count(),

            // Sudah diteruskan ke admin, menunggu penilaian SMART
            'menunggu_admin'     => Pengajuan::where('marketing_id', $marketingId)
                ->where('status', Pengajuan::STATUS_ANTRIAN_ADMIN)
                ->count(),

            // Pengajuan yang diminta revisi & belum direspon debitur
            'menunggu_revisi'    => Pengajuan::where('marketing_id', $marketingId)
                ->where('status', Pengajuan::STATUS_REVISI_DEBITUR)
                ->count(),
        ];
    }
}

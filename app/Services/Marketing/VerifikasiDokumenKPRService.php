<?php

namespace App\Services\Marketing;

use Illuminate\Support\Facades\{DB, Log};

use App\Models\{Notifikasi, Pengajuan, RiwayatStatus, VerifikasiMarketing};

/**
 * Alur verifikasi marketing (tanpa kunjungan lapangan):
 *
 *  1. Marketing memeriksa kelengkapan & keabsahan dokumen debitur
 *  2. Marketing mengisi status tiap dokumen (valid / tidak valid)
 *  3. Marketing memilih keputusan langsung: ajukan ke admin | minta revisi | tolak
 *  4. Sistem memindah status pengajuan & kirim notifikasi
 */
class VerifikasiDokumenKPRService
{
    /**
     * Mapping key input form → kolom di tabel verifikasi_marketing.
     * Tepat 7 kolom dok_*_valid sesuai migration.
     */
    private const DOK_MAP = [
        'dok_ktp'         => 'dok_ktp_valid',
        'dok_kk'          => 'dok_kk_valid',
        'dok_slip_gaji'   => 'dok_slip_gaji_valid',
        'dok_rek_koran'   => 'dok_rek_koran_valid',
        'dok_slik'        => 'dok_slik_valid',
        'dok_surat_kerja' => 'dok_surat_kerja_valid',
        'dok_npwp'        => 'dok_npwp_valid',
    ];

    // ─── Public API ─────────────────────────────────────────────────────────

    /**
     * Proses verifikasi dokumen + keputusan akhir dalam satu transaksi.
     *
     * Data yang diharapkan dari controller (setelah validate()):
     *   dok_ktp, dok_kk, dok_slip_gaji, dok_rek_koran,
     *   dok_slik, dok_surat_kerja, dok_npwp (opsional),
     *   rekomendasi (layak | perlu_pertimbangan | tidak_layak),
     *   keputusan   (ajukan_ke_admin | minta_revisi | tolak),
     *   alasan_keputusan (wajib jika keputusan = minta_revisi / tolak)
     */
    public function prosesVerifikasi(Pengajuan $pengajuan, int $marketingId, array $data): array
    {
        DB::beginTransaction();
        try {
            /** @var VerifikasiMarketing $verifikasi */
            $verifikasi = VerifikasiMarketing::firstOrCreate(
                ['pengajuan_id' => $pengajuan->id],
                ['marketing_id' => $marketingId]
            );

            // 1. Simpan status tiap dokumen
            $updateDok = [];
            foreach (self::DOK_MAP as $inputKey => $dbColumn) {
                if (array_key_exists($inputKey, $data)) {
                    $updateDok[$dbColumn] = (bool) $data[$inputKey];
                }
            }
            $verifikasi->update($updateDok);
            $verifikasi->refresh();

            // 2. Rekomendasi: pakai pilihan marketing atau hitung otomatis
            $rekomendasi = $data['rekomendasi'] ?? $verifikasi->hitungRekomendasiOtomatis();
            $verifikasi->update(['rekomendasi_marketing' => $rekomendasi]);

            // 3. Keputusan akhir → ubah status pengajuan + kirim notifikasi
            $keputusan = $data['keputusan'];
            $alasan    = trim($data['alasan_keputusan'] ?? '');

            match ($keputusan) {
                VerifikasiMarketing::KEPUTUSAN_AJUKAN_KE_ADMIN
                    => $this->ajukanKeAdmin($pengajuan, $verifikasi, $marketingId),
                VerifikasiMarketing::KEPUTUSAN_MINTA_REVISI
                    => $this->mintaRevisi($pengajuan, $verifikasi, $marketingId, $alasan),
                VerifikasiMarketing::KEPUTUSAN_TOLAK
                    => $this->tolakPengajuan($pengajuan, $verifikasi, $marketingId, $alasan),
                default
                    => throw new \InvalidArgumentException("Keputusan '{$keputusan}' tidak dikenal."),
            };

            DB::commit();

            return [
                'success'     => true,
                'keputusan'   => $keputusan,
                'rekomendasi' => $rekomendasi,
                'persentase'  => $verifikasi->getDocumentValidationPercentage(),
                'kurang'      => $verifikasi->getMissingDocuments(),
            ];

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Verifikasi dokumen KPR gagal', [
                'pengajuan_id' => $pengajuan->id,
                'marketing_id' => $marketingId,
                'error'        => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage() ?: 'Terjadi kesalahan saat memproses verifikasi.',
            ];
        }
    }

    /**
     * Daftar dokumen untuk form verifikasi beserta flag wajib/opsional.
     */
    public function getDaftarDokumen(Pengajuan $pengajuan): array
    {
        $penghasilan = (float) (optional($pengajuan->debiturPekerjaan)->total_penghasilan ?? 0);

        return [
            ['key' => 'dok_ktp',         'label' => 'KTP Debitur',                     'wajib' => true],
            ['key' => 'dok_kk',          'label' => 'Kartu Keluarga',                  'wajib' => true],
            ['key' => 'dok_slip_gaji',   'label' => 'Slip Gaji / Bukti Penghasilan',   'wajib' => true],
            ['key' => 'dok_rek_koran',   'label' => 'Rekening Koran 3 Bulan Terakhir', 'wajib' => true],
            ['key' => 'dok_slik',        'label' => 'Laporan SLIK OJK',                'wajib' => true],
            ['key' => 'dok_surat_kerja', 'label' => 'Surat Keterangan Kerja / SK',     'wajib' => true],
            ['key' => 'dok_npwp',        'label' => 'NPWP',                            'wajib' => $penghasilan > 4_500_000],
        ];
    }

    // ─── Private: aksi per keputusan ────────────────────────────────────────

    private function ajukanKeAdmin(
        Pengajuan           $pengajuan,
        VerifikasiMarketing $verifikasi,
        int                 $marketingId
    ): void {
        $verifikasi->setAjukanKeAdmin();

        $oldStatus = $pengajuan->status;
        $pengajuan->update(['status' => Pengajuan::STATUS_ANTRIAN_ADMIN]);

        RiwayatStatus::createHistory(
            pengajuanId: $pengajuan->id,
            statusLama : $oldStatus,
            statusBaru : Pengajuan::STATUS_ANTRIAN_ADMIN,
            diubahOleh : $marketingId,
            keterangan : 'Verifikasi dokumen selesai. Dokumen dinyatakan valid. '
                       . 'Pengajuan masuk antrian admin untuk penilaian kelayakan KPR.'
        );

        Notifikasi::sendToRole(
            role       : 'admin',
            judul      : 'Pengajuan KPR Siap Dinilai',
            pesan      : "Pengajuan {$pengajuan->kode_pengajuan} lolos verifikasi dokumen "
                       . "dan siap dinilai dengan metode SMART.",
            tipe       : Notifikasi::TIPE_INFO,
            pengajuanId: $pengajuan->id
        );

        Notifikasi::send(
            userId     : $pengajuan->user_id,
            judul      : 'Dokumen Terverifikasi',
            pesan      : "Dokumen KPR Anda ({$pengajuan->kode_pengajuan}) dinyatakan lengkap "
                       . "dan valid. Pengajuan sedang menunggu penilaian kelayakan.",
            tipe       : Notifikasi::TIPE_SUKSES,
            pengajuanId: $pengajuan->id
        );
    }

    private function mintaRevisi(
        Pengajuan           $pengajuan,
        VerifikasiMarketing $verifikasi,
        int                 $marketingId,
        string              $alasan
    ): void {
        $verifikasi->setMintaRevisi($alasan);

        $oldStatus = $pengajuan->status;
        $pengajuan->update([
            'status'          => Pengajuan::STATUS_REVISI_DEBITUR,
            'catatan_debitur' => $alasan,
        ]);

        RiwayatStatus::createHistory(
            pengajuanId: $pengajuan->id,
            statusLama : $oldStatus,
            statusBaru : Pengajuan::STATUS_REVISI_DEBITUR,
            diubahOleh : $marketingId,
            keterangan : "Dokumen belum lengkap atau tidak sesuai. Revisi diminta dengan alasan: {$alasan}"
        );

        Notifikasi::send(
            userId     : $pengajuan->user_id,
            judul      : 'Dokumen Perlu Diperbaiki',
            pesan      : "Dokumen KPR Anda ({$pengajuan->kode_pengajuan}) perlu dilengkapi "
                       . "atau diperbaiki. Catatan dari marketing: {$alasan}",
            tipe       : Notifikasi::TIPE_PERINGATAN,
            pengajuanId: $pengajuan->id
        );
    }

    private function tolakPengajuan(
        Pengajuan           $pengajuan,
        VerifikasiMarketing $verifikasi,
        int                 $marketingId,
        string              $alasan
    ): void {
        $verifikasi->setTolak($alasan);

        $oldStatus = $pengajuan->status;
        $pengajuan->update([
            'status'          => Pengajuan::STATUS_DITOLAK_MARKETING,
            'catatan_debitur' => $alasan,
            'tgl_selesai'     => now(),
        ]);

        RiwayatStatus::createHistory(
            pengajuanId: $pengajuan->id,
            statusLama : $oldStatus,
            statusBaru : Pengajuan::STATUS_DITOLAK_MARKETING,
            diubahOleh : $marketingId,
            keterangan : "Pengajuan KPR ditolak pada tahap verifikasi dokumen. Alasan: {$alasan}"
        );

        Notifikasi::send(
            userId     : $pengajuan->user_id,
            judul      : 'Pengajuan KPR Ditolak',
            pesan      : "Pengajuan KPR Anda ({$pengajuan->kode_pengajuan}) tidak dapat diproses "
                       . "lebih lanjut. Alasan: {$alasan}",
            tipe       : Notifikasi::TIPE_ERROR,
            pengajuanId: $pengajuan->id
        );
    }
}

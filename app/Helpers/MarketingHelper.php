<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Cache;

use App\Models\{Pengajuan, VerifikasiMarketing};

class MarketingHelper
{
    /**
     * Get status badge HTML
     */
    public static function getStatusBadge(string $status): string
    {
        $badges = [
            Pengajuan::STATUS_DRAFT => '<span class="badge badge-secondary px-3 py-2">Draft</span>',
            Pengajuan::STATUS_SUBMITTED => '<span class="badge badge-primary px-3 py-2">Menunggu Verifikasi</span>',
            Pengajuan::STATUS_VERIFIKASI_MARKETING => '<span class="badge badge-info px-3 py-2">Sedang Diverifikasi</span>',
            Pengajuan::STATUS_REVISI_DEBITUR => '<span class="badge badge-warning px-3 py-2">Perlu Revisi</span>',
            Pengajuan::STATUS_DITOLAK_MARKETING => '<span class="badge badge-danger px-3 py-2">Ditolak Marketing</span>',
            Pengajuan::STATUS_ANTRIAN_ADMIN => '<span class="badge badge-secondary px-3 py-2">Antrian Admin</span>',
            Pengajuan::STATUS_PENILAIAN_ADMIN => '<span class="badge bg-gradient-info px-3 py-2">Penilaian Admin</span>',
            Pengajuan::STATUS_SELESAI_DINILAI => '<span class="badge bg-gradient-primary px-3 py-2">Selesai Dinilai</span>',
            Pengajuan::STATUS_DISETUJUI_SISTEM => '<span class="badge badge-success px-3 py-2">Disetujui</span>',
            Pengajuan::STATUS_DITOLAK_SISTEM => '<span class="badge badge-danger px-3 py-2">Ditolak Sistem</span>',
        ];

        return $badges[$status] ?? '<span class="badge badge-secondary px-3 py-2">' . e($status) . '</span>';
    }

    /**
     * Get rekomendasi badge
     */
    public static function getRekomendasiBadge(?string $rekomendasi): string
    {
        $badges = [
            VerifikasiMarketing::REKOMENDASI_LAYAK => '<span class="badge badge-success">Layak</span>',
            VerifikasiMarketing::REKOMENDASI_PERLU_PERTIMBANGAN => '<span class="badge badge-warning">Perlu Pertimbangan</span>',
            VerifikasiMarketing::REKOMENDASI_TIDAK_LAYAK => '<span class="badge badge-danger">Tidak Layak</span>',
        ];

        return $badges[$rekomendasi] ?? '<span class="badge badge-secondary">-</span>';
    }

    /**
     * Get document verification progress bar
     */
    public static function getDocumentProgressBar(VerifikasiMarketing $verifikasi): string
    {
        $percentage = $verifikasi->getDocumentValidationPercentage();
        $color = $percentage >= 80 ? 'success' : ($percentage >= 50 ? 'warning' : 'danger');

        return '
            <div class="progress" style="height: 8px;">
                <div class="progress-bar bg-' . $color . '"
                     role="progressbar"
                     style="width: ' . $percentage . '%;"
                     aria-valuenow="' . $percentage . '"
                     aria-valuemin="0"
                     aria-valuemax="100">
                </div>
            </div>
            <small class="text-muted">' . $percentage . '% Dokumen Valid</small>
        ';
    }

    /**
     * Format rupiah
     */
    public static function formatRupiah($amount): string
    {
        if (!$amount) {
            return 'Rp 0';
        }

        return 'Rp ' . number_format($amount, 0, ',', '.');
    }

    /**
     * Get waktu relatif (time ago)
     */
    public static function timeAgo($datetime): string
    {
        if (!$datetime) {
            return '-';
        }

        return \Carbon\Carbon::parse($datetime)->diffForHumans();
    }

    /**
     * Get queue count for marketing dashboard
     */
    public static function getQueueCount(int $marketingId): array
    {
        $cacheKey = "marketing_queue_{$marketingId}";

        return Cache::remember($cacheKey, 300, function () use ($marketingId) {
            return [
                'menunggu_verifikasi' => Pengajuan::where('marketing_id', $marketingId)
                    ->where('status', Pengajuan::STATUS_SUBMITTED)
                    ->count(),

                'sedang_diverifikasi' => Pengajuan::where('marketing_id', $marketingId)
                    ->where('status', Pengajuan::STATUS_VERIFIKASI_MARKETING)
                    ->count(),

                'revisi' => Pengajuan::where('marketing_id', $marketingId)
                    ->where('status', Pengajuan::STATUS_REVISI_DEBITUR)
                    ->count(),

                'antrian_admin' => Pengajuan::where('marketing_id', $marketingId)
                    ->where('status', Pengajuan::STATUS_ANTRIAN_ADMIN)
                    ->count(),
            ];
        });
    }

    /**
     * Clear cache for marketing
     */
    public static function clearCache(int $marketingId): void
    {
        Cache::forget("marketing_queue_{$marketingId}");
        Cache::forget("marketing_stats_{$marketingId}");
    }
}

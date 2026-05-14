<?php

namespace App\Http\Controllers\Marketing;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB};

use App\Http\Controllers\Controller;
use App\Models\{DokumenPengajuan, Pengajuan, Unit, VAntrianMarketing, VStatistikBulanan, VerifikasiMarketing};

class MarketingDashboardController extends Controller
{
    /**
     * Display marketing dashboard
     */
    public function index()
    {
        $marketingId = Auth::id();

        // Queue counts from VAntrianMarketing view
        $queueCounts = VAntrianMarketing::getQueueCounts($marketingId);

        // Enhanced queue counts for dashboard
        $queueCounts['document_verification'] = Pengajuan::where('marketing_id', $marketingId)
            ->where('status', Pengajuan::STATUS_VERIFIKASI_MARKETING)
            ->whereHas('verifikasiMarketing', function($q) {
                $q->whereNull('dok_ktp_valid');
            })->count();

        $queueCounts['field_verification'] = Pengajuan::where('marketing_id', $marketingId)
            ->where('status', Pengajuan::STATUS_VERIFIKASI_MARKETING)
            ->whereHas('verifikasiMarketing', function($q) {
                $q->whereNotNull('dok_ktp_valid')
                  ->whereNull('tgl_kunjungan');
            })->count();

        $queueCounts['sent_to_admin'] = Pengajuan::where('marketing_id', $marketingId)
            ->where('status', Pengajuan::STATUS_ANTRIAN_ADMIN)
            ->count();

        // Performance statistics for KPR
        $stats = $this->getPerformanceStats($marketingId);

        // Monthly statistics from view
        $monthlyStats = VStatistikBulanan::lastMonths(12)
            ->orderBy('periode', 'desc')
            ->get();

        // Summary statistics
        $summaryStats = VStatistikBulanan::getSummaryStatistics();

        // Recent pengajuan KPR
        $recentPengajuan = Pengajuan::where('marketing_id', $marketingId)
            ->with(['user', 'unit.tipeUnit.proyek', 'verifikasiMarketing'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Chart data
        $chartData = $this->getChartData($marketingId);

        // Document verification statistics
        $documentStats = $this->getDocumentStats($marketingId);

        // Decision distribution statistics
        $decisionStats = $this->getDecisionStats($marketingId);

        return view('marketing.pages.dashboard.index', compact(
            'queueCounts',
            'stats',
            'monthlyStats',
            'summaryStats',
            'recentPengajuan',
            'chartData',
            'documentStats',
            'decisionStats'
        ));
    }

    /**
     * Get performance statistics for KPR marketing
     */
    private function getPerformanceStats(int $marketingId): array
    {
        // Total pengajuan yang diproses
        $totalProcessed = Pengajuan::where('marketing_id', $marketingId)
            ->whereNotNull('tgl_marketing_proses')
            ->count();

        // KPR disetujui (melalui sistem SMART)
        $totalApproved = Pengajuan::where('marketing_id', $marketingId)
            ->where('status', Pengajuan::STATUS_DISETUJUI_SISTEM)
            ->count();

        // KPR ditolak (oleh marketing atau sistem)
        $totalRejected = Pengajuan::where('marketing_id', $marketingId)
            ->whereIn('status', [
                Pengajuan::STATUS_DITOLAK_MARKETING,
                Pengajuan::STATUS_DITOLAK_SISTEM,
            ])->count();

        // Perlu revisi
        $totalRevision = Pengajuan::where('marketing_id', $marketingId)
            ->where('status', Pengajuan::STATUS_REVISI_DEBITUR)
            ->count();

        // Total plafon KPR yang disetujui
        $totalPlafon = Pengajuan::where('marketing_id', $marketingId)
            ->where('status', Pengajuan::STATUS_DISETUJUI_SISTEM)
            ->sum('jumlah_pinjaman');

        // Total properti terjual melalui KPR
        $propertySold = Pengajuan::where('marketing_id', $marketingId)
            ->where('status', Pengajuan::STATUS_DISETUJUI_SISTEM)
            ->whereHas('unit', function($q) {
                $q->where('status', Unit::STATUS_TERJUAL);
            })
            ->count();

        // Approval rate
        $approvalRate = $totalProcessed > 0
            ? ($totalApproved / $totalProcessed) * 100
            : 0;

        return [
            'total_processed' => $totalProcessed,
            'total_approved' => $totalApproved,
            'total_rejected' => $totalRejected,
            'total_revision' => $totalRevision,
            'total_plafon' => $totalPlafon,
            'property_sold' => $propertySold,
            'approval_rate' => round($approvalRate, 1),
        ];
    }

    /**
     * Get chart data for performance chart
     */
    private function getChartData(int $marketingId, int $months = 12): array
    {
        $labels = [];
        $processed = [];
        $approved = [];
        $rejected = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthName = $date->format('M Y');

            $labels[] = $monthName;

            $processed[] = Pengajuan::where('marketing_id', $marketingId)
                ->whereYear('tgl_marketing_proses', $date->year)
                ->whereMonth('tgl_marketing_proses', $date->month)
                ->count();

            $approved[] = Pengajuan::where('marketing_id', $marketingId)
                ->where('status', Pengajuan::STATUS_DISETUJUI_SISTEM)
                ->whereYear('tgl_selesai', $date->year)
                ->whereMonth('tgl_selesai', $date->month)
                ->count();

            $rejected[] = Pengajuan::where('marketing_id', $marketingId)
                ->whereIn('status', [
                    Pengajuan::STATUS_DITOLAK_MARKETING,
                    Pengajuan::STATUS_DITOLAK_SISTEM,
                ])
                ->whereYear('tgl_selesai', $date->year)
                ->whereMonth('tgl_selesai', $date->month)
                ->count();
        }

        return [
            'labels' => $labels,
            'processed' => $processed,
            'approved' => $approved,
            'rejected' => $rejected,
        ];
    }

    /**
     * Get document verification statistics
     */
    private function getDocumentStats(int $marketingId): array
    {
        $verifications = VerifikasiMarketing::where('marketing_id', $marketingId)->get();

        $valid = 0;
        $invalid = 0;
        $unchecked = 0;

        foreach ($verifications as $verif) {
            if ($verif->dok_ktp_valid && $verif->dok_kk_valid && $verif->dok_slip_gaji_valid) {
                $valid++;
            } elseif ($verif->dok_ktp_valid === false || $verif->dok_kk_valid === false) {
                $invalid++;
            } else {
                $unchecked++;
            }
        }

        return [
            'valid' => $valid,
            'invalid' => $invalid,
            'unchecked' => $unchecked,
        ];
    }

    /**
     * Get decision distribution statistics
     */
    private function getDecisionStats(int $marketingId): array
    {
        $verifications = VerifikasiMarketing::where('marketing_id', $marketingId)
            ->whereNotNull('keputusan')
            ->get();

        return [
            'ajukan_ke_admin' => $verifications->where('keputusan', VerifikasiMarketing::KEPUTUSAN_AJUKAN_KE_ADMIN)->count(),
            'minta_revisi' => $verifications->where('keputusan', VerifikasiMarketing::KEPUTUSAN_MINTA_REVISI)->count(),
            'tolak' => $verifications->where('keputusan', VerifikasiMarketing::KEPUTUSAN_TOLAK)->count(),
        ];
    }

    /**
     * AJAX endpoint for chart data
     */
    public function chartData(Request $request)
    {
        $marketingId = Auth::id();
        $months = $request->get('months', 12);

        $data = $this->getChartData($marketingId, (int)$months);

        return response()->json($data);
    }
}

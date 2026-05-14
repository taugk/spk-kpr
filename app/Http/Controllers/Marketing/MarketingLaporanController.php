<?php

namespace App\Http\Controllers\Marketing;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;
use App\Models\{Pengajuan, VerifikasiMarketing};
use App\Helpers\MarketingHelper;

class MarketingLaporanController extends Controller
{
    /**
     * Laporan kinerja marketing KPR
     */
    public function kinerja(Request $request)
    {
        $marketingId = Auth::id();
        $year = $request->get('year', date('Y'));
        $month = $request->get('month', null);

        // Monthly performance data
        $monthlyData = $this->getMonthlyPerformance($marketingId, $year);

        // Summary statistics
        $summary = $this->getPerformanceSummary($marketingId, $year);

        // Verification statistics
        $verificationStats = $this->getVerificationStatistics($marketingId, $year);

        // Decision distribution
        $decisionDistribution = $this->getDecisionDistribution($marketingId, $year);

        // Average processing time (hari)
        $avgProcessingTime = $this->getAverageProcessingTime($marketingId, $year);

        // Top projects by KPR approval
        $topProjects = $this->getTopProjectsByKPR($marketingId, $year);

        return view('marketing.laporan.kinerja', compact(
            'monthlyData',
            'summary',
            'verificationStats',
            'decisionDistribution',
            'avgProcessingTime',
            'topProjects',
            'year',
            'month'
        ));
    }

    /**
     * Get monthly performance data
     */
    private function getMonthlyPerformance(int $marketingId, int $year): array
    {
        $data = [];

        for ($month = 1; $month <= 12; $month++) {
            $processed = Pengajuan::where('marketing_id', $marketingId)
                ->whereYear('tgl_marketing_proses', $year)
                ->whereMonth('tgl_marketing_proses', $month)
                ->count();

            $approved = Pengajuan::where('marketing_id', $marketingId)
                ->where('status', Pengajuan::STATUS_DISETUJUI_SISTEM)
                ->whereYear('tgl_selesai', $year)
                ->whereMonth('tgl_selesai', $month)
                ->count();

            $rejected = Pengajuan::where('marketing_id', $marketingId)
                ->whereIn('status', [
                    Pengajuan::STATUS_DITOLAK_SISTEM,
                    Pengajuan::STATUS_DITOLAK_MARKETING,
                ])
                ->whereYear('tgl_selesai', $year)
                ->whereMonth('tgl_selesai', $month)
                ->count();

            $totalPlafon = Pengajuan::where('marketing_id', $marketingId)
                ->where('status', Pengajuan::STATUS_DISETUJUI_SISTEM)
                ->whereYear('tgl_selesai', $year)
                ->whereMonth('tgl_selesai', $month)
                ->sum('jumlah_pinjaman');

            $data[] = [
                'month' => $month,
                'month_name' => MarketingHelper::getMonthName($month),
                'processed' => $processed,
                'approved' => $approved,
                'rejected' => $rejected,
                'total_plafon' => $totalPlafon,
                'approval_rate' => $processed > 0 ? round(($approved / $processed) * 100, 1) : 0,
            ];
        }

        return $data;
    }

    /**
     * Get performance summary
     */
    private function getPerformanceSummary(int $marketingId, int $year): array
    {
        $baseQuery = Pengajuan::where('marketing_id', $marketingId)
            ->whereYear('tgl_marketing_proses', $year);

        $totalProcessed = (clone $baseQuery)->count();
        $totalApproved = (clone $baseQuery)->where('status', Pengajuan::STATUS_DISETUJUI_SISTEM)->count();
        $totalRejected = (clone $baseQuery)->whereIn('status', Pengajuan::getRejectedStatuses())->count();

        $totalPlafon = (clone $baseQuery)
            ->where('status', Pengajuan::STATUS_DISETUJUI_SISTEM)
            ->sum('jumlah_pinjaman');

        return [
            'total_processed' => $totalProcessed,
            'total_approved' => $totalApproved,
            'total_rejected' => $totalRejected,
            'approval_rate' => $totalProcessed > 0 ? round(($totalApproved / $totalProcessed) * 100, 1) : 0,
            'total_plafon' => $totalPlafon,
            'avg_plafon' => $totalApproved > 0 ? $totalPlafon / $totalApproved : 0,
        ];
    }

    /**
     * Get verification statistics
     */
    private function getVerificationStatistics(int $marketingId, int $year): array
    {
        $verifications = VerifikasiMarketing::where('marketing_id', $marketingId)
            ->whereYear('created_at', $year)
            ->get();

        $total = $verifications->count();

        if ($total === 0) {
            return [
                'total' => 0,
                'avg_document_valid' => 0,
                'rekomendasi_layak' => 0,
                'rekomendasi_perlu' => 0,
                'rekomendasi_tidak' => 0,
            ];
        }

        $avgValidDocuments = $verifications->avg(function($verif) {
            return $verif->getTotalValidDocuments();
        });

        return [
            'total' => $total,
            'avg_document_valid' => round($avgValidDocuments, 1),
            'rekomendasi_layak' => $verifications->where('rekomendasi_marketing', VerifikasiMarketing::REKOMENDASI_LAYAK)->count(),
            'rekomendasi_perlu' => $verifications->where('rekomendasi_marketing', VerifikasiMarketing::REKOMENDASI_PERLU_PERTIMBANGAN)->count(),
            'rekomendasi_tidak' => $verifications->where('rekomendasi_marketing', VerifikasiMarketing::REKOMENDASI_TIDAK_LAYAK)->count(),
        ];
    }

    /**
     * Get decision distribution
     */
    private function getDecisionDistribution(int $marketingId, int $year): array
    {
        $verifications = VerifikasiMarketing::where('marketing_id', $marketingId)
            ->whereYear('created_at', $year)
            ->whereNotNull('keputusan')
            ->get();

        $total = $verifications->count();

        if ($total === 0) {
            return [
                'ajukan_ke_admin' => 0,
                'minta_revisi' => 0,
                'tolak' => 0,
                'ajukan_percentage' => 0,
                'revisi_percentage' => 0,
                'tolak_percentage' => 0,
            ];
        }

        $ajukan = $verifications->where('keputusan', VerifikasiMarketing::KEPUTUSAN_AJUKAN_KE_ADMIN)->count();
        $revisi = $verifications->where('keputusan', VerifikasiMarketing::KEPUTUSAN_MINTA_REVISI)->count();
        $tolak = $verifications->where('keputusan', VerifikasiMarketing::KEPUTUSAN_TOLAK)->count();

        return [
            'ajukan_ke_admin' => $ajukan,
            'minta_revisi' => $revisi,
            'tolak' => $tolak,
            'ajukan_percentage' => round(($ajukan / $total) * 100, 1),
            'revisi_percentage' => round(($revisi / $total) * 100, 1),
            'tolak_percentage' => round(($tolak / $total) * 100, 1),
        ];
    }

    /**
     * Get average processing time (in days)
     */
    private function getAverageProcessingTime(int $marketingId, int $year): ?float
    {
        $pengajuan = Pengajuan::where('marketing_id', $marketingId)
            ->whereNotNull('tgl_marketing_proses')
            ->whereNotNull('tgl_selesai')
            ->whereYear('tgl_marketing_proses', $year)
            ->get();

        if ($pengajuan->isEmpty()) {
            return null;
        }

        $totalDays = $pengajuan->sum(function($item) {
            return $item->tgl_marketing_proses->diffInDays($item->tgl_selesai);
        });

        return round($totalDays / $pengajuan->count(), 1);
    }

    /**
     * Get top projects by KPR approval
     */
    private function getTopProjectsByKPR(int $marketingId, int $year): array
    {
        return Pengajuan::where('marketing_id', $marketingId)
            ->where('status', Pengajuan::STATUS_DISETUJUI_SISTEM)
            ->whereYear('tgl_selesai', $year)
            ->with('unit.tipeUnit.proyek')
            ->get()
            ->groupBy('unit.tipeUnit.proyek.nama_proyek')
            ->map(function($items, $projectName) {
                return [
                    'project_name' => $projectName,
                    'total_kpr' => $items->count(),
                    'total_plafon' => $items->sum('jumlah_pinjaman'),
                ];
            })
            ->sortByDesc('total_kpr')
            ->take(5)
            ->values()
            ->toArray();
    }
}

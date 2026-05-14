<?php

namespace App\Http\Controllers\Marketing;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;
use App\Models\Pengajuan;
use App\Helpers\MarketingHelper;

class MarketingRiwayatController extends Controller
{
    /**
     * Semua pengajuan KPR yang ditangani marketing
     */
    public function semua(Request $request)
    {
        $query = Pengajuan::where('marketing_id', Auth::id())
            ->with(['user', 'unit.tipeUnit.proyek', 'verifikasiMarketing'])
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Search by kode_pengajuan or nama debitur
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('kode_pengajuan', 'like', "%{$search}%")
                  ->orWhereHas('user', function($sub) use ($search) {
                      $sub->where('nama_lengkap', 'like', "%{$search}%");
                  });
            });
        }

        $pengajuan = $query->paginate(20);

        // Calculate totals
        $totals = [
            'total_plafon' => $query->sum('jumlah_pinjaman'),
            'total_approved' => (clone $query)->where('status', Pengajuan::STATUS_DISETUJUI_SISTEM)->count(),
            'total_rejected' => (clone $query)->whereIn('status', Pengajuan::getRejectedStatuses())->count(),
        ];

        $filters = $request->only(['status', 'start_date', 'end_date', 'search']);

        $statusOptions = [
            Pengajuan::STATUS_ANTRIAN_ADMIN => 'Diteruskan ke Admin',
            Pengajuan::STATUS_REVISI_DEBITUR => 'Perlu Revisi',
            Pengajuan::STATUS_DITOLAK_MARKETING => 'Ditolak Marketing',
            Pengajuan::STATUS_DISETUJUI_SISTEM => 'KPR Disetujui',
            Pengajuan::STATUS_DITOLAK_SISTEM => 'KPR Ditolak Sistem',
        ];

        return view('marketing.riwayat.semua', compact('pengajuan', 'filters', 'statusOptions', 'totals'));
    }
}

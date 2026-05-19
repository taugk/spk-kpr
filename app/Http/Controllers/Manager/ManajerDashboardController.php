<?php

namespace App\Http\Controllers\Manager;
use Carbon\Carbon;

use App\Http\Controllers\Controller;
use App\Models\{Pengajuan, Penilaian, User};

class ManajerDashboardController extends Controller
{
    public function index()
    {
        // Statistik utama
        $stats = [
            'total_pengajuan' => Pengajuan::count(),
            'pengajuan_bulan_ini' => Pengajuan::whereMonth('created_at', Carbon::now()->month)->count(),
            'pengajuan_minggu_ini' => Pengajuan::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count(),
            'approval_rate' => $this->getApprovalRate(),
            'rata_rata_waktu_proses' => $this->getRataRataWaktuProses(),
        ];

        // Data untuk chart
        $chartData = $this->getDashboardChartData();

        // Pengajuan terbaru
        $pengajuanTerbaru = Pengajuan::with(['user', 'penilaian', 'marketing'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Statistik marketing
        $marketingStats = $this->getMarketingStatistics();

        // Statistik admin
    $adminStats = User::where('role', User::ROLE_ADMIN)
        ->where('status', User::STATUS_AKTIF)
        ->get();


        // Notifikasi
        $notifications = [
            'pengajuan_baru' => Pengajuan::where('status', Pengajuan::STATUS_SUBMITTED)->count(),
            'verifikasi_tertunda' => Pengajuan::where('status', Pengajuan::STATUS_VERIFIKASI_MARKETING)
                ->where('updated_at', '<', Carbon::now()->subDays(3))
                ->count(),
            'antrian_admin' => Pengajuan::where('status', Pengajuan::STATUS_ANTRIAN_ADMIN)->count(),
            'penilaian_tertunda' => Pengajuan::where('status', Pengajuan::STATUS_PENILAIAN_ADMIN)
                ->where('updated_at', '<', Carbon::now()->subDays(2))
                ->count(),
        ];

        return view('manager.pages.dashboard.index', compact(
            'stats',
            'chartData',
            'pengajuanTerbaru',
            'marketingStats',
            'adminStats',
            'notifications'
        ));
    }

    private function getApprovalRate()
    {
        $total = Pengajuan::whereIn('status', [Pengajuan::STATUS_DISETUJUI_SISTEM, Pengajuan::STATUS_DITOLAK_SISTEM])->count();
        $disetujui = Pengajuan::where('status', Pengajuan::STATUS_DISETUJUI_SISTEM)->count();

        return $total > 0 ? round(($disetujui / $total) * 100, 2) : 0;
    }

    private function getRataRataWaktuProses()
    {
        $selesai = Pengajuan::whereIn('status', [Pengajuan::STATUS_DISETUJUI_SISTEM, Pengajuan::STATUS_DITOLAK_SISTEM, Pengajuan::STATUS_DITOLAK_MARKETING])
            ->whereNotNull('tgl_selesai')
            ->get();

        if ($selesai->isEmpty()) return 0;

        $totalHari = $selesai->sum(function($p) {
            return $p->tgl_submitted ? $p->tgl_submitted->diffInDays($p->tgl_selesai) : 0;
        });

        return round($totalHari / $selesai->count(), 1);
    }

    private function getDashboardChartData()
    {
        $last7Days = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $last7Days->push([
                'tanggal' => $date->format('d/m'),
                'pengajuan' => Pengajuan::whereDate('created_at', $date)->count(),
                'disetujui' => Pengajuan::whereDate('tgl_selesai', $date)->where('status', Pengajuan::STATUS_DISETUJUI_SISTEM)->count(),
            ]);
        }

        return $last7Days;
    }

    private function getMarketingStatistics()
    {
        return User::where('role', User::ROLE_MARKETING)
            ->where('status', User::STATUS_AKTIF)
            ->withCount(['pengajuan as total' => function($q) {
                $q->whereMonth('created_at', Carbon::now()->month);
            }])
            ->withCount(['pengajuan as disetujui' => function($q) {
                $q->where('status', Pengajuan::STATUS_DISETUJUI_SISTEM)
                  ->whereMonth('tgl_selesai', Carbon::now()->month);
            }])
            ->get()
            ->map(function($user) {
                $user->rate = $user->total > 0
                    ? round(($user->disetujui / $user->total) * 100, 2)
                    : 0;
                return $user;
            });
    }
}

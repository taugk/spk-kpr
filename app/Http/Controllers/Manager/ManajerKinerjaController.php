<?php

namespace App\Http\Controllers\Manager;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Http\Controllers\Controller;
use App\Models\{Pengajuan, Penilaian, User};

class ManajerKinerjaController extends Controller
{
    public function admin(Request $request)
    {
        $periode = $request->get('periode', 'bulanan');
        $tanggalMulai = $request->get('tanggal_mulai');
        $tanggalSelesai = $request->get('tanggal_selesai');

        $query = User::where('role', User::ROLE_ADMIN)
            ->where('status', User::STATUS_AKTIF)
            ->with(['penilaian' => function($q) use ($tanggalMulai, $tanggalSelesai) {
                if ($tanggalMulai && $tanggalSelesai) {
                    $q->whereBetween('tgl_penilaian', [$tanggalMulai, $tanggalSelesai]);
                }
            }]);

        $admins = $query->get()->map(function($admin) {
            $penilaian = $admin->penilaian;
            $totalPenilaian = $penilaian->count();
            $rataRataSkor = $penilaian->avg('skor_akhir') ?? 0;

            $admin->statistik = [
                'total_penilaian' => $totalPenilaian,
                'rata_rata_skor' => round($rataRataSkor, 2),
                'penilaian_cepat' => $penilaian->filter(function($p) {
                    $pengajuan = $p->pengajuan;
                    return $pengajuan && $p->tgl_penilaian && $pengajuan->tgl_admin_proses &&
                           $p->tgl_penilaian->diffInHours($pengajuan->tgl_admin_proses) < 24;
                })->count(),
                'penilaian_lambat' => $penilaian->filter(function($p) {
                    $pengajuan = $p->pengajuan;
                    return $pengajuan && $p->tgl_penilaian && $pengajuan->tgl_admin_proses &&
                           $p->tgl_penilaian->diffInDays($pengajuan->tgl_admin_proses) > 7;
                })->count(),
            ];

            return $admin;
        });

        $ranking = $admins->sortByDesc(function($admin) {
            return $admin->statistik['total_penilaian'];
        })->values();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $admins,
                'ranking' => $ranking
            ]);
        }

        return view('manager.pages.kinerja.admin', compact('admins', 'ranking', 'periode'));
    }

    public function adminDetail($id, Request $request)
    {
        $admin = User::where('role', User::ROLE_ADMIN)->findOrFail($id);

        $bulan = $request->get('bulan', Carbon::now()->month);
        $tahun = $request->get('tahun', Carbon::now()->year);

        $penilaianBulanIni = $admin->penilaian()
            ->whereMonth('tgl_penilaian', $bulan)
            ->whereYear('tgl_penilaian', $tahun)
            ->get();

        $detail = [
            'admin' => $admin,
            'total_penilaian' => $penilaianBulanIni->count(),
            'rata_rata_skor' => $penilaianBulanIni->avg('skor_akhir') ?? 0,
            'penilaian_per_hari' => $this->getPenilaianPerHari($admin->id, $bulan, $tahun),
            'distribusi_status' => $this->getDistribusiStatus($admin->id, $bulan, $tahun),
        ];

        if ($request->ajax()) {
            return response()->json($detail);
        }

        return view('manager.pages.kinerja.admin-detail', compact('detail', 'bulan', 'tahun'));
    }

    public function marketing(Request $request)
    {
        $periode = $request->get('periode', 'bulanan');
        $tanggalMulai = $request->get('tanggal_mulai');
        $tanggalSelesai = $request->get('tanggal_selesai');

        $query = User::where('role', User::ROLE_MARKETING)
            ->where('status', User::STATUS_AKTIF);

        $marketings = $query->get()->map(function($marketing) use ($tanggalMulai, $tanggalSelesai) {
            $pengajuanQuery = $marketing->pengajuan();

            if ($tanggalMulai && $tanggalSelesai) {
                $pengajuanQuery->whereBetween('created_at', [$tanggalMulai, $tanggalSelesai]);
            }

            $pengajuan = $pengajuanQuery->get();
            $total = $pengajuan->count();
            $disetujui = $pengajuan->where('status', Pengajuan::STATUS_DISETUJUI_SISTEM)->count();
            $ditolak = $pengajuan->whereIn('status', Pengajuan::getRejectedStatuses())->count();
            $proses = $pengajuan->whereIn('status', [
                Pengajuan::STATUS_VERIFIKASI_MARKETING,
                Pengajuan::STATUS_PENILAIAN_ADMIN,
                Pengajuan::STATUS_SELESAI_DINILAI,
            ])->count();

            $marketing->statistik = [
                'total_pengajuan' => $total,
                'disetujui' => $disetujui,
                'ditolak' => $ditolak,
                'proses' => $proses,
                'approval_rate' => $total > 0 ? round(($disetujui / $total) * 100, 2) : 0,
                'rejection_rate' => $total > 0 ? round(($ditolak / $total) * 100, 2) : 0,
                'rata_rata_waktu_proses' => $this->hitungRataWaktuProses($pengajuan),
            ];

            return $marketing;
        });

        $ranking = $marketings->sortByDesc(function($m) {
            return $m->statistik['approval_rate'];
        })->values();

        $leaderboard = [
            'terbanyak' => $marketings->sortByDesc(fn($m) => $m->statistik['total_pengajuan'])->first(),
            'tertinggi_approval' => $ranking->first(),
            'tercepat' => $marketings->sortBy(fn($m) => $m->statistik['rata_rata_waktu_proses'])->first(),
        ];

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $marketings,
                'ranking' => $ranking,
                'leaderboard' => $leaderboard
            ]);
        }

        return view('manager.pages.kinerja.marketing', compact('marketings', 'ranking', 'leaderboard', 'periode'));
    }

    private function getPenilaianPerHari($adminId, $bulan, $tahun)
    {
        $daysInMonth = Carbon::create($tahun, $bulan)->daysInMonth;
        $data = [];

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = Carbon::create($tahun, $bulan, $day);
            $data[] = [
                'tanggal' => $day,
                'jumlah' => Penilaian::where('admin_id', $adminId)
                    ->whereDate('tgl_penilaian', $date)
                    ->count()
            ];
        }

        return $data;
    }

    private function getDistribusiStatus($adminId, $bulan, $tahun)
    {
        return Penilaian::where('admin_id', $adminId)
            ->whereMonth('tgl_penilaian', $bulan)
            ->whereYear('tgl_penilaian', $tahun)
            ->select('hasil', DB::raw('count(*) as total'))
            ->groupBy('hasil')
            ->get();
    }

    private function hitungRataWaktuProses($pengajuan)
    {
        if ($pengajuan->isEmpty()) return 0;

        $selesai = $pengajuan->filter(function($p) {
            return in_array($p->status, [Pengajuan::STATUS_DISETUJUI_SISTEM, Pengajuan::STATUS_DITOLAK_SISTEM]) && $p->tgl_selesai;
        });

        if ($selesai->isEmpty()) return 0;

        $totalHari = $selesai->sum(function($p) {
            return $p->tgl_submitted ? $p->tgl_submitted->diffInDays($p->tgl_selesai) : 0;
        });

        return round($totalHari / $selesai->count(), 1);
    }
}

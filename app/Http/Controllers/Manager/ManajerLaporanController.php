<?php

namespace App\Http\Controllers\Manager;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Http\Controllers\Controller;
use App\Models\{Laporan, Pengajuan, Penilaian, User};

class ManajerLaporanController extends Controller
{
    public function bulanan(Request $request)
    {
        $bulan = $request->get('bulan', Carbon::now()->month);
        $tahun = $request->get('tahun', Carbon::now()->year);

        $startDate = Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $endDate = Carbon::create($tahun, $bulan, 1)->endOfMonth();

        $laporan = [
            'periode' => [
                'bulan' => $bulan,
                'tahun' => $tahun,
                'nama_bulan' => Carbon::create()->month($bulan)->format('F'),
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
            'ringkasan' => $this->getRingkasanBulanan($startDate, $endDate),
            'statistik_harian' => $this->getStatistikHarian($startDate, $endDate),
            'kriteria_tertinggi' => $this->getKriteriaTertinggi($startDate, $endDate),
            'performansi_marketing' => $this->getPerformansiMarketing($startDate, $endDate),
            'pengajuan_terbaru' => Pengajuan::with(['user', 'penilaian'])
                ->whereBetween('created_at', [$startDate, $endDate])
                ->orderBy('created_at', 'desc')
                ->limit(20)
                ->get(),
        ];

        $previousMonth = Carbon::create($tahun, $bulan, 1)->subMonth();
        $laporan['perbandingan'] = $this->getPerbandinganBulanan(
            $previousMonth->startOfMonth(),
            $previousMonth->endOfMonth(),
            $laporan['ringkasan']
        );

        if ($request->ajax()) {
            return response()->json($laporan);
        }

        return view('manager.pages.laporan.bulanan', compact('laporan', 'bulan', 'tahun'));
    }

    public function tahunan(Request $request)
    {
        $tahun = $request->get('tahun', Carbon::now()->year);

        $laporan = [
            'tahun' => $tahun,
            'ringkasan_tahunan' => $this->getRingkasanTahunan($tahun),
            'per_bulan' => $this->getDataPerBulan($tahun),
            'trend_kuartal' => $this->getTrendKuartal($tahun),
            'top_marketing' => $this->getTopMarketingTahunan($tahun),
            'kriteria_analysis' => $this->getKriteriaAnalysisTahunan($tahun),
            'yoy_growth' => $this->getYoYGrowth($tahun),
        ];

        if ($request->ajax()) {
            return response()->json($laporan);
        }

        return view('manager.pages.laporan.tahunan', compact('laporan', 'tahun'));
    }

    public function exportIndex()
    {
        $tahunTersedia = Pengajuan::select(DB::raw('YEAR(created_at) as tahun'))
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        return view('manager.pages.laporan.export', compact('tahunTersedia'));
    }

    public function exportProses(Request $request)
    {
        $request->validate([
            'jenis_laporan' => 'required|in:bulanan,tahunan,kinerja,penilaian',
            'format' => 'required|in:excel,pdf,csv',
            'bulan' => 'required_if:jenis_laporan,bulanan|nullable|integer|between:1,12',
            'tahun' => 'required|integer|min:2022',
        ]);

        $jenis = $request->jenis_laporan;
        $format = $request->format;
        $bulan = $request->bulan;
        $tahun = $request->tahun;

        $data = match($jenis) {
            'bulanan' => $this->getDataLaporanBulanan($bulan, $tahun),
            'tahunan' => $this->getDataLaporanTahunan($tahun),
            'kinerja' => $this->getDataLaporanKinerja($tahun),
            'penilaian' => $this->getDataLaporanPenilaian($bulan, $tahun),
        };

        $filename = "laporan_{$jenis}_{$tahun}" . ($bulan ? "_{$bulan}" : "") . ".{$format}";

        return response()->json([
            'success' => true,
            'message' => "Laporan sedang diproses",
            'filename' => $filename,
            'data_preview' => array_slice($data, 0, 10)
        ]);
    }

    private function getRingkasanBulanan($startDate, $endDate)
    {
        $pengajuan = Pengajuan::whereBetween('created_at', [$startDate, $endDate]);

        return [
            'total_pengajuan' => $pengajuan->count(),
            'disetujui' => (clone $pengajuan)->where('status', Pengajuan::STATUS_DISETUJUI_SISTEM)->count(),
            'ditolak' => (clone $pengajuan)->whereIn('status', Pengajuan::getRejectedStatuses())->count(),
            'proses' => (clone $pengajuan)->whereIn('status', [
                Pengajuan::STATUS_VERIFIKASI_MARKETING,
                Pengajuan::STATUS_PENILAIAN_ADMIN,
                Pengajuan::STATUS_SELESAI_DINILAI,
            ])->count(),
            'verifikasi' => (clone $pengajuan)->where('status', Pengajuan::STATUS_VERIFIKASI_MARKETING)->count(),
            'total_nilai_pinjaman' => (clone $pengajuan)->sum('jumlah_pinjaman') ?? 0,
            'rata_rata_skor' => Penilaian::whereBetween('tgl_penilaian', [$startDate, $endDate])->avg('skor_akhir') ?? 0,
            'approval_rate' => $this->calculateRate($pengajuan->count(), (clone $pengajuan)->where('status', Pengajuan::STATUS_DISETUJUI_SISTEM)->count()),
        ];
    }

    private function getStatistikHarian($startDate, $endDate)
    {
        $data = [];
        $currentDate = clone $startDate;

        while ($currentDate <= $endDate) {
            $dateStr = $currentDate->format('Y-m-d');
            $pengajuan = Pengajuan::whereDate('created_at', $dateStr);

            $data[] = [
                'tanggal' => $currentDate->format('d/m/Y'),
                'hari' => $currentDate->format('l'),
                'pengajuan' => $pengajuan->count(),
                'disetujui' => (clone $pengajuan)->where('status', Pengajuan::STATUS_DISETUJUI_SISTEM)->count(),
            ];

            $currentDate->addDay();
        }

        return $data;
    }

    private function getKriteriaTertinggi($startDate, $endDate)
    {
        return DB::table('penilaian_detail')
            ->join('penilaian', 'penilaian_detail.penilaian_id', '=', 'penilaian.id')
            ->join('kriteria', 'penilaian_detail.kriteria_id', '=', 'kriteria.id')
            ->whereBetween('penilaian.tgl_penilaian', [$startDate, $endDate])
            ->select('kriteria.nama_kriteria as nama', DB::raw('AVG(penilaian_detail.nilai_normalisasi * 100) as rata_rata'))
            ->groupBy('kriteria.id', 'kriteria.nama_kriteria')
            ->orderBy('rata_rata', 'desc')
            ->limit(5)
            ->get();
    }

    private function getPerformansiMarketing($startDate, $endDate)
    {
        return User::where('role', User::ROLE_MARKETING)
            ->withCount(['pengajuan as total' => function($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->withCount(['pengajuan as disetujui' => function($q) use ($startDate, $endDate) {
                $q->where('status', Pengajuan::STATUS_DISETUJUI_SISTEM)
                  ->whereBetween('tgl_selesai', [$startDate, $endDate]);
            }])
            ->having('total', '>', 0)
            ->get()
            ->map(function($user) {
                $user->approval_rate = round(($user->disetujui / $user->total) * 100, 2);
                return $user;
            })
            ->sortByDesc('approval_rate');
    }

    private function getPerbandinganBulanan($prevStart, $prevEnd, $currentSummary)
    {
        $prevPengajuan = Pengajuan::whereBetween('created_at', [$prevStart, $prevEnd]);
        $prevTotal = $prevPengajuan->count();
        $prevDisetujui = (clone $prevPengajuan)->where('status', Pengajuan::STATUS_DISETUJUI_SISTEM)->count();

        return [
            'total_pengajuan' => [
                'sebelumnya' => $prevTotal,
                'perubahan' => $currentSummary['total_pengajuan'] - $prevTotal,
                'persen' => $prevTotal > 0 ? round((($currentSummary['total_pengajuan'] - $prevTotal) / $prevTotal) * 100, 2) : 0,
            ],
            'approval_rate' => [
                'sebelumnya' => $this->calculateRate($prevTotal, $prevDisetujui),
                'perubahan' => $currentSummary['approval_rate'] - $this->calculateRate($prevTotal, $prevDisetujui),
            ]
        ];
    }

    private function getRingkasanTahunan($tahun)
    {
        $pengajuan = Pengajuan::whereYear('created_at', $tahun);

        return [
            'total_pengajuan' => $pengajuan->count(),
            'disetujui' => (clone $pengajuan)->where('status', Pengajuan::STATUS_DISETUJUI_SISTEM)->count(),
            'ditolak' => (clone $pengajuan)->whereIn('status', Pengajuan::getRejectedStatuses())->count(),
            'rata_rata_per_bulan' => round((clone $pengajuan)->count() / 12, 2),
            'total_nilai_pinjaman' => (clone $pengajuan)->sum('jumlah_pinjaman') ?? 0,
        ];
    }

    private function getDataPerBulan($tahun)
    {
        $data = [];
        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $pengajuan = Pengajuan::whereYear('created_at', $tahun)->whereMonth('created_at', $bulan);

            $data[] = [
                'bulan' => Carbon::create()->month($bulan)->format('F'),
                'jumlah' => $pengajuan->count(),
                'disetujui' => (clone $pengajuan)->where('status', Pengajuan::STATUS_DISETUJUI_SISTEM)->count(),
                'ditolak' => (clone $pengajuan)->whereIn('status', Pengajuan::getRejectedStatuses())->count(),
            ];
        }
        return $data;
    }

    private function getTrendKuartal($tahun)
    {
        $kuartal = [
            1 => ['months' => [1,2,3], 'name' => 'Kuartal 1'],
            2 => ['months' => [4,5,6], 'name' => 'Kuartal 2'],
            3 => ['months' => [7,8,9], 'name' => 'Kuartal 3'],
            4 => ['months' => [10,11,12], 'name' => 'Kuartal 4'],
        ];

        $trend = [];
        foreach ($kuartal as $q => $data) {
            $total = Pengajuan::whereYear('created_at', $tahun)
                ->whereIn(DB::raw('MONTH(created_at)'), $data['months'])
                ->count();

            $trend[] = [
                'kuartal' => $data['name'],
                'jumlah' => $total,
                'persen_dari_tahun' => $total > 0 ? round(($total / $this->getRingkasanTahunan($tahun)['total_pengajuan']) * 100, 2) : 0,
            ];
        }
        return $trend;
    }

    private function getTopMarketingTahunan($tahun)
    {
        return User::where('role', User::ROLE_MARKETING)
            ->withCount(['pengajuan as total' => function($q) use ($tahun) {
                $q->whereYear('created_at', $tahun);
            }])
            ->withCount(['pengajuan as disetujui' => function($q) use ($tahun) {
                $q->where('status', Pengajuan::STATUS_DISETUJUI_SISTEM)->whereYear('tgl_selesai', $tahun);
            }])
            ->having('total', '>', 0)
            ->orderBy('disetujui', 'desc')
            ->limit(5)
            ->get();
    }

    private function getKriteriaAnalysisTahunan($tahun)
    {
        return DB::table('penilaian_detail')
            ->join('penilaian', 'penilaian_detail.penilaian_id', '=', 'penilaian.id')
            ->join('kriteria', 'penilaian_detail.kriteria_id', '=', 'kriteria.id')
            ->whereYear('penilaian.tgl_penilaian', $tahun)
            ->select(
                'kriteria.nama_kriteria as nama',
                DB::raw('AVG(penilaian_detail.nilai_normalisasi * 100) as rata_rata'),
                DB::raw('MIN(penilaian_detail.nilai_input) as minimal'),
                DB::raw('MAX(penilaian_detail.nilai_input) as maksimal')
            )
            ->groupBy('kriteria.id', 'kriteria.nama_kriteria')
            ->get();
    }

    private function getYoYGrowth($currentYear)
    {
        $previousYear = $currentYear - 1;

        $currentTotal = Pengajuan::whereYear('created_at', $currentYear)->count();
        $previousTotal = Pengajuan::whereYear('created_at', $previousYear)->count();

        return [
            'tahun_sekarang' => $currentYear,
            'tahun_lalu' => $previousYear,
            'total_sekarang' => $currentTotal,
            'total_lalu' => $previousTotal,
            'pertumbuhan' => $previousTotal > 0 ? round((($currentTotal - $previousTotal) / $previousTotal) * 100, 2) : 0,
            'trend' => $currentTotal >= $previousTotal ? 'positif' : 'negatif',
        ];
    }

    private function calculateRate($total, $success)
    {
        return $total > 0 ? round(($success / $total) * 100, 2) : 0;
    }

    private function getDataLaporanBulanan($bulan, $tahun)
    {
        $startDate = Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $endDate = Carbon::create($tahun, $bulan, 1)->endOfMonth();

        return Pengajuan::with(['user', 'penilaian'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get()
            ->map(function($p) {
                return [
                    'kode_pengajuan' => $p->kode_pengajuan,
                    'nama' => $p->user->nama_lengkap ?? '-',
                    'jumlah_pinjaman' => $p->jumlah_pinjaman,
                    'status' => $p->status_text,
                    'tanggal_pengajuan' => $p->created_at->format('d/m/Y'),
                    'skor' => $p->penilaian->skor_akhir ?? '-',
                ];
            });
    }

    private function getDataLaporanTahunan($tahun)
    {
        $data = [];
        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $pengajuan = Pengajuan::whereYear('created_at', $tahun)->whereMonth('created_at', $bulan);
            $data[$bulan] = [
                'bulan' => Carbon::create()->month($bulan)->format('F'),
                'total_pengajuan' => $pengajuan->count(),
                'disetujui' => (clone $pengajuan)->where('status', Pengajuan::STATUS_DISETUJUI_SISTEM)->count(),
                'ditolak' => (clone $pengajuan)->whereIn('status', Pengajuan::getRejectedStatuses())->count(),
                'rata_rata_skor' => Penilaian::whereYear('tgl_penilaian', $tahun)
                    ->whereMonth('tgl_penilaian', $bulan)
                    ->avg('skor_akhir') ?? 0,
            ];
        }
        return $data;
    }

    private function getDataLaporanKinerja($tahun)
    {
        return User::where('role', User::ROLE_MARKETING)
            ->withCount(['pengajuan as total' => function($q) use ($tahun) {
                $q->whereYear('created_at', $tahun);
            }])
            ->withCount(['pengajuan as disetujui' => function($q) use ($tahun) {
                $q->where('status', Pengajuan::STATUS_DISETUJUI_SISTEM)->whereYear('tgl_selesai', $tahun);
            }])
            ->get()
            ->map(function($user) {
                return [
                    'nama' => $user->nama_lengkap,
                    'email' => $user->email,
                    'total_pengajuan' => $user->total,
                    'disetujui' => $user->disetujui,
                    'approval_rate' => $user->total > 0 ? round(($user->disetujui / $user->total) * 100, 2) : 0,
                ];
            });
    }

    private function getDataLaporanPenilaian($bulan, $tahun)
    {
        $query = Penilaian::with(['admin', 'pengajuan.user']);

        if ($bulan && $tahun) {
            $query->whereMonth('tgl_penilaian', $bulan)->whereYear('tgl_penilaian', $tahun);
        } elseif ($tahun) {
            $query->whereYear('tgl_penilaian', $tahun);
        }

        return $query->get()->map(function($p) {
            return [
                'penilai' => $p->admin->nama_lengkap ?? '-',
                'pemohon' => $p->pengajuan->user->nama_lengkap ?? '-',
                'total_skor' => $p->skor_akhir,
                'hasil' => $p->hasil_text,
                'tanggal_penilaian' => $p->tgl_penilaian ? $p->tgl_penilaian->format('d/m/Y H:i') : '-',
            ];
        });
    }
}

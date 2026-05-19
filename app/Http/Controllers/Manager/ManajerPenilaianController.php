<?php

namespace App\Http\Controllers\Manager;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Http\Controllers\Controller;
use App\Models\{Kriteria, Pengajuan, Penilaian};

class ManajerPenilaianController extends Controller
{
    public function index(Request $request)
    {
        $query = Penilaian::with(['admin', 'pengajuan.user', 'details.kriteria']);

        if ($request->date_from) {
            $query->whereDate('tgl_penilaian', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('tgl_penilaian', '<=', $request->date_to);
        }

        if ($request->penilai_id) {
            $query->where('admin_id', $request->penilai_id);
        }

        if ($request->skor_min) {
            $query->where('skor_akhir', '>=', $request->skor_min);
        }

        if ($request->skor_max) {
            $query->where('skor_akhir', '<=', $request->skor_max);
        }

        if ($request->hasil) {
            $query->where('hasil', $request->hasil);
        }

        $perPage = $request->get('per_page', 15);
        $penilaian = $query->orderBy('tgl_penilaian', 'desc')->paginate($perPage);

        $statistik = Penilaian::getStatistics();
        $distribusiSkor = $this->getDistribusiSkor();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $penilaian,
                'statistik' => $statistik,
                'distribusi_skor' => $distribusiSkor
            ]);
        }

        return view('manager.pages.penilaian.index', compact('penilaian', 'statistik', 'distribusiSkor'));
    }

    public function show($id)
    {
        $penilaian = Penilaian::with([
            'admin',
            'pengajuan' => function($q) {
                $q->with(['user', 'dokumen', 'unit.tipeUnit.proyek']);
            },
            'details.kriteria'
        ])->findOrFail($id);

        $analisisKriteria = $penilaian->getBreakdownByKriteria();
        $summary = $penilaian->getSummary();
        $perbandinganRata = $this->perbandinganDenganRataRata($penilaian);

        return view('manager.pages.penilaian.show', compact('penilaian', 'analisisKriteria', 'summary', 'perbandinganRata'));
    }

    public function rekap(Request $request)
    {
        $tahun = $request->get('tahun', now()->year);

        $rekap = [
            'tahun' => $tahun,
            'per_bulan' => $this->getRekapPerBulan($tahun),
            'per_penilai' => $this->getRekapPerPenilai($tahun),
            'per_kriteria' => $this->getRekapPerKriteria($tahun),
            'trend_skor' => $this->getTrendSkor($tahun),
        ];

        if ($request->ajax()) {
            return response()->json($rekap);
        }

        return view('manager.pages.penilaian.rekap', compact('rekap', 'tahun'));
    }

    public function export(Request $request)
    {
        $request->validate([
            'format' => 'required|in:excel,csv,pdf',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date'
        ]);

        $query = Penilaian::with(['admin', 'pengajuan.user']);

        if ($request->date_from) {
            $query->whereDate('tgl_penilaian', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('tgl_penilaian', '<=', $request->date_to);
        }

        $data = $query->get()->map(function($p) {
            return [
                'ID Penilaian' => $p->id,
                'Kode Pengajuan' => $p->pengajuan->kode_pengajuan ?? '-',
                'Nama Debitur' => $p->pengajuan->user->nama_lengkap ?? '-',
                'Nama Penilai' => $p->admin->nama_lengkap ?? '-',
                'Total Skor' => $p->skor_akhir,
                'Threshold' => $p->threshold,
                'Hasil' => $p->hasil_text,
                'Catatan' => $p->catatan_admin,
                'Tanggal Penilaian' => $p->tgl_penilaian ? $p->tgl_penilaian->format('d/m/Y H:i') : '-',
            ];
        });

        return response()->json([
            'success' => true,
            'total_data' => $data->count(),
            'data' => $data
        ]);
    }

    private function getDistribusiSkor()
    {
        $ranges = [
            '0-20' => [0, 20],
            '21-40' => [21, 40],
            '41-60' => [41, 60],
            '61-80' => [61, 80],
            '81-100' => [81, 100],
        ];

        $distribusi = [];
        foreach ($ranges as $label => [$min, $max]) {
            $distribusi[$label] = Penilaian::whereBetween('skor_akhir', [$min, $max])->count();
        }

        return $distribusi;
    }

    private function perbandinganDenganRataRata($penilaian)
    {
        $rataNasional = Penilaian::avg('skor_akhir') ?? 0;
        $selisih = $penilaian->skor_akhir - $rataNasional;

        $total = Penilaian::count();
        $dibawah = Penilaian::where('skor_akhir', '<', $penilaian->skor_akhir)->count();
        $persentil = $total > 0 ? ($dibawah / $total) * 100 : 0;

        return [
            'skor_penilaian' => $penilaian->skor_akhir,
            'rata_rata_nasional' => round($rataNasional, 2),
            'selisih' => round($selisih, 2),
            'posisi' => $selisih >= 0 ? 'di atas rata-rata' : 'di bawah rata-rata',
            'persentil' => round($persentil, 2),
        ];
    }

    private function getRekapPerBulan($tahun)
    {
        $data = [];
        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $penilaian = Penilaian::whereYear('tgl_penilaian', $tahun)
                ->whereMonth('tgl_penilaian', $bulan);

            $data[] = [
                'bulan' => $bulan,
                'nama_bulan' => Carbon::create()->month($bulan)->format('F'),
                'jumlah' => $penilaian->count(),
                'rata_skor' => round($penilaian->avg('skor_akhir') ?? 0, 2),
                'max_skor' => $penilaian->max('skor_akhir') ?? 0,
                'min_skor' => $penilaian->min('skor_akhir') ?? 0,
            ];
        }
        return $data;
    }

    private function getRekapPerPenilai($tahun)
    {
        return Penilaian::whereYear('tgl_penilaian', $tahun)
            ->with('admin')
            ->select('admin_id', DB::raw('count(*) as total'), DB::raw('avg(skor_akhir) as rata_skor'))
            ->groupBy('admin_id')
            ->get()
            ->map(function($item) {
                return [
                    'penilai' => $item->admin->nama_lengkap ?? 'Unknown',
                    'total_penilaian' => $item->total,
                    'rata_rata_skor' => round($item->rata_skor, 2),
                ];
            });
    }

    private function getRekapPerKriteria($tahun)
    {
        return DB::table('penilaian_detail')
            ->join('penilaian', 'penilaian_detail.penilaian_id', '=', 'penilaian.id')
            ->join('kriteria', 'penilaian_detail.kriteria_id', '=', 'kriteria.id')
            ->whereYear('penilaian.tgl_penilaian', $tahun)
            ->select(
                'kriteria.nama_kriteria as nama',
                DB::raw('AVG(penilaian_detail.nilai_normalisasi * 100) as rata_rata'),
                DB::raw('COUNT(DISTINCT penilaian.id) as jumlah_penilaian')
            )
            ->groupBy('kriteria.id', 'kriteria.nama_kriteria')
            ->get();
    }

    private function getTrendSkor($tahun)
    {
        $trend = [];
        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $trend[] = [
                'bulan' => Carbon::create()->month($bulan)->format('M'),
                'skor' => round(Penilaian::whereYear('tgl_penilaian', $tahun)
                    ->whereMonth('tgl_penilaian', $bulan)
                    ->avg('skor_akhir') ?? 0, 2),
            ];
        }
        return $trend;
    }
}

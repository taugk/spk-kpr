<?php

namespace App\Http\Controllers\Manager;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Http\Controllers\Controller;
use App\Models\{Kriteria, Pengajuan, Penilaian, User};

class ManajerAnalisisController extends Controller
{
    public function statistikPenilaian(Request $request)
    {
        $tahun = $request->get('tahun', Carbon::now()->year);

        // Statistik berdasarkan status pengajuan
        $statistik = [
            'total_pengajuan' => Pengajuan::count(),
            'disetujui' => Pengajuan::where('status', Pengajuan::STATUS_DISETUJUI_SISTEM)->count(),
            'ditolak' => Pengajuan::whereIn('status', Pengajuan::getRejectedStatuses())->count(),
            'proses' => Pengajuan::whereIn('status', [
                Pengajuan::STATUS_VERIFIKASI_MARKETING,
                Pengajuan::STATUS_PENILAIAN_ADMIN,
                Pengajuan::STATUS_SELESAI_DINILAI,
            ])->count(),
            'verifikasi' => Pengajuan::where('status', Pengajuan::STATUS_VERIFIKASI_MARKETING)->count(),

            // Rata-rata skor penilaian
            'rata_rata_skor' => Penilaian::avg('skor_akhir') ?? 0,

            // Per bulan
            'per_bulan' => $this->getStatistikPerBulan($tahun),

            // Per kriteria
            'per_kriteria' => $this->getStatistikPerKriteria(),
        ];

        $chartData = [
            'labels' => ['Disetujui', 'Ditolak', 'Diproses', 'Verifikasi'],
            'data' => [
                $statistik['disetujui'],
                $statistik['ditolak'],
                $statistik['proses'],
                $statistik['verifikasi']
            ]
        ];

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $statistik,
                'chart' => $chartData
            ]);
        }

        return view('manager.pages.analisis.statistik', compact('statistik', 'chartData', 'tahun'));
    }

    public function trenPengajuan(Request $request)
    {
        $periode = $request->get('periode', 'bulanan');
        $tahun = $request->get('tahun', Carbon::now()->year);

        $trenData = [];

        switch ($periode) {
            case 'tahunan':
                $trenData = $this->getTrenTahunan();
                break;
            case 'triwulan':
                $trenData = $this->getTrenTriwulan($tahun);
                break;
            default:
                $trenData = $this->getTrenBulanan($tahun);
                break;
        }

        $pertumbuhan = $this->hitungPertumbuhan($trenData);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $trenData,
                'pertumbuhan' => $pertumbuhan
            ]);
        }

        return view('manager.pages.analisis.tren', compact('trenData', 'pertumbuhan', 'periode', 'tahun'));
    }

    public function prediksiPengajuan(Request $request)
    {
        $last6Months = Pengajuan::select(
                DB::raw('MONTH(created_at) as bulan'),
                DB::raw('YEAR(created_at) as tahun'),
                DB::raw('COUNT(*) as jumlah')
            )
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->whereIn('status', [Pengajuan::STATUS_SUBMITTED, Pengajuan::STATUS_DISETUJUI_SISTEM])
            ->groupBy('tahun', 'bulan')
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->limit(6)
            ->get();

        $prediksi = $last6Months->avg('jumlah') ?? 0;

        return response()->json([
            'success' => true,
            'prediksi_bulan_depan' => round($prediksi),
            'data_historis' => $last6Months
        ]);
    }

    private function getStatistikPerBulan($tahun)
    {
        $data = [];
        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $data[$bulan] = [
                'bulan' => Carbon::create()->month($bulan)->format('F'),
                'pengajuan' => Pengajuan::whereYear('created_at', $tahun)
                                        ->whereMonth('created_at', $bulan)
                                        ->count(),
                'disetujui' => Pengajuan::whereYear('created_at', $tahun)
                                        ->whereMonth('created_at', $bulan)
                                        ->where('status', Pengajuan::STATUS_DISETUJUI_SISTEM)
                                        ->count()
            ];
        }
        return $data;
    }

    private function getStatistikPerKriteria()
    {
        return DB::table('penilaian_detail')
            ->join('kriteria', 'penilaian_detail.kriteria_id', '=', 'kriteria.id')
            ->select(
                'kriteria.nama_kriteria as nama',
                'kriteria.kode_kriteria as kode',
                DB::raw('AVG(penilaian_detail.nilai_normalisasi * 100) as rata_rata_nilai'),
                DB::raw('MIN(penilaian_detail.nilai_input) as nilai_min'),
                DB::raw('MAX(penilaian_detail.nilai_input) as nilai_max')
            )
            ->groupBy('kriteria.id', 'kriteria.nama_kriteria', 'kriteria.kode_kriteria')
            ->get();
    }

    private function getTrenBulanan($tahun)
    {
        $data = [];
        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $date = Carbon::create($tahun, $bulan, 1);
            $data[] = [
                'periode' => $date->format('M Y'),
                'bulan' => $bulan,
                'jumlah' => Pengajuan::whereYear('created_at', $tahun)
                                     ->whereMonth('created_at', $bulan)
                                     ->count(),
                'disetujui' => Pengajuan::whereYear('created_at', $tahun)
                                        ->whereMonth('created_at', $bulan)
                                        ->where('status', Pengajuan::STATUS_DISETUJUI_SISTEM)
                                        ->count()
            ];
        }
        return $data;
    }

    private function getTrenTriwulan($tahun)
    {
        $triwulan = [
            1 => ['months' => [1,2,3], 'name' => 'Q1 (Jan-Mar)'],
            2 => ['months' => [4,5,6], 'name' => 'Q2 (Apr-Jun)'],
            3 => ['months' => [7,8,9], 'name' => 'Q3 (Jul-Sep)'],
            4 => ['months' => [10,11,12], 'name' => 'Q4 (Oct-Dec)']
        ];

        $data = [];
        foreach ($triwulan as $key => $tw) {
            $data[] = [
                'periode' => $tw['name'],
                'jumlah' => Pengajuan::whereYear('created_at', $tahun)
                                     ->whereIn(DB::raw('MONTH(created_at)'), $tw['months'])
                                     ->count()
            ];
        }
        return $data;
    }

    private function getTrenTahunan()
    {
        $data = [];
        $tahunMulai = 2022;
        $tahunSekarang = Carbon::now()->year;

        for ($tahun = $tahunMulai; $tahun <= $tahunSekarang; $tahun++) {
            $data[] = [
                'periode' => $tahun,
                'jumlah' => Pengajuan::whereYear('created_at', $tahun)->count()
            ];
        }
        return $data;
    }

    private function hitungPertumbuhan($data)
    {
        $pertumbuhan = [];
        for ($i = 1; $i < count($data); $i++) {
            $sebelumnya = $data[$i-1]['jumlah'];
            $sekarang = $data[$i]['jumlah'];

            if ($sebelumnya > 0) {
                $persen = (($sekarang - $sebelumnya) / $sebelumnya) * 100;
            } else {
                $persen = $sekarang > 0 ? 100 : 0;
            }

            $pertumbuhan[] = [
                'periode' => $data[$i]['periode'],
                'persentase' => round($persen, 2),
                'trend' => $persen >= 0 ? 'naik' : 'turun'
            ];
        }
        return $pertumbuhan;
    }
}

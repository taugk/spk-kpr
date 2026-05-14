<?php

namespace App\Http\Controllers\LandingPage;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;

class LandingPageController extends Controller
{
    public function index()
    {
        // Ambil semua proyek aktif
        $proyek = DB::table('proyek')
            ->where('status', 'aktif')
            ->orderBy('created_at', 'desc')
            ->get();

        // Ambil semua tipe unit dengan join proyek
        $tipeUnit = DB::table('tipe_unit')
            ->join('proyek', 'tipe_unit.proyek_id', '=', 'proyek.id')
            ->select(
                'tipe_unit.*',
                'proyek.nama_proyek',
                'proyek.lokasi as lokasi_proyek',
                'proyek.kota',
                'proyek.provinsi',
                'proyek.status as status_proyek'
            )
            ->where('proyek.status', 'aktif')
            ->orderBy('tipe_unit.harga', 'asc')
            ->get();

        // Ambil semua unit tersedia dengan informasi lengkap
        $unitTersedia = DB::table('unit')
            ->join('tipe_unit', 'unit.tipe_unit_id', '=', 'tipe_unit.id')
            ->join('proyek', 'tipe_unit.proyek_id', '=', 'proyek.id')
            ->select(
                'unit.id as unit_id',
                'unit.kode_unit',
                'unit.foto_unit',
                'unit.fasilitas as unit_fasilitas',
                'unit.status as unit_status',
                'tipe_unit.id as tipe_id',
                'tipe_unit.kode_tipe',
                'tipe_unit.nama_tipe',
                'tipe_unit.luas_tanah',
                'tipe_unit.luas_bangunan',
                'tipe_unit.jumlah_kamar',
                'tipe_unit.jumlah_wc',
                'tipe_unit.harga',
                'tipe_unit.stok_tersedia',
                'tipe_unit.gambar as tipe_gambar',
                'proyek.id as proyek_id',
                'proyek.nama_proyek',
                'proyek.lokasi',
                'proyek.kota',
                'proyek.provinsi',
                'proyek.deskripsi as proyek_deskripsi',
                'proyek.foto_proyek'
            )
            ->where('unit.status', 'tersedia')
            ->orderBy('tipe_unit.harga', 'asc')
            ->get();

        // Statistik dari database
        $statistik = [
            'total_proyek' => DB::table('proyek')->where('status', 'aktif')->count(),
            'total_unit_tersedia' => DB::table('unit')->where('status', 'tersedia')->count(),
            'total_tipe_unit' => DB::table('tipe_unit')
                ->whereExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('proyek')
                        ->whereColumn('tipe_unit.proyek_id', 'proyek.id')
                        ->where('proyek.status', 'aktif');
                })
                ->count(),
            'harga_termurah' => DB::table('tipe_unit')
                ->whereExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('proyek')
                        ->whereColumn('tipe_unit.proyek_id', 'proyek.id')
                        ->where('proyek.status', 'aktif');
                })
                ->min('harga'),
            'harga_termahal' => DB::table('tipe_unit')
                ->whereExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('proyek')
                        ->whereColumn('tipe_unit.proyek_id', 'proyek.id')
                        ->where('proyek.status', 'aktif');
                })
                ->max('harga'),
        ];

        // Proyek terbaru untuk ditampilkan
        $proyekTerbaru = DB::table('proyek')
            ->where('status', 'aktif')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        // HAPUS - karena kolom 'unggulan' dan 'urutan' tidak ada di tabel proyek
        // $proyekUnggulan = DB::table('proyek')
        //     ->where('status', 'aktif')
        //     ->where('unggulan', true)
        //     ->orderBy('urutan', 'asc')
        //     ->limit(4)
        //     ->get();
        

        return view('home', compact(
            'proyek',
            'tipeUnit',
            'unitTersedia',
            'statistik',
            'proyekTerbaru'
            // 'proyekUnggulan' - HAPUS dari compact
        ));
    }

    // Method untuk detail proyek
    public function detailProyek($id)
    {
        $proyek = DB::table('proyek')
            ->where('id', $id)
            ->where('status', 'aktif')
            ->first();

        if (!$proyek) {
            abort(404, 'Proyek tidak ditemukan');
        }

        // Ambil tipe unit dalam proyek ini
        $tipeUnit = DB::table('tipe_unit')
            ->where('proyek_id', $id)
            ->orderBy('harga', 'asc')
            ->get();

        // Ambil unit yang tersedia dalam proyek ini
        $unitTersedia = DB::table('unit')
            ->join('tipe_unit', 'unit.tipe_unit_id', '=', 'tipe_unit.id')
            ->where('tipe_unit.proyek_id', $id)
            ->where('unit.status', 'tersedia')
            ->select('unit.*', 'tipe_unit.nama_tipe', 'tipe_unit.harga')
            ->get();

        return view('landing-page.proyek-detail', compact('proyek', 'tipeUnit', 'unitTersedia'));
    }

    // Method untuk detail unit
    public function detailUnit($id)
    {
        $unit = DB::table('unit')
            ->join('tipe_unit', 'unit.tipe_unit_id', '=', 'tipe_unit.id')
            ->join('proyek', 'tipe_unit.proyek_id', '=', 'proyek.id')
            ->select(
                'unit.*',
                'tipe_unit.kode_tipe',
                'tipe_unit.nama_tipe',
                'tipe_unit.luas_tanah',
                'tipe_unit.luas_bangunan',
                'tipe_unit.jumlah_kamar',
                'tipe_unit.jumlah_wc',
                'tipe_unit.harga',
                'tipe_unit.gambar as tipe_gambar',
                'proyek.id as proyek_id',
                'proyek.nama_proyek',
                'proyek.lokasi',
                'proyek.kota',
                'proyek.provinsi',
                'proyek.deskripsi as proyek_deskripsi'
            )
            ->where('unit.id', $id)
            ->where('unit.status', 'tersedia')
            ->first();

        if (!$unit) {
            abort(404, 'Unit tidak ditemukan');
        }

        // Unit sejenis yang masih tersedia
        $unitSejenis = DB::table('unit')
            ->join('tipe_unit', 'unit.tipe_unit_id', '=', 'tipe_unit.id')
            ->where('tipe_unit.id', $unit->tipe_unit_id)
            ->where('unit.id', '!=', $id)
            ->where('unit.status', 'tersedia')
            ->select('unit.*', 'tipe_unit.nama_tipe', 'tipe_unit.harga')
            ->limit(3)
            ->get();

        return view('landing-page.unit-detail', compact('unit', 'unitSejenis'));
    }

    // Method untuk simulasi KPR (menghitung dari database)
    public function simulasiKPR(Request $request)
    {
        $request->validate([
            'unit_id' => 'required|exists:unit,id',
            'dp_persen' => 'required|numeric|min:5|max:50',
            'tenor' => 'required|integer|min:1|max:30',
        ]);

        // Ambil data unit
        $unit = DB::table('unit')
            ->join('tipe_unit', 'unit.tipe_unit_id', '=', 'tipe_unit.id')
            ->where('unit.id', $request->unit_id)
            ->select('tipe_unit.harga', 'unit.kode_unit')
            ->first();

        if (!$unit) {
            return response()->json(['error' => 'Unit tidak ditemukan'], 404);
        }

        $hargaUnit = $unit->harga;
        $dpPersen = $request->dp_persen;
        $tenorTahun = $request->tenor;
        $sukuBunga = 8.5; // Bisa diambil dari konfigurasi atau database

        $dp = ($dpPersen / 100) * $hargaUnit;
        $pokokKPR = $hargaUnit - $dp;
        
        // Bunga per bulan
        $bungaPerBulan = ($sukuBunga / 100) / 12;
        $tenorBulan = $tenorTahun * 12;
        
        // Perhitungan angsuran per bulan (metode anuitas)
        if ($bungaPerBulan > 0 && $tenorBulan > 0) {
            $factor = pow(1 + $bungaPerBulan, $tenorBulan);
            $angsuranPerBulan = $pokokKPR * ($bungaPerBulan * $factor) / ($factor - 1);
        } else {
            $angsuranPerBulan = $pokokKPR / $tenorBulan;
        }

        // Rekomendasi penghasilan minimal (angsuran 30% dari penghasilan)
        $penghasilanMinimal = $angsuranPerBulan / 0.3;

        return response()->json([
            'success' => true,
            'data' => [
                'harga_unit' => $hargaUnit,
                'dp_persen' => $dpPersen,
                'dp_nominal' => $dp,
                'pokok_kpr' => $pokokKPR,
                'tenor_tahun' => $tenorTahun,
                'suku_bunga' => $sukuBunga,
                'angsuran_per_bulan' => round($angsuranPerBulan, 2),
                'penghasilan_minimal' => round($penghasilanMinimal, 2)
            ]
        ]);
    }

    // Method untuk pencarian unit
    public function cariUnit(Request $request)
    {
        $query = DB::table('unit')
            ->join('tipe_unit', 'unit.tipe_unit_id', '=', 'tipe_unit.id')
            ->join('proyek', 'tipe_unit.proyek_id', '=', 'proyek.id')
            ->where('unit.status', 'tersedia');

        // Filter berdasarkan harga
        if ($request->has('harga_min') && $request->harga_min) {
            $query->where('tipe_unit.harga', '>=', $request->harga_min);
        }
        if ($request->has('harga_max') && $request->harga_max) {
            $query->where('tipe_unit.harga', '<=', $request->harga_max);
        }

        // Filter berdasarkan kota
        if ($request->has('kota') && $request->kota) {
            $query->where('proyek.kota', 'like', '%' . $request->kota . '%');
        }

        // Filter berdasarkan jumlah kamar
        if ($request->has('kamar') && $request->kamar) {
            $query->where('tipe_unit.jumlah_kamar', '>=', $request->kamar);
        }

        // Filter berdasarkan luas bangunan
        if ($request->has('luas_min') && $request->luas_min) {
            $query->where('tipe_unit.luas_bangunan', '>=', $request->luas_min);
        }

        $results = $query->select(
            'unit.id',
            'unit.kode_unit',
            'tipe_unit.nama_tipe',
            'tipe_unit.luas_bangunan',
            'tipe_unit.jumlah_kamar',
            'tipe_unit.harga',
            'proyek.nama_proyek',
            'proyek.kota'
        )
        ->orderBy('tipe_unit.harga', 'asc')
        ->paginate(12);

        return response()->json($results);
    }
}
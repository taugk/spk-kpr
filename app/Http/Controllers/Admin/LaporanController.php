<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Services\Admin\ReportService;

class LaporanController extends Controller
{
    public function __construct(private readonly ReportService $reportService) {}

    public function index(Request $request)
    {
        $laporan = $this->reportService->rekapPengajuan($request->dari, $request->sampai);
        
        return view('admin.pages.laporan.index', [
            'laporan' => $laporan,
            'dari' => $request->dari,
            'sampai' => $request->sampai,
        ]);
    }

    /**
     * Tampilkan laporan yang sudah digenerate
     */
    public function generated(Request $request)
    {
        $laporan = $this->reportService->getAllLaporan($request->dari, $request->sampai);
        
        return view('admin.pages.laporan.generated', [
            'laporan' => $laporan,
            'dari' => $request->dari,
            'sampai' => $request->sampai,
        ]);
    }

    /**
 * Cetak laporan untuk pengajuan tertentu
 */
public function cetakLaporan(Request $request)
{
    $request->validate([
        'pengajuan_id' => 'required|exists:pengajuan,id'
    ]);

    try {
        // Generate PDF atau Word
        // Simpan ke storage
        $pathFile = $this->generateLaporanFile($request->pengajuan_id);
        
        // Simpan ke database
        $this->reportService->simpanLogLaporan(
            $request->pengajuan_id,
            null,
            'pengajuan',
            $pathFile
        );

        return response()->json([
            'success' => true,
            'message' => 'Laporan berhasil dicetak'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}

    private function generateLaporanFile(int $pengajuanId): string
    {
        // Implementasi logika untuk generate file laporan (PDF/Word)
        // Simpan file ke storage dan return path-nya
        // Contoh:
        $fileName = "laporan_pengajuan_{$pengajuanId}_" . time() . ".pdf";
        $path = storage_path("app/laporan/{$fileName}");
        
        // Logic generate PDF menggunakan library seperti Dompdf atau Snappy
        // ...

        return "laporan/{$fileName}";
    }
}
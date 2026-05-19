<?php

namespace App\Http\Controllers\Marketing;

use Illuminate\Http\{JsonResponse, RedirectResponse, Request};
use Illuminate\Support\Facades\{Auth, Log, Storage};

use App\Http\Controllers\Controller;
use App\Models\{DokumenPengajuan, Pengajuan, RiwayatStatus, VerifikasiMarketing};
use App\Services\Marketing\VerifikasiDokumenKPRService;

class MarketingVerifikasiController extends Controller
{
    public function __construct(
        private VerifikasiDokumenKPRService $verifikasiService
    ) {}

    // ─── Index: daftar pengajuan yang perlu diverifikasi ────────────────────

    public function index(Request $request)
    {
        $marketingId = Auth::id();

        $baseQuery = Pengajuan::where('marketing_id', $marketingId)
            ->where('status', Pengajuan::STATUS_VERIFIKASI_MARKETING)
            ->with(['user', 'unit.tipeUnit.proyek', 'verifikasiMarketing']);

        // Filter pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $baseQuery->where(function ($q) use ($search) {
                $q->where('kode_pengajuan', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($s) => $s->where('nama_lengkap', 'like', "%{$search}%"))
                  ->orWhereHas('debiturPribadi', fn($s) => $s->where('nik', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('start_date')) {
            $baseQuery->whereDate('tgl_marketing_proses', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $baseQuery->whereDate('tgl_marketing_proses', '<=', $request->end_date);
        }

        // Statistik
        $totalAktif = (clone $baseQuery)->count();
        $belumDiverifikasi = (clone $baseQuery)
            ->whereDoesntHave('verifikasiMarketing', fn($q) => $q->whereNotNull('dok_ktp_valid'))
            ->count();
        $sedangDiverifikasi = $totalAktif - $belumDiverifikasi;

        // Hitung berdasarkan rekomendasi
        $layak = (clone $baseQuery)
            ->whereHas('verifikasiMarketing', fn($q) => $q->where('rekomendasi_marketing', 'layak'))
            ->count();
        $perluPertimbangan = (clone $baseQuery)
            ->whereHas('verifikasiMarketing', fn($q) => $q->where('rekomendasi_marketing', 'perlu_pertimbangan'))
            ->count();
        $tidakLayak = (clone $baseQuery)
            ->whereHas('verifikasiMarketing', fn($q) => $q->where('rekomendasi_marketing', 'tidak_layak'))
            ->count();

        $stats = [
            'total_menunggu' => $belumDiverifikasi,
            'verifikasi_berkas' => $sedangDiverifikasi,
            'layak' => $layak,
            'perlu_pertimbangan' => $perluPertimbangan,
            'tidak_layak' => $tidakLayak,
        ];

        $avgPersentase = 0;

        $pengajuan = $baseQuery
            ->orderBy('tgl_marketing_proses', 'asc')
            ->paginate(15)
            ->withQueryString();

        return view('marketing.pages.verifikasi.dokumen.index', compact(
            'pengajuan',
            'totalAktif',
            'belumDiverifikasi',
            'sedangDiverifikasi',
            'avgPersentase',
            'stats'
        ));
    }

    // ─── Show: form verifikasi dokumen ──────────────────────────

    public function show(Pengajuan $pengajuan)
    {
        $this->otorisasi($pengajuan);

        $pengajuan->load([
            'user',
            'unit.tipeUnit.proyek',
            'debiturPribadi',
            'debiturPekerjaan',
            'debiturKeuangan',
            'dokumen',
            'verifikasiMarketing',
        ]);

        $dokumenUpload = $pengajuan->dokumen->keyBy('jenis_dokumen');

        $verifikasi = $pengajuan->verifikasiMarketing ?? VerifikasiMarketing::firstOrCreate(
            ['pengajuan_id' => $pengajuan->id],
            ['marketing_id' => Auth::id()]
        );

        $daftarDokumen = $this->verifikasiService->getDaftarDokumen($pengajuan);
        $kemampuanBayar = $this->hitungKemampuanBayar($pengajuan);

        $riwayat = $pengajuan->riwayatStatus()
            ->with('pengubah')
            ->orderBy('created_at', 'asc')
            ->get();

        return view('marketing.pages.verifikasi.dokumen.create', compact(
            'pengajuan',
            'dokumenUpload',
            'verifikasi',
            'daftarDokumen',
            'kemampuanBayar',
            'riwayat'
        ));
    }

    // ─── API: Get dokumen by pengajuan via AJAX ─────────────────────────────

   public function getDokumenByPengajuan(Pengajuan $pengajuan): JsonResponse
{
    try {
        $this->otorisasi($pengajuan);

        // Jika service mengembalikan Collection dari Model Dokumen
        $daftarDokumen = $this->verifikasiService->getDaftarDokumen($pengajuan);

        $dokumenUpload = $pengajuan->dokumen->keyBy('jenis_dokumen');
        $verifikasi = $pengajuan->verifikasiMarketing ?? new VerifikasiMarketing();

        // Mapping jenis dokumen ke field di tabel verifikasi_marketing
        $fieldMapping = [
            'ktp' => 'dok_ktp_valid',
            'kk' => 'dok_kk_valid',
            'slip_gaji' => 'dok_slip_gaji_valid',
            'rek_koran' => 'dok_rek_koran_valid',
            'slik' => 'dok_slik_valid',
            'surat_kerja' => 'dok_surat_kerja_valid',
            'npwp' => 'dok_npwp_valid',
        ];

        $data = [];

        foreach ($daftarDokumen as $dokumen) {
            // Jika $dokumen adalah object/model
            $jenis = $dokumen->jenis_dokumen ?? $dokumen->jenis ?? null;

            if (!$jenis) {
                continue;
            }

            $uploaded = $dokumenUpload[$jenis] ?? null;
            $fieldName = $fieldMapping[$jenis] ?? null;

            $verifikasiStatus = 'belum_diverifikasi';
            if ($fieldName && $verifikasi->$fieldName === true) {
                $verifikasiStatus = 'lengkap';
            } elseif ($fieldName && $verifikasi->$fieldName === false && $verifikasi->$fieldName !== null) {
                $verifikasiStatus = 'tidak_valid';
            }

            $data[] = [
                'id' => $dokumen->id ?? null,
                'jenis' => $jenis,
                'nama_dokumen' => $dokumen->nama_dokumen ?? $dokumen->nama ?? $jenis,
                'wajib' => $dokumen->wajib ?? true,
                'is_uploaded' => !is_null($uploaded),
                'file_name' => $uploaded->file_name ?? $dokumen->file_name ?? null,
                'file_size' => $uploaded->file_size ?? $dokumen->file_size ?? null,
                'file_type' => $uploaded->file_type ?? $dokumen->file_type ?? null,
                'file_path' => $uploaded->file_path ?? $dokumen->file_path ?? null,
                'verifikasi_status' => $verifikasiStatus,
                'verifikasi_catatan' => null,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]);

    } catch (\Exception $e) {
        Log::error('Error in getDokumenByPengajuan: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan: ' . $e->getMessage()
        ], 500);
    }
}

    // ─── API: Download dokumen ──────────────────────────────────────────────

    public function downloadDokumen(DokumenPengajuan $dokumen)
    {
        $pengajuan = $dokumen->pengajuan;
        $this->otorisasi($pengajuan);

        $path = storage_path('app/public/' . $dokumen->file_path);

        if (!file_exists($path)) {
            abort(404, 'File tidak ditemukan');
        }

        return response()->download($path, $dokumen->file_name, [
            'Content-Type' => $dokumen->file_type,
        ]);
    }

    // ─── API: Preview dokumen ───────────────────────────────────────────────

    public function previewDokumen(DokumenPengajuan $dokumen)
    {
        $pengajuan = $dokumen->pengajuan;
        $this->otorisasi($pengajuan);

        $path = storage_path('app/public/' . $dokumen->file_path);

        if (!file_exists($path)) {
            abort(404, 'File tidak ditemukan');
        }

        $mimeType = $dokumen->file_type ?: mime_content_type($path);

        return response()->file($path, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $dokumen->file_name . '"'
        ]);
    }

    // ─── API: Proses verifikasi via AJAX ────────────────────────────────────

public function prosesVerifikasi(Request $request, Pengajuan $pengajuan): JsonResponse
{
    try {
        $this->otorisasi($pengajuan);

        $validated = $request->validate([
            'rekomendasi' => 'required|in:layak,perlu_pertimbangan,tidak_layak',
            'keputusan' => 'required|in:ajukan_ke_admin,minta_revisi,tolak',
            'alasan_keputusan' => 'nullable|string|max:1000',
            'catatan_verifikasi' => 'nullable|string|max:1000',
            'verifikasi_dokumen' => 'nullable|array',
        ]);

        // DEBUG: Log nilai status
        Log::info('=== DEBUG VERIFIKASI ===');
        Log::info('Status pengajuan saat ini: ' . $pengajuan->status);
        Log::info('Keputusan: ' . $validated['keputusan']);

        // Mapping status dokumen ke field
        $fieldMapping = [
            'ktp' => 'dok_ktp_valid',
            'kk' => 'dok_kk_valid',
            'slip_gaji' => 'dok_slip_gaji_valid',
            'rek_koran' => 'dok_rek_koran_valid',
            'slik' => 'dok_slik_valid',
            'surat_kerja' => 'dok_surat_kerja_valid',
            'npwp' => 'dok_npwp_valid',
        ];

        // Prepare data untuk verifikasi marketing
        $verifikasiData = [
            'marketing_id' => Auth::id(),
            'rekomendasi_marketing' => $validated['rekomendasi'],
            'keputusan' => $validated['keputusan'],
            'alasan_keputusan' => $validated['alasan_keputusan'] ?? null,
            'tgl_keputusan' => now(),
        ];

        // Set status dokumen
        if (isset($validated['verifikasi_dokumen']) && is_array($validated['verifikasi_dokumen'])) {
            foreach ($validated['verifikasi_dokumen'] as $jenis => $data) {
                $fieldName = $fieldMapping[$jenis] ?? null;
                if ($fieldName && isset($data['status'])) {
                    $verifikasiData[$fieldName] = $data['status'] === 'lengkap';
                }
            }
        }

        // Update atau create verifikasi marketing
        $verifikasi = VerifikasiMarketing::updateOrCreate(
            ['pengajuan_id' => $pengajuan->id],
            $verifikasiData
        );

        // Tentukan status baru berdasarkan keputusan
        $newStatus = null;
        switch($validated['keputusan']) {
            case 'ajukan_ke_admin':
                $newStatus = Pengajuan::STATUS_ANTRIAN_ADMIN;
                break;
            case 'minta_revisi':
                $newStatus = Pengajuan::STATUS_REVISI_DEBITUR;
                break;
            case 'tolak':
                $newStatus = Pengajuan::STATUS_DITOLAK_MARKETING;
                break;
        }

        // DEBUG: Log status baru
        Log::info('New status: ' . ($newStatus ?? 'null'));

        // Ambil status lama dengan aman
        $statusLama = $pengajuan->status;
        Log::info('Status lama (raw): ' . var_export($statusLama, true));

        // Jika status lama null, gunakan draft
        if (empty($statusLama)) {
            $statusLama = Pengajuan::STATUS_DRAFT;
            Log::info('Status lama diubah menjadi: ' . $statusLama);
        }

        Log::info('Final status lama: ' . $statusLama);
        Log::info('Final status baru: ' . ($newStatus ?? 'null'));

        // HANYA simpan riwayat jika status baru tidak null
        if ($newStatus) {
            // Simpan catatan verifikasi ke riwayat
            $catatan = $validated['catatan_verifikasi'] ?? $validated['alasan_keputusan'] ?? null;

            // DEBUG: Log sebelum simpan
            Log::info('Menyimpan riwayat dengan status: ' . $newStatus);

            try {
                // PERBAIKAN: Kirim semua field yang required di tabel riwayat_status
                $riwayatData = [
                    'pengajuan_id' => $pengajuan->id,
                    'status_lama' => $statusLama,
                    'status_baru' => $newStatus,
                    'diubah_oleh' => Auth::id(),
                    'keterangan' => $catatan,
                    'created_at' => now(),
                ];

                Log::info('Data riwayat yang akan disimpan:', $riwayatData);

                // Simpan ke riwayat_status
                \App\Models\RiwayatStatus::create($riwayatData);

                Log::info('Riwayat berhasil disimpan');
            } catch (\Exception $e) {
                Log::error('Error simpan riwayat: ' . $e->getMessage());
                throw $e;
            }

            // Update status pengajuan
            $pengajuan->update(['status' => $newStatus]);
            Log::info('Status pengajuan diupdate menjadi: ' . $newStatus);
        } else {
            Log::warning('New status is null, tidak menyimpan riwayat');
        }

        // Redirect ke halaman daftar verifikasi setelah sukses
        return response()->json([
            'success' => true,
            'message' => 'Verifikasi berhasil disimpan',
            'redirect' => route('marketing.verifikasi.dokumen')
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {
        Log::error('Validation error: ' . json_encode($e->errors()));
        return response()->json([
            'success' => false,
            'message' => 'Validasi gagal',
            'errors' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        Log::error('General error: ' . $e->getMessage());
        Log::error('Stack trace: ' . $e->getTraceAsString());
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan: ' . $e->getMessage()
        ], 500);
    }
}

    // ─── API: Get detail verifikasi untuk riwayat ───────────────────────────

    public function detail(Pengajuan $pengajuan): JsonResponse
    {
        $this->otorisasi($pengajuan);

        $riwayat = $pengajuan->riwayatStatus()
            ->with('pengubah')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($item) {
                $statusLabels = [
                    'antrian_admin' => 'Diteruskan ke Admin',
                    'revisi_debitur' => 'Perlu Revisi',
                    'ditolak_marketing' => 'Ditolak Marketing',
                ];

                return [
                    'id' => $item->id,
                    'status' => $item->status,
                    'status_label' => $statusLabels[$item->status] ?? ucfirst(str_replace('_', ' ', $item->status)),
                    'keterangan' => $item->keterangan,
                    'created_at' => $item->created_at,
                    'created_at_formatted' => $item->created_at->format('d/m/Y H:i'),
                    'pengubah' => $item->pengubah->nama_lengkap ?? $item->pengubah->name ?? null
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $riwayat
        ]);
    }

    /**
 * Get file dari storage private (via AJAX)
 */
public function getFile(Request $request)
{
    try {
        $path = $request->input('path');

        if (!$path) {
            return response()->json(['error' => 'Path tidak ditemukan'], 400);
        }

        // Cek apakah file exists
        if (!Storage::disk('private')->exists($path)) {
            return response()->json(['error' => 'File tidak ditemukan'], 404);
        }

        // Get file contents
        $file = Storage::disk('private')->get($path);
        $mimeType = Storage::disk('private')->mimeType($path);

        // Return file response
        return response($file, 200)
            ->header('Content-Type', $mimeType)
            ->header('Content-Disposition', 'inline');

    } catch (\Exception $e) {
        Log::error('Error getting file: ' . $e->getMessage());
        return response()->json(['error' => 'Gagal mengambil file'], 500);
    }
}

    // ─── Private helpers ────────────────────────────────────────────────────

    private function otorisasi(Pengajuan $pengajuan): void
{
    $marketingId = Auth::id();

    $bolehAkses = ($pengajuan->marketing_id === $marketingId)
        || ($pengajuan->marketing_id === null && $pengajuan->status === Pengajuan::STATUS_SUBMITTED);

    if (!$bolehAkses) {
        abort(403, 'Anda tidak memiliki akses ke pengajuan ini.');
    }

    // Tambahkan STATUS_ANTRIAN_ADMIN jika ingin mengizinkan verifikasi ulang
    $statusValid = [
        Pengajuan::STATUS_SUBMITTED,
        Pengajuan::STATUS_VERIFIKASI_MARKETING,
        Pengajuan::STATUS_ANTRIAN_ADMIN, // Tambahkan jika perlu
    ];

    if (!in_array($pengajuan->status, $statusValid)) {
        // Log status saat ini untuk debugging
        Log::error('Status tidak valid untuk verifikasi: ' . $pengajuan->status);
        abort(400, 'Pengajuan ini tidak dapat diverifikasi pada status saat ini. Status saat ini: ' . $pengajuan->status);
    }
}

    private function hitungKemampuanBayar(Pengajuan $pengajuan): array
    {
        $penghasilan  = (float) (optional($pengajuan->debiturPekerjaan)->total_penghasilan ?? 0);
        $angsuran     = (float) ($pengajuan->estimasi_angsuran ?? 0);
        $cicilanLain  = (float) (optional($pengajuan->debiturKeuangan)->total_cicilan_perbulan ?? 0);

        $totalKewajiban = $angsuran + $cicilanLain;
        $dti = $penghasilan > 0 ? ($totalKewajiban / $penghasilan) * 100 : 0;

        return [
            'dsr' => round($dti, 2),
            'sisa_pendapatan' => $penghasilan - $totalKewajiban,
            'penghasilan_bulanan' => $penghasilan,
            'estimasi_angsuran'   => $angsuran,
            'cicilan_lain'        => $cicilanLain,
            'total_kewajiban'     => $totalKewajiban,
            'status'              => match (true) {
                $dti <= 30 => 'aman',
                $dti <= 40 => 'waspada',
                default    => 'berisiko',
            },
            'keterangan'          => match (true) {
                $dti <= 30 => 'Total kewajiban ≤ 30% penghasilan. Kemampuan bayar baik.',
                $dti <= 40 => 'Total kewajiban 30–40% penghasilan. Perlu pertimbangan.',
                default    => 'Total kewajiban > 40% penghasilan. Risiko gagal bayar tinggi.',
            },
        ];
    }
}

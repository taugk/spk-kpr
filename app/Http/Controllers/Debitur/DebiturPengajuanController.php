<?php

namespace App\Http\Controllers\Debitur;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Log, DB};

use App\Http\Controllers\Controller;
use App\Http\Requests\DebiturPengajuanRequest;
use App\Models\{Pengajuan, Proyek, TipeUnit, Unit};
use App\Services\Debitur\DebiturPengajuanService;
use App\Helpers\DebiturPengajuanHelper;

class DebiturPengajuanController extends Controller
{
    public function __construct(
        protected DebiturPengajuanService $service
    ) {}

    /**
     * Tampilkan halaman buat pengajuan baru.
     */
    public function create()
    {
        $properti = $this->service->getAvailableUnits();


        return view('debitur.pages.pengajuan-kpr', compact('properti'));
    }

    /**
     * Simpan pengajuan baru (draft atau submit).
     */
    public function store(DebiturPengajuanRequest $request)
{
    // LOGGING 1: Data mentah yang masuk
    Log::info('=== START KPR STORE PROCESS ===');
    Log::info('Request Method: ' . $request->method());
    Log::info('Request URL: ' . $request->fullUrl());
    Log::info('Is AJAX: ' . ($request->ajax() ? 'Yes' : 'No'));
    Log::info('Is Draft: ' . ($request->input('action') === 'draft' ? 'Yes' : 'No'));

    // LOGGING 2: Semua input data (termasuk file)
    Log::info('All Request Data:', $request->all());

    // LOGGING 3: Data yang sudah tervalidasi
    try {
        $validatedData = $request->validated();
        Log::info('Validated Data:', $validatedData);
    } catch (\Exception $e) {
        Log::error('Validation Error: ' . $e->getMessage());
        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'error' => 'Validation Error',
                'message' => $e->getMessage()
            ], 422);
        }
        throw $e;
    }

    // LOGGING 4: File uploads
    $files = $request->allFiles();
    Log::info('Uploaded Files Count: ' . count($files));
    foreach ($files as $key => $file) {
        if (is_array($file)) {
            Log::info("File array '{$key}': " . count($file) . ' files');
            foreach ($file as $index => $subFile) {
                if ($subFile instanceof \Illuminate\Http\UploadedFile) {
                    Log::info("  - {$key}[{$index}]: {$subFile->getClientOriginalName()} ({$subFile->getSize()} bytes)");
                }
            }
        } elseif ($file instanceof \Illuminate\Http\UploadedFile) {
            Log::info("File '{$key}': {$file->getClientOriginalName()} ({$file->getSize()} bytes)");
        } else {
            Log::warning("Invalid file for '{$key}': " . gettype($file));
        }
    }

    // LOGGING 5: User info
    Log::info('User ID: ' . (Auth::id() ?? 'Guest'));
    Log::info('User Email: ' . (Auth::user()->email ?? 'N/A'));

    // DEBUG: Untuk development, tampilkan data di browser
    // Hapus comment jika ingin melihat data di browser (sementara)
    // dd($request->all(), $validatedData, $files);

    $isDraft = $request->input('action') === 'draft';
    Log::info('Is Draft: ' . ($isDraft ? 'Yes' : 'No'));

    try {
        // LOGGING 6: Proses service
        Log::info('Calling service->storePengajuan...');

        $pengajuan = $this->service->storePengajuan(
            user: Auth::user(),
            data: $validatedData,
            files: $files,
            isDraft: $isDraft
        );

        Log::info('Service completed successfully');
        Log::info('Pengajuan ID: ' . ($pengajuan->id ?? 'N/A'));

        // LOGGING 7: Response type
        if ($request->ajax() || $request->wantsJson()) {
            Log::info('Returning JSON response for AJAX request');
            return response()->json([
                'success' => true,
                'message' => $isDraft ? 'Draft berhasil disimpan' : 'Pengajuan berhasil dikirim',
                'data' => $pengajuan,
                'redirect_url' => $isDraft
                    ? route('debitur.pengajuan.edit', $pengajuan->id)
                    : route('debitur.pengajuan.show', $pengajuan->id)
            ]);
        }

        // LOGGING 8: Redirect for normal form submit
        if ($isDraft) {
            Log::info('Redirecting to edit route for draft');
            return redirect()
                ->route('debitur.pengajuan.edit', $pengajuan->id)
                ->with('success', 'Draft pengajuan berhasil disimpan.');
        }

        Log::info('Redirecting to show route for final submit');
        return redirect()
            ->route('debitur.pengajuan.show', $pengajuan->id)
            ->with('success', 'Pengajuan KPR berhasil dikirim. Kami akan segera menghubungi Anda.');

    } catch (\Illuminate\Validation\ValidationException $e) {
        Log::error('Validation Exception: ' . $e->getMessage());
        Log::error('Validation Errors:', $e->errors());

        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        }

        return redirect()->back()
            ->withErrors($e->errors())
            ->withInput();

    } catch (\Exception $e) {
        Log::error('=== KPR STORE ERROR ===');
        Log::error('Error Message: ' . $e->getMessage());
        Log::error('Error Code: ' . $e->getCode());
        Log::error('Error File: ' . $e->getFile());
        Log::error('Error Line: ' . $e->getLine());
        Log::error('Stack Trace: ' . $e->getTraceAsString());

        // Log request data for debugging
        Log::error('Request Data at error time:', $request->all());

        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server: ' . $e->getMessage(),
                'error_detail' => config('app.debug') ? $e->getTraceAsString() : null
            ], 500);
        }

        if (config('app.debug')) {
            // Di environment development, tampilkan error
            throw $e;
        }

        return redirect()->back()
            ->with('error', 'Terjadi kesalahan. Silakan coba lagi atau hubungi administrator.')
            ->withInput();
    } finally {
        Log::info('=== END KPR STORE PROCESS ===');
    }
}

   /**
     * Show detail pengajuan - Route Model Binding
     */
    public function show(Pengajuan $pengajuan)
    {
        // Route model binding otomatis akan mencari pengajuan berdasarkan ID dari URL
        // dan menghasilkan 404 jika tidak ditemukan

        // Pastikan pengajuan milik user yang login
        if ($pengajuan->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke pengajuan ini.');
        }

        $pengajuan->load([
            'unit.tipeUnit.proyek',
            'dokumen',
            'marketing:id,nama_lengkap,email',
        ]);

        $timeline = DebiturPengajuanHelper::buildTimeline($pengajuan);
        $summary  = DebiturPengajuanHelper::buildFinancialSummary($pengajuan);

        return view('debitur.pages.pengajuan-show', compact('pengajuan', 'timeline', 'summary'));
    }

    /**
     * Edit pengajuan - Route Model Binding
     */
    public function edit(Pengajuan $pengajuan)
    {
        // Pastikan pengajuan milik user yang login
        if ($pengajuan->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke pengajuan ini.');
        }

        // Cek apakah bisa diedit
        if (!in_array($pengajuan->status, ['draft', 'revisi'])) {
            return redirect()
                ->route('debitur.pengajuan.show', $pengajuan)
                ->with('error', 'Pengajuan tidak dapat diedit karena sudah dalam proses.');
        }

        $pengajuan->load(['unit.tipeUnit.proyek', 'dokumen']);
        $properti = $this->getAvailableUnits();

        return view('debitur.pages.pengajuan-kpr', compact('pengajuan', 'properti'));
    }

    /**
     * Update pengajuan - Route Model Binding
     */
    public function update(DebiturPengajuanRequest $request, Pengajuan $pengajuan)
    {
        // Pastikan pengajuan milik user yang login
        if ($pengajuan->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke pengajuan ini.');
        }

        // Cek apakah bisa diedit
        if (!in_array($pengajuan->status, ['draft', 'revisi'])) {
            return back()->with('error', 'Pengajuan tidak dapat diedit.');
        }

        $isDraft = $request->input('action') === 'draft';

        DB::beginTransaction();
        try {
            // Update pengajuan
            $pengajuan->update($request->validated());

            // Handle upload dokumen
            if ($request->hasFile('dokumen')) {
                // Proses upload
            }

            DB::commit();

            if ($isDraft) {
                return redirect()
                    ->route('debitur.pengajuan.edit', $pengajuan)
                    ->with('success', 'Draft berhasil diperbarui.');
            }

            return redirect()
                ->route('debitur.pengajuan.show', $pengajuan)
                ->with('success', 'Pengajuan berhasil dikirim.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Hapus pengajuan berstatus draft.
     */
    public function destroy(Pengajuan $pengajuan)
    {
        $this->authorizeOwner($pengajuan);

        if ($pengajuan->status !== 'draft') {
            return back()->with('error', 'Hanya pengajuan berstatus draft yang dapat dihapus.');
        }

        $this->service->deletePengajuan($pengajuan);

        return redirect()
            ->route('debitur.dashboard')
            ->with('success', 'Pengajuan draft berhasil dihapus.');
    }

    /**
     * Daftar semua pengajuan milik debitur yang login.
     */
    public function index()
    {
        $pengajuanList = Pengajuan::with(['unit.tipeUnit.proyek'])
            ->where('user_id', Auth::id())
            ->latest('tgl_submitted')
            ->paginate(10);

        return view('debitur.pengajuan.index', compact('pengajuanList'));
    }

/**
 * Daftar riwayat pengajuan debitur
 */
public function history(Request $request)
{
    $query = Pengajuan::with(['unit.tipeUnit.proyek'])
        ->where('user_id', Auth::id());

    // Filter pencarian
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('kode_pengajuan', 'like', "%{$search}%")
              ->orWhereHas('unit.tipeUnit.proyek', function($sub) use ($search) {
                  $sub->where('nama_proyek', 'like', "%{$search}%");
              });
        });
    }

    // Filter status
    if ($request->filled('status') && $request->status !== 'all') {
        $statusMap = [
            'draft' => 'draft',
            'pending' => 'pending',
            'diproses' => 'diproses',
            'survey' => 'survey',
            'analisis' => 'analisis',
            'disetujui' => 'disetujui',
            'ditolak' => 'ditolak'
        ];

        if (isset($statusMap[$request->status])) {
            $query->where('status', $statusMap[$request->status]);
        }
    }

    // Sorting
    switch ($request->get('sort', 'newest')) {
        case 'oldest':
            $query->orderBy('created_at', 'asc');
            break;
        case 'price_high':
            $query->orderBy('harga_properti', 'desc');
            break;
        case 'price_low':
            $query->orderBy('harga_properti', 'asc');
            break;
        case 'newest':
        default:
            $query->orderBy('created_at', 'desc');
            break;
    }

    $pengajuanModels = $query->get();

    // Hitung statistik untuk summary cards
    $total = $pengajuanModels->count();
    $draft = $pengajuanModels->where('status', 'draft')->count();
    $diproses = $pengajuanModels->whereIn('status', ['pending', 'submitted', 'diproses', 'verifikasi', 'menunggu', 'survey', 'analisis'])->count();
    $disetujui = $pengajuanModels->whereIn('status', ['disetujui', 'approved', 'layak'])->count();

    // Format data untuk JavaScript (harus bernama $dataPengajuan)
    $dataPengajuan = $pengajuanModels->map(function($item) {
        // Hitung skor (jika ada penilaian kelayakan)
        $skor = 0;
        if ($item->penilaianKelayakan) {
            $skor = $item->penilaianKelayakan->skor_akhir ?? 0;
        } elseif ($item->status === 'disetujui') {
            $skor = rand(75, 95); // Temporary, ganti dengan data real
        } elseif ($item->status === 'diproses') {
            $skor = rand(50, 74);
        }

        // Mapping status ke format blade
        $statusBlade = $item->status;
        if (in_array($item->status, ['diproses', 'menunggu', 'pending', 'submitted', 'verifikasi'])) {
            $statusBlade = 'diproses';
        } elseif (in_array($item->status, ['disetujui', 'approved', 'layak'])) {
            $statusBlade = 'disetujui';
        } elseif (in_array($item->status, ['ditolak', 'rejected', 'tidak_layak'])) {
            $statusBlade = 'ditolak';
        }

        return [
            'id' => $item->id,
            'kode' => $item->kode_pengajuan,
            'tanggal' => $item->created_at->format('Y-m-d'),
            'properti' => $item->unit->tipeUnit->proyek->nama_proyek ?? '-',
            'tipe' => $item->unit->tipeUnit->nama_tipe ?? '-',
            'harga' => (int) $item->harga_properti,
            'dp' => (int) $item->uang_muka,
            'tenor' => (int) $item->tenor_tahun,
            'status' => $statusBlade,
            'skor' => (int) $skor,
            'catatan' => $item->catatan_debitur ?? $this->getStatusMessage($item->status),
        ];
    });

    return view('debitur.pages.riwayat-pengajuan', compact('dataPengajuan', 'total', 'draft', 'diproses', 'disetujui'));
}





    // -----------------------------------------------------------------------
    // Private helpers
    // -----------------------------------------------------------------------

    private function authorizeOwner(Pengajuan $pengajuan): void
    {
        abort_if($pengajuan->user_id !== Auth::id(), 403, 'Anda tidak berhak mengakses pengajuan ini.');
    }

    private function authorizeEditable(Pengajuan $pengajuan): void
    {
        $editableStatuses = ['draft', 'revisi_debitur'];

        abort_if(
            !in_array($pengajuan->status, $editableStatuses, true),
            403,
            'Pengajuan ini tidak dapat diedit karena statusnya: ' . $pengajuan->status
        );
    }

    /**
 * Get status message based on status
 */
private function getStatusMessage($status)
{
    $messages = [
        'draft' => 'Draft belum dikirim, silakan lengkapi data',
        'submitted' => 'Pengajuan telah dikirim, menunggu verifikasi',
        'pending' => 'Menunggu verifikasi dokumen',
        'diproses' => 'Sedang dalam proses analisis',
        'survey' => 'Jadwal survey sedang diatur',
        'analisis' => 'Dokumen sedang dianalisis',
        'verifikasi' => 'Menunggu verifikasi marketing',
        'revisi' => 'Perlu perbaikan data, silakan edit',
        'disetujui' => 'Pengajuan disetujui, lanjut ke akad kredit',
        'approved' => 'Pengajuan disetujui',
        'ditolak' => 'Pengajuan tidak memenuhi persyaratan',
        'rejected' => 'Pengajuan ditolak',
    ];

    return $messages[$status] ?? 'Pengajuan sedang diproses';
}
}

<?php

namespace App\Http\Controllers\Marketing;

use Illuminate\Http\{RedirectResponse, Request};
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;
use App\Models\{Pengajuan, RiwayatStatus};
use App\Services\Marketing\PengajuanKPRService;

class MarketingPengajuanController extends Controller
{
    public function __construct(
        private PengajuanKPRService $pengajuanService
    ) {}

    // ─── Antrian masuk (status: submitted, belum diambil) ───────────────────

    public function masuk(Request $request)
    {
        // Query base: pengajuan submitted yang belum/sudah diambil marketing ini
        $baseQuery = Pengajuan::submitted()
            ->with(['user', 'debiturPribadi', 'unit.tipeUnit.proyek']);

        if ($request->filled('search')) {
            $search = $request->search;
            $baseQuery->where(function ($q) use ($search) {
                $q->where('kode_pengajuan', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($s) =>
                      $s->where('nama_lengkap', 'like', "%{$search}%")
                  )
                  ->orWhereHas('debiturPribadi', fn($s) =>
                      $s->where('nik', 'like', "%{$search}%")
                  );
            });
        }

        if ($request->filled('start_date')) {
            $baseQuery->whereDate('tgl_submitted', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $baseQuery->whereDate('tgl_submitted', '<=', $request->end_date);
        }

        // Statistik (sebelum paginate)
        $totalAntrian = (clone $baseQuery)->count();
        $totalPlafon  = (clone $baseQuery)->sum('jumlah_pinjaman');
        $avgTenor     = round((clone $baseQuery)->avg('tenor_tahun') ?? 0, 1);
        $hariIni      = (clone $baseQuery)->whereDate('tgl_submitted', today())->count();

        $antrian = $baseQuery
            ->orderBy('tgl_submitted', 'asc')
            ->paginate(15)
            ->withQueryString();

        return view('marketing.pages.pengajuan.masuk', compact(
            'antrian',
            'totalAntrian',
            'totalPlafon',
            'avgTenor',
            'hariIni'
        ));
    }

    // ─── Pengajuan yang sedang diverifikasi marketing ini ───────────────────

    public function proses(Request $request)
    {
        $marketingId = Auth::id();

        $baseQuery = Pengajuan::where('marketing_id', $marketingId)
            ->where('status', Pengajuan::STATUS_VERIFIKASI_MARKETING)
            ->with(['user', 'debiturPribadi', 'unit.tipeUnit.proyek', 'verifikasiMarketing']);

        if ($request->filled('search')) {
            $search = $request->search;
            $baseQuery->where(function ($q) use ($search) {
                $q->where('kode_pengajuan', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($s) =>
                      $s->where('nama_lengkap', 'like', "%{$search}%")
                  );
            });
        }

        $totalProses = (clone $baseQuery)->count();
        $pengajuan   = $baseQuery->orderBy('tgl_marketing_proses', 'asc')->paginate(15)->withQueryString();

        return view('marketing.pages.pengajuan.proses', compact('pengajuan', 'totalProses'));
    }

    // ─── Sudah diteruskan ke admin (menunggu penilaian SMART) ───────────────

    public function menungguAdmin(Request $request)
    {
        $marketingId = Auth::id();

        $baseQuery = Pengajuan::where('marketing_id', $marketingId)
            ->where('status', Pengajuan::STATUS_ANTRIAN_ADMIN)
            ->with(['user', 'debiturPribadi', 'unit.tipeUnit.proyek', 'verifikasiMarketing']);

        if ($request->filled('search')) {
            $search = $request->search;
            $baseQuery->where(function ($q) use ($search) {
                $q->where('kode_pengajuan', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($s) =>
                      $s->where('nama_lengkap', 'like', "%{$search}%")
                  );
            });
        }

        if ($request->filled('start_date')) {
            $baseQuery->whereDate('tgl_submitted', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $baseQuery->whereDate('tgl_submitted', '<=', $request->end_date);
        }

        $totalAntrian = (clone $baseQuery)->count();
        $totalPlafon  = (clone $baseQuery)->sum('jumlah_pinjaman');
        $avgTenor     = round((clone $baseQuery)->avg('tenor_tahun') ?? 0, 1);

        $antrian = $baseQuery->orderBy('tgl_submitted', 'asc')->paginate(15)->withQueryString();

        return view('marketing.pages.pengajuan.antrian-admin', compact(
            'antrian',
            'totalAntrian',
            'totalPlafon',
            'avgTenor'
        ));
    }

    // ─── Perlu revisi dari debitur ───────────────────────────────────────────

    public function revisi(Request $request)
    {
        $marketingId = Auth::id();

        $baseQuery = Pengajuan::where('marketing_id', $marketingId)
            ->where('status', Pengajuan::STATUS_REVISI_DEBITUR)
            ->with(['user', 'debiturPribadi', 'unit.tipeUnit.proyek', 'verifikasiMarketing']);

        if ($request->filled('search')) {
            $search = $request->search;
            $baseQuery->where(function ($q) use ($search) {
                $q->where('kode_pengajuan', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($s) =>
                      $s->where('nama_lengkap', 'like', "%{$search}%")
                  );
            });
        }

        if ($request->filled('start_date')) {
            $baseQuery->whereDate('tgl_submitted', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $baseQuery->whereDate('tgl_submitted', '<=', $request->end_date);
        }

        $totalAntrian = (clone $baseQuery)->count();
        $totalPlafon  = (clone $baseQuery)->sum('jumlah_pinjaman');
        $avgTenor     = round((clone $baseQuery)->avg('tenor_tahun') ?? 0, 1);

        $pengajuan = $baseQuery->orderBy('tgl_submitted', 'asc')->paginate(15)->withQueryString();

        return view('marketing.pages.pengajuan.revisi', compact(
            'pengajuan',
            'totalAntrian',
            'totalPlafon',
            'avgTenor'
        ));
    }


    // ________Pengajuan Ditolak_______
    public function ditolak(Request $request)
    {
        $marketingId = Auth::id();

        $baseQuery = Pengajuan::where('marketing_id', $marketingId)
            ->where('status', Pengajuan::STATUS_REVISI_DEBITUR)
            ->with(['user', 'debiturPribadi', 'unit.tipeUnit.proyek', 'verifikasiMarketing']);

        if ($request->filled('search')) {
            $search = $request->search;
            $baseQuery->where(function ($q) use ($search) {
                $q->where('kode_pengajuan', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($s) =>
                      $s->where('nama_lengkap', 'like', "%{$search}%")
                  );
            });
        }

        if ($request->filled('start_date')) {
            $baseQuery->whereDate('tgl_submitted', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $baseQuery->whereDate('tgl_submitted', '<=', $request->end_date);
        }

        $totalAntrian = (clone $baseQuery)->count();
        $totalPlafon  = (clone $baseQuery)->sum('jumlah_pinjaman');
        $avgTenor     = round((clone $baseQuery)->avg('tenor_tahun') ?? 0, 1);

        $pengajuan = $baseQuery->orderBy('tgl_submitted', 'asc')->paginate(15)->withQueryString();

        return view('marketing.pages.pengajuan.ditolak', compact(
            'pengajuan',
            'totalAntrian',
            'totalPlafon',
            'avgTenor'
        ));
    }

    // ─── Detail pengajuan ───────────────────────────────────────────────────

    public function show(Pengajuan $pengajuan)
    {
        $marketingId = Auth::id();

        // Marketing hanya boleh lihat pengajuan miliknya atau yang masih di antrian
        $bolehLihat = $pengajuan->marketing_id === $marketingId
            || ($pengajuan->marketing_id === null
                && $pengajuan->status === Pengajuan::STATUS_SUBMITTED);

        if (!$bolehLihat) {
            abort(403);
        }

        $pengajuan->load([
            'user',
            'unit.tipeUnit.proyek',
            'debiturPribadi',
            'debiturPekerjaan',
            'debiturKeuangan',
            'dokumen',
            'verifikasiMarketing',
        ]);

        $riwayat = RiwayatStatus::where('pengajuan_id', $pengajuan->id)
            ->with('pengubah')
            ->orderBy('created_at')
            ->get();

        // Kemampuan bayar
        $penghasilan = (float) (optional($pengajuan->debiturPekerjaan)->total_penghasilan ?? 0);
        $angsuran    = (float) ($pengajuan->estimasi_angsuran ?? 0);
        $dti         = $penghasilan > 0 ? round(($angsuran / $penghasilan) * 100, 2) : 0;

        $kemampuanBayar = [
            'penghasilan_bulanan' => $penghasilan,
            'estimasi_angsuran'   => $angsuran,
            'dti_persen'          => $dti,
            'status'              => $dti <= 30 ? 'aman' : ($dti <= 40 ? 'waspada' : 'berisiko'),
        ];

        return view('marketing.pages.pengajuan.show', compact(
            'pengajuan',
            'riwayat',
            'kemampuanBayar'
        ));
    }

    // ─── Ambil pengajuan dari antrian & mulai verifikasi ────────────────────

    public function ambil(Pengajuan $pengajuan): RedirectResponse
    {
        $result = $this->pengajuanService->startVerifikasi($pengajuan, Auth::id());

        if (!$result) {
            return back()->with('error', 'Gagal mengambil pengajuan. Pengajuan mungkin sudah diambil marketing lain.');
        }

        return redirect()
            ->route('marketing.pages.verifikasi.dokumen.create', $pengajuan)
            ->with('success', "Pengajuan {$pengajuan->kode_pengajuan} berhasil diambil. Silakan lakukan verifikasi dokumen.");
    }
}

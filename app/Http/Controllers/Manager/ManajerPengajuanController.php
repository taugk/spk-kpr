<?php

namespace App\Http\Controllers\Manager;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;
use App\Models\{Pengajuan, Penilaian, RiwayatStatus};

class ManajerPengajuanController extends Controller
{
    public function semua(Request $request)
    {
        $query = Pengajuan::with(['user', 'penilaian', 'marketing', 'admin']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->search) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('nama_lengkap', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            })->orWhere('kode_pengajuan', 'like', "%{$request->search}%");
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $perPage = $request->get('per_page', 15);
        $pengajuan = $query->orderBy('created_at', 'desc')->paginate($perPage);

        $statistik = [
            'semua' => Pengajuan::count(),
            'submitted' => Pengajuan::where('status', Pengajuan::STATUS_SUBMITTED)->count(),
            'verifikasi_marketing' => Pengajuan::where('status', Pengajuan::STATUS_VERIFIKASI_MARKETING)->count(),
            'antrian_admin' => Pengajuan::where('status', Pengajuan::STATUS_ANTRIAN_ADMIN)->count(),
            'penilaian_admin' => Pengajuan::where('status', Pengajuan::STATUS_PENILAIAN_ADMIN)->count(),
            'selesai_dinilai' => Pengajuan::where('status', Pengajuan::STATUS_SELESAI_DINILAI)->count(),
            'disetujui' => Pengajuan::where('status', Pengajuan::STATUS_DISETUJUI_SISTEM)->count(),
            'ditolak' => Pengajuan::whereIn('status', Pengajuan::getRejectedStatuses())->count(),
        ];

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $pengajuan,
                'statistik' => $statistik
            ]);
        }

        return view('manager.pages.pengajuan.semua', compact('pengajuan', 'statistik'));
    }

    public function sedangDiproses(Request $request)
    {
        $query = Pengajuan::with(['user', 'penilaian', 'marketing', 'admin'])
            ->whereIn('status', [
                Pengajuan::STATUS_SUBMITTED,
                Pengajuan::STATUS_VERIFIKASI_MARKETING,
                Pengajuan::STATUS_ANTRIAN_ADMIN,
                Pengajuan::STATUS_PENILAIAN_ADMIN,
                Pengajuan::STATUS_SELESAI_DINILAI,
            ]);

        $perPage = $request->get('per_page', 15);
        $pengajuan = $query->orderBy('updated_at', 'desc')->paginate($perPage);

        $rataWaktu = $this->getRataWaktuProses();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $pengajuan,
                'rata_rata_waktu_proses' => $rataWaktu
            ]);
        }

        return view('manager.pages.pengajuan.sedang-diproses', compact('pengajuan', 'rataWaktu'));
    }

    public function selesai(Request $request)
    {
        $query = Pengajuan::with(['user', 'penilaian', 'marketing', 'admin'])
            ->whereIn('status', [
                Pengajuan::STATUS_DISETUJUI_SISTEM,
                Pengajuan::STATUS_DITOLAK_SISTEM,
                Pengajuan::STATUS_DITOLAK_MARKETING,
            ]);

        $perPage = $request->get('per_page', 15);
        $pengajuan = $query->orderBy('tgl_selesai', 'desc')->paginate($perPage);

        if ($request->ajax()) {
            return response()->json($pengajuan);
        }

        return view('manager.pages.pengajuan.selesai', compact('pengajuan'));
    }

    public function show($id)
    {
        $pengajuan = Pengajuan::with([
            'user',
            'marketing',
            'admin',
            'penilaian' => function($q) {
                $q->with(['details.kriteria']);
            },
            'dokumen',
            'debiturPribadi',
            'debiturPekerjaan',
            'debiturKeuangan',
            'unit.tipeUnit.proyek'
        ])->findOrFail($id);

        $timeline = RiwayatStatus::getTimeline($pengajuan->id);
        $rekomendasi = $this->getRekomendasi($pengajuan);
        $totalProcessingTime = RiwayatStatus::getTotalProcessingTime($pengajuan->id);

        return view('manager.pages.pengajuan.show', compact('pengajuan', 'timeline', 'rekomendasi', 'totalProcessingTime'));
    }

    public function export(Request $request)
    {
        $request->validate([
            'status' => 'nullable|string',
            'format' => 'required|in:excel,csv,pdf',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date'
        ]);

        $query = Pengajuan::with(['user', 'penilaian']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $data = $query->get()->map(function($p) {
            return [
                'Kode Pengajuan' => $p->kode_pengajuan,
                'Nama Debitur' => $p->user->nama_lengkap ?? '-',
                'Jumlah Pinjaman' => $p->jumlah_pinjaman,
                'Tenor' => $p->tenor_tahun . ' tahun',
                'Status' => $p->status_text,
                'Tanggal Pengajuan' => $p->created_at->format('d/m/Y H:i'),
                'Tanggal Selesai' => $p->tgl_selesai ? $p->tgl_selesai->format('d/m/Y H:i') : '-',
                'Skor Penilaian' => $p->penilaian->skor_akhir ?? '-',
                'Hasil' => $p->penilaian->hasil_text ?? '-',
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Export berhasil',
            'total_data' => $data->count(),
            'data' => $data
        ]);
    }

    private function getRataWaktuProses()
    {
        $selesai = Pengajuan::whereIn('status', [
            Pengajuan::STATUS_DISETUJUI_SISTEM,
            Pengajuan::STATUS_DITOLAK_SISTEM
        ])->whereNotNull('tgl_selesai')->get();

        if ($selesai->isEmpty()) return 0;

        $totalHari = $selesai->sum(function($p) {
            return $p->tgl_submitted ? $p->tgl_submitted->diffInDays($p->tgl_selesai) : 0;
        });

        return round($totalHari / $selesai->count(), 1);
    }

    private function getRekomendasi($pengajuan)
    {
        $rekomendasi = [];

        if ($pengajuan->status === Pengajuan::STATUS_VERIFIKASI_MARKETING &&
            $pengajuan->tgl_marketing_proses &&
            $pengajuan->tgl_marketing_proses->diffInDays(now()) > 7) {
            $rekomendasi[] = [
                'jenis' => 'warning',
                'pesan' => 'Pengajuan ini sudah dalam proses verifikasi marketing lebih dari 7 hari.',
                'action' => 'Segera verifikasi dokumen'
            ];
        }

        if ($pengajuan->status === Pengajuan::STATUS_PENILAIAN_ADMIN &&
            $pengajuan->tgl_admin_proses &&
            $pengajuan->tgl_admin_proses->diffInDays(now()) > 5) {
            $rekomendasi[] = [
                'jenis' => 'danger',
                'pesan' => 'Penilaian admin sudah berlangsung lebih dari 5 hari.',
                'action' => 'Segera selesaikan penilaian'
            ];
        }

        $dokumenLengkap = $pengajuan->dokumen->count();
        if ($dokumenLengkap < 10) {
            $rekomendasi[] = [
                'jenis' => 'warning',
                'pesan' => "Dokumen belum lengkap. Hanya {$dokumenLengkap} dokumen yang diupload.",
                'action' => 'Hubungi debitur untuk melengkapi dokumen'
            ];
        }

        if ($pengajuan->penilaian) {
            $skor = $pengajuan->penilaian->skor_akhir;
            if ($skor < 50) {
                $rekomendasi[] = [
                    'jenis' => 'danger',
                    'pesan' => "Skor penilaian rendah ({$skor}%). Kemungkinan pengajuan akan ditolak.",
                    'action' => 'Evaluasi ulang kriteria penilaian'
                ];
            } elseif ($skor >= 75) {
                $rekomendasi[] = [
                    'jenis' => 'success',
                    'pesan' => "Skor penilaian tinggi ({$skor}%). Debitur layak mendapatkan persetujuan.",
                    'action' => 'Proses persetujuan'
                ];
            }
        }

        return $rekomendasi;
    }
}

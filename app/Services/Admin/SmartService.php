<?php
// app/Services/Admin/SmartService.php

namespace App\Services\Admin;

use Illuminate\Support\Facades\{Auth, Cache, DB, Log};
use Exception;

class SmartService
{
    /**
     * Get all assessments with pagination
     */
    public function getAllAssessments(int $perPage = 10)
    {
        return DB::table('penilaian as pn')
            ->join('pengajuan as p', 'p.id', '=', 'pn.pengajuan_id')
            ->join('users as u', 'u.id', '=', 'p.user_id')
            ->leftJoin('users as admin', 'admin.id', '=', 'pn.admin_id')
            ->select(
                'pn.id',
                'pn.pengajuan_id',
                'p.kode_pengajuan',
                'u.nama_lengkap as nama_debitur',
                'admin.nama_lengkap as nama_admin',
                'pn.tgl_penilaian',
                'pn.skor_akhir',
                'pn.threshold',
                'pn.hasil',
                'pn.catatan_admin',
                'pn.created_at'
            )
            ->orderByDesc('pn.id')
            ->paginate($perPage);
    }

    /**
     * Simpan penilaian baru dengan perhitungan detail dan full log
     */
    public function storeAssessment(array $data): array
    {
        $logData = [
            'start_time' => now(),
            'admin_id' => Auth::id(),
            'pengajuan_id' => $data['pengajuan_id'],
            'threshold' => $data['threshold'],
            'nilai_input' => $data['nilai']
        ];
        
        try {
            DB::beginTransaction();
            
            Log::channel('smart')->info('=== START SMART ASSESSMENT ===', $logData);
            
            $adminId = Auth::id();
            if (!$adminId) {
                throw new Exception('Admin tidak terdeteksi');
            }
            
            // Cek apakah sudah ada penilaian untuk pengajuan ini
            $existingPenilaian = DB::table('penilaian')
                ->where('pengajuan_id', $data['pengajuan_id'])
                ->first();
            
            Log::channel('smart')->info('Cek existing penilaian', [
                'exists' => $existingPenilaian ? true : false,
                'penilaian_id' => $existingPenilaian->id ?? null
            ]);
            
            if ($existingPenilaian) {
                // UPDATE existing penilaian
                DB::table('penilaian')
                    ->where('id', $existingPenilaian->id)
                    ->update([
                        'admin_id' => $adminId,
                        'threshold' => $data['threshold'],
                        'catatan_admin' => $data['catatan'] ?? null,
                        'tgl_penilaian' => now(),
                        'updated_at' => now()
                    ]);
                
                $penilaianId = $existingPenilaian->id;
                
                // Hapus detail lama
                DB::table('penilaian_detail')
                    ->where('penilaian_id', $penilaianId)
                    ->delete();
                    
                Log::channel('smart')->info('Update existing penilaian', ['penilaian_id' => $penilaianId]);
            } else {
                // INSERT baru
                $penilaianId = DB::table('penilaian')->insertGetId([
                    'pengajuan_id' => $data['pengajuan_id'],
                    'admin_id' => $adminId,
                    'threshold' => $data['threshold'],
                    'catatan_admin' => $data['catatan'] ?? null,
                    'tgl_penilaian' => now(),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                
                Log::channel('smart')->info('Create new penilaian', ['penilaian_id' => $penilaianId]);
            }
            
            // Simpan detail penilaian dengan log lengkap
            $totalSkor = 0;
            $perhitungan = [];
            $detailLogs = [];
            
            foreach ($data['nilai'] as $kriteriaId => $nilaiInput) {
                $kriteria = DB::table('kriteria')->where('id', $kriteriaId)->first();
                
                if (!$kriteria) {
                    throw new Exception("Kriteria ID {$kriteriaId} tidak ditemukan");
                }
                
                // Log detail per kriteria
                $detailLog = [
                    'kriteria_id' => $kriteriaId,
                    'kode_kriteria' => $kriteria->kode_kriteria,
                    'nama_kriteria' => $kriteria->nama_kriteria,
                    'tipe' => $kriteria->tipe,
                    'nilai_input' => $nilaiInput,
                    'nilai_min' => $kriteria->nilai_min,
                    'nilai_max' => $kriteria->nilai_max,
                    'bobot' => $kriteria->bobot
                ];
                
                // Hitung normalisasi
                $nilaiNormalisasi = $this->hitungNormalisasi(
                    (float)$nilaiInput,
                    (float)$kriteria->nilai_min,
                    (float)$kriteria->nilai_max,
                    $kriteria->tipe
                );
                
                // Hitung skor kontribusi
                $skorKontribusi = $nilaiNormalisasi * $kriteria->bobot;
                $totalSkor += $skorKontribusi;
                
                // Log perhitungan
                $detailLog['normalisasi'] = [
                    'rumus' => $kriteria->tipe === 'benefit' 
                        ? "({$nilaiInput} - {$kriteria->nilai_min}) / ({$kriteria->nilai_max} - {$kriteria->nilai_min})"
                        : "({$kriteria->nilai_max} - {$nilaiInput}) / ({$kriteria->nilai_max} - {$kriteria->nilai_min})",
                    'pembilang' => $kriteria->tipe === 'benefit' 
                        ? $nilaiInput - $kriteria->nilai_min
                        : $kriteria->nilai_max - $nilaiInput,
                    'penyebut' => $kriteria->nilai_max - $kriteria->nilai_min,
                    'hasil' => $nilaiNormalisasi
                ];
                
                $detailLog['skor_kontribusi'] = [
                    'rumus' => "{$nilaiNormalisasi} × {$kriteria->bobot}",
                    'hasil' => $skorKontribusi
                ];
                
                $detailLogs[] = $detailLog;
                
                // Simpan ke database
                DB::table('penilaian_detail')->insert([
                    'penilaian_id' => $penilaianId,
                    'kriteria_id' => $kriteriaId,
                    'nilai_input' => $nilaiInput,
                    // 'nilai_min_snapshot' => $kriteria->nilai_min,
                    // 'nilai_max_snapshot' => $kriteria->nilai_max,
                    // 'tipe_snapshot' => $kriteria->tipe,
                    'nilai_normalisasi' => $nilaiNormalisasi,
                    'bobot_snapshot' => $kriteria->bobot,
                    'skor_kontribusi' => $skorKontribusi,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                
                $perhitungan[] = [
                    'kriteria_id' => $kriteriaId,
                    'kode_kriteria' => $kriteria->kode_kriteria,
                    'nama_kriteria' => $kriteria->nama_kriteria,
                    'tipe' => $kriteria->tipe,
                    'nilai_input' => $nilaiInput,
                    'min' => $kriteria->nilai_min,
                    'max' => $kriteria->nilai_max,
                    'normalisasi' => round($nilaiNormalisasi, 6),
                    'bobot' => $kriteria->bobot . '%',
                    'skor' => round($skorKontribusi, 2),
                    'rumus_normalisasi' => $detailLog['normalisasi']['rumus'],
                    'rumus_skor' => $detailLog['skor_kontribusi']['rumus']
                ];
            }
            
            // Log semua detail perhitungan
            Log::channel('smart')->info('Detail Perhitungan per Kriteria', ['details' => $detailLogs]);
            
            $totalSkorAkhir = round($totalSkor, 2);
            $threshold = $data['threshold'];
            $hasil = $totalSkorAkhir >= $threshold ? 'layak' : 'tidak_layak';
            
            // Log hasil perhitungan
            Log::channel('smart')->info('Hasil Perhitungan', [
                'total_skor' => $totalSkorAkhir,
                'threshold' => $threshold,
                'selisih' => round($totalSkorAkhir - $threshold, 2),
                'hasil_akhir' => $hasil,
                'keputusan' => $hasil == 'layak' ? 'DISETUJUI' : 'DITOLAK'
            ]);
            
            // Update penilaian
            DB::table('penilaian')
                ->where('id', $penilaianId)
                ->update([
                    'skor_akhir' => $totalSkorAkhir,
                    'hasil' => $hasil,
                    'updated_at' => now()
                ]);
            
            // Update status pengajuan
            DB::table('pengajuan')
                ->where('id', $data['pengajuan_id'])
                ->update([
                    'status' => $hasil == 'layak' ? 'disetujui_sistem' : 'ditolak_sistem',
                    'tgl_selesai' => now(),
                    'updated_at' => now()
                ]);
            
            DB::commit();
            
            $logData['end_time'] = now();
            $logData['duration_ms'] = now()->diffInMilliseconds($logData['start_time']);
            $logData['status'] = 'success';
            $logData['hasil'] = $hasil;
            $logData['skor_akhir'] = $totalSkorAkhir;
            
            Log::channel('smart')->info('=== SMART ASSESSMENT COMPLETED ===', $logData);
            
            return [
                'success' => true,
                'penilaian_id' => $penilaianId,
                'skor_akhir' => $totalSkorAkhir,
                'threshold' => $threshold,
                'hasil' => $hasil,
                'is_update' => $existingPenilaian ? true : false,
                'perhitungan' => $perhitungan,
                'detail_logs' => $detailLogs,
                'message' => ($existingPenilaian ? 'Penilaian berhasil diperbarui. ' : 'Penilaian berhasil disimpan. ') . 
                    ($hasil == 'layak' ? "✅ Pengajuan DISETUJUI (Skor: {$totalSkorAkhir} ≥ {$threshold})" 
                        : "❌ Pengajuan DITOLAK (Skor: {$totalSkorAkhir} < {$threshold})")
            ];
            
        } catch (Exception $e) {
            DB::rollBack();
            
            $logData['error'] = $e->getMessage();
            $logData['trace'] = $e->getTraceAsString();
            $logData['status'] = 'failed';
            
            Log::channel('smart')->error('=== SMART ASSESSMENT FAILED ===', $logData);
            Log::error('SMART Service Error: ' . $e->getMessage(), [
                'data' => $data,
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'message' => 'Gagal menyimpan penilaian: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Hitung normalisasi (nilai utility) dengan log detail
     * Rumus: 
     * - Benefit: (X - min) / (max - min)
     * - Cost: (max - X) / (max - min)
     * 
     * @param float $nilai Nilai input
     * @param float $min Nilai minimum kriteria
     * @param float $max Nilai maksimum kriteria
     * @param string $tipe 'benefit' atau 'cost'
     * @return float
     */
    private function hitungNormalisasi(float $nilai, float $min, float $max, string $tipe): float
    {
        $logNormalisasi = [
            'nilai' => $nilai,
            'min' => $min,
            'max' => $max,
            'tipe' => $tipe
        ];
        
        // Cegah pembagian dengan nol
        if ($max - $min == 0) {
            Log::channel('smart')->warning('Normalisasi: pembagian dengan nol', $logNormalisasi);
            return 1.0;
        }
        
        if ($tipe === 'benefit') {
            // Benefit: semakin besar nilai, semakin baik
            $normalisasi = ($nilai - $min) / ($max - $min);
            $logNormalisasi['rumus'] = "({$nilai} - {$min}) / ({$max} - {$min})";
        } else {
            // Cost: semakin kecil nilai, semakin baik
            $normalisasi = ($max - $nilai) / ($max - $min);
            $logNormalisasi['rumus'] = "({$max} - {$nilai}) / ({$max} - {$min})";
        }
        
        $logNormalisasi['pembilang'] = $tipe === 'benefit' ? $nilai - $min : $max - $nilai;
        $logNormalisasi['penyebut'] = $max - $min;
        $logNormalisasi['hasil_normalisasi'] = $normalisasi;
        $logNormalisasi['hasil_terbatas'] = max(0, min(1, $normalisasi));
        
        Log::channel('smart')->debug('Hitung Normalisasi', $logNormalisasi);
        
        // Batasi nilai antara 0 dan 1
        return max(0, min(1, $normalisasi));
    }
    
    /**
     * Get data penilaian lengkap dengan detail perhitungan
     */
    public function getAssessmentData(int $penilaianId): ?object
    {
        $penilaian = DB::table('penilaian as pn')
            ->join('pengajuan as p', 'p.id', '=', 'pn.pengajuan_id')
            ->join('users as u', 'u.id', '=', 'p.user_id')
            ->leftJoin('users as admin', 'admin.id', '=', 'pn.admin_id')
            ->select(
                'pn.*',
                'p.kode_pengajuan',
                'p.jumlah_pinjaman',
                'p.tenor_tahun',
                'u.nama_lengkap as nama_debitur',
                'u.email as email_debitur',
                'admin.nama_lengkap as nama_admin'
            )
            ->where('pn.id', $penilaianId)
            ->first();
        
        if ($penilaian) {
            // Ambil detail perhitungan lengkap
            $penilaian->details = DB::table('penilaian_detail as pd')
                ->join('kriteria as k', 'k.id', '=', 'pd.kriteria_id')
                ->select(
                    'pd.*',
                    'k.kode_kriteria',
                    'k.nama_kriteria',
                    'k.tipe',
                    'k.nilai_min',
                    'k.nilai_max',
                    'k.bobot as bobot_asli'
                )
                ->where('pd.penilaian_id', $penilaianId)
                ->orderBy('k.urutan')
                ->get();
            
            // Hitung ulang untuk memastikan
            foreach ($penilaian->details as $detail) {
                $detail->nilai_normalisasi_verify = $this->hitungNormalisasi(
                    $detail->nilai_input,
                    $detail->nilai_min,
                    $detail->nilai_max,
                    $detail->tipe
                );
                $detail->skor_kontribusi_verify = $detail->nilai_normalisasi_verify * $detail->bobot_snapshot;
                
                // Tambahkan penjelasan perhitungan
                $detail->rumus_normalisasi = $this->getRumusNormalisasi($detail);
                $detail->rumus_kontribusi = $this->getRumusKontribusi($detail);
            }
            
            // Hitung total skor
            $penilaian->total_skor_verify = round($penilaian->details->sum('skor_kontribusi_verify'), 2);
            $penilaian->keputusan = $penilaian->total_skor_verify >= $penilaian->threshold ? 'LAYAK' : 'TIDAK LAYAK';
            
            // Persentase kelayakan
            $penilaian->persentase_kelayakan = round(($penilaian->total_skor_verify / $penilaian->threshold) * 100, 2);
        }
        
        return $penilaian;
    }
    
    /**
     * Get rumus normalisasi dengan format lengkap
     */
    private function getRumusNormalisasi($detail): string
    {
        $range = $detail->nilai_max - $detail->nilai_min;
        
        if ($detail->tipe === 'benefit') {
            $pembilang = $detail->nilai_input - $detail->nilai_min;
            return "U = ({$detail->nilai_input} - {$detail->nilai_min}) / ({$detail->nilai_max} - {$detail->nilai_min}) = {$pembilang} / {$range} = " . round($detail->nilai_normalisasi_verify, 6);
        } else {
            $pembilang = $detail->nilai_max - $detail->nilai_input;
            return "U = ({$detail->nilai_max} - {$detail->nilai_input}) / ({$detail->nilai_max} - {$detail->nilai_min}) = {$pembilang} / {$range} = " . round($detail->nilai_normalisasi_verify, 6);
        }
    }
    
    /**
     * Get rumus kontribusi dengan format lengkap
     */
    private function getRumusKontribusi($detail): string
    {
        return "S = {$detail->nilai_normalisasi_verify} × {$detail->bobot_snapshot}% = " . round($detail->skor_kontribusi_verify, 2);
    }
    
    /**
     * Get data untuk form penilaian dengan default values
     */
    public function getFormData(int $pengajuanId): array
    {
        Log::channel('smart')->info('Mengambil data form untuk pengajuan', ['pengajuan_id' => $pengajuanId]);
        
        // Ambil data pengajuan
        $pengajuan = DB::table('v_pengajuan_lengkap')
            ->where('pengajuan_id', $pengajuanId)
            ->first();
        
        if (!$pengajuan) {
            throw new Exception('Data pengajuan tidak ditemukan');
        }
        
        // Ambil data debitur
        $debiturPribadi = DB::table('debitur_pribadi')
            ->where('user_id', $pengajuan->debitur_id)
            ->first();
        
        $debiturPekerjaan = DB::table('debitur_pekerjaan')
            ->where('user_id', $pengajuan->debitur_id)
            ->first();
        
        $debiturKeuangan = DB::table('debitur_keuangan')
            ->where('user_id', $pengajuan->debitur_id)
            ->first();
        
        // Ambil dokumen
        $dokumen = DB::table('dokumen_pengajuan')
            ->where('pengajuan_id', $pengajuanId)
            ->get()
            ->groupBy('jenis_dokumen');
        
        // Ambil kriteria dengan skala
        $kriteria = DB::table('kriteria')
            ->where('aktif', true)
            ->orderBy('urutan')
            ->get();
        
        foreach ($kriteria as $k) {
            $k->skala = DB::table('kriteria_skala')
                ->where('kriteria_id', $k->id)
                ->orderBy('skor')
                ->get();
            
            // Tambahkan informasi range untuk benefit/cost
            $k->range_info = $this->getRangeInfo($k);
        }
        
        Log::channel('smart')->info('Data form berhasil diambil', [
            'pengajuan_id' => $pengajuanId,
            'jml_kriteria' => $kriteria->count()
        ]);
        
        return [
            'pengajuan' => $pengajuan,
            'debiturPribadi' => $debiturPribadi,
            'debiturPekerjaan' => $debiturPekerjaan,
            'debiturKeuangan' => $debiturKeuangan,
            'dokumen' => $dokumen,
            'kriteria' => $kriteria
        ];
    }
    
    /**
     * Get range info for kriteria
     */
    private function getRangeInfo($kriteria): string
    {
        if ($kriteria->tipe === 'benefit') {
            return "📈 Benefit: Nilai {$kriteria->nilai_min} - {$kriteria->nilai_max} (semakin tinggi semakin baik)";
        } else {
            return "📉 Cost: Nilai {$kriteria->nilai_min} - {$kriteria->nilai_max} (semakin rendah semakin baik)";
        }
    }
    
    /**
     * Get complete calculation details for display
     */
    public function getCalculationDetails(int $penilaianId): array
    {
        $penilaian = DB::table('penilaian')
            ->where('id', $penilaianId)
            ->first();
        
        if (!$penilaian) {
            return [];
        }
        
        $details = DB::table('penilaian_detail as pd')
            ->join('kriteria as k', 'k.id', '=', 'pd.kriteria_id')
            ->select(
                'k.kode_kriteria',
                'k.nama_kriteria',
                'k.tipe',
                'k.nilai_min',
                'k.nilai_max',
                'k.bobot',
                'pd.nilai_input',
                'pd.nilai_normalisasi',
                'pd.skor_kontribusi'
            )
            ->where('pd.penilaian_id', $penilaianId)
            ->orderBy('k.urutan')
            ->get();
        
        $totalBobot = $details->sum('bobot');
        $totalSkor = $details->sum('skor_kontribusi');
        
        // Generate ringkasan perhitungan
        $ringkasan = [
            'total_bobot' => $totalBobot . '%',
            'total_skor' => round($totalSkor, 2),
            'threshold' => $penilaian->threshold,
            'selisih' => round($totalSkor - $penilaian->threshold, 2),
            'hasil' => $totalSkor >= $penilaian->threshold ? 'LAYAK' : 'TIDAK LAYAK'
        ];
        
        return [
            'penilaian' => $penilaian,
            'details' => $details,
            'total_bobot' => $totalBobot,
            'total_skor' => round($totalSkor, 2),
            'threshold' => $penilaian->threshold,
            'hasil' => $totalSkor >= $penilaian->threshold ? 'LAYAK' : 'TIDAK LAYAK',
            'status_warna' => $totalSkor >= $penilaian->threshold ? 'success' : 'danger',
            'ringkasan' => $ringkasan
        ];
    }
    
    /**
     * Export log perhitungan ke format array lengkap
     */
    public function exportCalculationLog(int $penilaianId): array
    {
        $penilaian = $this->getAssessmentData($penilaianId);
        
        if (!$penilaian) {
            return [];
        }
        
        $export = [
            'metadata' => [
                'penilaian_id' => $penilaian->id,
                'kode_pengajuan' => $penilaian->kode_pengajuan,
                'nama_debitur' => $penilaian->nama_debitur,
                'tanggal_penilaian' => $penilaian->tgl_penilaian,
                'admin_penilai' => $penilaian->nama_admin,
                'threshold' => $penilaian->threshold,
                'skor_akhir' => $penilaian->skor_akhir,
                'hasil_akhir' => $penilaian->hasil,
                'keputusan_final' => $penilaian->hasil == 'layak' ? 'DISETUJUI' : 'DITOLAK'
            ],
            'perhitungan_per_kriteria' => [],
            'ringkasan' => [
                'total_skor' => $penilaian->total_skor_verify,
                'threshold' => $penilaian->threshold,
                'selisih' => round($penilaian->total_skor_verify - $penilaian->threshold, 2),
                'status' => $penilaian->keputusan
            ]
        ];
        
        foreach ($penilaian->details as $detail) {
            $export['perhitungan_per_kriteria'][] = [
                'kode' => $detail->kode_kriteria,
                'nama' => $detail->nama_kriteria,
                'tipe' => $detail->tipe,
                'nilai_input' => $detail->nilai_input,
                'range' => "{$detail->nilai_min} - {$detail->nilai_max}",
                'rumus_normalisasi' => $detail->rumus_normalisasi,
                'nilai_normalisasi' => round($detail->nilai_normalisasi_verify, 6),
                'bobot' => $detail->bobot_snapshot . '%',
                'rumus_skor' => $detail->rumus_kontribusi,
                'skor_kontribusi' => round($detail->skor_kontribusi_verify, 2)
            ];
        }
        
        return $export;
    }
}
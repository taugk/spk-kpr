<?php

namespace App\Services\Debitur;

use Illuminate\Support\Facades\{DB, Log, Storage};
use Illuminate\Support\Collection;

use App\Helpers\DebiturPengajuanHelper;
use App\Models\{DebiturKeuangan, DebiturPekerjaan, DebiturPribadi, DokumenPengajuan, Pengajuan, Unit, User};

class DebiturPengajuanService
{
    /**
     * Ambil unit yang masih tersedia beserta relasi tipe & proyek,
     * diformat agar siap dipakai oleh view select-option.
     */
    public function getAvailableUnits(): Collection
    {
        Log::info('[DebiturPengajuanService] getAvailableUnits called');

        try {
            $units = Unit::with('tipeUnit.proyek')
                ->where('status', 'tersedia')
                ->get()
                ->map(fn(Unit $unit) => (object) [
                    'id'             => $unit->id,
                    'kode_unit'      => $unit->kode_unit,
                    'nama_proyek'    => $unit->tipeUnit->proyek->nama_proyek,
                    'tipe'           => $unit->tipeUnit->nama_tipe,
                    'harga'          => $unit->tipeUnit->harga,
                    'luas_tanah'     => $unit->tipeUnit->luas_tanah,
                    'luas_bangunan'  => $unit->tipeUnit->luas_bangunan,
                ]);

            Log::info('[DebiturPengajuanService] getAvailableUnits success', [
                'total_units' => $units->count()
            ]);

            return $units;
        } catch (\Exception $e) {
            Log::error('[DebiturPengajuanService] getAvailableUnits failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Buat pengajuan baru (draft atau submitted).
     */
    public function storePengajuan(User $user, array $data, array $files, bool $isDraft): Pengajuan
    {
        Log::info('[DebiturPengajuanService] storePengajuan started', [
            'user_id' => $user->id,
            'is_draft' => $isDraft,
            'data_keys' => array_keys($data)
        ]);

        return DB::transaction(function () use ($user, $data, $files, $isDraft) {
            Log::info('[DebiturPengajuanService] storePengajuan - transaction started');

            // 1. Upsert data profil debitur
            Log::info('[DebiturPengajuanService] Upserting debitur profiles');
            $this->upsertDebiturPribadi($user->id, $data);
            $this->upsertDebiturPekerjaan($user->id, $data);
            $this->upsertDebiturKeuangan($user->id, $data);
            Log::info('[DebiturPengajuanService] Debitur profiles upserted successfully');

            // 2. Hitung nilai finansial
            $unit = Unit::with('tipeUnit')->findOrFail($data['id_properti']);
            Log::info('[DebiturPengajuanService] Unit found', [
                'unit_id' => $unit->id,
                'harga_properti' => $unit->tipeUnit->harga
            ]);

            $hargaProperti = $unit->tipeUnit->harga;
            $dp = (float) ($data['dp'] ?? 0);
            $jumlahPinjaman = max($hargaProperti - $dp, 0);
            $tenor = (int) ($data['tenor'] ?? 20);
            $estimasi = DebiturPengajuanHelper::hitungAngsuran($jumlahPinjaman, $tenor);
            $totalPenghasilan = DebiturPengajuanHelper::totalPenghasilan($data);
            $rasio = DebiturPengajuanHelper::hitungRasio($estimasi, (float) ($data['total_cicilan'] ?? 0), $totalPenghasilan);

            Log::info('[DebiturPengajuanService] Financial calculations completed', [
                'harga_properti' => $hargaProperti,
                'dp' => $dp,
                'jumlah_pinjaman' => $jumlahPinjaman,
                'tenor' => $tenor,
                'estimasi_angsuran' => $estimasi,
                'rasio_angsuran' => $rasio
            ]);

            // 3. Simpan record pengajuan
            $kodePengajuan = DebiturPengajuanHelper::generateKode();
            Log::info('[DebiturPengajuanService] Generated pengajuan code', ['kode_pengajuan' => $kodePengajuan]);

            $pengajuan = Pengajuan::create([
                'kode_pengajuan'     => $kodePengajuan,
                'user_id'            => $user->id,
                'unit_id'            => $unit->id,
                'harga_properti'     => $hargaProperti,
                'uang_muka'          => $dp,
                'persen_dp'          => $hargaProperti > 0 ? round($dp / $hargaProperti * 100, 2) : 0,
                'jumlah_pinjaman'    => $jumlahPinjaman,
                'tenor_tahun'        => $tenor,
                'estimasi_angsuran'  => $estimasi,
                'rasio_angsuran'     => $rasio,
                'tujuan_pembelian'   => DebiturPengajuanHelper::mapTujuanPembelian($data['tujuan_pembelian'] ?? ''),
                'sumber_dp'          => DebiturPengajuanHelper::mapSumberDp($data['sumber_dp'] ?? ''),
                'catatan_debitur'    => $data['catatan_debitur'] ?? null,
                'status'             => $isDraft ? 'draft' : 'submitted',
                'tgl_submitted'      => $isDraft ? null : now(),
            ]);

            Log::info('[DebiturPengajuanService] Pengajuan created', [
                'pengajuan_id' => $pengajuan->id,
                'status' => $pengajuan->status
            ]);

            // 4. Upload dokumen
            if (!$isDraft) {
                Log::info('[DebiturPengajuanService] Processing document uploads for submitted pengajuan');
                $this->uploadDokumen($pengajuan, $files);
                
                // Tandai unit sebagai dipesan
                $unit->update(['status' => 'dipesan']);
                Log::info('[DebiturPengajuanService] Unit status updated to dipesan', ['unit_id' => $unit->id]);
            } else {
                Log::info('[DebiturPengajuanService] Skipping document upload (draft mode)');
            }

            Log::info('[DebiturPengajuanService] storePengajuan completed successfully', [
                'pengajuan_id' => $pengajuan->id
            ]);

            return $pengajuan;
        });
    }

    /**
     * Update pengajuan yang sedang dalam status draft atau revisi_debitur.
     */
    public function updatePengajuan(Pengajuan $pengajuan, array $data, array $files, bool $isDraft): Pengajuan
    {
        Log::info('[DebiturPengajuanService] updatePengajuan started', [
            'pengajuan_id' => $pengajuan->id,
            'current_status' => $pengajuan->status,
            'is_draft' => $isDraft,
            'user_id' => $pengajuan->user_id
        ]);

        return DB::transaction(function () use ($pengajuan, $data, $files, $isDraft) {
            Log::info('[DebiturPengajuanService] updatePengajuan - transaction started');

            $this->upsertDebiturPribadi($pengajuan->user_id, $data);
            $this->upsertDebiturPekerjaan($pengajuan->user_id, $data);
            $this->upsertDebiturKeuangan($pengajuan->user_id, $data);
            Log::info('[DebiturPengajuanService] Debitur profiles updated');

            $unit = Unit::with('tipeUnit')->findOrFail($data['id_properti']);
            $hargaProperti = $unit->tipeUnit->harga;
            $dp = (float) ($data['dp'] ?? 0);
            $jumlahPinjaman = max($hargaProperti - $dp, 0);
            $tenor = (int) ($data['tenor'] ?? 20);
            $estimasi = DebiturPengajuanHelper::hitungAngsuran($jumlahPinjaman, $tenor);
            $totalPenghasilan = DebiturPengajuanHelper::totalPenghasilan($data);
            $rasio = DebiturPengajuanHelper::hitungRasio($estimasi, (float) ($data['total_cicilan'] ?? 0), $totalPenghasilan);

            Log::info('[DebiturPengajuanService] Recalculated financials for update', [
                'harga_properti' => $hargaProperti,
                'dp' => $dp,
                'jumlah_pinjaman' => $jumlahPinjaman,
                'estimasi_angsuran' => $estimasi,
                'rasio_angsuran' => $rasio
            ]);

            $pengajuan->update([
                'unit_id'           => $unit->id,
                'harga_properti'    => $hargaProperti,
                'uang_muka'         => $dp,
                'persen_dp'         => $hargaProperti > 0 ? round($dp / $hargaProperti * 100, 2) : 0,
                'jumlah_pinjaman'   => $jumlahPinjaman,
                'tenor_tahun'       => $tenor,
                'estimasi_angsuran' => $estimasi,
                'rasio_angsuran'    => $rasio,
                'tujuan_pembelian'  => DebiturPengajuanHelper::mapTujuanPembelian($data['tujuan_pembelian'] ?? ''),
                'sumber_dp'         => DebiturPengajuanHelper::mapSumberDp($data['sumber_dp'] ?? ''),
                'catatan_debitur'   => $data['catatan_debitur'] ?? null,
                'status'            => $isDraft ? 'draft' : 'submitted',
                'tgl_submitted'     => $isDraft ? $pengajuan->tgl_submitted : now(),
            ]);

            Log::info('[DebiturPengajuanService] Pengajuan updated', [
                'pengajuan_id' => $pengajuan->id,
                'new_status' => $isDraft ? 'draft' : 'submitted'
            ]);

            if (!$isDraft) {
                Log::info('[DebiturPengajuanService] Uploading documents for updated pengajuan');
                $this->uploadDokumen($pengajuan, $files);
                $unit->update(['status' => 'dipesan']);
                Log::info('[DebiturPengajuanService] Unit status updated to dipesan', ['unit_id' => $unit->id]);
            }

            Log::info('[DebiturPengajuanService] updatePengajuan completed successfully');

            return $pengajuan->fresh();
        });
    }

    /**
     * Hapus pengajuan draft beserta dokumennya dari storage.
     */
    public function deletePengajuan(Pengajuan $pengajuan): void
    {
        Log::info('[DebiturPengajuanService] deletePengajuan started', [
            'pengajuan_id' => $pengajuan->id,
            'status' => $pengajuan->status
        ]);

        DB::transaction(function () use ($pengajuan) {
            $deletedDocsCount = 0;
            
            foreach ($pengajuan->dokumen as $dok) {
                Log::info('[DebiturPengajuanService] Deleting file from storage', [
                    'dokumen_id' => $dok->id,
                    'path' => $dok->path_file
                ]);
                
                Storage::disk('private')->delete($dok->path_file);
                $deletedDocsCount++;
            }
            
            $pengajuan->dokumen()->delete();
            Log::info('[DebiturPengajuanService] Deleted documents', ['count' => $deletedDocsCount]);
            
            $pengajuan->delete();
            Log::info('[DebiturPengajuanService] Pengajuan deleted successfully');
        });
    }

    // -----------------------------------------------------------------------
    // Private: upsert tabel profil debitur
    // -----------------------------------------------------------------------

    private function upsertDebiturPribadi(int $userId, array $data): void
    {
        Log::info('[DebiturPengajuanService] upsertDebiturPribadi', ['user_id' => $userId]);
        
        try {
            DebiturPribadi::updateOrCreate(
                ['user_id' => $userId],
                [
                    'nik'                    => $data['nik'],
                    'tempat_lahir'           => $data['tempat_lahir'],
                    'tanggal_lahir'          => $data['tanggal_lahir'],
                    'jenis_kelamin'          => $data['jenis_kelamin'] === 'Laki-laki' ? 'L' : 'P',
                    'agama'                  => $data['agama'] ?? null,
                    'status_pernikahan'      => DebiturPengajuanHelper::mapStatusPernikahan($data['status_pernikahan'] ?? ''),
                    'jumlah_tanggungan'      => (int) ($data['jumlah_tanggungan'] ?? 0),
                    'pendidikan_terakhir'    => $data['pendidikan'] ?? null,
                    'kewarganegaraan'        => $data['kewarganegaraan'] ?? 'WNI',
                    'nama_ibu_kandung'       => $data['nama_ibu'],
                    'no_kk'                  => $data['no_kk'] ?? null,
                    'nama_pasangan'          => $data['nama_pasangan'] ?? null,
                    'nik_pasangan'           => $data['nik_pasangan'] ?? null,
                    'alamat_ktp'             => $data['alamat_ktp'],
                    'kota'                   => $data['kota'],
                    'provinsi'               => $data['provinsi'],
                    'kode_pos'               => $data['kode_pos'] ?? null,
                    'status_tempat_tinggal'  => DebiturPengajuanHelper::mapStatusTempatTinggal($data['status_tempat_tinggal'] ?? ''),
                    'no_hp'                  => $data['no_hp'],
                    'no_telepon'             => $data['no_hp'],
                    'email_aktif'            => $data['email'],
                ]
            );
            
            Log::info('[DebiturPengajuanService] upsertDebiturPribadi success', ['user_id' => $userId]);
        } catch (\Exception $e) {
            Log::error('[DebiturPengajuanService] upsertDebiturPribadi failed', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    private function upsertDebiturPekerjaan(int $userId, array $data): void
    {
        Log::info('[DebiturPengajuanService] upsertDebiturPekerjaan', ['user_id' => $userId]);
        
        try {
            [$tahun, $bulan] = DebiturPengajuanHelper::parseLamaBekerja($data['lama_bekerja'] ?? '');
            
            Log::info('[DebiturPengajuanService] Parsed lama bekerja', [
                'user_id' => $userId,
                'tahun' => $tahun,
                'bulan' => $bulan
            ]);

            DebiturPekerjaan::updateOrCreate(
                ['user_id' => $userId],
                [
                    'status_pekerjaan'   => DebiturPengajuanHelper::mapStatusPekerjaan($data['status_pekerjaan'] ?? ''),
                    'nama_perusahaan'    => $data['nama_perusahaan'],
                    'bidang_usaha'       => $data['bidang_usaha'] ?? null,
                    'jabatan'            => $data['jabatan'] ?? null,
                    'status_kepegawaian' => DebiturPengajuanHelper::mapStatusKepegawaian($data['status_kepegawaian'] ?? ''),
                    'lama_bekerja_tahun' => $tahun,
                    'lama_bekerja_bulan' => $bulan,
                    'alamat_perusahaan'  => $data['alamat_perusahaan'] ?? null,
                    'kota_perusahaan'    => null,
                    'telp_perusahaan'    => $data['telp_perusahaan'] ?? null,
                    'npwp'               => $data['npwp'] ?? null,
                    'penghasilan_pokok'  => (float) ($data['penghasilan_pokok'] ?? 0),
                    'tunjangan_tetap'    => (float) ($data['tunjangan'] ?? 0),
                    'penghasilan_lain'   => (float) ($data['penghasilan_lain'] ?? 0),
                ]
            );
            
            Log::info('[DebiturPengajuanService] upsertDebiturPekerjaan success', ['user_id' => $userId]);
        } catch (\Exception $e) {
            Log::error('[DebiturPengajuanService] upsertDebiturPekerjaan failed', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    private function upsertDebiturKeuangan(int $userId, array $data): void
    {
        Log::info('[DebiturPengajuanService] upsertDebiturKeuangan', ['user_id' => $userId]);
        
        try {
            DebiturKeuangan::updateOrCreate(
                ['user_id' => $userId],
                [
                    'nama_bank'               => $data['nama_bank'],
                    'nomor_rekening'          => $data['nomor_rekening'],
                    'nama_pemilik_rekening'   => $data['pemilik_rekening'],
                    'jenis_rekening'          => strtolower($data['jenis_rekening'] ?? 'tabungan'),
                    'rata_saldo_3bln'         => (float) ($data['rata_saldo'] ?? 0),
                    'rata_mutasi_kredit'      => (float) ($data['rata_mutasi'] ?? 0),
                    'total_cicilan_perbulan'  => (float) ($data['total_cicilan'] ?? 0),
                    'jumlah_kredit_aktif'     => (int) ($data['jumlah_kredit_aktif'] ?? 0),
                    'limit_kartu_kredit'      => (float) ($data['limit_kartu_kredit'] ?? 0),
                    'tagihan_kartu_kredit'    => (float) ($data['tagihan_kartu_kredit'] ?? 0),
                    'memiliki_kpr_aktif'      => ($data['memiliki_kpr_aktif'] ?? 'Tidak') === 'Ya',
                    'sisa_pokok_kpr_aktif'    => (float) ($data['sisa_pokok_kpr'] ?? 0),
                    'status_kredit'           => DebiturPengajuanHelper::mapStatusKredit($data['status_kredit'] ?? ''),
                    'pernah_gagal_bayar'      => ($data['pernah_gagal_bayar'] ?? 'Tidak pernah') !== 'Tidak pernah',
                    'aset_properti_lain'      => (float) ($data['aset_properti'] ?? 0),
                    'aset_kendaraan'          => (float) ($data['aset_kendaraan'] ?? 0),
                    'aset_tabungan_deposito'  => (float) ($data['aset_tabungan'] ?? 0),
                    'aset_investasi_lain'     => (float) ($data['aset_lainnya'] ?? 0),
                ]
            );
            
            Log::info('[DebiturPengajuanService] upsertDebiturKeuangan success', ['user_id' => $userId]);
        } catch (\Exception $e) {
            Log::error('[DebiturPengajuanService] upsertDebiturKeuangan failed', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    // -----------------------------------------------------------------------
    // Private: upload dokumen
    // -----------------------------------------------------------------------

    /**
     * Map nama input form → jenis_dokumen enum di tabel dokumen_pengajuan.
     */
    private const DOKUMEN_MAP = [
        'ktp'                    => 'ktp',
        'kk'                     => 'kk',
        'dokumen_npwp'           => 'npwp',
        'buku_nikah'             => 'buku_nikah',
        'ktp_pasangan'           => 'ktp_pasangan',
        'foto_diri'              => 'pas_foto',
        'slip_gaji'              => 'slip_gaji',
        'sk_kerja'               => 'surat_keterangan_kerja',
        'sk_pengangkatan'        => 'sk_pengangkatan',
        'spt'                    => 'spt_pph21',
        'rekening_koran'         => 'rekening_koran',
        'slik'                   => 'slik_ojk',
        'tagihan_kartu_kredit'   => 'tagihan_kartu_kredit',
        'bukti_cicilan'          => 'bukti_cicilan_aktif',
        'izin_usaha'             => 'siup_nib',
        'laporan_keuangan'       => 'laporan_keuangan_usaha',
        'rekening_usaha'         => 'rekening_koran_usaha',
        'sip'                    => 'surat_izin_praktik',
    ];

    private function uploadDokumen(Pengajuan $pengajuan, array $files): void
    {
        Log::info('[DebiturPengajuanService] uploadDokumen started', [
            'pengajuan_id' => $pengajuan->id,
            'files_count' => count($files)
        ]);

        $basePath = "dokumen/pengajuan/{$pengajuan->id}";
        Log::info('[DebiturPengajuanService] Base storage path', ['path' => $basePath]);

        $uploadedCount = 0;
        $errorCount = 0;

        foreach (self::DOKUMEN_MAP as $inputName => $jenisDokumen) {
            if (!isset($files[$inputName])) {
                Log::debug('[DebiturPengajuanService] Input not found in files', ['input_name' => $inputName]);
                continue;
            }

            $fileList = is_array($files[$inputName]) ? $files[$inputName] : [$files[$inputName]];
            Log::info('[DebiturPengajuanService] Processing files for input', [
                'input_name' => $inputName,
                'jenis_dokumen' => $jenisDokumen,
                'file_count' => count($fileList)
            ]);

            foreach ($fileList as $file) {
                if (!$file || !$file->isValid()) {
                    Log::warning('[DebiturPengajuanService] Invalid file skipped', [
                        'input_name' => $inputName,
                        'file_exists' => !is_null($file),
                        'is_valid' => $file ? $file->isValid() : false
                    ]);
                    $errorCount++;
                    continue;
                }

                try {
                    $originalName = $file->getClientOriginalName();
                    Log::info('[DebiturPengajuanService] Uploading file', [
                        'original_name' => $originalName,
                        'size' => $file->getSize(),
                        'mime' => $file->getMimeType()
                    ]);

                    $stored = $file->store($basePath, 'private');
                    
                    DokumenPengajuan::create([
                        'pengajuan_id'       => $pengajuan->id,
                        'jenis_dokumen'      => $jenisDokumen,
                        'nama_file'          => $originalName,
                        'path_file'          => $stored,
                        'ukuran_file'        => $file->getSize(),
                        'mime_type'          => $file->getMimeType(),
                        'status_verifikasi'  => 'belum_diperiksa',
                    ]);
                    
                    $uploadedCount++;
                    Log::info('[DebiturPengajuanService] File uploaded successfully', [
                        'stored_path' => $stored,
                        'jenis_dokumen' => $jenisDokumen
                    ]);
                    
                } catch (\Exception $e) {
                    $errorCount++;
                    Log::error('[DebiturPengajuanService] Failed to upload file', [
                        'input_name' => $inputName,
                        'jenis_dokumen' => $jenisDokumen,
                        'original_name' => $originalName ?? 'unknown',
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }

        Log::info('[DebiturPengajuanService] uploadDokumen completed', [
            'pengajuan_id' => $pengajuan->id,
            'uploaded_count' => $uploadedCount,
            'error_count' => $errorCount
        ]);
    }
}
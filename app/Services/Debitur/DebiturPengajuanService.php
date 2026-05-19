<?php

namespace App\Services\Debitur;

use Illuminate\Support\Facades\{DB, Log, Storage};
use Illuminate\Support\Collection;
use Illuminate\Http\UploadedFile;

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
            $basePath = "dokumen/pengajuan/{$pengajuan->id}";
            
            // Hapus seluruh folder dokumen
            if (Storage::disk('private')->exists($basePath)) {
                Storage::disk('private')->deleteDirectory($basePath);
                Log::info('[DebiturPengajuanService] Deleted document directory', ['path' => $basePath]);
            }
            
            // Hapus record dari database
            $pengajuan->dokumen()->delete();
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
    // Private: upload dokumen dengan struktur folder dan penamaan terorganisir
    // -----------------------------------------------------------------------

    /**
     * Konfigurasi dokumen lengkap dengan folder dan label
     */
    private const DOKUMEN_CONFIG = [
        'ktp' => [
            'jenis' => 'ktp',
            'folder' => 'KTP',
            'label' => 'KTP'
        ],
        'kk' => [
            'jenis' => 'kk',
            'folder' => 'KK',
            'label' => 'KK'
        ],
        'dokumen_npwp' => [
            'jenis' => 'npwp',
            'folder' => 'NPWP',
            'label' => 'NPWP'
        ],
        'buku_nikah' => [
            'jenis' => 'buku_nikah',
            'folder' => 'BUKU_NIKAH',
            'label' => 'BUKU_NIKAH'
        ],
        'ktp_pasangan' => [
            'jenis' => 'ktp_pasangan',
            'folder' => 'KTP_PASANGAN',
            'label' => 'KTP_PASANGAN'
        ],
        'foto_diri' => [
            'jenis' => 'pas_foto',
            'folder' => 'FOTO_DIRI',
            'label' => 'FOTO'
        ],
        'slip_gaji' => [
            'jenis' => 'slip_gaji',
            'folder' => 'SLIP_GAJI',
            'label' => 'SLIP_GAJI'
        ],
        'sk_kerja' => [
            'jenis' => 'surat_keterangan_kerja',
            'folder' => 'SK_KERJA',
            'label' => 'SK_KERJA'
        ],
        'sk_pengangkatan' => [
            'jenis' => 'sk_pengangkatan',
            'folder' => 'SK_PENGANGKATAN',
            'label' => 'SK_PENGANGKATAN'
        ],
        'spt' => [
            'jenis' => 'spt_pph21',
            'folder' => 'SPT',
            'label' => 'SPT'
        ],
        'rekening_koran' => [
            'jenis' => 'rekening_koran',
            'folder' => 'REKENING_KORAN',
            'label' => 'REKENING_KORAN'
        ],
        'slik' => [
            'jenis' => 'slik_ojk',
            'folder' => 'SLIK',
            'label' => 'SLIK'
        ],
        'tagihan_kartu_kredit' => [
            'jenis' => 'tagihan_kartu_kredit',
            'folder' => 'TAGIHAN_KK',
            'label' => 'TAGIHAN_KK'
        ],
        'bukti_cicilan' => [
            'jenis' => 'bukti_cicilan_aktif',
            'folder' => 'BUKTI_CICILAN',
            'label' => 'BUKTI_CICILAN'
        ],
        'izin_usaha' => [
            'jenis' => 'siup_nib',
            'folder' => 'IZIN_USAHA',
            'label' => 'IZIN_USAHA'
        ],
        'laporan_keuangan' => [
            'jenis' => 'laporan_keuangan_usaha',
            'folder' => 'LAPORAN_KEUANGAN',
            'label' => 'LAPORAN_KEUANGAN'
        ],
        'rekening_usaha' => [
            'jenis' => 'rekening_koran_usaha',
            'folder' => 'REKENING_USAHA',
            'label' => 'REKENING_USAHA'
        ],
        'sip' => [
            'jenis' => 'surat_izin_praktik',
            'folder' => 'SIP',
            'label' => 'SIP'
        ],
    ];

    
    /**
     * Ambil nama lengkap debitur dari tabel users
     */
    private function getDebiturFullName(int $userId): string
    {
        // Ambil dari tabel users
        $user = User::find($userId);
        
        if ($user && !empty($user->nama_lengkap)) {
            return $user->nama_lengkap;
        }
        
        // Fallback ke name jika ada
        if ($user && !empty($user->name)) {
            return $user->name;
        }
        
        // Fallback terakhir
        return 'Debitur_' . $userId;
    }

    /**
     * Generate nama file yang terstruktur
     * Format: JENIS_KODE_PENGAJUAN_NAMA_LENGKAP[_SEQUENCE].ext
     * Contoh: KTP_KPR-20260515-9131F_Budi_Santoso_Debug.pdf
     */
    private function generateFileName(
        string $jenisLabel,
        string $kodePengajuan,
        string $namaLengkap,
        UploadedFile $file,
        int $sequence = 0
    ): string {
        // Bersihkan nama lengkap untuk nama file (hilangkan spasi, ganti underscore)
        $cleanName = preg_replace('/[^a-zA-Z0-9]/', '_', $namaLengkap);
        $cleanName = trim(preg_replace('/_+/', '_', $cleanName), '_');
        
        // Ambil ekstensi file asli
        $extension = $file->getClientOriginalExtension();
        
        // Jika ekstensi kosong, ambil dari MIME type
        if (empty($extension)) {
            $mimeMap = [
                'application/pdf' => 'pdf',
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/jpg' => 'jpg',
                'image/gif' => 'gif',
            ];
            $extension = $mimeMap[$file->getMimeType()] ?? 'bin';
        }
        
        // Format nama file: JENIS_KODE_PENGAJUAN_NAMA_LENGKAP[_SEQUENCE].ext
        $baseName = "{$jenisLabel}_{$kodePengajuan}_{$cleanName}";
        
        if ($sequence > 0) {
            $baseName .= "_{$sequence}";
        }
        
        return $baseName . '.' . $extension;
    }

    /**
     * Upload dokumen dengan struktur folder dan penamaan yang teratur
     * Struktur: dokumen/pengajuan/{id}/{FOLDER}/{FILE}
     * Contoh: dokumen/pengajuan/9/KTP/KTP_KPR-20260515-9131F_Budi_Santoso.pdf
     */
    private function uploadDokumen(Pengajuan $pengajuan, array $files): void
    {
        Log::info('[DebiturPengajuanService] uploadDokumen started', [
            'pengajuan_id' => $pengajuan->id,
            'files_count' => count($files)
        ]);

        // Ambil nama lengkap debitur
        $namaLengkap = $this->getDebiturFullName($pengajuan->user_id);
        $basePath = "dokumen/pengajuan/{$pengajuan->id}";
        $kodePengajuan = $pengajuan->kode_pengajuan;
        
        // Cek dan buat private disk jika perlu
        if (!Storage::disk('private')->exists('/')) {
            Storage::disk('private')->makeDirectory('/');
            Log::info('[DebiturPengajuanService] Created private disk directory');
        }
        
        // Buat folder utama pengajuan
        if (!Storage::disk('private')->exists($basePath)) {
            Storage::disk('private')->makeDirectory($basePath);
            Log::info('[DebiturPengajuanService] Created main directory', ['path' => $basePath]);
        }

        $uploadedCount = 0;
        $errorCount = 0;

        foreach (self::DOKUMEN_CONFIG as $inputName => $config) {
            if (!isset($files[$inputName])) {
                Log::debug('[DebiturPengajuanService] Input not found', ['input' => $inputName]);
                continue;
            }

            // Normalisasi ke array (support multiple files)
            $fileList = is_array($files[$inputName]) ? $files[$inputName] : [$files[$inputName]];
            $fileList = array_filter($fileList, function($file) {
                return $file instanceof UploadedFile && $file->isValid();
            });
            
            if (empty($fileList)) {
                Log::warning('[DebiturPengajuanService] No valid file objects', ['input' => $inputName]);
                continue;
            }

            $jenisDokumen = $config['jenis'];
            $folderName = $config['folder'];
            $jenisLabel = $config['label'];
            
            // Buat folder spesifik untuk jenis dokumen ini
            $dokumenFolder = $basePath . '/' . $folderName;
            if (!Storage::disk('private')->exists($dokumenFolder)) {
                Storage::disk('private')->makeDirectory($dokumenFolder);
                Log::info('[DebiturPengajuanService] Created sub-directory', ['path' => $dokumenFolder]);
            }
            
            Log::info('[DebiturPengajuanService] Processing files', [
                'input' => $inputName,
                'jenis' => $jenisDokumen,
                'folder' => $folderName,
                'count' => count($fileList)
            ]);

            // Proses setiap file (support multiple files per jenis)
            $isMultiple = count($fileList) > 1;
            
            foreach ($fileList as $index => $file) {
                try {
                    // Validasi ukuran file (max 5MB)
                    if ($file->getSize() > 5 * 1024 * 1024) {
                        Log::warning('[DebiturPengajuanService] File too large', [
                            'input' => $inputName,
                            'size' => $file->getSize(),
                            'max' => '5MB'
                        ]);
                        $errorCount++;
                        continue;
                    }
                    
                    // Generate sequence number untuk multiple files
                    $sequence = $isMultiple ? $index + 1 : 0;
                    
                    // Generate nama file yang terstruktur
                    $fileName = $this->generateFileName(
                        $jenisLabel,
                        $kodePengajuan,
                        $namaLengkap,
                        $file,
                        $sequence
                    );
                    
                    $fullPath = $dokumenFolder . '/' . $fileName;
                    
                    // Simpan file
                    $stored = Storage::disk('private')->putFileAs(
                        $dokumenFolder, 
                        $file, 
                        $fileName
                    );
                    
                    if (!$stored) {
                        throw new \Exception('Failed to store file');
                    }
                    
                    // Simpan ke database
                    DokumenPengajuan::create([
                        'pengajuan_id'       => $pengajuan->id,
                        'jenis_dokumen'      => $jenisDokumen,
                        'nama_file'          => $fileName,
                        'path_file'          => $fullPath,
                        'ukuran_file'        => $file->getSize(),
                        'mime_type'          => $file->getMimeType(),
                        'status_verifikasi'  => 'belum_diperiksa',
                    ]);
                    
                    $uploadedCount++;
                    Log::info('[DebiturPengajuanService] File uploaded successfully', [
                        'jenis' => $jenisDokumen,
                        'folder' => $folderName,
                        'file_name' => $fileName,
                        'size' => $file->getSize()
                    ]);
                    
                } catch (\Exception $e) {
                    $errorCount++;
                    Log::error('[DebiturPengajuanService] Failed to upload file', [
                        'input' => $inputName,
                        'jenis' => $jenisDokumen,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }

        if ($uploadedCount === 0 && $errorCount > 0) {
            throw new \Exception('Gagal mengupload dokumen. Pastikan file valid dan ukuran tidak melebihi 5MB.');
        }

        Log::info('[DebiturPengajuanService] uploadDokumen completed', [
            'pengajuan_id' => $pengajuan->id,
            'uploaded_count' => $uploadedCount,
            'error_count' => $errorCount
        ]);
    }
}
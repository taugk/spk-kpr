<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{Builder, Model};
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;

class DokumenPengajuan extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terkait dengan model
     */
    protected $table = 'dokumen_pengajuan';

    /**
     * Primary key yang tidak auto-incrementing
     */
    public $incrementing = true;

    /**
     * Tipe primary key
     */
    protected $keyType = 'int';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'pengajuan_id',
        'jenis_dokumen',
        'nama_file',
        'path_file',
        'ukuran_file',
        'mime_type',
        'status_verifikasi',
        'catatan_verifikasi',
        'diperiksa_oleh',
        'tgl_diperiksa',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'ukuran_file' => 'integer',
        'tgl_diperiksa' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Enum values untuk jenis_dokumen
     */
    const JENIS_DOKUMEN_KTP = 'ktp';
    const JENIS_DOKUMEN_KK = 'kk';
    const JENIS_DOKUMEN_NPWP = 'npwp';
    const JENIS_DOKUMEN_BUKU_NIKAH = 'buku_nikah';
    const JENIS_DOKUMEN_AKTA_CERAI = 'akta_cerai';
    const JENIS_DOKUMEN_KTP_PASANGAN = 'ktp_pasangan';
    const JENIS_DOKUMEN_PAS_FOTO = 'pas_foto';
    const JENIS_DOKUMEN_SLIP_GAJI = 'slip_gaji';
    const JENIS_DOKUMEN_SURAT_KETERANGAN_KERJA = 'surat_keterangan_kerja';
    const JENIS_DOKUMEN_SK_PENGANGKATAN = 'sk_pengangkatan';
    const JENIS_DOKUMEN_SPT_PPH21 = 'spt_pph21';
    const JENIS_DOKUMEN_REKENING_KORAN = 'rekening_koran';
    const JENIS_DOKUMEN_SLIK_OJK = 'slik_ojk';
    const JENIS_DOKUMEN_TAGIHAN_KARTU_KREDIT = 'tagihan_kartu_kredit';
    const JENIS_DOKUMEN_BUKTI_CICILAN_AKTIF = 'bukti_cicilan_aktif';
    const JENIS_DOKUMEN_SIUP_NIB = 'siup_nib';
    const JENIS_DOKUMEN_LAPORAN_KEUANGAN_USAHA = 'laporan_keuangan_usaha';
    const JENIS_DOKUMEN_REKENING_KORAN_USAHA = 'rekening_koran_usaha';
    const JENIS_DOKUMEN_SURAT_IZIN_PRAKTIK = 'surat_izin_praktik';
    const JENIS_DOKUMEN_LAINNYA = 'lainnya';

    /**
     * Enum values untuk status_verifikasi
     */
    const STATUS_BELUM_DIPERIKSA = 'belum_diperiksa';
    const STATUS_VALID = 'valid';
    const STATUS_TIDAK_VALID = 'tidak_valid';
    const STATUS_PERLU_REVISI = 'perlu_revisi';

    /**
     * Category groups for document types
     */
    const CATEGORY_IDENTITAS = [
        self::JENIS_DOKUMEN_KTP,
        self::JENIS_DOKUMEN_KK,
        self::JENIS_DOKUMEN_NPWP,
        self::JENIS_DOKUMEN_BUKU_NIKAH,
        self::JENIS_DOKUMEN_AKTA_CERAI,
        self::JENIS_DOKUMEN_KTP_PASANGAN,
        self::JENIS_DOKUMEN_PAS_FOTO,
    ];

    const CATEGORY_PEKERJAAN = [
        self::JENIS_DOKUMEN_SLIP_GAJI,
        self::JENIS_DOKUMEN_SURAT_KETERANGAN_KERJA,
        self::JENIS_DOKUMEN_SK_PENGANGKATAN,
        self::JENIS_DOKUMEN_SPT_PPH21,
    ];

    const CATEGORY_KEUANGAN = [
        self::JENIS_DOKUMEN_REKENING_KORAN,
        self::JENIS_DOKUMEN_SLIK_OJK,
        self::JENIS_DOKUMEN_TAGIHAN_KARTU_KREDIT,
        self::JENIS_DOKUMEN_BUKTI_CICILAN_AKTIF,
    ];

    const CATEGORY_USAHA = [
        self::JENIS_DOKUMEN_SIUP_NIB,
        self::JENIS_DOKUMEN_LAPORAN_KEUANGAN_USAHA,
        self::JENIS_DOKUMEN_REKENING_KORAN_USAHA,
        self::JENIS_DOKUMEN_SURAT_IZIN_PRAKTIK,
    ];

    /**
     * Relationships
     */
    public function pengajuan()
    {
        return $this->belongsTo(Pengajuan::class, 'pengajuan_id', 'id');
    }

    public function pemeriksa()
    {
        return $this->belongsTo(User::class, 'diperiksa_oleh', 'id');
    }

    /**
     * Get all jenis dokumen options
     */
    public static function getJenisDokumenOptions(): array
    {
        return [
            self::JENIS_DOKUMEN_KTP,
            self::JENIS_DOKUMEN_KK,
            self::JENIS_DOKUMEN_NPWP,
            self::JENIS_DOKUMEN_BUKU_NIKAH,
            self::JENIS_DOKUMEN_AKTA_CERAI,
            self::JENIS_DOKUMEN_KTP_PASANGAN,
            self::JENIS_DOKUMEN_PAS_FOTO,
            self::JENIS_DOKUMEN_SLIP_GAJI,
            self::JENIS_DOKUMEN_SURAT_KETERANGAN_KERJA,
            self::JENIS_DOKUMEN_SK_PENGANGKATAN,
            self::JENIS_DOKUMEN_SPT_PPH21,
            self::JENIS_DOKUMEN_REKENING_KORAN,
            self::JENIS_DOKUMEN_SLIK_OJK,
            self::JENIS_DOKUMEN_TAGIHAN_KARTU_KREDIT,
            self::JENIS_DOKUMEN_BUKTI_CICILAN_AKTIF,
            self::JENIS_DOKUMEN_SIUP_NIB,
            self::JENIS_DOKUMEN_LAPORAN_KEUANGAN_USAHA,
            self::JENIS_DOKUMEN_REKENING_KORAN_USAHA,
            self::JENIS_DOKUMEN_SURAT_IZIN_PRAKTIK,
            self::JENIS_DOKUMEN_LAINNYA,
        ];
    }

    /**
     * Get grouped document options by category
     */
    public static function getJenisDokumenGrouped(): array
    {
        return [
            'Dokumen Identitas' => self::CATEGORY_IDENTITAS,
            'Dokumen Pekerjaan' => self::CATEGORY_PEKERJAAN,
            'Dokumen Keuangan' => self::CATEGORY_KEUANGAN,
            'Dokumen Usaha' => self::CATEGORY_USAHA,
            'Lainnya' => [self::JENIS_DOKUMEN_LAINNYA],
        ];
    }

    /**
     * Get label for jenis dokumen
     */
    public static function getJenisDokumenLabel(string $jenis): string
    {
        $labels = [
            self::JENIS_DOKUMEN_KTP => 'KTP Debitur',
            self::JENIS_DOKUMEN_KK => 'Kartu Keluarga',
            self::JENIS_DOKUMEN_NPWP => 'NPWP',
            self::JENIS_DOKUMEN_BUKU_NIKAH => 'Buku Nikah',
            self::JENIS_DOKUMEN_AKTA_CERAI => 'Akta Cerai',
            self::JENIS_DOKUMEN_KTP_PASANGAN => 'KTP Pasangan',
            self::JENIS_DOKUMEN_PAS_FOTO => 'Pas Foto',
            self::JENIS_DOKUMEN_SLIP_GAJI => 'Slip Gaji',
            self::JENIS_DOKUMEN_SURAT_KETERANGAN_KERJA => 'Surat Keterangan Kerja',
            self::JENIS_DOKUMEN_SK_PENGANGKATAN => 'SK Pengangkatan',
            self::JENIS_DOKUMEN_SPT_PPH21 => 'SPT PPH21',
            self::JENIS_DOKUMEN_REKENING_KORAN => 'Rekening Koran',
            self::JENIS_DOKUMEN_SLIK_OJK => 'SLIK OJK',
            self::JENIS_DOKUMEN_TAGIHAN_KARTU_KREDIT => 'Tagihan Kartu Kredit',
            self::JENIS_DOKUMEN_BUKTI_CICILAN_AKTIF => 'Bukti Cicilan Aktif',
            self::JENIS_DOKUMEN_SIUP_NIB => 'SIUP / NIB',
            self::JENIS_DOKUMEN_LAPORAN_KEUANGAN_USAHA => 'Laporan Keuangan Usaha',
            self::JENIS_DOKUMEN_REKENING_KORAN_USAHA => 'Rekening Koran Usaha',
            self::JENIS_DOKUMEN_SURAT_IZIN_PRAKTIK => 'Surat Izin Praktik',
            self::JENIS_DOKUMEN_LAINNYA => 'Dokumen Lainnya',
        ];
        
        return $labels[$jenis] ?? ucfirst(str_replace('_', ' ', $jenis));
    }

    /**
     * Get all status verifikasi options
     */
    public static function getStatusVerifikasiOptions(): array
    {
        return [
            self::STATUS_BELUM_DIPERIKSA,
            self::STATUS_VALID,
            self::STATUS_TIDAK_VALID,
            self::STATUS_PERLU_REVISI,
        ];
    }

    /**
     * Get status badge HTML
     */
    protected function statusBadge(): Attribute
    {
        return Attribute::make(
            get: function () {
                $badges = [
                    self::STATUS_BELUM_DIPERIKSA => '<span class="badge badge-secondary">Belum Diperiksa</span>',
                    self::STATUS_VALID => '<span class="badge badge-success">Valid</span>',
                    self::STATUS_TIDAK_VALID => '<span class="badge badge-danger">Tidak Valid</span>',
                    self::STATUS_PERLU_REVISI => '<span class="badge badge-warning">Perlu Revisi</span>',
                ];
                
                return $badges[$this->status_verifikasi] ?? '<span class="badge badge-secondary">' . e($this->status_verifikasi) . '</span>';
            }
        );
    }

    /**
     * Get status text
     */
    protected function statusText(): Attribute
    {
        return Attribute::make(
            get: function () {
                $texts = [
                    self::STATUS_BELUM_DIPERIKSA => 'Belum Diperiksa',
                    self::STATUS_VALID => 'Valid',
                    self::STATUS_TIDAK_VALID => 'Tidak Valid',
                    self::STATUS_PERLU_REVISI => 'Perlu Revisi',
                ];
                
                return $texts[$this->status_verifikasi] ?? $this->status_verifikasi;
            }
        );
    }

    /**
     * Get formatted file size
     */
    protected function formattedFileSize(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->ukuran_file) {
                    return '-';
                }
                
                $bytes = $this->ukuran_file;
                $units = ['B', 'KB', 'MB', 'GB'];
                $i = 0;
                
                while ($bytes >= 1024 && $i < count($units) - 1) {
                    $bytes /= 1024;
                    $i++;
                }
                
                return round($bytes, 2) . ' ' . $units[$i];
            }
        );
    }

    /**
     * Get file URL
     */
    protected function fileUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->path_file) {
                    return null;
                }
                
                if (Storage::disk('public')->exists($this->path_file)) {
                    return Storage::disk('public')->url($this->path_file);
                }
                
                return asset($this->path_file);
            }
        );
    }

    /**
     * Get file extension
     */
    protected function fileExtension(): Attribute
    {
        return Attribute::make(
            get: function () {
                return strtolower(pathinfo($this->nama_file, PATHINFO_EXTENSION));
            }
        );
    }

    /**
     * Check if file is image
     */
    protected function isImage(): Attribute
    {
        return Attribute::make(
            get: function () {
                $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'];
                return in_array($this->file_extension, $imageExtensions);
            }
        );
    }

    /**
     * Check if file is PDF
     */
    protected function isPdf(): Attribute
    {
        return Attribute::make(
            get: function () {
                return $this->file_extension === 'pdf';
            }
        );
    }

    /**
     * Business Logic Methods
     */
    public function isValid(): bool
    {
        return $this->status_verifikasi === self::STATUS_VALID;
    }

    public function isInvalid(): bool
    {
        return $this->status_verifikasi === self::STATUS_TIDAK_VALID;
    }

    public function needsRevision(): bool
    {
        return $this->status_verifikasi === self::STATUS_PERLU_REVISI;
    }

    public function isUnchecked(): bool
    {
        return $this->status_verifikasi === self::STATUS_BELUM_DIPERIKSA;
    }

    /**
     * Verify document as valid
     */
    public function verifyAsValid(int $pemeriksaId, ?string $catatan = null): bool
    {
        return $this->update([
            'status_verifikasi' => self::STATUS_VALID,
            'diperiksa_oleh' => $pemeriksaId,
            'tgl_diperiksa' => now(),
            'catatan_verifikasi' => $catatan,
        ]);
    }

    /**
     * Verify document as invalid
     */
    public function verifyAsInvalid(int $pemeriksaId, string $catatan): bool
    {
        return $this->update([
            'status_verifikasi' => self::STATUS_TIDAK_VALID,
            'diperiksa_oleh' => $pemeriksaId,
            'tgl_diperiksa' => now(),
            'catatan_verifikasi' => $catatan,
        ]);
    }

    /**
     * Request revision for document
     */
    public function requestRevision(int $pemeriksaId, string $catatan): bool
    {
        return $this->update([
            'status_verifikasi' => self::STATUS_PERLU_REVISI,
            'diperiksa_oleh' => $pemeriksaId,
            'tgl_diperiksa' => now(),
            'catatan_verifikasi' => $catatan,
        ]);
    }

    /**
     * Reset verification status
     */
    public function resetVerification(): bool
    {
        return $this->update([
            'status_verifikasi' => self::STATUS_BELUM_DIPERIKSA,
            'diperiksa_oleh' => null,
            'tgl_diperiksa' => null,
            'catatan_verifikasi' => null,
        ]);
    }

    /**
     * Delete file from storage
     */
    public function deleteFile(): bool
    {
        if ($this->path_file && Storage::disk('public')->exists($this->path_file)) {
            Storage::disk('public')->delete($this->path_file);
        }
        
        return true;
    }

    /**
     * Update file with new version
     */
    public function updateFile(string $newPath, string $newName, int $newSize, string $newMime): bool
    {
        // Delete old file
        $this->deleteFile();
        
        // Update with new file info
        return $this->update([
            'nama_file' => $newName,
            'path_file' => $newPath,
            'ukuran_file' => $newSize,
            'mime_type' => $newMime,
            'status_verifikasi' => self::STATUS_BELUM_DIPERIKSA,
            'diperiksa_oleh' => null,
            'tgl_diperiksa' => null,
            'catatan_verifikasi' => null,
        ]);
    }

    /**
     * Get document category
     */
    public function getCategory(): string
    {
        if (in_array($this->jenis_dokumen, self::CATEGORY_IDENTITAS)) {
            return 'identitas';
        }
        
        if (in_array($this->jenis_dokumen, self::CATEGORY_PEKERJAAN)) {
            return 'pekerjaan';
        }
        
        if (in_array($this->jenis_dokumen, self::CATEGORY_KEUANGAN)) {
            return 'keuangan';
        }
        
        if (in_array($this->jenis_dokumen, self::CATEGORY_USAHA)) {
            return 'usaha';
        }
        
        return 'lainnya';
    }

    /**
     * Get category label
     */
    public function getCategoryLabel(): string
    {
        $labels = [
            'identitas' => 'Dokumen Identitas',
            'pekerjaan' => 'Dokumen Pekerjaan',
            'keuangan' => 'Dokumen Keuangan',
            'usaha' => 'Dokumen Usaha',
            'lainnya' => 'Dokumen Lainnya',
        ];
        
        return $labels[$this->getCategory()] ?? 'Lainnya';
    }

    /**
     * Scopes
     */
    public function scopeByPengajuan(Builder $query, int $pengajuanId): Builder
    {
        return $query->where('pengajuan_id', $pengajuanId);
    }

    public function scopeByJenisDokumen(Builder $query, string $jenis): Builder
    {
        return $query->where('jenis_dokumen', $jenis);
    }

    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status_verifikasi', $status);
    }

    public function scopeValid(Builder $query): Builder
    {
        return $query->where('status_verifikasi', self::STATUS_VALID);
    }

    public function scopeInvalid(Builder $query): Builder
    {
        return $query->where('status_verifikasi', self::STATUS_TIDAK_VALID);
    }

    public function scopeNeedRevision(Builder $query): Builder
    {
        return $query->where('status_verifikasi', self::STATUS_PERLU_REVISI);
    }

    public function scopeUnchecked(Builder $query): Builder
    {
        return $query->where('status_verifikasi', self::STATUS_BELUM_DIPERIKSA);
    }

    public function scopeByCategory(Builder $query, string $category): Builder
    {
        switch ($category) {
            case 'identitas':
                return $query->whereIn('jenis_dokumen', self::CATEGORY_IDENTITAS);
            case 'pekerjaan':
                return $query->whereIn('jenis_dokumen', self::CATEGORY_PEKERJAAN);
            case 'keuangan':
                return $query->whereIn('jenis_dokumen', self::CATEGORY_KEUANGAN);
            case 'usaha':
                return $query->whereIn('jenis_dokumen', self::CATEGORY_USAHA);
            default:
                return $query->where('jenis_dokumen', self::JENIS_DOKUMEN_LAINNYA);
        }
    }

    public function scopeByPemeriksa(Builder $query, int $pemeriksaId): Builder
    {
        return $query->where('diperiksa_oleh', $pemeriksaId);
    }

    public function scopeDiperiksa(Builder $query): Builder
    {
        return $query->whereNotNull('tgl_diperiksa');
    }

    public function scopeBelumDiperiksa(Builder $query): Builder
    {
        return $query->whereNull('tgl_diperiksa')
            ->where('status_verifikasi', self::STATUS_BELUM_DIPERIKSA);
    }

    /**
     * Get required documents for pengajuan
     */
    public static function getRequiredDocumentsForPengajuan(Pengajuan $pengajuan): array
    {
        $required = [
            self::JENIS_DOKUMEN_KTP,
            self::JENIS_DOKUMEN_KK,
            self::JENIS_DOKUMEN_PAS_FOTO,
        ];
        
        // Add NPWP if available
        $debiturPekerjaan = DebiturPekerjaan::where('user_id', $pengajuan->user_id)->first();
        if ($debiturPekerjaan && $debiturPekerjaan->hasNPWP()) {
            $required[] = self::JENIS_DOKUMEN_NPWP;
        }
        
        // Add marriage documents if married
        $debiturPribadi = DebiturPribadi::where('user_id', $pengajuan->user_id)->first();
        if ($debiturPribadi && $debiturPribadi->isMarried()) {
            $required[] = self::JENIS_DOKUMEN_BUKU_NIKAH;
            $required[] = self::JENIS_DOKUMEN_KTP_PASANGAN;
        }
        
        // Add employment documents
        if ($debiturPekerjaan) {
            $required[] = self::JENIS_DOKUMEN_SLIP_GAJI;
            $required[] = self::JENIS_DOKUMEN_SURAT_KETERANGAN_KERJA;
            
            if ($debiturPekerjaan->isPermanent()) {
                $required[] = self::JENIS_DOKUMEN_SK_PENGANGKATAN;
            }
        }
        
        // Add financial documents
        $required[] = self::JENIS_DOKUMEN_REKENING_KORAN;
        $required[] = self::JENIS_DOKUMEN_SLIK_OJK;
        
        return $required;
    }

    /**
     * Check if all required documents are uploaded and valid
     */
    public static function isCompleteAndValid(int $pengajuanId): bool
    {
        $pengajuan = Pengajuan::find($pengajuanId);
        if (!$pengajuan) {
            return false;
        }
        
        $required = self::getRequiredDocumentsForPengajuan($pengajuan);
        
        foreach ($required as $jenis) {
            $doc = self::where('pengajuan_id', $pengajuanId)
                ->where('jenis_dokumen', $jenis)
                ->first();
            
            if (!$doc || !$doc->isValid()) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();
        
        // Delete file from storage when model is deleted
        static::deleting(function ($dokumen) {
            $dokumen->deleteFile();
        });
    }
}
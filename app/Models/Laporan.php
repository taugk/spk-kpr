<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{Builder, Model};
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;

class Laporan extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terkait dengan model
     */
    protected $table = 'laporan';

    /**
     * Primary key yang tidak auto-incrementing
     */
    public $incrementing = true;

    /**
     * Tipe primary key
     */
    protected $keyType = 'int';

    /**
     * Tidak menggunakan timestamps (menggunakan tgl_cetak)
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'pengajuan_id',
        'penilaian_id',
        'jenis_laporan',
        'nomor_laporan',
        'dibuat_oleh',
        'path_file',
        'tgl_cetak',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'tgl_cetak' => 'datetime',
    ];

    /**
     * Enum values untuk jenis_laporan
     */
    const JENIS_HASIL_PENILAIAN = 'hasil_penilaian';
    const JENIS_SURAT_PERSETUJUAN = 'surat_persetujuan';
    const JENIS_SURAT_PENOLAKAN = 'surat_penolakan';
    const JENIS_REKAP_BULANAN = 'rekap_bulanan';

    /**
     * Relationships
     */
    public function pengajuan()
    {
        return $this->belongsTo(Pengajuan::class, 'pengajuan_id', 'id');
    }

    public function penilaian()
    {
        return $this->belongsTo(Penilaian::class, 'penilaian_id', 'id');
    }

    public function pembuat()
    {
        return $this->belongsTo(User::class, 'dibuat_oleh', 'id');
    }

    /**
     * Get all jenis laporan options
     */
    public static function getJenisLaporanOptions(): array
    {
        return [
            self::JENIS_HASIL_PENILAIAN,
            self::JENIS_SURAT_PERSETUJUAN,
            self::JENIS_SURAT_PENOLAKAN,
            self::JENIS_REKAP_BULANAN,
        ];
    }

    /**
     * Accessors
     */
    protected function jenisLaporanBadge(): Attribute
    {
        return Attribute::make(
            get: function () {
                $badges = [
                    self::JENIS_HASIL_PENILAIAN => '<span class="badge badge-primary">Hasil Penilaian</span>',
                    self::JENIS_SURAT_PERSETUJUAN => '<span class="badge badge-success">Surat Persetujuan</span>',
                    self::JENIS_SURAT_PENOLAKAN => '<span class="badge badge-danger">Surat Penolakan</span>',
                    self::JENIS_REKAP_BULANAN => '<span class="badge badge-info">Rekap Bulanan</span>',
                ];
                
                return $badges[$this->jenis_laporan] ?? '<span class="badge badge-secondary">' . e($this->jenis_laporan) . '</span>';
            }
        );
    }

    protected function jenisLaporanText(): Attribute
    {
        return Attribute::make(
            get: function () {
                $texts = [
                    self::JENIS_HASIL_PENILAIAN => 'Hasil Penilaian',
                    self::JENIS_SURAT_PERSETUJUAN => 'Surat Persetujuan',
                    self::JENIS_SURAT_PENOLAKAN => 'Surat Penolakan',
                    self::JENIS_REKAP_BULANAN => 'Rekap Bulanan',
                ];
                
                return $texts[$this->jenis_laporan] ?? $this->jenis_laporan;
            }
        );
    }

    protected function formattedTglCetak(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->tgl_cetak ? $this->tgl_cetak->format('d/m/Y H:i:s') : '-'
        );
    }

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
     * Business Logic Methods
     */
    
    /**
     * Generate nomor laporan
     */
    public static function generateNomorLaporan(string $jenisLaporan): string
    {
        $prefix = [
            self::JENIS_HASIL_PENILAIAN => 'HP',
            self::JENIS_SURAT_PERSETUJUAN => 'SP',
            self::JENIS_SURAT_PENOLAKAN => 'SJ',
            self::JENIS_REKAP_BULANAN => 'RB',
        ];
        
        $code = $prefix[$jenisLaporan] ?? 'LAP';
        $year = date('Y');
        $month = date('m');
        
        $lastLaporan = self::where('jenis_laporan', $jenisLaporan)
            ->whereYear('tgl_cetak', $year)
            ->whereMonth('tgl_cetak', $month)
            ->orderBy('id', 'desc')
            ->first();
        
        if ($lastLaporan) {
            $lastNumber = (int) substr($lastLaporan->nomor_laporan, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        
        return "{$code}-{$year}{$month}-{$newNumber}";
    }

    /**
     * Create laporan from penilaian
     */
    public static function createFromPenilaian(Penilaian $penilaian, int $userId): ?self
    {
        $jenisLaporan = $penilaian->isLayak() 
            ? self::JENIS_SURAT_PERSETUJUAN 
            : self::JENIS_SURAT_PENOLAKAN;
        
        return self::create([
            'pengajuan_id' => $penilaian->pengajuan_id,
            'penilaian_id' => $penilaian->id,
            'jenis_laporan' => $jenisLaporan,
            'nomor_laporan' => self::generateNomorLaporan($jenisLaporan),
            'dibuat_oleh' => $userId,
            'tgl_cetak' => now(),
        ]);
    }

    /**
     * Create hasil penilaian laporan
     */
    public static function createHasilPenilaian(int $pengajuanId, int $penilaianId, int $userId): self
    {
        return self::create([
            'pengajuan_id' => $pengajuanId,
            'penilaian_id' => $penilaianId,
            'jenis_laporan' => self::JENIS_HASIL_PENILAIAN,
            'nomor_laporan' => self::generateNomorLaporan(self::JENIS_HASIL_PENILAIAN),
            'dibuat_oleh' => $userId,
            'tgl_cetak' => now(),
        ]);
    }

    /**
     * Create rekap bulanan laporan
     */
    public static function createRekapBulanan(int $userId, int $month, int $year): self
    {
        return self::create([
            'pengajuan_id' => 0,
            'jenis_laporan' => self::JENIS_REKAP_BULANAN,
            'nomor_laporan' => self::generateNomorLaporan(self::JENIS_REKAP_BULANAN),
            'dibuat_oleh' => $userId,
            'tgl_cetak' => now(),
        ]);
    }

    /**
     * Update file path after generation
     */
    public function updateFilePath(string $path): bool
    {
        return $this->update(['path_file' => $path]);
    }

    /**
     * Download laporan
     */
    public function download()
    {
        if ($this->path_file && Storage::disk('public')->exists($this->path_file)) {
            return Storage::disk('public')->download($this->path_file);
        }
        
        return null;
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
     * Scopes
     */
    public function scopeByPengajuan(Builder $query, int $pengajuanId): Builder
    {
        return $query->where('pengajuan_id', $pengajuanId);
    }

    public function scopeByJenis(Builder $query, string $jenis): Builder
    {
        return $query->where('jenis_laporan', $jenis);
    }

    public function scopeByPembuat(Builder $query, int $userId): Builder
    {
        return $query->where('dibuat_oleh', $userId);
    }

    public function scopeHasilPenilaian(Builder $query): Builder
    {
        return $query->where('jenis_laporan', self::JENIS_HASIL_PENILAIAN);
    }

    public function scopeSuratPersetujuan(Builder $query): Builder
    {
        return $query->where('jenis_laporan', self::JENIS_SURAT_PERSETUJUAN);
    }

    public function scopeSuratPenolakan(Builder $query): Builder
    {
        return $query->where('jenis_laporan', self::JENIS_SURAT_PENOLAKAN);
    }

    public function scopeRekapBulanan(Builder $query): Builder
    {
        return $query->where('jenis_laporan', self::JENIS_REKAP_BULANAN);
    }

    public function scopeByDateRange(Builder $query, $startDate, $endDate): Builder
    {
        return $query->whereBetween('tgl_cetak', [$startDate, $endDate]);
    }

    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('tgl_cetak', today());
    }

    public function scopeThisMonth(Builder $query): Builder
    {
        return $query->whereMonth('tgl_cetak', now()->month)
            ->whereYear('tgl_cetak', now()->year);
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();
        
        // Auto-generate nomor_laporan if not set
        static::creating(function ($laporan) {
            if (empty($laporan->nomor_laporan)) {
                $laporan->nomor_laporan = self::generateNomorLaporan($laporan->jenis_laporan);
            }
        });
        
        // Delete file when laporan is deleted
        static::deleting(function ($laporan) {
            $laporan->deleteFile();
        });
    }
}
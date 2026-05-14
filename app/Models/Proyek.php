<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{Builder, Model};
use Illuminate\Database\Eloquent\Casts\Attribute;

class Proyek extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terkait dengan model
     */
    protected $table = 'proyek';

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
        'kode_proyek',
        'nama_proyek',
        'lokasi',
        'kota',
        'provinsi',
        'deskripsi',
        'foto_proyek',
        'status',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'foto_proyek' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Enum values untuk status
     */
    const STATUS_AKTIF = 'aktif';
    const STATUS_TUTUP = 'tutup';
    const STATUS_HABIS = 'habis';

    /**
     * Get all status options
     */
    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_AKTIF,
            self::STATUS_TUTUP,
            self::STATUS_HABIS,
        ];
    }

    /**
     * Accessor untuk foto_proyek (decode JSON)
     */
    protected function fotoProyek(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (is_null($value)) {
                    return [];
                }
                
                $fotos = is_array($value) ? $value : json_decode($value, true);
                
                if (empty($fotos)) {
                    return [];
                }
                
                // Convert to full URL if needed
                return array_map(function ($foto) {
                    return $foto ? asset('storage/' . $foto) : null;
                }, $fotos);
            },
            set: fn($value) => is_array($value) ? json_encode($value) : $value
        );
    }

    /**
     * Get first foto as thumbnail
     */
    public function getThumbnailAttribute(): ?string
    {
        $fotos = $this->foto_proyek;
        return !empty($fotos) ? $fotos[0] : null;
    }

    /**
     * Get formatted lokasi lengkap
     */
    public function getLokasiLengkapAttribute(): string
    {
        return "{$this->lokasi}, {$this->kota}, {$this->provinsi}";
    }

    /**
     * Check if proyek is active
     */
    public function isAktif(): bool
    {
        return $this->status === self::STATUS_AKTIF;
    }

    /**
     * Check if proyek is closed
     */
    public function isTutup(): bool
    {
        return $this->status === self::STATUS_TUTUP;
    }

    /**
     * Check if proyek is sold out
     */
    public function isHabis(): bool
    {
        return $this->status === self::STATUS_HABIS;
    }

    /**
     * Activate proyek
     */
    public function activate(): void
    {
        $this->update(['status' => self::STATUS_AKTIF]);
    }

    /**
     * Close proyek
     */
    public function close(): void
    {
        $this->update(['status' => self::STATUS_TUTUP]);
    }

    /**
     * Mark as sold out
     */
    public function markAsSoldOut(): void
    {
        $this->update(['status' => self::STATUS_HABIS]);
    }

    /**
     * Add foto to proyek
     */
    public function addFoto(string $fotoPath): void
    {
        $fotos = $this->getOriginalFotoProyek();
        $fotos[] = $fotoPath;
        $this->update(['foto_proyek' => json_encode($fotos)]);
    }

    /**
     * Remove foto from proyek
     */
    public function removeFoto(int $index): void
    {
        $fotos = $this->getOriginalFotoProyek();
        
        if (isset($fotos[$index])) {
            // Delete file if exists
            $filePath = storage_path('app/public/' . $fotos[$index]);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            
            array_splice($fotos, $index, 1);
            $this->update(['foto_proyek' => json_encode($fotos)]);
        }
    }

    /**
     * Get original foto proyek (before URL conversion)
     */
    private function getOriginalFotoProyek(): array
    {
        $fotoProyek = $this->getRawOriginal('foto_proyek');
        
        if (empty($fotoProyek)) {
            return [];
        }
        
        $fotos = json_decode($fotoProyek, true);
        return is_array($fotos) ? $fotos : [];
    }

    /**
     * Scope untuk proyek aktif
     */
    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_AKTIF);
    }

    /**
     * Scope untuk proyek tutup
     */
    public function scopeTutup(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_TUTUP);
    }

    /**
     * Scope untuk proyek habis
     */
    public function scopeHabis(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_HABIS);
    }

    /**
     * Scope untuk pencarian berdasarkan kode proyek
     */
    public function scopeKodeProyek(Builder $query, string $kode): Builder
    {
        return $query->where('kode_proyek', 'like', "%{$kode}%");
    }

    /**
     * Scope untuk pencarian berdasarkan nama proyek
     */
    public function scopeNamaProyek(Builder $query, string $nama): Builder
    {
        return $query->where('nama_proyek', 'like', "%{$nama}%");
    }

    /**
     * Scope untuk filter berdasarkan kota
     */
    public function scopeKota(Builder $query, string $kota): Builder
    {
        return $query->where('kota', 'like', "%{$kota}%");
    }

    /**
     * Scope untuk filter berdasarkan provinsi
     */
    public function scopeProvinsi(Builder $query, string $provinsi): Builder
    {
        return $query->where('provinsi', 'like', "%{$provinsi}%");
    }

    /**
     * Scope untuk pencarian multi-kriteria
     */
    public function scopeSearch(Builder $query, ?string $keyword): Builder
    {
        if (empty($keyword)) {
            return $query;
        }

        return $query->where(function ($q) use ($keyword) {
            $q->where('kode_proyek', 'like', "%{$keyword}%")
              ->orWhere('nama_proyek', 'like', "%{$keyword}%")
              ->orWhere('lokasi', 'like', "%{$keyword}%")
              ->orWhere('kota', 'like', "%{$keyword}%")
              ->orWhere('provinsi', 'like', "%{$keyword}%");
        });
    }

    /**
     * Get all proyek with active status for dropdown
     */
    public static function getActiveForDropdown(): array
    {
        return self::aktif()
            ->orderBy('nama_proyek')
            ->pluck('nama_proyek', 'id')
            ->toArray();
    }

    /**
     * Get all proyek with kode and nama for dropdown
     */
    public static function getForDropdown(): array
    {
        return self::orderBy('kode_proyek')
            ->get()
            ->mapWithKeys(fn($item) => [
                $item->id => "{$item->kode_proyek} - {$item->nama_proyek}"
            ])
            ->toArray();
    }

    /**
     * Generate unique kode proyek
     */
    public static function generateKodeProyek(): string
    {
        $prefix = 'PRJ';
        $year = date('Y');
        $month = date('m');
        
        $lastProyek = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();
        
        if ($lastProyek) {
            $lastNumber = (int) substr($lastProyek->kode_proyek, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        
        return "{$prefix}-{$year}{$month}-{$newNumber}";
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();
        
        // Auto-generate kode_proyek before creating
        static::creating(function ($proyek) {
            if (empty($proyek->kode_proyek)) {
                $proyek->kode_proyek = self::generateKodeProyek();
            }
        });
    }
}
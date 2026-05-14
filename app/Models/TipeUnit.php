<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{Builder, Model};
use Illuminate\Database\Eloquent\Casts\Attribute;

class TipeUnit extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terkait dengan model
     */
    protected $table = 'tipe_unit';

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
        'proyek_id',
        'kode_tipe',
        'nama_tipe',
        'luas_tanah',
        'luas_bangunan',
        'jumlah_kamar',
        'jumlah_wc',
        'harga',
        'stok_tersedia',
        'gambar',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'gambar' => 'array',
        'luas_tanah' => 'decimal:2',
        'luas_bangunan' => 'decimal:2',
        'harga' => 'decimal:2',
        'stok_tersedia' => 'integer',
        'jumlah_kamar' => 'integer',
        'jumlah_wc' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship dengan Proyek
     */
    public function proyek()
    {
        return $this->belongsTo(Proyek::class, 'proyek_id', 'id');
    }

    /**
     * Relationship dengan Unit (jika ada tabel units nanti)
     */
    public function units()
    {
        return $this->hasMany(Unit::class, 'tipe_unit_id', 'id');
    }

    /**
     * Accessor untuk gambar (decode JSON to array with full URLs)
     */
    protected function gambar(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (is_null($value)) {
                    return [];
                }
                
                $gambars = is_array($value) ? $value : json_decode($value, true);
                
                if (empty($gambars)) {
                    return [];
                }
                
                // Convert to full URL if needed
                return array_map(function ($gambar) {
                    return $gambar ? asset('storage/' . $gambar) : null;
                }, $gambars);
            },
            set: fn($value) => is_array($value) ? json_encode($value) : $value
        );
    }

    /**
     * Get first gambar as thumbnail
     */
    public function getThumbnailAttribute(): ?string
    {
        $gambars = $this->gambar;
        return !empty($gambars) ? $gambars[0] : null;
    }

    /**
     * Get formatted harga
     */
    public function getFormattedHargaAttribute(): string
    {
        return 'Rp ' . number_format($this->harga, 0, ',', '.');
    }

    /**
     * Get formatted luas tanah
     */
    public function getFormattedLuasTanahAttribute(): string
    {
        return number_format($this->luas_tanah, 2) . ' m²';
    }

    /**
     * Get formatted luas bangunan
     */
    public function getFormattedLuasBangunanAttribute(): string
    {
        return number_format($this->luas_bangunan, 2) . ' m²';
    }

    /**
     * Get total luas (tanah + bangunan)
     */
    public function getTotalLuasAttribute(): float
    {
        return $this->luas_tanah + $this->luas_bangunan;
    }

    /**
     * Get formatted total luas
     */
    public function getFormattedTotalLuasAttribute(): string
    {
        return number_format($this->getTotalLuasAttribute(), 2) . ' m²';
    }

    /**
     * Check if stock is available
     */
    public function isAvailable(): bool
    {
        return $this->stok_tersedia > 0;
    }

    /**
     * Check if stock is empty
     */
    public function isOutOfStock(): bool
    {
        return $this->stok_tersedia <= 0;
    }

    /**
     * Decrease stock by 1
     */
    public function decreaseStock(int $jumlah = 1): bool
    {
        if ($this->stok_tersedia >= $jumlah) {
            $this->decrement('stok_tersedia', $jumlah);
            return true;
        }
        return false;
    }

    /**
     * Increase stock by 1
     */
    public function increaseStock(int $jumlah = 1): void
    {
        $this->increment('stok_tersedia', $jumlah);
    }

    /**
     * Get harga after discount (example)
     */
    public function getHargaAfterDiscount(float $discountPercentage = 0): float
    {
        if ($discountPercentage <= 0) {
            return $this->harga;
        }
        
        return $this->harga * (1 - ($discountPercentage / 100));
    }

    /**
     * Get formatted harga after discount
     */
    public function getFormattedHargaAfterDiscount(float $discountPercentage = 0): string
    {
        return 'Rp ' . number_format($this->getHargaAfterDiscount($discountPercentage), 0, ',', '.');
    }

    /**
     * Add gambar to tipe unit
     */
    public function addGambar(string $gambarPath): void
    {
        $gambars = $this->getOriginalGambar();
        $gambars[] = $gambarPath;
        $this->update(['gambar' => json_encode($gambars)]);
    }

    /**
     * Remove gambar from tipe unit
     */
    public function removeGambar(int $index): void
    {
        $gambars = $this->getOriginalGambar();
        
        if (isset($gambars[$index])) {
            // Delete file if exists
            $filePath = storage_path('app/public/' . $gambars[$index]);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            
            array_splice($gambars, $index, 1);
            $this->update(['gambar' => json_encode($gambars)]);
        }
    }

    /**
     * Get original gambar (before URL conversion)
     */
    private function getOriginalGambar(): array
    {
        $gambar = $this->getRawOriginal('gambar');
        
        if (empty($gambar)) {
            return [];
        }
        
        $gambars = json_decode($gambar, true);
        return is_array($gambars) ? $gambars : [];
    }

    /**
     * Scope untuk tipe unit yang tersedia (stok > 0)
     */
    public function scopeTersedia(Builder $query): Builder
    {
        return $query->where('stok_tersedia', '>', 0);
    }

    /**
     * Scope untuk tipe unit habis (stok = 0)
     */
    public function scopeHabis(Builder $query): Builder
    {
        return $query->where('stok_tersedia', 0);
    }

    /**
     * Scope untuk filter berdasarkan proyek
     */
    public function scopeByProyek(Builder $query, int $proyekId): Builder
    {
        return $query->where('proyek_id', $proyekId);
    }

    /**
     * Scope untuk filter berdasarkan range harga
     */
    public function scopeHargaBetween(Builder $query, float $min, float $max): Builder
    {
        return $query->whereBetween('harga', [$min, $max]);
    }

    /**
     * Scope untuk filter berdasarkan minimal luas tanah
     */
    public function scopeMinLuasTanah(Builder $query, float $luas): Builder
    {
        return $query->where('luas_tanah', '>=', $luas);
    }

    /**
     * Scope untuk filter berdasarkan minimal luas bangunan
     */
    public function scopeMinLuasBangunan(Builder $query, float $luas): Builder
    {
        return $query->where('luas_bangunan', '>=', $luas);
    }

    /**
     * Scope untuk filter berdasarkan jumlah kamar
     */
    public function scopeJumlahKamar(Builder $query, int $jumlah): Builder
    {
        return $query->where('jumlah_kamar', '>=', $jumlah);
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
            $q->where('kode_tipe', 'like', "%{$keyword}%")
              ->orWhere('nama_tipe', 'like', "%{$keyword}%");
        });
    }

    /**
     * Scope untuk sorting berdasarkan harga
     */
    public function scopeSortByHarga(Builder $query, string $direction = 'asc'): Builder
    {
        return $query->orderBy('harga', $direction);
    }

    /**
     * Get all tipe unit for dropdown by proyek
     */
    public static function getForDropdownByProyek(int $proyekId): array
    {
        return self::byProyek($proyekId)
            ->orderBy('kode_tipe')
            ->get()
            ->mapWithKeys(fn($item) => [
                $item->id => "{$item->kode_tipe} - {$item->nama_tipe} (Rp " . number_format($item->harga, 0, ',', '.') . ")"
            ])
            ->toArray();
    }

    /**
     * Generate unique kode_tipe for a proyek
     */
    public static function generateKodeTipe(int $proyekId, string $namaTipe): string
    {
        $proyek = Proyek::find($proyekId);
        $prefix = $proyek ? substr($proyek->kode_proyek, 0, 6) : 'PRJ';
        
        // Create slug from nama_tipe
        $slug = strtoupper(substr(preg_replace('/[^A-Za-z0-9-]+/', '', $namaTipe), 0, 3));
        
        $lastTipe = self::byProyek($proyekId)
            ->where('kode_tipe', 'like', "{$prefix}-{$slug}%")
            ->orderBy('id', 'desc')
            ->first();
        
        if ($lastTipe) {
            $lastNumber = (int) substr($lastTipe->kode_tipe, -3);
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '001';
        }
        
        return "{$prefix}-{$slug}-{$newNumber}";
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();
        
        // Auto-generate kode_tipe before creating
        static::creating(function ($tipeUnit) {
            if (empty($tipeUnit->kode_tipe)) {
                $tipeUnit->kode_tipe = self::generateKodeTipe($tipeUnit->proyek_id, $tipeUnit->nama_tipe);
            }
        });
        
        // Update stok di tabel unit (optional, jika ada relasi)
        static::updated(function ($tipeUnit) {
            if ($tipeUnit->wasChanged('stok_tersedia')) {
                // Logic to update related units if needed
                // This would be implemented when Unit model exists
            }
        });
    }
}
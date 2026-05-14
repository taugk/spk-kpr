<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{Builder, Model};
use Illuminate\Database\Eloquent\Casts\Attribute;

class Unit extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terkait dengan model
     */
    protected $table = 'unit';

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
        'tipe_unit_id',
        'kode_unit',
        'foto_unit',
        'fasilitas',
        'status',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'foto_unit' => 'array',
        'fasilitas' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Enum values untuk status
     */
    const STATUS_TERSEDIA = 'tersedia';
    const STATUS_DIPESAN = 'dipesan';
    const STATUS_TERJUAL = 'terjual';
    const STATUS_DIBATALKAN = 'dibatalkan';

    /**
     * Relationship dengan TipeUnit
     */
    public function tipeUnit()
    {
        return $this->belongsTo(TipeUnit::class, 'tipe_unit_id', 'id');
    }

    /**
     * Relationship dengan Proyek (through TipeUnit)
     */
    public function proyek()
    {
        return $this->hasOneThrough(
            Proyek::class,
            TipeUnit::class,
            'id', // Foreign key on tipe_unit table
            'id', // Foreign key on proyek table
            'tipe_unit_id', // Local key on unit table
            'proyek_id' // Local key on tipe_unit table
        );
    }



    /**
     * Accessor untuk foto_unit (decode JSON to array with full URLs)
     */
    protected function fotoUnit(): Attribute
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
     * Accessor untuk fasilitas (decode JSON to array)
     */
    protected function fasilitas(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (is_null($value)) {
                    return [];
                }

                $fasilitas = is_array($value) ? $value : json_decode($value, true);
                return is_array($fasilitas) ? $fasilitas : [];
            },
            set: fn($value) => is_array($value) ? json_encode($value) : $value
        );
    }

    /**
     * Get first foto as thumbnail
     */
    public function getThumbnailAttribute(): ?string
    {
        $fotos = $this->foto_unit;
        return !empty($fotos) ? $fotos[0] : null;
    }

    /**
     * Get formatted fasilitas as string
     */
    public function getFormattedFasilitasAttribute(): string
    {
        if (empty($this->fasilitas)) {
            return '-';
        }

        return implode(', ', $this->fasilitas);
    }

    /**
     * Get fasilitas as badge list
     */
    public function getFasilitasBadgeAttribute(): string
    {
        if (empty($this->fasilitas)) {
            return '<span class="badge badge-secondary">Tidak ada fasilitas</span>';
        }

        $badges = array_map(function($fasilitas) {
            return '<span class="badge badge-info">' . e($fasilitas) . '</span>';
        }, $this->fasilitas);

        return implode(' ', $badges);
    }

    /**
     * Get status with badge HTML
     */
    public function getStatusBadgeAttribute(): string
    {
        $badges = [
            self::STATUS_TERSEDIA => '<span class="badge badge-success">Tersedia</span>',
            self::STATUS_DIPESAN => '<span class="badge badge-warning">Dipesan</span>',
            self::STATUS_TERJUAL => '<span class="badge badge-danger">Terjual</span>',
            self::STATUS_DIBATALKAN => '<span class="badge badge-secondary">Dibatalkan</span>',
        ];

        return $badges[$this->status] ?? '<span class="badge badge-secondary">' . e($this->status) . '</span>';
    }

    /**
     * Get status text in Indonesian
     */
    public function getStatusTextAttribute(): string
    {
        $texts = [
            self::STATUS_TERSEDIA => 'Tersedia',
            self::STATUS_DIPESAN => 'Dipesan',
            self::STATUS_TERJUAL => 'Terjual',
            self::STATUS_DIBATALKAN => 'Dibatalkan',
        ];

        return $texts[$this->status] ?? $this->status;
    }

    /**
     * Get complete unit info (with tipe and proyek)
     */
    public function getCompleteInfoAttribute(): string
    {
        $tipeInfo = $this->tipeUnit ? "{$this->tipeUnit->kode_tipe} - {$this->tipeUnit->nama_tipe}" : 'Tipe tidak diketahui';
        return "{$this->kode_unit} | {$tipeInfo}";
    }

    /**
     * Check if unit is available
     */
    public function isAvailable(): bool
    {
        return $this->status === self::STATUS_TERSEDIA;
    }

    /**
     * Check if unit is booked
     */
    public function isBooked(): bool
    {
        return $this->status === self::STATUS_DIPESAN;
    }

    /**
     * Check if unit is sold
     */
    public function isSold(): bool
    {
        return $this->status === self::STATUS_TERJUAL;
    }

    /**
     * Check if unit is cancelled
     */
    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_DIBATALKAN;
    }

    /**
     * Mark unit as available
     */
    public function markAsAvailable(): void
    {
        $this->update(['status' => self::STATUS_TERSEDIA]);

        // Increase stock in tipe_unit
        if ($this->tipeUnit) {
            $this->tipeUnit->increaseStock();
        }
    }

    /**
     * Mark unit as booked
     */
    public function markAsBooked(): void
    {
        $this->update(['status' => self::STATUS_DIPESAN]);

        // Decrease stock in tipe_unit
        if ($this->tipeUnit) {
            $this->tipeUnit->decreaseStock();
        }
    }

    /**
     * Mark unit as sold
     */
    public function markAsSold(): void
    {
        $this->update(['status' => self::STATUS_TERJUAL]);
    }

    /**
     * Mark unit as cancelled
     */
    public function markAsCancelled(): void
    {
        $oldStatus = $this->status;
        $this->update(['status' => self::STATUS_DIBATALKAN]);

        // If it was booked, return stock
        if ($oldStatus === self::STATUS_DIPESAN && $this->tipeUnit) {
            $this->tipeUnit->increaseStock();
        }
    }

    /**
     * Add foto to unit
     */
    public function addFoto(string $fotoPath): void
    {
        $fotos = $this->getOriginalFotoUnit();
        $fotos[] = $fotoPath;
        $this->update(['foto_unit' => json_encode($fotos)]);
    }

    /**
     * Remove foto from unit
     */
    public function removeFoto(int $index): void
    {
        $fotos = $this->getOriginalFotoUnit();

        if (isset($fotos[$index])) {
            // Delete file if exists
            $filePath = storage_path('app/public/' . $fotos[$index]);
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            array_splice($fotos, $index, 1);
            $this->update(['foto_unit' => json_encode($fotos)]);
        }
    }

    /**
     * Get original foto_unit (before URL conversion)
     */
    private function getOriginalFotoUnit(): array
    {
        $fotoUnit = $this->getRawOriginal('foto_unit');

        if (empty($fotoUnit)) {
            return [];
        }

        $fotos = json_decode($fotoUnit, true);
        return is_array($fotos) ? $fotos : [];
    }

    /**
     * Add fasilitas
     */
    public function addFasilitas(string $fasilitas): void
    {
        $fasilitasArr = $this->fasilitas;
        if (!in_array($fasilitas, $fasilitasArr)) {
            $fasilitasArr[] = $fasilitas;
            $this->update(['fasilitas' => json_encode($fasilitasArr)]);
        }
    }

    /**
     * Remove fasilitas
     */
    public function removeFasilitas(string $fasilitas): void
    {
        $fasilitasArr = $this->fasilitas;
        $key = array_search($fasilitas, $fasilitasArr);

        if ($key !== false) {
            array_splice($fasilitasArr, $key, 1);
            $this->update(['fasilitas' => json_encode($fasilitasArr)]);
        }
    }

    /**
     * Get harga from tipe unit
     */
    public function getHargaAttribute(): ?float
    {
        return $this->tipeUnit ? $this->tipeUnit->harga : null;
    }

    /**
     * Get formatted harga
     */
    public function getFormattedHargaAttribute(): ?string
    {
        $harga = $this->getHargaAttribute();
        return $harga ? 'Rp ' . number_format($harga, 0, ',', '.') : null;
    }

    /**
     * Get luas tanah from tipe unit
     */
    public function getLuasTanahAttribute(): ?float
    {
        return $this->tipeUnit ? $this->tipeUnit->luas_tanah : null;
    }

    /**
     * Get luas bangunan from tipe unit
     */
    public function getLuasBangunanAttribute(): ?float
    {
        return $this->tipeUnit ? $this->tipeUnit->luas_bangunan : null;
    }

    /**
     * Scope untuk unit tersedia
     */
    public function scopeTersedia(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_TERSEDIA);
    }

    /**
     * Scope untuk unit dipesan
     */
    public function scopeDipesan(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_DIPESAN);
    }

    /**
     * Scope untuk unit terjual
     */
    public function scopeTerjual(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_TERJUAL);
    }

    /**
     * Scope untuk unit dibatalkan
     */
    public function scopeDibatalkan(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_DIBATALKAN);
    }

    /**
     * Scope untuk filter berdasarkan tipe unit
     */
    public function scopeByTipeUnit(Builder $query, int $tipeUnitId): Builder
    {
        return $query->where('tipe_unit_id', $tipeUnitId);
    }

    /**
     * Scope untuk filter berdasarkan proyek
     */
    public function scopeByProyek(Builder $query, int $proyekId): Builder
    {
        return $query->whereHas('tipeUnit', function ($q) use ($proyekId) {
            $q->where('proyek_id', $proyekId);
        });
    }

    /**
     * Scope untuk pencarian berdasarkan kode unit
     */
    public function scopeKodeUnit(Builder $query, string $kode): Builder
    {
        return $query->where('kode_unit', 'like', "%{$kode}%");
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
            $q->where('kode_unit', 'like', "%{$keyword}%")
              ->orWhereHas('tipeUnit', function ($subQ) use ($keyword) {
                  $subQ->where('kode_tipe', 'like', "%{$keyword}%")
                       ->orWhere('nama_tipe', 'like', "%{$keyword}%");
              });
        });
    }

    /**
     * Get all units for dropdown
     */
    public static function getForDropdown(bool $onlyAvailable = false): array
    {
        $query = self::query();

        if ($onlyAvailable) {
            $query->tersedia();
        }

        return $query->with('tipeUnit')
            ->orderBy('kode_unit')
            ->get()
            ->mapWithKeys(fn($item) => [
                $item->id => $item->getCompleteInfoAttribute()
            ])
            ->toArray();
    }

    /**
     * Get available units for a specific tipe unit
     */
    public static function getAvailableByTipeUnit(int $tipeUnitId): array
    {
        return self::byTipeUnit($tipeUnitId)
            ->tersedia()
            ->orderBy('kode_unit')
            ->pluck('kode_unit', 'id')
            ->toArray();
    }

    /**
     * Generate unique kode_unit
     */
    public static function generateKodeUnit(int $tipeUnitId): string
    {
        $tipeUnit = TipeUnit::find($tipeUnitId);

        if (!$tipeUnit) {
            return 'UNIT-' . uniqid();
        }

        $prefix = $tipeUnit->kode_tipe;

        $lastUnit = self::where('tipe_unit_id', $tipeUnitId)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastUnit) {
            $lastNumber = (int) substr($lastUnit->kode_unit, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return "{$prefix}-{$newNumber}";
    }

    /**
     * Get all status options
     */
    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_TERSEDIA,
            self::STATUS_DIPESAN,
            self::STATUS_TERJUAL,
            self::STATUS_DIBATALKAN,
        ];
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate kode_unit before creating
        static::creating(function ($unit) {
            if (empty($unit->kode_unit)) {
                $unit->kode_unit = self::generateKodeUnit($unit->tipe_unit_id);
            }
        });

        // Update tipe_unit stock when status changes
        static::updated(function ($unit) {
            if ($unit->wasChanged('status')) {
                $oldStatus = $unit->getOriginal('status');
                $newStatus = $unit->status;

                // Handle stock changes based on status transition
                if ($oldStatus === self::STATUS_TERSEDIA && $newStatus === self::STATUS_DIPESAN) {
                    if ($unit->tipeUnit) {
                        $unit->tipeUnit->decreaseStock();
                    }
                } elseif ($oldStatus === self::STATUS_DIPESAN && $newStatus === self::STATUS_TERSEDIA) {
                    if ($unit->tipeUnit) {
                        $unit->tipeUnit->increaseStock();
                    }
                } elseif ($oldStatus === self::STATUS_DIPESAN && $newStatus === self::STATUS_DIBATALKAN) {
                    if ($unit->tipeUnit) {
                        $unit->tipeUnit->increaseStock();
                    }
                }
            }
        });
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{Builder, Model};
use Illuminate\Database\Eloquent\Casts\Attribute;

class Kriteria extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terkait dengan model
     */
    protected $table = 'kriteria';

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
        'kode_kriteria',
        'nama_kriteria',
        'deskripsi',
        'tipe',
        'bobot',
        'satuan',
        'nilai_min',
        'nilai_max',
        'urutan',
        'aktif',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'bobot' => 'decimal:2',
        'nilai_min' => 'decimal:4',
        'nilai_max' => 'decimal:4',
        'urutan' => 'integer',
        'aktif' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Enum values untuk tipe
     */
    const TIPE_BENEFIT = 'benefit';
    const TIPE_COST = 'cost';

    /**
     * Relationships
     */
    public function skala()
    {
        return $this->hasMany(KriteriaSkala::class, 'kriteria_id', 'id');
    }

    /**
     * Get all tipe options
     */
    public static function getTipeOptions(): array
    {
        return [
            self::TIPE_BENEFIT,
            self::TIPE_COST,
        ];
    }

    /**
     * Accessors
     */
    protected function tipeBadge(): Attribute
    {
        return Attribute::make(
            get: function () {
                $badges = [
                    self::TIPE_BENEFIT => '<span class="badge badge-success">Benefit</span>',
                    self::TIPE_COST => '<span class="badge badge-danger">Cost</span>',
                ];
                
                return $badges[$this->tipe] ?? '<span class="badge badge-secondary">' . e($this->tipe) . '</span>';
            }
        );
    }

    protected function tipeText(): Attribute
    {
        return Attribute::make(
            get: function () {
                $texts = [
                    self::TIPE_BENEFIT => 'Benefit (Semakin besar semakin baik)',
                    self::TIPE_COST => 'Cost (Semakin kecil semakin baik)',
                ];
                
                return $texts[$this->tipe] ?? $this->tipe;
            }
        );
    }

    protected function statusBadge(): Attribute
    {
        return Attribute::make(
            get: function () {
                return $this->aktif 
                    ? '<span class="badge badge-success">Aktif</span>'
                    : '<span class="badge badge-danger">Nonaktif</span>';
            }
        );
    }

    protected function formattedBobot(): Attribute
    {
        return Attribute::make(
            get: fn() => number_format($this->bobot, 2) . '%'
        );
    }

    protected function formattedNilaiMin(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->nilai_min !== null ? number_format($this->nilai_min, 2) : '-'
        );
    }

    protected function formattedNilaiMax(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->nilai_max !== null ? number_format($this->nilai_max, 2) : '-'
        );
    }

    /**
     * Business Logic Methods
     */
    
    /**
     * Check if criteria is benefit type
     */
    public function isBenefit(): bool
    {
        return $this->tipe === self::TIPE_BENEFIT;
    }

    /**
     * Check if criteria is cost type
     */
    public function isCost(): bool
    {
        return $this->tipe === self::TIPE_COST;
    }

    /**
     * Check if criteria is active
     */
    public function isActive(): bool
    {
        return $this->aktif;
    }

    /**
     * Activate criteria
     */
    public function activate(): bool
    {
        return $this->update(['aktif' => true]);
    }

    /**
     * Deactivate criteria
     */
    public function deactivate(): bool
    {
        return $this->update(['aktif' => false]);
    }

    /**
     * Get skala by score
     */
    public function getSkalaByScore(int $skor): ?KriteriaSkala
    {
        return $this->skala()->where('skor', $skor)->first();
    }

    /**
     * Get skala by nilai
     */
    public function getSkalaByNilai(float $nilai): ?KriteriaSkala
    {
        return $this->skala()
            ->where(function ($query) use ($nilai) {
                $query->whereNull('nilai_min')
                    ->orWhere('nilai_min', '<=', $nilai);
            })
            ->where(function ($query) use ($nilai) {
                $query->whereNull('nilai_max')
                    ->orWhere('nilai_max', '>=', $nilai);
            })
            ->first();
    }

    /**
     * Normalize value based on criteria type
     */
    public function normalizeValue(float $value, float $minValue, float $maxValue): float
    {
        if ($maxValue - $minValue == 0) {
            return 0;
        }

        if ($this->isBenefit()) {
            // Benefit: (value - min) / (max - min)
            return ($value - $minValue) / ($maxValue - $minValue);
        } else {
            // Cost: (max - value) / (max - min)
            return ($maxValue - $value) / ($maxValue - $minValue);
        }
    }

    /**
     * Get weighted score
     */
    public function getWeightedScore(float $normalizedValue): float
    {
        return ($this->bobot / 100) * $normalizedValue;
    }

    /**
     * Validate nilai against min/max
     */
    public function validateNilai(float $nilai): bool
    {
        if ($this->nilai_min !== null && $nilai < $this->nilai_min) {
            return false;
        }
        
        if ($this->nilai_max !== null && $nilai > $this->nilai_max) {
            return false;
        }
        
        return true;
    }

    /**
     * Get total bobot (for validation)
     */
    public static function getTotalBobot(): float
    {
        return self::where('aktif', true)->sum('bobot');
    }

    /**
     * Validate total bobot is 100%
     */
    public static function isTotalBobotValid(): bool
    {
        $total = self::getTotalBobot();
        return abs($total - 100) <= 0.01; // Allow small floating point error
    }

    /**
     * Get active criteria ordered by urutan
     */
    public static function getActiveOrdered(): \Illuminate\Database\Eloquent\Collection
    {
        return self::where('aktif', true)
            ->orderBy('urutan')
            ->orderBy('id')
            ->get();
    }

    /**
     * Generate unique kode_kriteria
     */
    public static function generateKodeKriteria(): string
    {
        $lastKriteria = self::orderBy('id', 'desc')->first();
        
        if ($lastKriteria && $lastKriteria->kode_kriteria) {
            $lastNumber = (int) substr($lastKriteria->kode_kriteria, 1);
            $newNumber = str_pad($lastNumber + 1, 2, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '01';
        }
        
        return "C{$newNumber}";
    }

    /**
     * Scopes
     */
    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('aktif', true);
    }

    public function scopeNonaktif(Builder $query): Builder
    {
        return $query->where('aktif', false);
    }

    public function scopeBenefit(Builder $query): Builder
    {
        return $query->where('tipe', self::TIPE_BENEFIT);
    }

    public function scopeCost(Builder $query): Builder
    {
        return $query->where('tipe', self::TIPE_COST);
    }

    public function scopeByKode(Builder $query, string $kode): Builder
    {
        return $query->where('kode_kriteria', 'like', "%{$kode}%");
    }

    public function scopeByNama(Builder $query, string $nama): Builder
    {
        return $query->where('nama_kriteria', 'like', "%{$nama}%");
    }

    public function scopeOrderByUrutan(Builder $query): Builder
    {
        return $query->orderBy('urutan')->orderBy('id');
    }

    public function scopeSearch(Builder $query, ?string $keyword): Builder
    {
        if (empty($keyword)) {
            return $query;
        }

        return $query->where(function ($q) use ($keyword) {
            $q->where('kode_kriteria', 'like', "%{$keyword}%")
              ->orWhere('nama_kriteria', 'like', "%{$keyword}%")
              ->orWhere('deskripsi', 'like', "%{$keyword}%");
        });
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();
        
        // Auto-generate kode_kriteria before creating
        static::creating(function ($kriteria) {
            if (empty($kriteria->kode_kriteria)) {
                $kriteria->kode_kriteria = self::generateKodeKriteria();
            }
        });
        
        // Validate bobot before saving
        static::saving(function ($kriteria) {
            if ($kriteria->bobot < 0 || $kriteria->bobot > 100) {
                throw new \Exception('Bobot kriteria harus antara 0-100');
            }
        });
    }
}
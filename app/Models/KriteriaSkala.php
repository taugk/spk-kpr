<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{Builder, Model};
use Illuminate\Database\Eloquent\Casts\Attribute;

class KriteriaSkala extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terkait dengan model
     */
    protected $table = 'kriteria_skala';

    /**
     * Primary key yang tidak auto-incrementing
     */
    public $incrementing = true;

    /**
     * Tipe primary key
     */
    protected $keyType = 'int';

    /**
     * Tidak menggunakan timestamps
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'kriteria_id',
        'skor',
        'keterangan',
        'nilai_min',
        'nilai_max',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'skor' => 'integer',
        'nilai_min' => 'decimal:4',
        'nilai_max' => 'decimal:4',
    ];

    /**
     * Relationships
     */
    public function kriteria()
    {
        return $this->belongsTo(Kriteria::class, 'kriteria_id', 'id');
    }

    /**
     * Accessors
     */
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

    protected function rangeNilai(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->nilai_min !== null && $this->nilai_max !== null) {
                    return number_format($this->nilai_min, 2) . ' - ' . number_format($this->nilai_max, 2);
                } elseif ($this->nilai_min !== null) {
                    return '≥ ' . number_format($this->nilai_min, 2);
                } elseif ($this->nilai_max !== null) {
                    return '≤ ' . number_format($this->nilai_max, 2);
                }
                return '-';
            }
        );
    }

    /**
     * Business Logic Methods
     */
    
    /**
     * Check if nilai is within range
     */
    public function isNilaiInRange(float $nilai): bool
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
     * Get score for a given value
     */
    public static function getScoreByNilai(int $kriteriaId, float $nilai): ?int
    {
        $skala = self::where('kriteria_id', $kriteriaId)
            ->where(function ($query) use ($nilai) {
                $query->whereNull('nilai_min')
                    ->orWhere('nilai_min', '<=', $nilai);
            })
            ->where(function ($query) use ($nilai) {
                $query->whereNull('nilai_max')
                    ->orWhere('nilai_max', '>=', $nilai);
            })
            ->first();
        
        return $skala ? $skala->skor : null;
    }

    /**
     * Get all skala for dropdown
     */
    public static function getForDropdown(int $kriteriaId): array
    {
        return self::where('kriteria_id', $kriteriaId)
            ->orderBy('skor')
            ->get()
            ->mapWithKeys(fn($item) => [
                $item->id => "Skor {$item->skor}: {$item->keterangan}"
            ])
            ->toArray();
    }

    /**
     * Validate no overlapping ranges
     */
    public static function validateNoOverlap(int $kriteriaId, ?float $min, ?float $max, ?int $excludeId = null): bool
    {
        $query = self::where('kriteria_id', $kriteriaId);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        $existing = $query->get();
        
        foreach ($existing as $item) {
            // Check if ranges overlap
            if ($min !== null && $item->nilai_max !== null && $min <= $item->nilai_max) {
                return false;
            }
            if ($max !== null && $item->nilai_min !== null && $max >= $item->nilai_min) {
                return false;
            }
            if ($min === null && $max !== null && $item->nilai_min !== null && $max >= $item->nilai_min) {
                return false;
            }
            if ($max === null && $min !== null && $item->nilai_max !== null && $min <= $item->nilai_max) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Scopes
     */
    public function scopeByKriteria(Builder $query, int $kriteriaId): Builder
    {
        return $query->where('kriteria_id', $kriteriaId);
    }

    public function scopeBySkor(Builder $query, int $skor): Builder
    {
        return $query->where('skor', $skor);
    }

    public function scopeMinSkor(Builder $query, int $skor): Builder
    {
        return $query->where('skor', '>=', $skor);
    }

    public function scopeMaxSkor(Builder $query, int $skor): Builder
    {
        return $query->where('skor', '<=', $skor);
    }

    public function scopeOrderBySkorAsc(Builder $query): Builder
    {
        return $query->orderBy('skor', 'asc');
    }

    public function scopeOrderBySkorDesc(Builder $query): Builder
    {
        return $query->orderBy('skor', 'desc');
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();
        
        // Validate skor uniqueness per kriteria
        static::creating(function ($skala) {
            $exists = self::where('kriteria_id', $skala->kriteria_id)
                ->where('skor', $skala->skor)
                ->exists();
            
            if ($exists) {
                throw new \Exception('Skor ' . $skala->skor . ' sudah digunakan untuk kriteria ini');
            }
            
            // Validate no overlapping ranges
            if (!self::validateNoOverlap($skala->kriteria_id, $skala->nilai_min, $skala->nilai_max)) {
                throw new \Exception('Range nilai overlapping dengan skala yang sudah ada');
            }
        });
        
        // Validate on update
        static::updating(function ($skala) {
            if ($skala->isDirty('skor')) {
                $exists = self::where('kriteria_id', $skala->kriteria_id)
                    ->where('skor', $skala->skor)
                    ->where('id', '!=', $skala->id)
                    ->exists();
                
                if ($exists) {
                    throw new \Exception('Skor ' . $skala->skor . ' sudah digunakan untuk kriteria ini');
                }
            }
            
            if ($skala->isDirty('nilai_min') || $skala->isDirty('nilai_max')) {
                if (!self::validateNoOverlap($skala->kriteria_id, $skala->nilai_min, $skala->nilai_max, $skala->id)) {
                    throw new \Exception('Range nilai overlapping dengan skala yang sudah ada');
                }
            }
        });
    }
}
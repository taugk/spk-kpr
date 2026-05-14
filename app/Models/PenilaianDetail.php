<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{Builder, Model};
use Illuminate\Database\Eloquent\Casts\Attribute;

class PenilaianDetail extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terkait dengan model
     */
    protected $table = 'penilaian_detail';

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
        'penilaian_id',
        'kriteria_id',
        'nilai_input',
        'nilai_normalisasi',
        'bobot_snapshot',
        'skor_kontribusi',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'nilai_input' => 'decimal:4',
        'nilai_normalisasi' => 'decimal:6',
        'bobot_snapshot' => 'decimal:4',
        'skor_kontribusi' => 'decimal:4',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function penilaian()
    {
        return $this->belongsTo(Penilaian::class, 'penilaian_id', 'id');
    }

    public function kriteria()
    {
        return $this->belongsTo(Kriteria::class, 'kriteria_id', 'id');
    }

    /**
     * Accessors
     */
    protected function formattedNilaiInput(): Attribute
    {
        return Attribute::make(
            get: fn() => number_format($this->nilai_input, 2)
        );
    }

    protected function formattedNilaiNormalisasi(): Attribute
    {
        return Attribute::make(
            get: fn() => number_format($this->nilai_normalisasi, 4)
        );
    }

    protected function formattedBobotSnapshot(): Attribute
    {
        return Attribute::make(
            get: fn() => number_format($this->bobot_snapshot * 100, 2) . '%'
        );
    }

    protected function formattedSkorKontribusi(): Attribute
    {
        return Attribute::make(
            get: fn() => number_format($this->skor_kontribusi, 4)
        );
    }

    protected function persenKontribusi(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->penilaian || $this->penilaian->skor_akhir <= 0) {
                    return 0;
                }
                return ($this->skor_kontribusi / $this->penilaian->skor_akhir) * 100;
            }
        );
    }

    /**
     * Business Logic Methods
     */
    
    /**
     * Recalculate normalized value and contribution
     */
    public function recalculate(): bool
    {
        $kriteria = $this->kriteria;
        if (!$kriteria) {
            return false;
        }
        
        $minValue = $kriteria->nilai_min ?? 0;
        $maxValue = $kriteria->nilai_max ?? 100;
        
        $nilaiNormalisasi = $kriteria->normalizeValue($this->nilai_input, $minValue, $maxValue);
        $skorKontribusi = $kriteria->getWeightedScore($nilaiNormalisasi);
        
        $this->nilai_normalisasi = $nilaiNormalisasi;
        $this->skor_kontribusi = $skorKontribusi;
        
        return $this->save();
    }

    /**
     * Update nilai input and recalculate
     */
    public function updateNilaiInput(float $nilaiInput): bool
    {
        $this->nilai_input = $nilaiInput;
        return $this->recalculate();
    }

    /**
     * Check if contribution is high (above average)
     */
    public function isHighContribution(): bool
    {
        $averageContribution = $this->penilaian->details->avg('skor_kontribusi') ?? 0;
        return $this->skor_kontribusi > $averageContribution;
    }

    /**
     * Check if contribution is low (below average)
     */
    public function isLowContribution(): bool
    {
        $averageContribution = $this->penilaian->details->avg('skor_kontribusi') ?? 0;
        return $this->skor_kontribusi < $averageContribution;
    }

    /**
     * Get contribution level
     */
    public function getContributionLevel(): string
    {
        if ($this->skor_kontribusi >= 0.8) {
            return 'Sangat Tinggi';
        } elseif ($this->skor_kontribusi >= 0.6) {
            return 'Tinggi';
        } elseif ($this->skor_kontribusi >= 0.4) {
            return 'Sedang';
        } elseif ($this->skor_kontribusi >= 0.2) {
            return 'Rendah';
        } else {
            return 'Sangat Rendah';
        }
    }

    /**
     * Scopes
     */
    public function scopeByPenilaian(Builder $query, int $penilaianId): Builder
    {
        return $query->where('penilaian_id', $penilaianId);
    }

    public function scopeByKriteria(Builder $query, int $kriteriaId): Builder
    {
        return $query->where('kriteria_id', $kriteriaId);
    }

    public function scopeSkorKontribusiMin(Builder $query, float $minScore): Builder
    {
        return $query->where('skor_kontribusi', '>=', $minScore);
    }

    public function scopeSkorKontribusiMax(Builder $query, float $maxScore): Builder
    {
        return $query->where('skor_kontribusi', '<=', $maxScore);
    }

    public function scopeHighContribution(Builder $query): Builder
    {
        return $query->whereHas('penilaian', function ($q) {
            $q->whereRaw('skor_kontribusi > (SELECT AVG(skor_kontribusi) FROM penilaian_detail)');
        });
    }

    public function scopeOrderBySkorDesc(Builder $query): Builder
    {
        return $query->orderBy('skor_kontribusi', 'desc');
    }

    public function scopeOrderBySkorAsc(Builder $query): Builder
    {
        return $query->orderBy('skor_kontribusi', 'asc');
    }

    /**
     * Get statistics for a penilaian
     */
    public static function getStatisticsForPenilaian(int $penilaianId): array
    {
        $details = self::where('penilaian_id', $penilaianId)->get();
        
        return [
            'total_kriteria' => $details->count(),
            'rata_rata_nilai_input' => $details->avg('nilai_input'),
            'rata_rata_normalisasi' => $details->avg('nilai_normalisasi'),
            'rata_rata_skor_kontribusi' => $details->avg('skor_kontribusi'),
            'skor_tertinggi' => $details->max('skor_kontribusi'),
            'skor_terendah' => $details->min('skor_kontribusi'),
            'kriteria_tertinggi' => $details->sortByDesc('skor_kontribusi')->first()?->kriteria?->nama_kriteria,
            'kriteria_terendah' => $details->sortBy('skor_kontribusi')->first()?->kriteria?->nama_kriteria,
        ];
    }
}
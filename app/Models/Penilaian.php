<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{Builder, Model};
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

class Penilaian extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terkait dengan model
     */
    protected $table = 'penilaian';

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
        'admin_id',
        'tgl_penilaian',
        'skor_akhir',
        'threshold',
        'hasil',
        'catatan_admin',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'skor_akhir' => 'decimal:4',
        'threshold' => 'decimal:4',
        'tgl_penilaian' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Enum values untuk hasil
     */
    const HASIL_LAYAK = 'layak';
    const HASIL_TIDAK_LAYAK = 'tidak_layak';

    /**
     * Relationships
     */
    public function pengajuan()
    {
        return $this->belongsTo(Pengajuan::class, 'pengajuan_id', 'id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id', 'id');
    }

    public function details()
    {
        return $this->hasMany(PenilaianDetail::class, 'penilaian_id', 'id');
    }

    /**
     * Get all hasil options
     */
    public static function getHasilOptions(): array
    {
        return [
            self::HASIL_LAYAK,
            self::HASIL_TIDAK_LAYAK,
        ];
    }

    /**
     * Accessors
     */
    protected function hasilBadge(): Attribute
    {
        return Attribute::make(
            get: function () {
                $badges = [
                    self::HASIL_LAYAK => '<span class="badge badge-success">Layak</span>',
                    self::HASIL_TIDAK_LAYAK => '<span class="badge badge-danger">Tidak Layak</span>',
                ];
                
                return $badges[$this->hasil] ?? '<span class="badge badge-secondary">-</span>';
            }
        );
    }

    protected function hasilText(): Attribute
    {
        return Attribute::make(
            get: function () {
                $texts = [
                    self::HASIL_LAYAK => 'Layak',
                    self::HASIL_TIDAK_LAYAK => 'Tidak Layak',
                ];
                
                return $texts[$this->hasil] ?? '-';
            }
        );
    }

    protected function formattedSkorAkhir(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->skor_akhir !== null ? number_format($this->skor_akhir, 2) . ' %' : '-'
        );
    }

    protected function formattedThreshold(): Attribute
    {
        return Attribute::make(
            get: fn() => number_format($this->threshold, 2) . ' %'
        );
    }

    protected function formattedTglPenilaian(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->tgl_penilaian ? $this->tgl_penilaian->format('d/m/Y H:i:s') : '-'
        );
    }

    /**
     * Business Logic Methods
     */
    
    /**
     * Check if hasil is layak
     */
    public function isLayak(): bool
    {
        return $this->hasil === self::HASIL_LAYAK;
    }

    /**
     * Check if hasil is tidak layak
     */
    public function isTidakLayak(): bool
    {
        return $this->hasil === self::HASIL_TIDAK_LAYAK;
    }

    /**
     * Check if skor_akhir meets threshold
     */
    public function meetsThreshold(): bool
    {
        return $this->skor_akhir >= $this->threshold;
    }

    /**
     * Calculate final score from details
     */
    public function calculateSkorAkhir(): float
    {
        $total = $this->details->sum('skor_kontribusi');
        $this->skor_akhir = round($total, 4);
        return $this->skor_akhir;
    }

    /**
     * Determine hasil based on skor_akhir and threshold
     */
    public function determineHasil(): string
    {
        if ($this->skor_akhir === null) {
            $this->calculateSkorAkhir();
        }
        
        $this->hasil = $this->meetsThreshold() ? self::HASIL_LAYAK : self::HASIL_TIDAK_LAYAK;
        return $this->hasil;
    }

    /**
     * Complete penilaian with all details
     */
    public function completePenilaian(array $nilaiInput, ?string $catatanAdmin = null): bool
    {
        // Clear existing details
        $this->details()->delete();
        
        // Create new details
        foreach ($nilaiInput as $kriteriaId => $nilai) {
            $kriteria = Kriteria::find($kriteriaId);
            if ($kriteria) {
                $this->addDetail($kriteriaId, $nilai);
            }
        }
        
        // Calculate final score
        $this->calculateSkorAkhir();
        
        // Determine result
        $this->determineHasil();
        
        // Add catatan
        if ($catatanAdmin) {
            $this->catatan_admin = $catatanAdmin;
        }
        
        return $this->save();
    }

    /**
     * Add detail to penilaian
     */
    public function addDetail(int $kriteriaId, float $nilaiInput): PenilaianDetail
    {
        $kriteria = Kriteria::findOrFail($kriteriaId);
        
        // Get min and max values for normalization
        $minValue = $kriteria->nilai_min ?? 0;
        $maxValue = $kriteria->nilai_max ?? 100;
        
        // Normalize value
        $nilaiNormalisasi = $kriteria->normalizeValue($nilaiInput, $minValue, $maxValue);
        
        // Calculate contribution score
        $skorKontribusi = $kriteria->getWeightedScore($nilaiNormalisasi);
        
        return $this->details()->create([
            'kriteria_id' => $kriteriaId,
            'nilai_input' => $nilaiInput,
            'nilai_normalisasi' => $nilaiNormalisasi,
            'bobot_snapshot' => $kriteria->bobot / 100,
            'skor_kontribusi' => $skorKontribusi,
        ]);
    }

    /**
     * Get penilaian summary
     */
    public function getSummary(): array
    {
        return [
            'skor_akhir' => $this->skor_akhir,
            'threshold' => $this->threshold,
            'hasil' => $this->hasil,
            'selisih' => $this->skor_akhir - $this->threshold,
            'status' => $this->meetsThreshold() ? 'Lolos' : 'Gagal',
            'total_kriteria' => $this->details->count(),
            'rata_rata_nilai' => $this->details->avg('nilai_input'),
            'rata_rata_normalisasi' => $this->details->avg('nilai_normalisasi'),
            'max_kontribusi' => $this->details->max('skor_kontribusi'),
            'min_kontribusi' => $this->details->min('skor_kontribusi'),
        ];
    }

    /**
     * Get breakdown by kriteria
     */
    public function getBreakdownByKriteria(): array
    {
        $breakdown = [];
        
        foreach ($this->details as $detail) {
            $breakdown[] = [
                'kriteria_id' => $detail->kriteria_id,
                'kode_kriteria' => $detail->kriteria->kode_kriteria ?? 'N/A',
                'nama_kriteria' => $detail->kriteria->nama_kriteria ?? 'N/A',
                'nilai_input' => $detail->nilai_input,
                'nilai_normalisasi' => $detail->nilai_normalisasi,
                'bobot' => $detail->bobot_snapshot * 100,
                'skor_kontribusi' => $detail->skor_kontribusi,
                'persen_kontribusi' => $this->skor_akhir > 0 
                    ? ($detail->skor_kontribusi / $this->skor_akhir) * 100 
                    : 0,
            ];
        }
        
        return $breakdown;
    }

    /**
     * Update threshold
     */
    public function updateThreshold(float $newThreshold): bool
    {
        $this->threshold = $newThreshold;
        $this->determineHasil();
        return $this->save();
    }

    /**
     * Scopes
     */
    public function scopeByPengajuan(Builder $query, int $pengajuanId): Builder
    {
        return $query->where('pengajuan_id', $pengajuanId);
    }

    public function scopeByAdmin(Builder $query, int $adminId): Builder
    {
        return $query->where('admin_id', $adminId);
    }

    public function scopeHasilLayak(Builder $query): Builder
    {
        return $query->where('hasil', self::HASIL_LAYAK);
    }

    public function scopeHasilTidakLayak(Builder $query): Builder
    {
        return $query->where('hasil', self::HASIL_TIDAK_LAYAK);
    }

    public function scopeSkorMin(Builder $query, float $minScore): Builder
    {
        return $query->where('skor_akhir', '>=', $minScore);
    }

    public function scopeSkorMax(Builder $query, float $maxScore): Builder
    {
        return $query->where('skor_akhir', '<=', $maxScore);
    }

    public function scopeLulusThreshold(Builder $query): Builder
    {
        return $query->whereRaw('skor_akhir >= threshold');
    }

    public function scopeGagalThreshold(Builder $query): Builder
    {
        return $query->whereRaw('skor_akhir < threshold');
    }

    public function scopeByDateRange(Builder $query, $startDate, $endDate): Builder
    {
        return $query->whereBetween('tgl_penilaian', [$startDate, $endDate]);
    }

    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('tgl_penilaian', today());
    }

    public function scopeThisWeek(Builder $query): Builder
    {
        return $query->whereBetween('tgl_penilaian', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    public function scopeThisMonth(Builder $query): Builder
    {
        return $query->whereMonth('tgl_penilaian', now()->month)
            ->whereYear('tgl_penilaian', now()->year);
    }

    /**
     * Get statistics for dashboard
     */
    public static function getStatistics($startDate = null, $endDate = null): array
    {
        $query = self::query();
        
        if ($startDate && $endDate) {
            $query->whereBetween('tgl_penilaian', [$startDate, $endDate]);
        }
        
        return [
            'total_penilaian' => $query->count(),
            'layak' => (clone $query)->hasilLayak()->count(),
            'tidak_layak' => (clone $query)->hasilTidakLayak()->count(),
            'persentase_kelayakan' => $query->count() > 0 
                ? ((clone $query)->hasilLayak()->count() / $query->count()) * 100 
                : 0,
            'rata_rata_skor' => $query->avg('skor_akhir'),
            'skor_tertinggi' => $query->max('skor_akhir'),
            'skor_terendah' => $query->min('skor_akhir'),
            'threshold_rata_rata' => $query->avg('threshold'),
        ];
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();
        
        // Auto set tgl_penilaian when creating
        static::creating(function ($penilaian) {
            if (!$penilaian->tgl_penilaian) {
                $penilaian->tgl_penilaian = now();
            }
        });
        
        // Update pengajuan status when penilaian is completed
        static::created(function ($penilaian) {
            $pengajuan = $penilaian->pengajuan;
            if ($pengajuan && $pengajuan->status === Pengajuan::STATUS_PENILAIAN_ADMIN) {
                $pengajuan->completePenilaian();
            }
        });
        
        // After penilaian is saved with hasil, update pengajuan
        static::saved(function ($penilaian) {
            if ($penilaian->wasChanged('hasil') && $penilaian->hasil) {
                $pengajuan = $penilaian->pengajuan;
                if ($pengajuan && $pengajuan->status === Pengajuan::STATUS_SELESAI_DINILAI) {
                    if ($penilaian->isLayak()) {
                        $pengajuan->approveBySystem();
                    } else {
                        $pengajuan->rejectBySystem();
                    }
                }
            }
        });
    }
}
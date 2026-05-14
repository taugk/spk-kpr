<?php

namespace App\Models;

use Illuminate\Database\Eloquent\{Builder, Model};

class VPenilaianDetail extends Model
{
    /**
     * Nama view yang terkait dengan model
     */
    protected $table = 'v_penilaian_detail';

    /**
     * View tidak memiliki primary key
     */
    protected $primaryKey = null;
    public $incrementing = false;

    /**
     * View tidak memiliki timestamps
     */
    public $timestamps = false;

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'penilaian_id' => 'integer',
        'pengajuan_id' => 'integer',
        'skor_akhir' => 'decimal:4',
        'threshold' => 'decimal:4',
        'nilai_input' => 'decimal:4',
        'nilai_normalisasi' => 'decimal:6',
        'bobot_snapshot' => 'decimal:4',
        'skor_kontribusi' => 'decimal:4',
        'tgl_penilaian' => 'datetime',
    ];

    /**
     * Accessors
     */
    public function getHasilBadgeAttribute(): string
    {
        $badges = [
            Penilaian::HASIL_LAYAK => '<span class="badge badge-success">Layak</span>',
            Penilaian::HASIL_TIDAK_LAYAK => '<span class="badge badge-danger">Tidak Layak</span>',
        ];

        return $badges[$this->hasil] ?? '<span class="badge badge-secondary">-</span>';
    }

    public function getTipeKriteriaBadgeAttribute(): string
    {
        $badges = [
            Kriteria::TIPE_BENEFIT => '<span class="badge badge-success">Benefit</span>',
            Kriteria::TIPE_COST => '<span class="badge badge-danger">Cost</span>',
        ];

        return $badges[$this->tipe_kriteria] ?? '<span class="badge badge-secondary">' . e($this->tipe_kriteria) . '</span>';
    }

    public function getFormattedNilaiInputAttribute(): string
    {
        return number_format($this->nilai_input, 2);
    }

    public function getFormattedNilaiNormalisasiAttribute(): string
    {
        return number_format($this->nilai_normalisasi, 4);
    }

    public function getFormattedBobotSnapshotAttribute(): string
    {
        return number_format($this->bobot_snapshot * 100, 2) . '%';
    }

    public function getFormattedSkorKontribusiAttribute(): string
    {
        return number_format($this->skor_kontribusi, 4);
    }

    public function getFormattedSkorAkhirAttribute(): string
    {
        return number_format($this->skor_akhir, 2) . '%';
    }

    public function getPersenKontribusiAttribute(): float
    {
        if ($this->skor_akhir <= 0) {
            return 0;
        }
        return ($this->skor_kontribusi / $this->skor_akhir) * 100;
    }

    public function getFormattedPersenKontribusiAttribute(): string
    {
        return number_format($this->persen_kontribusi, 2) . '%';
    }

    /**
     * Scopes
     */
    public function scopeByPenilaian($query, int $penilaianId)
    {
        return $query->where('penilaian_id', $penilaianId);
    }

    public function scopeByPengajuan($query, int $pengajuanId)
    {
        return $query->where('pengajuan_id', $pengajuanId);
    }

    public function scopeByKriteria($query, string $kodeKriteria)
    {
        return $query->where('kode_kriteria', $kodeKriteria);
    }

    public function scopeHasilLayak($query)
    {
        return $query->where('hasil', Penilaian::HASIL_LAYAK);
    }

    public function scopeHasilTidakLayak($query)
    {
        return $query->where('hasil', Penilaian::HASIL_TIDAK_LAYAK);
    }

    public function scopeBenefit($query)
    {
        return $query->where('tipe_kriteria', Kriteria::TIPE_BENEFIT);
    }

    public function scopeCost($query)
    {
        return $query->where('tipe_kriteria', Kriteria::TIPE_COST);
    }

    /**
     * Get summary for a penilaian
     */
    public static function getSummaryForPenilaian(int $penilaianId): array
    {
        $details = self::where('penilaian_id', $penilaianId)->get();

        return [
            'total_kriteria' => $details->count(),
            'skor_akhir' => $details->first()?->skor_akhir,
            'threshold' => $details->first()?->threshold,
            'hasil' => $details->first()?->hasil,
            'rata_rata_nilai_input' => $details->avg('nilai_input'),
            'rata_rata_normalisasi' => $details->avg('nilai_normalisasi'),
            'rata_rata_skor_kontribusi' => $details->avg('skor_kontribusi'),
            'kontribusi_tertinggi' => $details->sortByDesc('skor_kontribusi')->first()?->nama_kriteria,
            'kontribusi_terendah' => $details->sortBy('skor_kontribusi')->first()?->nama_kriteria,
        ];
    }
}
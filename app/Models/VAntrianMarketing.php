<?php

namespace App\Models;

use Illuminate\Database\Eloquent\{Builder, Model};

class VAntrianMarketing extends Model
{
    /**
     * Nama view yang terkait dengan model
     */
    protected $table = 'v_antrian_marketing';

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
        'pengajuan_id' => 'integer',
        'marketing_id' => 'integer',
        'jumlah_pinjaman' => 'decimal:2',
        'tenor_tahun' => 'integer',
        'tgl_submitted' => 'datetime',
    ];




    /**
     * Accessors
     */
    public function getStatusBadgeAttribute(): string
    {
        $badges = [
            Pengajuan::STATUS_SUBMITTED => '<span class="badge badge-primary">Submitted</span>',
            Pengajuan::STATUS_VERIFIKASI_MARKETING => '<span class="badge badge-info">Verifikasi Marketing</span>',
            Pengajuan::STATUS_REVISI_DEBITUR => '<span class="badge badge-warning">Revisi Debitur</span>',
        ];

        return $badges[$this->status] ?? '<span class="badge badge-secondary">' . e($this->status) . '</span>';
    }

    public function getKeputusanMarketingBadgeAttribute(): ?string
    {
        if (!$this->keputusan_marketing) {
            return '<span class="badge badge-secondary">Menunggu</span>';
        }

        $badges = [
            VerifikasiMarketing::KEPUTUSAN_AJUKAN_KE_ADMIN => '<span class="badge badge-success">Ajukan ke Admin</span>',
            VerifikasiMarketing::KEPUTUSAN_MINTA_REVISI => '<span class="badge badge-warning">Minta Revisi</span>',
            VerifikasiMarketing::KEPUTUSAN_TOLAK => '<span class="badge badge-danger">Tolak</span>',
        ];

        return $badges[$this->keputusan_marketing] ?? '<span class="badge badge-secondary">' . e($this->keputusan_marketing) . '</span>';
    }

    public function getRekomendasiMarketingBadgeAttribute(): ?string
    {
        if (!$this->rekomendasi_marketing) {
            return '<span class="badge badge-secondary">-</span>';
        }

        $badges = [
            VerifikasiMarketing::REKOMENDASI_LAYAK => '<span class="badge badge-success">Layak</span>',
            VerifikasiMarketing::REKOMENDASI_PERLU_PERTIMBANGAN => '<span class="badge badge-warning">Perlu Pertimbangan</span>',
            VerifikasiMarketing::REKOMENDASI_TIDAK_LAYAK => '<span class="badge badge-danger">Tidak Layak</span>',
        ];

        return $badges[$this->rekomendasi_marketing] ?? '<span class="badge badge-secondary">' . e($this->rekomendasi_marketing) . '</span>';
    }

    public function getFormattedJumlahPinjamanAttribute(): string
    {
        return 'Rp ' . number_format($this->jumlah_pinjaman, 0, ',', '.');
    }

    public function getFormattedTanggalAttribute(): string
    {
        return $this->tgl_submitted ? $this->tgl_submitted->format('d/m/Y H:i') : '-';
    }

    public function getTimeAgoAttribute(): string
    {
        return $this->tgl_submitted ? $this->tgl_submitted->diffForHumans() : '-';
    }

    /**
     * Scopes
     */
    public function scopeByMarketing($query, int $marketingId)
    {
        return $query->where('marketing_id', $marketingId);
    }

    public function scopeByMarketingId($query, ?int $marketingId)
    {
        if ($marketingId) {
            return $query->where('marketing_id', $marketingId);
        }
        return $query->whereNull('marketing_id');
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeSubmitted($query)
    {
        return $query->where('status', Pengajuan::STATUS_SUBMITTED);
    }

    public function scopeInVerification($query)
    {
        return $query->where('status', Pengajuan::STATUS_VERIFIKASI_MARKETING);
    }

    public function scopeNeedRevision($query)
    {
        return $query->where('status', Pengajuan::STATUS_REVISI_DEBITUR);
    }

    public function scopePendingDecision($query)
    {
        return $query->whereNull('keputusan_marketing');
    }

    public function scopeByRekomendasi($query, string $rekomendasi)
    {
        return $query->where('rekomendasi_marketing', $rekomendasi);
    }

    public function scopeSearch($query, ?string $keyword)
    {
        if (empty($keyword)) {
            return $query;
        }

        return $query->where(function ($q) use ($keyword) {
            $q->where('kode_pengajuan', 'like', "%{$keyword}%")
              ->orWhere('nama_debitur', 'like', "%{$keyword}%")
              ->orWhere('no_hp', 'like', "%{$keyword}%")
              ->orWhere('nama_proyek', 'like', "%{$keyword}%");
        });
    }

    /**
     * Get queue counts
     */
    public static function getQueueCounts(?int $marketingId = null): array
    {
        $query = self::query();

        if ($marketingId) {
            $query->where('marketing_id', $marketingId);
        }

        return [
            'total' => $query->count(),
            'submitted' => (clone $query)->submitted()->count(),
            'in_verification' => (clone $query)->inVerification()->count(),
            'need_revision' => (clone $query)->needRevision()->count(),
            'pending_decision' => (clone $query)->pendingDecision()->count(),
        ];
    }
}

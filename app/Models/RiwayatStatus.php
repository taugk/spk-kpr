<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{Builder, Model};
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

class RiwayatStatus extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terkait dengan model
     */
    protected $table = 'riwayat_status';

    /**
     * Primary key yang tidak auto-incrementing
     */
    public $incrementing = true;

    /**
     * Tipe primary key
     */
    protected $keyType = 'int';

    /**
     * Tidak menggunakan timestamps (created_at manual)
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'pengajuan_id',
        'status_lama',
        'status_baru',
        'diubah_oleh',
        'keterangan',
        'created_at',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'created_at' => 'datetime',
        'pengajuan_id' => 'integer',
        'diubah_oleh' => 'integer',
    ];

    /**
     * Relationships
     */
    public function pengajuan()
    {
        return $this->belongsTo(Pengajuan::class, 'pengajuan_id', 'id');
    }

    public function pengubah()
    {
        return $this->belongsTo(User::class, 'diubah_oleh', 'id');
    }

    /**
     * Accessors
     */
    protected function statusLamaBadge(): Attribute
    {
        return Attribute::make(
            get: function () {
                return $this->getStatusBadge($this->status_lama);
            }
        );
    }

    protected function statusBaruBadge(): Attribute
    {
        return Attribute::make(
            get: function () {
                return $this->getStatusBadge($this->status_baru);
            }
        );
    }

    protected function statusLamaText(): Attribute
    {
        return Attribute::make(
            get: function () {
                return $this->getStatusText($this->status_lama);
            }
        );
    }

    protected function statusBaruText(): Attribute
    {
        return Attribute::make(
            get: function () {
                return $this->getStatusText($this->status_baru);
            }
        );
    }

    protected function formattedCreatedAt(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->created_at) {
                    return '-';
                }

                return $this->created_at->format('d/m/Y H:i:s');
            }
        );
    }

    protected function timeAgo(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->created_at) {
                    return '-';
                }

                return $this->created_at->diffForHumans();
            }
        );
    }

    protected function tanggalOnly(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->created_at) {
                    return '-';
                }

                return $this->created_at->format('d/m/Y');
            }
        );
    }

    protected function waktuOnly(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->created_at) {
                    return '-';
                }

                return $this->created_at->format('H:i:s');
            }
        );
    }

    /**
     * Helper methods untuk status badge dan text
     */
    private function getStatusBadge(?string $status): string
    {
        if (!$status) {
            return '<span class="badge badge-secondary">-</span>';
        }

        $badges = [
            Pengajuan::STATUS_DRAFT => '<span class="badge badge-secondary">Draft</span>',
            Pengajuan::STATUS_SUBMITTED => '<span class="badge badge-primary">Submitted</span>',
            Pengajuan::STATUS_VERIFIKASI_MARKETING => '<span class="badge badge-info">Verifikasi Marketing</span>',
            Pengajuan::STATUS_REVISI_DEBITUR => '<span class="badge badge-warning">Revisi Debitur</span>',
            Pengajuan::STATUS_DITOLAK_MARKETING => '<span class="badge badge-danger">Ditolak Marketing</span>',
            Pengajuan::STATUS_ANTRIAN_ADMIN => '<span class="badge badge-secondary">Antrian Admin</span>',
            Pengajuan::STATUS_PENILAIAN_ADMIN => '<span class="badge badge-info">Penilaian Admin</span>',
            Pengajuan::STATUS_SELESAI_DINILAI => '<span class="badge badge-primary">Selesai Dinilai</span>',
            Pengajuan::STATUS_DITOLAK_SISTEM => '<span class="badge badge-danger">Ditolak Sistem</span>',
            Pengajuan::STATUS_DISETUJUI_SISTEM => '<span class="badge badge-success">Disetujui Sistem</span>',
        ];

        return $badges[$status] ?? '<span class="badge badge-secondary">' . e($status) . '</span>';
    }

    private function getStatusText(?string $status): string
    {
        if (!$status) {
            return '-';
        }

        $texts = [
            Pengajuan::STATUS_DRAFT => 'Draft',
            Pengajuan::STATUS_SUBMITTED => 'Submitted',
            Pengajuan::STATUS_VERIFIKASI_MARKETING => 'Verifikasi Marketing',
            Pengajuan::STATUS_REVISI_DEBITUR => 'Revisi Debitur',
            Pengajuan::STATUS_DITOLAK_MARKETING => 'Ditolak Marketing',
            Pengajuan::STATUS_ANTRIAN_ADMIN => 'Antrian Admin',
            Pengajuan::STATUS_PENILAIAN_ADMIN => 'Penilaian Admin',
            Pengajuan::STATUS_SELESAI_DINILAI => 'Selesai Dinilai',
            Pengajuan::STATUS_DITOLAK_SISTEM => 'Ditolak Sistem',
            Pengajuan::STATUS_DISETUJUI_SISTEM => 'Disetujui Sistem',
        ];

        return $texts[$status] ?? $status;
    }

    /**
     * Business Logic Methods
     */

    /**
     * Check if this is the first status (draft)
     */
    public function isFirstStatus(): bool
    {
        return is_null($this->status_lama);
    }

    /**
     * Check if status changed to rejected
     */
    public function isRejection(): bool
    {
        return in_array($this->status_baru, Pengajuan::getRejectedStatuses());
    }

    /**
     * Check if status changed to approved
     */
    public function isApproval(): bool
    {
        return $this->status_baru === Pengajuan::STATUS_DISETUJUI_SISTEM;
    }

    /**
     * Check if this is a revision request
     */
    public function isRevision(): bool
    {
        return $this->status_baru === Pengajuan::STATUS_REVISI_DEBITUR;
    }

    /**
     * Get duration between this status and next status
     */
    public function getDurationToNextStatus(): ?string
    {
        $nextStatus = self::where('pengajuan_id', $this->pengajuan_id)
            ->where('id', '>', $this->id)
            ->orderBy('id', 'asc')
            ->first();

        if (!$nextStatus || !$this->created_at || !$nextStatus->created_at) {
            return null;
        }

        $diff = $this->created_at->diff($nextStatus->created_at);

        $parts = [];
        if ($diff->d > 0) $parts[] = $diff->d . ' hari';
        if ($diff->h > 0) $parts[] = $diff->h . ' jam';
        if ($diff->i > 0) $parts[] = $diff->i . ' menit';

        return implode(' ', $parts) ?: 'kurang dari 1 menit';
    }

    /**
     * Get pengubah name
     */
    public function getPengubahName(): string
    {
        if (!$this->pengubah) {
            return 'Sistem';
        }

        return $this->pengubah->nama_lengkap;
    }

    /**
     * Get pengubah role
     */
    public function getPengubahRole(): ?string
    {
        if (!$this->pengubah) {
            return 'system';
        }

        return $this->pengubah->role;
    }

    /**
     * Get pengubah role label
     */
    public function getPengubahRoleLabel(): string
    {
        if (!$this->pengubah) {
            return '<span class="badge badge-secondary">Sistem</span>';
        }

        $roleLabels = [
            'debitur' => '<span class="badge badge-primary">Debitur</span>',
            'marketing' => '<span class="badge badge-info">Marketing</span>',
            'admin' => '<span class="badge badge-warning">Admin</span>',
            'manajer' => '<span class="badge badge-danger">Manajer</span>',
        ];

        return $roleLabels[$this->pengubah->role] ?? '<span class="badge badge-secondary">' . e($this->pengubah->role) . '</span>';
    }

    /**
     * Create status history entry
     */
    public static function createHistory(
        int $pengajuanId,
        string $statusBaru,
        ?string $statusLama = null,
        ?int $diubahOleh = null,
        ?string $keterangan = null
    ): self {
        return self::create([
            'pengajuan_id' => $pengajuanId,
            'status_lama' => $statusLama,
            'status_baru' => $statusBaru,
            'diubah_oleh' => $diubahOleh,
            'keterangan' => $keterangan,
            'created_at' => now(),
        ]);
    }

    /**
     * Get status transition timeline for a pengajuan
     */
    public static function getTimeline(int $pengajuanId): array
    {
        $histories = self::where('pengajuan_id', $pengajuanId)
            ->orderBy('created_at', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $timeline = [];

        foreach ($histories as $index => $history) {
            $timeline[] = [
                'id' => $history->id,
                'status_lama' => $history->status_lama,
                'status_baru' => $history->status_baru,
                'status_lama_text' => $history->status_lama_text,
                'status_baru_text' => $history->status_baru_text,
                'pengubah' => $history->getPengubahName(),
                'pengubah_role' => $history->getPengubahRole(),
                'keterangan' => $history->keterangan,
                'waktu' => $history->formatted_created_at,
                'time_ago' => $history->time_ago,
                'duration_to_next' => $history->getDurationToNextStatus(),
                'is_first' => $index === 0,
                'is_rejection' => $history->isRejection(),
                'is_approval' => $history->isApproval(),
            ];
        }

        return $timeline;
    }

    /**
     * Get total processing time from first to last status
     */
    public static function getTotalProcessingTime(int $pengajuanId): ?string
    {
        $firstStatus = self::where('pengajuan_id', $pengajuanId)
            ->orderBy('created_at', 'asc')
            ->first();

        $lastStatus = self::where('pengajuan_id', $pengajuanId)
            ->whereIn('status_baru', [
                Pengajuan::STATUS_DISETUJUI_SISTEM,
                Pengajuan::STATUS_DITOLAK_SISTEM,
                Pengajuan::STATUS_DITOLAK_MARKETING,
            ])
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$firstStatus || !$lastStatus || !$firstStatus->created_at || !$lastStatus->created_at) {
            return null;
        }

        $diff = $firstStatus->created_at->diff($lastStatus->created_at);

        $parts = [];
        if ($diff->d > 0) $parts[] = $diff->d . ' hari';
        if ($diff->h > 0) $parts[] = $diff->h . ' jam';
        if ($diff->m > 0) $parts[] = $diff->m . ' menit';

        return implode(' ', $parts) ?: 'kurang dari 1 menit';
    }

    /**
     * Get status count by period
     */
    public static function getStatusCountByPeriod($startDate, $endDate): array
    {
        $histories = self::whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $counts = [];

        foreach ($histories as $history) {
            $status = $history->status_baru;
            if (!isset($counts[$status])) {
                $counts[$status] = 0;
            }
            $counts[$status]++;
        }

        return $counts;
    }

    /**
     * Scopes
     */
    public function scopeByPengajuan(Builder $query, int $pengajuanId): Builder
    {
        return $query->where('pengajuan_id', $pengajuanId);
    }

    public function scopeByStatusBaru(Builder $query, string $status): Builder
    {
        return $query->where('status_baru', $status);
    }

    public function scopeByStatusLama(Builder $query, string $status): Builder
    {
        return $query->where('status_lama', $status);
    }

    public function scopeByPengubah(Builder $query, int $userId): Builder
    {
        return $query->where('diubah_oleh', $userId);
    }

    public function scopeByPengubahRole(Builder $query, string $role): Builder
    {
        return $query->whereHas('pengubah', function ($q) use ($role) {
            $q->where('role', $role);
        });
    }

    public function scopeByDateRange(Builder $query, $startDate, $endDate): Builder
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    public function scopeByDate(Builder $query, string $date): Builder
    {
        return $query->whereDate('created_at', $date);
    }

    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeThisWeek(Builder $query): Builder
    {
        return $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    public function scopeThisMonth(Builder $query): Builder
    {
        return $query->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year);
    }

    public function scopeRejections(Builder $query): Builder
    {
        return $query->whereIn('status_baru', Pengajuan::getRejectedStatuses());
    }

    public function scopeApprovals(Builder $query): Builder
    {
        return $query->where('status_baru', Pengajuan::STATUS_DISETUJUI_SISTEM);
    }

    public function scopeRevisions(Builder $query): Builder
    {
        return $query->where('status_baru', Pengajuan::STATUS_REVISI_DEBITUR);
    }

    public function scopeFirstStatus(Builder $query): Builder
    {
        return $query->whereNull('status_lama');
    }

    public function scopeOrderByRecent(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function scopeOrderByOldest(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'asc');
    }

    /**
     * Get latest status for a pengajuan
     */
    public static function getLatestStatus(int $pengajuanId): ?self
    {
        return self::where('pengajuan_id', $pengajuanId)
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->first();
    }

    /**
     * Check if status transition is valid
     */

    public static function isValidTransition(?string $statusLama, ?string $statusBaru): bool
{
    // Jika status baru null atau kosong, tidak valid
    if (empty($statusBaru)) {
        return false;
    }

    // Jika status lama null atau kosong, gunakan DRAFT
    if (empty($statusLama)) {
        $statusLama = Pengajuan::STATUS_DRAFT;
    }

    // Jika status sama, tidak valid
    if ($statusLama === $statusBaru) {
        return false;
    }

    $validTransitions = [
        Pengajuan::STATUS_DRAFT => [Pengajuan::STATUS_SUBMITTED],
        Pengajuan::STATUS_SUBMITTED => [Pengajuan::STATUS_VERIFIKASI_MARKETING],
        Pengajuan::STATUS_VERIFIKASI_MARKETING => [
            Pengajuan::STATUS_REVISI_DEBITUR,
            Pengajuan::STATUS_DITOLAK_MARKETING,
            Pengajuan::STATUS_ANTRIAN_ADMIN,
        ],
        Pengajuan::STATUS_REVISI_DEBITUR => [Pengajuan::STATUS_SUBMITTED],
        Pengajuan::STATUS_ANTRIAN_ADMIN => [Pengajuan::STATUS_PENILAIAN_ADMIN],
        Pengajuan::STATUS_PENILAIAN_ADMIN => [Pengajuan::STATUS_SELESAI_DINILAI],
        Pengajuan::STATUS_SELESAI_DINILAI => [
            Pengajuan::STATUS_DISETUJUI_SISTEM,
            Pengajuan::STATUS_DITOLAK_SISTEM,
        ],
    ];

    return isset($validTransitions[$statusLama]) &&
           in_array($statusBaru, $validTransitions[$statusLama]);
}
    /**
 * Boot the model
 */
protected static function boot()
{
    parent::boot();

    // Set created_at automatically if not set
    static::creating(function ($riwayat) {
        if (!$riwayat->created_at) {
            $riwayat->created_at = now();
        }
    });

    // Validate status transition before creating
    static::creating(function ($riwayat) {
        // Skip validation if this is first status (status_lama is null)
        if (is_null($riwayat->status_lama)) {
            return true;
        }

        // Ensure status_baru is not null
        if (empty($riwayat->status_baru)) {
            throw new \Exception('Status baru tidak boleh kosong');
        }

        if (!self::isValidTransition($riwayat->status_lama, $riwayat->status_baru)) {
            throw new \Exception('Transisi status tidak valid: ' .
                ($riwayat->status_lama ?? 'null') . ' -> ' . ($riwayat->status_baru ?? 'null'));
        }
    });
}
}

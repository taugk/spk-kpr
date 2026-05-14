<?php

namespace App\Models;

use Illuminate\Database\Eloquent\{Builder, Model};

class VStatistikBulanan extends Model
{
    /**
     * Nama view yang terkait dengan model
     */
    protected $table = 'v_statistik_bulanan';

    /**
     * View tidak memiliki primary key
     */
    protected $primaryKey = null;
    public $incrementing =false;

    /**
     * View tidak memiliki timestamps
     */
    public $timestamps = false;

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'total_pengajuan' => 'integer',
        'jumlah_disetujui' => 'integer',
        'jumlah_ditolak' => 'integer',
        'jumlah_proses' => 'integer',
        'rata_skor_smart' => 'decimal:2',
        'approval_rate_pct' => 'decimal:1',
    ];

    /**
     * Accessors
     */
    public function getFormattedPeriodeAttribute(): string
    {
        if (!$this->periode) {
            return '-';
        }
        
        $date = \Carbon\Carbon::createFromFormat('Y-m', $this->periode);
        return $date->format('F Y');
    }

    public function getApprovalRateBadgeAttribute(): string
    {
        $rate = $this->approval_rate_pct ?? 0;
        
        if ($rate >= 80) {
            return '<span class="badge badge-success">' . $rate . '%</span>';
        } elseif ($rate >= 60) {
            return '<span class="badge badge-warning">' . $rate . '%</span>';
        } else {
            return '<span class="badge badge-danger">' . $rate . '%</span>';
        }
    }

    /**
     * Scopes
     */
    public function scopePeriode($query, string $period)
    {
        return $query->where('periode', $period);
    }

    public function scopePeriodeYear($query, int $year)
    {
        return $query->where('periode', 'like', "{$year}-%");
    }

    public function scopePeriodeRange($query, string $start, string $end)
    {
        return $query->whereBetween('periode', [$start, $end]);
    }

    public function scopeLastMonths($query, int $months = 12)
    {
        $date = now()->subMonths($months - 1);
        return $query->where('periode', '>=', $date->format('Y-m'));
    }

    /**
     * Get monthly trend data
     */
    public static function getMonthlyTrend(int $months = 12): array
    {
        $stats = self::lastMonths($months)
            ->orderBy('periode', 'asc')
            ->get();

        return [
            'periode' => $stats->pluck('periode'),
            'total' => $stats->pluck('total_pengajuan'),
            'disetujui' => $stats->pluck('jumlah_disetujui'),
            'ditolak' => $stats->pluck('jumlah_ditolak'),
            'approval_rate' => $stats->pluck('approval_rate_pct'),
            'rata_skor' => $stats->pluck('rata_skor_smart'),
        ];
    }

    /**
     * Get summary statistics
     */
    public static function getSummaryStatistics(): array
    {
        return [
            'total_pengajuan' => self::sum('total_pengajuan'),
            'total_disetujui' => self::sum('jumlah_disetujui'),
            'total_ditolak' => self::sum('jumlah_ditolak'),
            'overall_approval_rate' => self::sum('total_pengajuan') > 0 
                ? round((self::sum('jumlah_disetujui') / self::sum('total_pengajuan')) * 100, 1)
                : 0,
            'rata_rata_skor' => round(self::avg('rata_skor_smart'), 2),
            'bulan_terbaik' => self::orderBy('approval_rate_pct', 'desc')->first()?->periode,
            'bulan_terbanyak' => self::orderBy('total_pengajuan', 'desc')->first()?->periode,
        ];
    }
}
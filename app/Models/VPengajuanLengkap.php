<?php

namespace App\Models;

use Illuminate\Database\Eloquent\{Builder, Model};

class VPengajuanLengkap extends Model
{
    /**
     * Nama view yang terkait dengan model
     */
    protected $table = 'v_pengajuan_lengkap';

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
        'harga_properti' => 'decimal:2',
        'uang_muka' => 'decimal:2',
        'persen_dp' => 'decimal:2',
        'jumlah_pinjaman' => 'decimal:2',
        'tenor_tahun' => 'integer',
        'estimasi_angsuran' => 'decimal:2',
        'rasio_angsuran' => 'decimal:2',
        'skor_akhir' => 'decimal:4',
        'tgl_submitted' => 'datetime',
        'tgl_selesai' => 'datetime',
    ];

    /**
     * Accessors for status badge
     */
    public function getStatusBadgeAttribute(): string
    {
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

        return $badges[$this->status] ?? '<span class="badge badge-secondary">' . e($this->status) . '</span>';
    }

    public function getStatusTextAttribute(): string
    {
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

        return $texts[$this->status] ?? $this->status;
    }

    public function getHasilSmartBadgeAttribute(): ?string
    {
        if (!$this->hasil_smart) {
            return '<span class="badge badge-secondary">-</span>';
        }

        $badges = [
            Penilaian::HASIL_LAYAK => '<span class="badge badge-success">Layak</span>',
            Penilaian::HASIL_TIDAK_LAYAK => '<span class="badge badge-danger">Tidak Layak</span>',
        ];

        return $badges[$this->hasil_smart] ?? '<span class="badge badge-secondary">' . e($this->hasil_smart) . '</span>';
    }

    public function getFormattedHargaAttribute(): string
    {
        return 'Rp ' . number_format($this->harga_properti, 0, ',', '.');
    }

    public function getFormattedUangMukaAttribute(): string
    {
        return 'Rp ' . number_format($this->uang_muka, 0, ',', '.');
    }

    public function getFormattedJumlahPinjamanAttribute(): string
    {
        return 'Rp ' . number_format($this->jumlah_pinjaman, 0, ',', '.');
    }

    public function getFormattedEstimasiAngsuranAttribute(): string
    {
        return $this->estimasi_angsuran ? 'Rp ' . number_format($this->estimasi_angsuran, 0, ',', '.') : '-';
    }

    public function getFormattedSkorAkhirAttribute(): string
    {
        return $this->skor_akhir ? number_format($this->skor_akhir, 2) . ' %' : '-';
    }

    /**
     * Scopes for filtering
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByDebitur($query, int $debiturId)
    {
        return $query->where('debitur_id', $debiturId);
    }

    public function scopeByMarketing($query, ?int $marketingId)
    {
        if ($marketingId) {
            return $query->where('marketing_id', $marketingId);
        }
        return $query;
    }

    public function scopeByProyek($query, string $namaProyek)
    {
        return $query->where('nama_proyek', 'like', "%{$namaProyek}%");
    }

    public function scopeSubmittedBetween($query, $startDate, $endDate)
    {
        return $query->whereBetween('tgl_submitted', [$startDate, $endDate]);
    }

    public function scopeHasilSmart($query, string $hasil)
    {
        return $query->where('hasil_smart', $hasil);
    }

    public function scopeSearch($query, ?string $keyword)
    {
        if (empty($keyword)) {
            return $query;
        }

        return $query->where(function ($q) use ($keyword) {
            $q->where('kode_pengajuan', 'like', "%{$keyword}%")
              ->orWhere('nama_debitur', 'like', "%{$keyword}%")
              ->orWhere('nik', 'like', "%{$keyword}%")
              ->orWhere('no_hp', 'like', "%{$keyword}%")
              ->orWhere('nama_proyek', 'like', "%{$keyword}%")
              ->orWhere('kode_unit', 'like', "%{$keyword}%");
        });
    }

    /**
     * Get pengajuan counts by status
     */
    public static function getCountByStatus(): array
    {
        $statuses = Pengajuan::getStatusOptions();
        $counts = [];

        foreach ($statuses as $status) {
            $counts[$status] = self::where('status', $status)->count();
        }

        return $counts;
    }

    /**
     * Get total pinjaman by status
     */
    public static function getTotalPinjamanByStatus(): array
    {
        $statuses = Pengajuan::getStatusOptions();
        $totals = [];

        foreach ($statuses as $status) {
            $totals[$status] = self::where('status', $status)->sum('jumlah_pinjaman');
        }

        return $totals;
    }
}
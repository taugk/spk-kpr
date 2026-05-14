<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{Builder, Model};
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

class Pengajuan extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terkait dengan model
     */
    protected $table = 'pengajuan';

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
        'kode_pengajuan',
        'user_id',
        'unit_id',
        'harga_properti',
        'uang_muka',
        'persen_dp',
        'jumlah_pinjaman',
        'tenor_tahun',
        'estimasi_angsuran',
        'rasio_angsuran',
        'tujuan_pembelian',
        'sumber_dp',
        'status',
        'tgl_submitted',
        'tgl_marketing_proses',
        'tgl_admin_proses',
        'tgl_selesai',
        'marketing_id',
        'admin_id',
        'catatan_debitur',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'harga_properti' => 'decimal:2',
        'uang_muka' => 'decimal:2',
        'persen_dp' => 'decimal:2',
        'jumlah_pinjaman' => 'decimal:2',
        'estimasi_angsuran' => 'decimal:2',
        'rasio_angsuran' => 'decimal:2',
        'tenor_tahun' => 'integer',
        'tgl_submitted' => 'datetime',
        'tgl_marketing_proses' => 'datetime',
        'tgl_admin_proses' => 'datetime',
        'tgl_selesai' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Status enum values
     */
    const STATUS_DRAFT = 'draft';
    const STATUS_SUBMITTED = 'submitted';
    const STATUS_VERIFIKASI_MARKETING = 'verifikasi_marketing';
    const STATUS_REVISI_DEBITUR = 'revisi_debitur';
    const STATUS_DITOLAK_MARKETING = 'ditolak_marketing';
    const STATUS_ANTRIAN_ADMIN = 'antrian_admin';
    const STATUS_PENILAIAN_ADMIN = 'penilaian_admin';
    const STATUS_SELESAI_DINILAI = 'selesai_dinilai';
    const STATUS_DITOLAK_SISTEM = 'ditolak_sistem';
    const STATUS_DISETUJUI_SISTEM = 'disetujui_sistem';

    /**
     * Tujuan pembelian enum values
     */
    const TUJUAN_HUNIAN_SENDIRI = 'hunian_sendiri';
    const TUJUAN_INVESTASI = 'investasi';

    /**
     * Sumber DP enum values
     */
    const SUMBER_DP_TABUNGAN = 'tabungan';
    const SUMBER_DP_KELUARGA = 'keluarga';
    const SUMBER_DP_JUAL_ASET = 'jual_aset';
    const SUMBER_DP_LAINNYA = 'lainnya';

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }


    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id', 'id');
    }



    public function marketing()
    {
        return $this->belongsTo(User::class, 'marketing_id', 'id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id', 'id');
    }

    public function debiturPribadi()
    {
        return $this->hasOne(DebiturPribadi::class, 'user_id', 'user_id');
    }

    public function debiturPekerjaan()
    {
        return $this->hasOne(DebiturPekerjaan::class, 'user_id', 'user_id');
    }

    public function debiturKeuangan()
    {
        return $this->hasOne(DebiturKeuangan::class, 'user_id', 'user_id');
    }

    public function dokumen(){
         return $this->hasMany(DokumenPengajuan::class, 'pengajuan_id');
    }

    public function verifikasiMarketing()
    {
        return $this->hasOne(VerifikasiMarketing::class, 'pengajuan_id', 'id');
    }

    public function riwayatStatus()
    {
        return $this->hasMany(RiwayatStatus::class, 'pengajuan_id', 'id');
    }

    /**
     * Get all status options
     */
    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_DRAFT,
            self::STATUS_SUBMITTED,
            self::STATUS_VERIFIKASI_MARKETING,
            self::STATUS_REVISI_DEBITUR,
            self::STATUS_DITOLAK_MARKETING,
            self::STATUS_ANTRIAN_ADMIN,
            self::STATUS_PENILAIAN_ADMIN,
            self::STATUS_SELESAI_DINILAI,
            self::STATUS_DITOLAK_SISTEM,
            self::STATUS_DISETUJUI_SISTEM,
        ];
    }

    /**
     * Get statuses for workflow progression
     */
    public static function getWorkflowStatuses(): array
    {
        return [
            self::STATUS_DRAFT,
            self::STATUS_SUBMITTED,
            self::STATUS_VERIFIKASI_MARKETING,
            self::STATUS_ANTRIAN_ADMIN,
            self::STATUS_PENILAIAN_ADMIN,
            self::STATUS_SELESAI_DINILAI,
            self::STATUS_DISETUJUI_SISTEM,
        ];
    }

    /**
     * Get rejected statuses
     */
    public static function getRejectedStatuses(): array
    {
        return [
            self::STATUS_DITOLAK_MARKETING,
            self::STATUS_DITOLAK_SISTEM,
        ];
    }

    /**
     * Get all tujuan pembelian options
     */
    public static function getTujuanOptions(): array
    {
        return [
            self::TUJUAN_HUNIAN_SENDIRI,
            self::TUJUAN_INVESTASI,
        ];
    }

    /**
     * Get all sumber DP options
     */
    public static function getSumberDPOptions(): array
    {
        return [
            self::SUMBER_DP_TABUNGAN,
            self::SUMBER_DP_KELUARGA,
            self::SUMBER_DP_JUAL_ASET,
            self::SUMBER_DP_LAINNYA,
        ];
    }

    /**
     * Accessors & Mutators
     */
    protected function formattedHargaProperti(): Attribute
    {
        return Attribute::make(
            get: fn() => 'Rp ' . number_format($this->harga_properti, 0, ',', '.')
        );
    }

    protected function formattedUangMuka(): Attribute
    {
        return Attribute::make(
            get: fn() => 'Rp ' . number_format($this->uang_muka, 0, ',', '.')
        );
    }

    protected function formattedJumlahPinjaman(): Attribute
    {
        return Attribute::make(
            get: fn() => 'Rp ' . number_format($this->jumlah_pinjaman, 0, ',', '.')
        );
    }

    protected function formattedEstimasiAngsuran(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->estimasi_angsuran ? 'Rp ' . number_format($this->estimasi_angsuran, 0, ',', '.') : '-'
        );
    }

    protected function statusBadge(): Attribute
    {
        return Attribute::make(
            get: function () {
                $badges = [
                    self::STATUS_DRAFT => '<span class="badge badge-secondary">Draft</span>',
                    self::STATUS_SUBMITTED => '<span class="badge badge-primary">Submitted</span>',
                    self::STATUS_VERIFIKASI_MARKETING => '<span class="badge badge-info">Verifikasi Marketing</span>',
                    self::STATUS_REVISI_DEBITUR => '<span class="badge badge-warning">Revisi Debitur</span>',
                    self::STATUS_DITOLAK_MARKETING => '<span class="badge badge-danger">Ditolak Marketing</span>',
                    self::STATUS_ANTRIAN_ADMIN => '<span class="badge badge-secondary">Antrian Admin</span>',
                    self::STATUS_PENILAIAN_ADMIN => '<span class="badge badge-info">Penilaian Admin</span>',
                    self::STATUS_SELESAI_DINILAI => '<span class="badge badge-primary">Selesai Dinilai</span>',
                    self::STATUS_DITOLAK_SISTEM => '<span class="badge badge-danger">Ditolak Sistem</span>',
                    self::STATUS_DISETUJUI_SISTEM => '<span class="badge badge-success">Disetujui Sistem</span>',
                ];

                return $badges[$this->status] ?? '<span class="badge badge-secondary">' . e($this->status) . '</span>';
            }
        );
    }

    protected function statusText(): Attribute
    {
        return Attribute::make(
            get: function () {
                $texts = [
                    self::STATUS_DRAFT => 'Draft',
                    self::STATUS_SUBMITTED => 'Submitted',
                    self::STATUS_VERIFIKASI_MARKETING => 'Verifikasi Marketing',
                    self::STATUS_REVISI_DEBITUR => 'Revisi Debitur',
                    self::STATUS_DITOLAK_MARKETING => 'Ditolak Marketing',
                    self::STATUS_ANTRIAN_ADMIN => 'Antrian Admin',
                    self::STATUS_PENILAIAN_ADMIN => 'Penilaian Admin',
                    self::STATUS_SELESAI_DINILAI => 'Selesai Dinilai',
                    self::STATUS_DITOLAK_SISTEM => 'Ditolak Sistem',
                    self::STATUS_DISETUJUI_SISTEM => 'Disetujui Sistem',
                ];

                return $texts[$this->status] ?? $this->status;
            }
        );
    }

    /**
     * Business Logic Methods
     */
    public function calculatePersenDP(): float
    {
        if ($this->harga_properti <= 0) {
            return 0;
        }

        return ($this->uang_muka / $this->harga_properti) * 100;
    }

    public function calculateJumlahPinjaman(): float
    {
        return $this->harga_properti - $this->uang_muka;
    }

    public function calculateEstimasiAngsuran(float $sukuBungaTahunan = 0.10): float
    {
        $pokok = $this->jumlah_pinjaman;
        $bulan = $this->tenor_tahun * 12;
        $sukuBungaBulanan = $sukuBungaTahunan / 12;

        if ($sukuBungaBulanan == 0) {
            return $pokok / $bulan;
        }

        $angsuran = $pokok * $sukuBungaBulanan * pow(1 + $sukuBungaBulanan, $bulan) / (pow(1 + $sukuBungaBulanan, $bulan) - 1);

        return round($angsuran, 2);
    }

    public function calculateRasioAngsuran(float $penghasilanBulanan): ?float
    {
        if ($penghasilanBulanan <= 0 || !$this->estimasi_angsuran) {
            return null;
        }

        return ($this->estimasi_angsuran / $penghasilanBulanan) * 100;
    }

    /**
     * Status transition methods
     */
    public function submit(): bool
    {
        if ($this->status !== self::STATUS_DRAFT) {
            return false;
        }

        $this->status = self::STATUS_SUBMITTED;
        $this->tgl_submitted = now();

        return $this->save();
    }

    public function startVerifikasiMarketing(int $marketingId): bool
    {
        if ($this->status !== self::STATUS_SUBMITTED) {
            return false;
        }

        $this->status = self::STATUS_VERIFIKASI_MARKETING;
        $this->marketing_id = $marketingId;
        $this->tgl_marketing_proses = now();

        return $this->save();
    }

    public function rejectByMarketing(string $catatan = null): bool
    {
        if ($this->status !== self::STATUS_VERIFIKASI_MARKETING) {
            return false;
        }

        $this->status = self::STATUS_DITOLAK_MARKETING;
        $this->catatan_debitur = $catatan;
        $this->tgl_selesai = now();

        return $this->save();
    }

    public function requestRevision(string $catatan): bool
    {
        if ($this->status !== self::STATUS_VERIFIKASI_MARKETING) {
            return false;
        }

        $this->status = self::STATUS_REVISI_DEBITUR;
        $this->catatan_debitur = $catatan;

        return $this->save();
    }

    public function submitRevision(): bool
    {
        if ($this->status !== self::STATUS_REVISI_DEBITUR) {
            return false;
        }

        $this->status = self::STATUS_SUBMITTED;
        $this->catatan_debitur = null;

        return $this->save();
    }

    public function sendToAdminQueue(): bool
    {
        if ($this->status !== self::STATUS_VERIFIKASI_MARKETING) {
            return false;
        }

        $this->status = self::STATUS_ANTRIAN_ADMIN;

        return $this->save();
    }

    public function startPenilaianAdmin(int $adminId): bool
    {
        if ($this->status !== self::STATUS_ANTRIAN_ADMIN) {
            return false;
        }

        $this->status = self::STATUS_PENILAIAN_ADMIN;
        $this->admin_id = $adminId;
        $this->tgl_admin_proses = now();

        return $this->save();
    }

    public function completePenilaian(): bool
    {
        if ($this->status !== self::STATUS_PENILAIAN_ADMIN) {
            return false;
        }

        $this->status = self::STATUS_SELESAI_DINILAI;

        return $this->save();
    }

    public function approveBySystem(): bool
    {
        if ($this->status !== self::STATUS_SELESAI_DINILAI) {
            return false;
        }

        $this->status = self::STATUS_DISETUJUI_SISTEM;
        $this->tgl_selesai = now();

        // Update unit status to sold
        if ($this->unit) {
            $this->unit->markAsSold();
        }

        return $this->save();
    }

    public function rejectBySystem(): bool
    {
        if ($this->status !== self::STATUS_SELESAI_DINILAI) {
            return false;
        }

        $this->status = self::STATUS_DITOLAK_SISTEM;
        $this->tgl_selesai = now();

        return $this->save();
    }

    /**
     * Check methods
     */
    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isSubmitted(): bool
    {
        return $this->status === self::STATUS_SUBMITTED;
    }

    public function isInVerification(): bool
    {
        return in_array($this->status, [
            self::STATUS_VERIFIKASI_MARKETING,
            self::STATUS_PENILAIAN_ADMIN,
        ]);
    }

    public function isRejected(): bool
    {
        return in_array($this->status, self::getRejectedStatuses());
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_DISETUJUI_SISTEM;
    }

    public function isCompleted(): bool
    {
        return in_array($this->status, [
            self::STATUS_DISETUJUI_SISTEM,
            self::STATUS_DITOLAK_SISTEM,
            self::STATUS_DITOLAK_MARKETING,
        ]);
    }

    /**
     * Get processing time in days
     */
    public function getProcessingTime(): ?int
    {
        if (!$this->tgl_submitted || !$this->tgl_selesai) {
            return null;
        }

        return $this->tgl_submitted->diffInDays($this->tgl_selesai);
    }

    /**
     * Get current workflow step (1-based)
     */
    public function getCurrentStep(): int
    {
        $steps = [
            self::STATUS_DRAFT => 1,
            self::STATUS_SUBMITTED => 2,
            self::STATUS_VERIFIKASI_MARKETING => 3,
            self::STATUS_REVISI_DEBITUR => 3,
            self::STATUS_DITOLAK_MARKETING => 0,
            self::STATUS_ANTRIAN_ADMIN => 4,
            self::STATUS_PENILAIAN_ADMIN => 5,
            self::STATUS_SELESAI_DINILAI => 6,
            self::STATUS_DITOLAK_SISTEM => 0,
            self::STATUS_DISETUJUI_SISTEM => 7,
        ];

        return $steps[$this->status] ?? 0;
    }

    /**
     * Scopes
     */
    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    public function scopeByUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByMarketing(Builder $query, int $marketingId): Builder
    {
        return $query->where('marketing_id', $marketingId);
    }

    public function scopeByAdmin(Builder $query, int $adminId): Builder
    {
        return $query->where('admin_id', $adminId);
    }

    public function scopeSubmitted(Builder $query): Builder
    {
        return $query->whereIn('status', [
            self::STATUS_SUBMITTED,
            self::STATUS_VERIFIKASI_MARKETING,
            self::STATUS_ANTRIAN_ADMIN,
            self::STATUS_PENILAIAN_ADMIN,
            self::STATUS_SELESAI_DINILAI,
        ]);
    }

    public function scopePendingMarketing(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_SUBMITTED);
    }

    public function scopePendingAdmin(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ANTRIAN_ADMIN);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_DISETUJUI_SISTEM);
    }

    public function scopeRejected(Builder $query): Builder
    {
        return $query->whereIn('status', self::getRejectedStatuses());
    }

    public function scopeTanggalBetween(Builder $query, $startDate, $endDate): Builder
    {
        return $query->whereBetween('tgl_submitted', [$startDate, $endDate]);
    }

    public function scopeSearch(Builder $query, ?string $keyword): Builder
    {
        if (empty($keyword)) {
            return $query;
        }

        return $query->where(function ($q) use ($keyword) {
            $q->where('kode_pengajuan', 'like', "%{$keyword}%")
              ->orWhereHas('user', fn($sub) => $sub->where('nama_lengkap', 'like', "%{$keyword}%"))
              ->orWhereHas('unit', fn($sub) => $sub->where('kode_unit', 'like', "%{$keyword}%"));
        });
    }

    /**
     * Generate unique kode_pengajuan
     */
    public static function generateKodePengajuan(): string
    {
        $prefix = 'AJU';
        $year = date('Y');
        $month = date('m');

        $lastPengajuan = self::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastPengajuan) {
            $lastNumber = (int) substr($lastPengajuan->kode_pengajuan, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return "{$prefix}-{$year}{$month}-{$newNumber}";
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate kode_pengajuan before creating
        static::creating(function ($pengajuan) {
            if (empty($pengajuan->kode_pengajuan)) {
                $pengajuan->kode_pengajuan = self::generateKodePengajuan();
            }

            // Auto-calculate persen DP
            if ($pengajuan->harga_properti > 0 && $pengajuan->uang_muka > 0) {
                $pengajuan->persen_dp = ($pengajuan->uang_muka / $pengajuan->harga_properti) * 100;
            }

            // Auto-calculate jumlah pinjaman
            if ($pengajuan->harga_properti > 0 && $pengajuan->uang_muka > 0) {
                $pengajuan->jumlah_pinjaman = $pengajuan->harga_properti - $pengajuan->uang_muka;
            }
        });

        // Auto-update related calculations before update
        static::updating(function ($pengajuan) {
            if ($pengajuan->isDirty('harga_properti') || $pengajuan->isDirty('uang_muka')) {
                if ($pengajuan->harga_properti > 0) {
                    $pengajuan->persen_dp = ($pengajuan->uang_muka / $pengajuan->harga_properti) * 100;
                    $pengajuan->jumlah_pinjaman = $pengajuan->harga_properti - $pengajuan->uang_muka;
                }
            }
        });
    }
}

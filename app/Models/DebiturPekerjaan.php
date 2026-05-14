<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class DebiturPekerjaan extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terkait dengan model
     */
    protected $table = 'debitur_pekerjaan';

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
        'user_id',
        'status_pekerjaan',
        'nama_perusahaan',
        'bidang_usaha',
        'jabatan',
        'status_kepegawaian',
        'lama_bekerja_tahun',
        'lama_bekerja_bulan',
        'alamat_perusahaan',
        'kota_perusahaan',
        'telp_perusahaan',
        'npwp',
        'penghasilan_pokok',
        'tunjangan_tetap',
        'penghasilan_lain',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'lama_bekerja_tahun' => 'integer',
        'lama_bekerja_bulan' => 'integer',
        'penghasilan_pokok' => 'decimal:2',
        'tunjangan_tetap' => 'decimal:2',
        'penghasilan_lain' => 'decimal:2',
        'total_penghasilan' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Enum values untuk status_pekerjaan
     */
    const STATUS_PEKERJAAN_KARYAWAN_SWASTA = 'karyawan_swasta';
    const STATUS_PEKERJAAN_KARYAWAN_BUMN = 'karyawan_bumn';
    const STATUS_PEKERJAAN_PNS = 'pns';
    const STATUS_PEKERJAAN_TNI_POLRI = 'tni_polri';
    const STATUS_PEKERJAAN_WIRASWASTA = 'wiraswasta';
    const STATUS_PEKERJAAN_PROFESIONAL = 'profesional';
    const STATUS_PEKERJAAN_LAINNYA = 'lainnya';

    /**
     * Enum values untuk status_kepegawaian
     */
    const STATUS_KEPEGAWAIAN_TETAP = 'tetap';
    const STATUS_KEPEGAWAIAN_KONTRAK = 'kontrak';
    const STATUS_KEPEGAWAIAN_PERCOBAAN = 'percobaan';
    const STATUS_KEPEGAWAIAN_PEMILIK = 'pemilik';

    /**
     * Relationship dengan User
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Relationship dengan DebiturPribadi
     */
    public function debiturPribadi()
    {
        return $this->hasOne(DebiturPribadi::class, 'user_id', 'user_id');
    }

    /**
     * Get all status pekerjaan options
     */
    public static function getStatusPekerjaanOptions(): array
    {
        return [
            self::STATUS_PEKERJAAN_KARYAWAN_SWASTA,
            self::STATUS_PEKERJAAN_KARYAWAN_BUMN,
            self::STATUS_PEKERJAAN_PNS,
            self::STATUS_PEKERJAAN_TNI_POLRI,
            self::STATUS_PEKERJAAN_WIRASWASTA,
            self::STATUS_PEKERJAAN_PROFESIONAL,
            self::STATUS_PEKERJAAN_LAINNYA,
        ];
    }

    /**
     * Get all status kepegawaian options
     */
    public static function getStatusKepegawaianOptions(): array
    {
        return [
            self::STATUS_KEPEGAWAIAN_TETAP,
            self::STATUS_KEPEGAWAIAN_KONTRAK,
            self::STATUS_KEPEGAWAIAN_PERCOBAAN,
            self::STATUS_KEPEGAWAIAN_PEMILIK,
        ];
    }

    /**
     * Check if employee is permanent
     */
    public function isPermanent(): bool
    {
        return $this->status_kepegawaian === self::STATUS_KEPEGAWAIAN_TETAP;
    }

    /**
     * Check if government employee (PNS or TNI/POLRI)
     */
    public function isGovernmentEmployee(): bool
    {
        return in_array($this->status_pekerjaan, [
            self::STATUS_PEKERJAAN_PNS,
            self::STATUS_PEKERJAAN_TNI_POLRI,
        ]);
    }

    /**
     * Check if private employee
     */
    public function isPrivateEmployee(): bool
    {
        return in_array($this->status_pekerjaan, [
            self::STATUS_PEKERJAAN_KARYAWAN_SWASTA,
            self::STATUS_PEKERJAAN_KARYAWAN_BUMN,
        ]);
    }

    /**
     * Get total lama bekerja in months
     */
    public function getTotalLamaBekerjaBulan(): int
    {
        return ($this->lama_bekerja_tahun * 12) + $this->lama_bekerja_bulan;
    }

    /**
     * Get formatted total penghasilan
     */
    public function getFormattedTotalPenghasilan(): string
    {
        return 'Rp ' . number_format($this->total_penghasilan, 0, ',', '.');
    }

    /**
     * Get formatted penghasilan pokok
     */
    public function getFormattedPenghasilanPokok(): string
    {
        return 'Rp ' . number_format($this->penghasilan_pokok, 0, ',', '.');
    }

    /**
     * Get complete alamat perusahaan
     */
    public function getCompleteAlamatPerusahaan(): string
    {
        $address = $this->alamat_perusahaan ?? '';
        
        if ($this->kota_perusahaan) {
            $address .= ($address ? ', ' : '') . $this->kota_perusahaan;
        }
        
        return $address;
    }

    /**
     * Mutator untuk penghasilan_pokok (auto convert ke decimal)
     */
    protected function penghasilanPokok(): Attribute
    {
        return Attribute::make(
            set: fn($value) => str_replace(['Rp', ' ', '.', ','], '', $value)
        );
    }

    /**
     * Mutator untuk tunjangan_tetap
     */
    protected function tunjanganTetap(): Attribute
    {
        return Attribute::make(
            set: fn($value) => str_replace(['Rp', ' ', '.', ','], '', $value)
        );
    }

    /**
     * Mutator untuk penghasilan_lain
     */
    protected function penghasilanLain(): Attribute
    {
        return Attribute::make(
            set: fn($value) => str_replace(['Rp', ' ', '.', ','], '', $value)
        );
    }

    /**
     * Accessor untuk total_penghasilan (manual calculation jika perlu)
     */
    protected function totalPenghasilan(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value ?? ($this->penghasilan_pokok + $this->tunjangan_tetap + $this->penghasilan_lain)
        );
    }

    /**
     * Scope untuk karyawan tetap
     */
    public function scopeKaryawanTetap($query)
    {
        return $query->where('status_kepegawaian', self::STATUS_KEPEGAWAIAN_TETAP);
    }

    /**
     * Scope untuk PNS
     */
    public function scopePNS($query)
    {
        return $query->where('status_pekerjaan', self::STATUS_PEKERJAAN_PNS);
    }

    /**
     * Scope untuk wiraswasta
     */
    public function scopeWiraswasta($query)
    {
        return $query->where('status_pekerjaan', self::STATUS_PEKERJAAN_WIRASWASTA);
    }

    /**
     * Scope untuk penghasilan di atas nominal tertentu
     */
    public function scopePenghasilanMin($query, float $nominal)
    {
        return $query->where('total_penghasilan', '>=', $nominal);
    }

    /**
     * Scope untuk lama bekerja minimal (dalam tahun)
     */
    public function scopeMinLamaBekerja($query, int $tahun)
    {
        return $query->where('lama_bekerja_tahun', '>=', $tahun);
    }

    /**
     * Scope yang memiliki NPWP
     */
    public function scopeMemilikiNPWP($query)
    {
        return $query->whereNotNull('npwp')->where('npwp', '!=', '');
    }

    /**
     * Check if has NPWP
     */
    public function hasNPWP(): bool
    {
        return !empty($this->npwp);
    }

    /**
     * Validate NPWP format (15/16 digits)
     */
    public function isValidNPWP(): bool
    {
        if (empty($this->npwp)) {
            return false;
        }
        
        $cleanNPWP = preg_replace('/[^0-9]/', '', $this->npwp);
        return strlen($cleanNPWP) === 15 || strlen($cleanNPWP) === 16;
    }

    /**
     * Get monthly income for credit calculation
     */
    public function getMonthlyIncome(): float
    {
        return (float) $this->total_penghasilan;
    }

    /**
     * Get annual income
     */
    public function getAnnualIncome(): float
    {
        return $this->getMonthlyIncome() * 12;
    }

    /**
     * Check if income meets minimum requirement
     */
    public function meetsMinimumIncome(float $minimum): bool
    {
        return $this->getMonthlyIncome() >= $minimum;
    }
}
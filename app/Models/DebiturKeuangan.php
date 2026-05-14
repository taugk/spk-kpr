<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class DebiturKeuangan extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terkait dengan model
     */
    protected $table = 'debitur_keuangan';

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
        'nama_bank',
        'nomor_rekening',
        'nama_pemilik_rekening',
        'jenis_rekening',
        'rata_saldo_3bln',
        'rata_mutasi_kredit',
        'total_cicilan_perbulan',
        'jumlah_kredit_aktif',
        'limit_kartu_kredit',
        'tagihan_kartu_kredit',
        'memiliki_kpr_aktif',
        'sisa_pokok_kpr_aktif',
        'status_kredit',
        'pernah_gagal_bayar',
        'aset_properti_lain',
        'aset_kendaraan',
        'aset_tabungan_deposito',
        'aset_investasi_lain',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'rata_saldo_3bln' => 'decimal:2',
        'rata_mutasi_kredit' => 'decimal:2',
        'total_cicilan_perbulan' => 'decimal:2',
        'jumlah_kredit_aktif' => 'integer',
        'limit_kartu_kredit' => 'decimal:2',
        'tagihan_kartu_kredit' => 'decimal:2',
        'memiliki_kpr_aktif' => 'boolean',
        'sisa_pokok_kpr_aktif' => 'decimal:2',
        'pernah_gagal_bayar' => 'boolean',
        'aset_properti_lain' => 'decimal:2',
        'aset_kendaraan' => 'decimal:2',
        'aset_tabungan_deposito' => 'decimal:2',
        'aset_investasi_lain' => 'decimal:2',
        'rasio_cicilan' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Enum values untuk jenis_rekening
     */
    const JENIS_REKENING_TABUNGAN = 'tabungan';
    const JENIS_REKENING_GIRO = 'giro';
    const JENIS_REKENING_DEPOSITO = 'deposito';

    /**
     * Enum values untuk status_kredit
     */
    const STATUS_KREDIT_LANCAR = 'lancar';
    const STATUS_KREDIT_DPK = 'dpk';
    const STATUS_KREDIT_KURANG_LANCAR = 'kurang_lancar';
    const STATUS_KREDIT_DIRAGUKAN = 'diragukan';
    const STATUS_KREDIT_MACET = 'macet';

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
     * Relationship dengan DebiturPekerjaan
     */
    public function debiturPekerjaan()
    {
        return $this->hasOne(DebiturPekerjaan::class, 'user_id', 'user_id');
    }

    /**
     * Get all jenis rekening options
     */
    public static function getJenisRekeningOptions(): array
    {
        return [
            self::JENIS_REKENING_TABUNGAN,
            self::JENIS_REKENING_GIRO,
            self::JENIS_REKENING_DEPOSITO,
        ];
    }

    /**
     * Get all status kredit options
     */
    public static function getStatusKreditOptions(): array
    {
        return [
            self::STATUS_KREDIT_LANCAR,
            self::STATUS_KREDIT_DPK,
            self::STATUS_KREDIT_KURANG_LANCAR,
            self::STATUS_KREDIT_DIRAGUKAN,
            self::STATUS_KREDIT_MACET,
        ];
    }

    /**
     * Get total aset
     */
    public function getTotalAset(): float
    {
        return $this->aset_properti_lain + 
               $this->aset_kendaraan + 
               $this->aset_tabungan_deposito + 
               $this->aset_investasi_lain;
    }

    /**
     * Get total utang per bulan
     */
    public function getTotalUtangPerBulan(): float
    {
        $kprMonthly = $this->memiliki_kpr_aktif ? ($this->sisa_pokok_kpr_aktif / 12) : 0;
        return $this->total_cicilan_perbulan + $this->tagihan_kartu_kredit + $kprMonthly;
    }

    /**
     * Check if credit is healthy (lancar or dpk)
     */
    public function isCreditHealthy(): bool
    {
        return in_array($this->status_kredit, [
            self::STATUS_KREDIT_LANCAR,
            self::STATUS_KREDIT_DPK,
        ]);
    }

    /**
     * Check if credit is problematic
     */
    public function isCreditProblematic(): bool
    {
        return in_array($this->status_kredit, [
            self::STATUS_KREDIT_KURANG_LANCAR,
            self::STATUS_KREDIT_DIRAGUKAN,
            self::STATUS_KREDIT_MACET,
        ]);
    }

    /**
     * Get formatted rata saldo 3 bulan
     */
    public function getFormattedRataSaldo(): string
    {
        return 'Rp ' . number_format($this->rata_saldo_3bln, 0, ',', '.');
    }

    /**
     * Get formatted total aset
     */
    public function getFormattedTotalAset(): string
    {
        return 'Rp ' . number_format($this->getTotalAset(), 0, ',', '.');
    }

    /**
     * Get formatted total cicilan per bulan
     */
    public function getFormattedTotalCicilan(): string
    {
        return 'Rp ' . number_format($this->total_cicilan_perbulan, 0, ',', '.');
    }

    /**
     * Calculate debt-to-income ratio (DTI)
     */
    public function calculateDebtToIncomeRatio(float $monthlyIncome): float
    {
        if ($monthlyIncome <= 0) {
            return 0;
        }
        
        return ($this->getTotalUtangPerBulan() / $monthlyIncome) * 100;
    }

    /**
     * Check if debt-to-income ratio is acceptable (max 40%)
     */
    public function isDebtToIncomeAcceptable(float $monthlyIncome): bool
    {
        $dti = $this->calculateDebtToIncomeRatio($monthlyIncome);
        return $dti <= 40;
    }

    /**
     * Get credit score based on financial health
     */
    public function getCreditScore(): int
    {
        $score = 700; // Base score
        
        // Adjust based on credit status
        switch ($this->status_kredit) {
            case self::STATUS_KREDIT_LANCAR:
                $score += 100;
                break;
            case self::STATUS_KREDIT_DPK:
                $score += 50;
                break;
            case self::STATUS_KREDIT_KURANG_LANCAR:
                $score -= 50;
                break;
            case self::STATUS_KREDIT_DIRAGUKAN:
                $score -= 100;
                break;
            case self::STATUS_KREDIT_MACET:
                $score -= 200;
                break;
        }
        
        // Adjust based on payment history
        if ($this->pernah_gagal_bayar) {
            $score -= 100;
        }
        
        // Adjust based on active credits
        if ($this->jumlah_kredit_aktif > 3) {
            $score -= 50;
        }
        
        // Adjust based on savings
        if ($this->rata_saldo_3bln > 10000000) {
            $score += 50;
        }
        
        // Ensure score is between 0 and 1000
        return max(0, min(1000, $score));
    }

    /**
     * Get credit rating based on score
     */
    public function getCreditRating(): string
    {
        $score = $this->getCreditScore();
        
        if ($score >= 800) {
            return 'Sangat Baik';
        } elseif ($score >= 700) {
            return 'Baik';
        } elseif ($score >= 600) {
            return 'Cukup';
        } elseif ($score >= 500) {
            return 'Kurang';
        } else {
            return 'Buruk';
        }
    }

    /**
     * Mutator for rupiah fields
     */
    protected function rataSaldo3bln(): Attribute
    {
        return Attribute::make(
            set: fn($value) => $this->cleanRupiah($value)
        );
    }

    protected function rataMutasiKredit(): Attribute
    {
        return Attribute::make(
            set: fn($value) => $this->cleanRupiah($value)
        );
    }

    protected function totalCicilanPerbulan(): Attribute
    {
        return Attribute::make(
            set: fn($value) => $this->cleanRupiah($value)
        );
    }

    protected function limitKartuKredit(): Attribute
    {
        return Attribute::make(
            set: fn($value) => $this->cleanRupiah($value)
        );
    }

    protected function tagihanKartuKredit(): Attribute
    {
        return Attribute::make(
            set: fn($value) => $this->cleanRupiah($value)
        );
    }

    protected function sisaPokokKprAktif(): Attribute
    {
        return Attribute::make(
            set: fn($value) => $this->cleanRupiah($value)
        );
    }

    protected function asetPropertiLain(): Attribute
    {
        return Attribute::make(
            set: fn($value) => $this->cleanRupiah($value)
        );
    }

    protected function asetKendaraan(): Attribute
    {
        return Attribute::make(
            set: fn($value) => $this->cleanRupiah($value)
        );
    }

    protected function asetTabunganDeposito(): Attribute
    {
        return Attribute::make(
            set: fn($value) => $this->cleanRupiah($value)
        );
    }

    protected function asetInvestasiLain(): Attribute
    {
        return Attribute::make(
            set: fn($value) => $this->cleanRupiah($value)
        );
    }

    /**
     * Clean rupiah format to numeric
     */
    private function cleanRupiah($value): float
    {
        if (is_numeric($value)) {
            return (float) $value;
        }
        
        return (float) str_replace(['Rp', ' ', '.', ','], '', $value);
    }

    /**
     * Scope untuk debitur dengan kredit lancar
     */
    public function scopeKreditLancar($query)
    {
        return $query->where('status_kredit', self::STATUS_KREDIT_LANCAR);
    }

    /**
     * Scope untuk debitur dengan status kredit bermasalah
     */
    public function scopeKreditBermasalah($query)
    {
        return $query->whereIn('status_kredit', [
            self::STATUS_KREDIT_KURANG_LANCAR,
            self::STATUS_KREDIT_DIRAGUKAN,
            self::STATUS_KREDIT_MACET,
        ]);
    }

    /**
     * Scope untuk debitur yang memiliki KPR aktif
     */
    public function scopeMemilikiKPR($query)
    {
        return $query->where('memiliki_kpr_aktif', true);
    }

    /**
     * Scope untuk debitur dengan saldo rata-rata di atas nominal
     */
    public function scopeRataSaldoMin($query, float $nominal)
    {
        return $query->where('rata_saldo_3bln', '>=', $nominal);
    }

    /**
     * Scope untuk debitur yang pernah gagal bayar
     */
    public function scopePernahGagalBayar($query)
    {
        return $query->where('pernah_gagal_bayar', true);
    }

    /**
     * Scope untuk debitur dengan total aset di atas nominal
     */
    public function scopeTotalAsetMin($query, float $nominal)
    {
        return $query->whereRaw('(aset_properti_lain + aset_kendaraan + aset_tabungan_deposito + aset_investasi_lain) >= ?', [$nominal]);
    }

    /**
     * Update rasio cicilan based on income
     */
    public function updateRasioCicilan(float $monthlyIncome): void
    {
        if ($monthlyIncome > 0) {
            $this->rasio_cicilan = ($this->getTotalUtangPerBulan() / $monthlyIncome) * 100;
            $this->saveQuietly();
        }
    }
}
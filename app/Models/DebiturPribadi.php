<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class DebiturPribadi extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terkait dengan model
     */
    protected $table = 'debitur_pribadi';

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
        'nik',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'agama',
        'status_pernikahan',
        'jumlah_tanggungan',
        'pendidikan_terakhir',
        'kewarganegaraan',
        'nama_ibu_kandung',
        'no_kk',
        'nama_pasangan',
        'nik_pasangan',
        'alamat_ktp',
        'rt_rw',
        'kelurahan',
        'kecamatan',
        'kota',
        'provinsi',
        'kode_pos',
        'status_tempat_tinggal',
        'no_telepon',
        'no_hp',
        'email_aktif',
        'pas_foto',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'tanggal_lahir' => 'date',
        'jumlah_tanggungan' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Enum values untuk jenis_kelamin
     */
    const JENIS_KELAMIN_L = 'L';
    const JENIS_KELAMIN_P = 'P';

    /**
     * Enum values untuk agama
     */
    const AGAMA_ISLAM = 'Islam';
    const AGAMA_KRISTEN = 'Kristen';
    const AGAMA_KATOLIK = 'Katolik';
    const AGAMA_HINDU = 'Hindu';
    const AGAMA_BUDDHA = 'Buddha';
    const AGAMA_KONGHUCU = 'Konghucu';
    const AGAMA_LAINNYA = 'Lainnya';

    /**
     * Enum values untuk status_pernikahan
     */
    const STATUS_MENIKAH_BELUM = 'belum_menikah';
    const STATUS_MENIKAH = 'menikah';
    const STATUS_CERAI = 'cerai';

    /**
     * Enum values untuk pendidikan_terakhir
     */
    const PENDIDIKAN_SD = 'SD';
    const PENDIDIKAN_SMP = 'SMP';
    const PENDIDIKAN_SMA = 'SMA';
    const PENDIDIKAN_D1 = 'D1';
    const PENDIDIKAN_D2 = 'D2';
    const PENDIDIKAN_D3 = 'D3';
    const PENDIDIKAN_S1 = 'S1';
    const PENDIDIKAN_S2 = 'S2';
    const PENDIDIKAN_S3 = 'S3';

    /**
     * Enum values untuk kewarganegaraan
     */
    const KEWARGANEGARAAN_WNI = 'WNI';
    const KEWARGANEGARAAN_WNA = 'WNA';

    /**
     * Enum values untuk status_tempat_tinggal
     */
    const STATUS_TEMPAT_MILIK_SENDIRI = 'milik_sendiri';
    const STATUS_TEMPAT_SEWA = 'sewa';
    const STATUS_TEMPAT_KELUARGA = 'keluarga';
    const STATUS_TEMPAT_LAINNYA = 'lainnya';

    /**
     * Relationship dengan User
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Get all agama options
     */
    public static function getAgamaOptions(): array
    {
        return [
            self::AGAMA_ISLAM,
            self::AGAMA_KRISTEN,
            self::AGAMA_KATOLIK,
            self::AGAMA_HINDU,
            self::AGAMA_BUDDHA,
            self::AGAMA_KONGHUCU,
            self::AGAMA_LAINNYA,
        ];
    }

    /**
     * Get all status pernikahan options
     */
    public static function getStatusPernikahanOptions(): array
    {
        return [
            self::STATUS_MENIKAH_BELUM,
            self::STATUS_MENIKAH,
            self::STATUS_CERAI,
        ];
    }

    /**
     * Get all pendidikan options
     */
    public static function getPendidikanOptions(): array
    {
        return [
            self::PENDIDIKAN_SD,
            self::PENDIDIKAN_SMP,
            self::PENDIDIKAN_SMA,
            self::PENDIDIKAN_D1,
            self::PENDIDIKAN_D2,
            self::PENDIDIKAN_D3,
            self::PENDIDIKAN_S1,
            self::PENDIDIKAN_S2,
            self::PENDIDIKAN_S3,
        ];
    }

    /**
     * Get all status tempat tinggal options
     */
    public static function getStatusTempatTinggalOptions(): array
    {
        return [
            self::STATUS_TEMPAT_MILIK_SENDIRI,
            self::STATUS_TEMPAT_SEWA,
            self::STATUS_TEMPAT_KELUARGA,
            self::STATUS_TEMPAT_LAINNYA,
        ];
    }

    /**
     * Check if married
     */
    public function isMarried(): bool
    {
        return $this->status_pernikahan === self::STATUS_MENIKAH;
    }

    /**
     * Check if has spouse data
     */
    public function hasSpouse(): bool
    {
        return $this->nama_pasangan !== null || $this->nik_pasangan !== null;
    }

    /**
     * Get complete address
     */
    public function getCompleteAddress(): string
    {
        $address = $this->alamat_ktp;
        
        if ($this->rt_rw) {
            $address .= " RT/RW: " . $this->rt_rw;
        }
        
        $address .= ", {$this->kelurahan}, {$this->kecamatan}, {$this->kota}, {$this->provinsi}";
        
        if ($this->kode_pos) {
            $address .= " - " . $this->kode_pos;
        }
        
        return $address;
    }

    /**
     * Accessor untuk pas_foto
     */
    protected function pasFoto(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? asset('storage/' . $value) : null,
        );
    }

    /**
     * Scope untuk debitur dengan jenis kelamin tertentu
     */
    public function scopeJenisKelamin($query, string $jenisKelamin)
    {
        return $query->where('jenis_kelamin', $jenisKelamin);
    }

    /**
     * Scope untuk debitur laki-laki
     */
    public function scopeLakiLaki($query)
    {
        return $query->where('jenis_kelamin', self::JENIS_KELAMIN_L);
    }

    /**
     * Scope untuk debitur perempuan
     */
    public function scopePerempuan($query)
    {
        return $query->where('jenis_kelamin', self::JENIS_KELAMIN_P);
    }

    /**
     * Scope untuk debitur menikah
     */
    public function scopeMenikah($query)
    {
        return $query->where('status_pernikahan', self::STATUS_MENIKAH);
    }

    /**
     * Scope untuk debitur dengan pendidikan minimal tertentu
     */
    public function scopeMinPendidikan($query, string $pendidikan)
    {
        $levels = array_flip(self::getPendidikanOptions());
        $minLevel = $levels[$pendidikan] ?? 0;
        
        return $query->whereIn('pendidikan_terakhir', array_keys(array_filter($levels, fn($level) => $level >= $minLevel)));
    }

    /**
     * Get usia debitur
     */
    public function getUsiaAttribute(): ?int
    {
        if (!$this->tanggal_lahir) {
            return null;
        }
        
        return $this->tanggal_lahir->age;
    }

    /**
     * Check if NIK is valid (16 digits)
     */
    public function isValidNIK(): bool
    {
        return preg_match('/^[0-9]{16}$/', $this->nik) === 1;
    }

    /**
     * Check if KK number is valid (16 digits)
     */
    public function isValidKK(): bool
    {
        return !$this->no_kk || preg_match('/^[0-9]{16}$/', $this->no_kk) === 1;
    }
}
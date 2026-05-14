<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{Builder, Model};
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Cache;

class Pengaturan extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terkait dengan model
     */
    protected $table = 'pengaturan';

    /**
     * Primary key yang tidak auto-incrementing
     */
    public $incrementing = true;

    /**
     * Tipe primary key
     */
    protected $keyType = 'int';

    /**
     * Tidak menggunakan timestamps (hanya updated_at)
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'kunci',
        'nilai',
        'keterangan',
        'updated_at',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'updated_at' => 'datetime',
    ];

    /**
     * Cache key for settings
     */
    const CACHE_KEY = 'app_settings';
    const CACHE_TTL = 3600; // 1 hour

    /**
     * Accessors
     */
    protected function formattedUpdatedAt(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->updated_at ? $this->updated_at->format('d/m/Y H:i:s') : '-'
        );
    }

    /**
     * Business Logic Methods
     */
    
    /**
     * Get setting value by key
     */
    public static function get(string $key, $default = null)
    {
        $setting = self::where('kunci', $key)->first();
        
        if (!$setting) {
            return $default;
        }
        
        $value = $setting->nilai;
        
        // Try to decode JSON if it's a JSON string
        $decoded = json_decode($value, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $decoded;
        }
        
        // Convert boolean strings
        if ($value === 'true') return true;
        if ($value === 'false') return false;
        if ($value === 'null') return null;
        
        // Convert numeric values
        if (is_numeric($value)) {
            return strpos($value, '.') !== false ? (float) $value : (int) $value;
        }
        
        return $value;
    }

    /**
     * Set setting value
     */
    public static function set(string $key, $value, ?string $keterangan = null): self
    {
        // Convert value to JSON if array/object
        if (is_array($value) || is_object($value)) {
            $value = json_encode($value);
        } elseif (is_bool($value)) {
            $value = $value ? 'true' : 'false';
        } elseif (is_null($value)) {
            $value = 'null';
        } else {
            $value = (string) $value;
        }
        
        $setting = self::updateOrCreate(
            ['kunci' => $key],
            [
                'nilai' => $value,
                'keterangan' => $keterangan,
                'updated_at' => now(),
            ]
        );
        
        // Clear cache
        self::clearCache();
        
        return $setting;
    }

    /**
     * Get all settings as key-value array
     */
    public static function getAll(): array
    {
        $settings = self::all();
        $result = [];
        
        foreach ($settings as $setting) {
            $result[$setting->kunci] = self::get($setting->kunci);
        }
        
        return $result;
    }

    /**
     * Get all settings with cache
     */
    public static function getAllCached(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            return self::getAll();
        });
    }

    /**
     * Clear settings cache
     */
    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Check if setting exists
     */
    public static function has(string $key): bool
    {
        return self::where('kunci', $key)->exists();
    }

    /**
     * Remove setting
     */
    public static function remove(string $key): bool
    {
        $deleted = self::where('kunci', $key)->delete();
        self::clearCache();
        return $deleted > 0;
    }

    /**
     * Get threshold value
     */
    public static function getThreshold(): float
    {
        return (float) self::get('threshold_penilaian', 60);
    }

    /**
     * Set threshold value
     */
    public static function setThreshold(float $value): self
    {
        return self::set('threshold_penilaian', $value, 'Nilai threshold minimum untuk kelayakan (dalam persen)');
    }

    /**
     * Get max tenor in years
     */
    public static function getMaxTenor(): int
    {
        return (int) self::get('max_tenor_tahun', 30);
    }

    /**
     * Get min down payment percentage
     */
    public static function getMinDP(): float
    {
        return (float) self::get('min_dp_persen', 10);
    }

    /**
     * Get max debt to income ratio
     */
    public static function getMaxDebtToIncomeRatio(): float
    {
        return (float) self::get('max_dti_ratio', 40);
    }

    /**
     * Get interest rate
     */
    public static function getInterestRate(): float
    {
        return (float) self::get('bunga_tahunan', 10);
    }

    /**
     * Get admin fee
     */
    public static function getAdminFee(): float
    {
        return (float) self::get('admin_fee', 0);
    }

    /**
     * Get notification settings
     */
    public static function getNotificationSettings(): array
    {
        return [
            'email_notifikasi' => self::get('email_notifikasi', true),
            'whatsapp_notifikasi' => self::get('whatsapp_notifikasi', false),
            'notifikasi_sound' => self::get('notifikasi_sound', true),
        ];
    }

    /**
     * Scopes
     */
    public function scopeByKunci(Builder $query, string $kunci): Builder
    {
        return $query->where('kunci', 'like', "%{$kunci}%");
    }

    public function scopeSearch(Builder $query, ?string $keyword): Builder
    {
        if (empty($keyword)) {
            return $query;
        }

        return $query->where(function ($q) use ($keyword) {
            $q->where('kunci', 'like', "%{$keyword}%")
              ->orWhere('nilai', 'like', "%{$keyword}%")
              ->orWhere('keterangan', 'like', "%{$keyword}%");
        });
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();
        
        // Set updated_at when creating or updating
        static::creating(function ($pengaturan) {
            $pengaturan->updated_at = now();
        });
        
        static::updating(function ($pengaturan) {
            $pengaturan->updated_at = now();
        });
        
        // Clear cache after save/delete
        static::saved(function () {
            self::clearCache();
        });
        
        static::deleted(function () {
            self::clearCache();
        });
    }
}
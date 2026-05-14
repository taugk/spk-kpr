<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Casts\Attribute;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Nama tabel yang terkait dengan model
     */
    protected $table = 'users';

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
        'nama_lengkap',
        'email',
        'password',
        'role',
        'status',
        'foto_profil',
        'last_login',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'last_login' => 'datetime',
        'email_verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Enum values untuk role
     */
    const ROLE_DEBITUR = 'debitur';
    const ROLE_MARKETING = 'marketing';
    const ROLE_ADMIN = 'admin';
    const ROLE_MANAJER = 'manajer';

    /**
     * Enum values untuk status
     */
    const STATUS_AKTIF = 'aktif';
    const STATUS_NONAKTIF = 'nonaktif';

    /**
     * Get all available roles
     */
    public static function getRoles(): array
    {
        return [
            self::ROLE_DEBITUR,
            self::ROLE_MARKETING,
            self::ROLE_ADMIN,
            self::ROLE_MANAJER,
        ];
    }

    /**
     * Get all available statuses
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_AKTIF,
            self::STATUS_NONAKTIF,
        ];
    }

    /**
     * Check if user has specific role
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Check if user is debitur
     */
    public function isDebitur(): bool
    {
        return $this->role === self::ROLE_DEBITUR;
    }

    /**
     * Check if user is marketing
     */
    public function isMarketing(): bool
    {
        return $this->role === self::ROLE_MARKETING;
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * Check if user is manajer
     */
    public function isManajer(): bool
    {
        return $this->role === self::ROLE_MANAJER;
    }

    /**
     * Check if user is active
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_AKTIF;
    }

    /**
     * Scope untuk user aktif
     */
    public function scopeAktif($query)
    {
        return $query->where('status', self::STATUS_AKTIF);
    }

    /**
     * Scope untuk user nonaktif
     */
    public function scopeNonaktif($query)
    {
        return $query->where('status', self::STATUS_NONAKTIF);
    }

    /**
     * Scope untuk role tertentu
     */
    public function scopeRole($query, string $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Accessor untuk foto profil
     */
    protected function fotoProfil(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? asset('storage/' . $value) : null,
        );
    }

    /**
     * Mutator untuk password (auto-hash)
     */
    protected function password(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => bcrypt($value),
        );
    }

    /**
     * Update last login timestamp
     */
    public function updateLastLogin(): void
    {
        $this->update(['last_login' => now()]);
    }

    /**
     * Activate user
     */
    public function activate(): void
    {
        $this->update(['status' => self::STATUS_AKTIF]);
    }

    /**
     * Deactivate user
     */
    public function deactivate(): void
    {
        $this->update(['status' => self::STATUS_NONAKTIF]);
    }
}
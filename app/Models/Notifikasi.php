<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{Builder, Model};
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

class Notifikasi extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terkait dengan model
     */
    protected $table = 'notifikasi';

    /**
     * Primary key yang tidak auto-incrementing
     */
    public $incrementing = true;

    /**
     * Tipe primary key
     */
    protected $keyType = 'int';

    /**
     * Tidak menggunakan timestamps (hanya created_at)
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'pengajuan_id',
        'judul',
        'pesan',
        'tipe',
        'dibaca',
        'tgl_dibaca',
        'created_at',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'dibaca' => 'boolean',
        'tgl_dibaca' => 'datetime',
        'created_at' => 'datetime',
    ];

    /**
     * Enum values untuk tipe
     */
    const TIPE_INFO = 'info';
    const TIPE_SUKSES = 'sukses';
    const TIPE_PERINGATAN = 'peringatan';
    const TIPE_ERROR = 'error';

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function pengajuan()
    {
        return $this->belongsTo(Pengajuan::class, 'pengajuan_id', 'id');
    }

    /**
     * Get all tipe options
     */
    public static function getTipeOptions(): array
    {
        return [
            self::TIPE_INFO,
            self::TIPE_SUKSES,
            self::TIPE_PERINGATAN,
            self::TIPE_ERROR,
        ];
    }

    /**
     * Accessors
     */
    protected function tipeBadge(): Attribute
    {
        return Attribute::make(
            get: function () {
                $badges = [
                    self::TIPE_INFO => '<span class="badge badge-info">Info</span>',
                    self::TIPE_SUKSES => '<span class="badge badge-success">Sukses</span>',
                    self::TIPE_PERINGATAN => '<span class="badge badge-warning">Peringatan</span>',
                    self::TIPE_ERROR => '<span class="badge badge-danger">Error</span>',
                ];
                
                return $badges[$this->tipe] ?? '<span class="badge badge-secondary">' . e($this->tipe) . '</span>';
            }
        );
    }

    protected function tipeIcon(): Attribute
    {
        return Attribute::make(
            get: function () {
                $icons = [
                    self::TIPE_INFO => 'fa-info-circle',
                    self::TIPE_SUKSES => 'fa-check-circle',
                    self::TIPE_PERINGATAN => 'fa-exclamation-triangle',
                    self::TIPE_ERROR => 'fa-times-circle',
                ];
                
                return $icons[$this->tipe] ?? 'fa-bell';
            }
        );
    }

    protected function formattedCreatedAt(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->created_at ? $this->created_at->format('d/m/Y H:i:s') : '-'
        );
    }

    protected function timeAgo(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->created_at ? $this->created_at->diffForHumans() : '-'
        );
    }

    protected function statusBadge(): Attribute
    {
        return Attribute::make(
            get: function () {
                return $this->dibaca 
                    ? '<span class="badge badge-secondary">Dibaca</span>'
                    : '<span class="badge badge-primary">Belum Dibaca</span>';
            }
        );
    }

    /**
     * Business Logic Methods
     */
    
    /**
     * Mark as read
     */
    public function markAsRead(): bool
    {
        if (!$this->dibaca) {
            $this->dibaca = true;
            $this->tgl_dibaca = now();
            return $this->save();
        }
        
        return false;
    }

    /**
     * Mark as unread
     */
    public function markAsUnread(): bool
    {
        if ($this->dibaca) {
            $this->dibaca = false;
            $this->tgl_dibaca = null;
            return $this->save();
        }
        
        return false;
    }

    /**
     * Create notification for user
     */
    public static function send(
        int $userId,
        string $judul,
        string $pesan,
        string $tipe = self::TIPE_INFO,
        ?int $pengajuanId = null
    ): self {
        return self::create([
            'user_id' => $userId,
            'pengajuan_id' => $pengajuanId,
            'judul' => $judul,
            'pesan' => $pesan,
            'tipe' => $tipe,
            'dibaca' => false,
            'created_at' => now(),
        ]);
    }

    /**
     * Send notification to multiple users
     */
    public static function sendBulk(
        array $userIds,
        string $judul,
        string $pesan,
        string $tipe = self::TIPE_INFO,
        ?int $pengajuanId = null
    ): int {
        $notifications = [];
        $now = now();
        
        foreach ($userIds as $userId) {
            $notifications[] = [
                'user_id' => $userId,
                'pengajuan_id' => $pengajuanId,
                'judul' => $judul,
                'pesan' => $pesan,
                'tipe' => $tipe,
                'dibaca' => false,
                'created_at' => $now,
            ];
        }
        
        return self::insert($notifications) ? count($notifications) : 0;
    }

    /**
     * Send notification to all users with specific role
     */
    public static function sendToRole(
        string $role,
        string $judul,
        string $pesan,
        string $tipe = self::TIPE_INFO,
        ?int $pengajuanId = null
    ): int {
        $users = User::where('role', $role)->where('status', 'aktif')->get();
        return self::sendBulk($users->pluck('id')->toArray(), $judul, $pesan, $tipe, $pengajuanId);
    }

    /**
     * Get unread count for user
     */
    public static function getUnreadCount(int $userId): int
    {
        return self::where('user_id', $userId)->where('dibaca', false)->count();
    }

    /**
     * Mark all as read for user
     */
    public static function markAllAsRead(int $userId): int
    {
        return self::where('user_id', $userId)
            ->where('dibaca', false)
            ->update([
                'dibaca' => true,
                'tgl_dibaca' => now(),
            ]);
    }

    /**
     * Delete old notifications (older than days)
     */
    public static function deleteOld(int $days = 30): int
    {
        return self::where('created_at', '<', now()->subDays($days))
            ->where('dibaca', true)
            ->delete();
    }

    /**
     * Scopes
     */
    public function scopeByUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByPengajuan(Builder $query, int $pengajuanId): Builder
    {
        return $query->where('pengajuan_id', $pengajuanId);
    }

    public function scopeByTipe(Builder $query, string $tipe): Builder
    {
        return $query->where('tipe', $tipe);
    }

    public function scopeBelumDibaca(Builder $query): Builder
    {
        return $query->where('dibaca', false);
    }

    public function scopeSudahDibaca(Builder $query): Builder
    {
        return $query->where('dibaca', true);
    }

    public function scopeInfo(Builder $query): Builder
    {
        return $query->where('tipe', self::TIPE_INFO);
    }

    public function scopeSukses(Builder $query): Builder
    {
        return $query->where('tipe', self::TIPE_SUKSES);
    }

    public function scopePeringatan(Builder $query): Builder
    {
        return $query->where('tipe', self::TIPE_PERINGATAN);
    }

    public function scopeError(Builder $query): Builder
    {
        return $query->where('tipe', self::TIPE_ERROR);
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

    public function scopeOrderByRecent(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();
        
        // Set created_at if not set
        static::creating(function ($notifikasi) {
            if (!$notifikasi->created_at) {
                $notifikasi->created_at = now();
            }
        });
    }
}
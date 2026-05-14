<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{Builder, Model};
use Illuminate\Database\Eloquent\Casts\Attribute;

/**
 * Kolom tabel (setelah migration hapus lapangan):
 *
 * id, pengajuan_id, marketing_id,
 * dok_ktp_valid, dok_kk_valid, dok_slip_gaji_valid,
 * dok_rek_koran_valid, dok_slik_valid, dok_surat_kerja_valid, dok_npwp_valid,
 * rekomendasi_marketing, keputusan, alasan_keputusan, tgl_keputusan,
 * created_at, updated_at
 */
class VerifikasiMarketing extends Model
{
    use HasFactory;

    protected $table = 'verifikasi_marketing';

    // ─── Konstanta ──────────────────────────────────────────────────────────

    const REKOMENDASI_LAYAK              = 'layak';
    const REKOMENDASI_PERLU_PERTIMBANGAN = 'perlu_pertimbangan';
    const REKOMENDASI_TIDAK_LAYAK        = 'tidak_layak';

    const KEPUTUSAN_AJUKAN_KE_ADMIN = 'ajukan_ke_admin';
    const KEPUTUSAN_MINTA_REVISI    = 'minta_revisi';
    const KEPUTUSAN_TOLAK           = 'tolak';

    // ─── Fillable ───────────────────────────────────────────────────────────

    protected $fillable = [
        'pengajuan_id',
        'marketing_id',
        // Verifikasi dokumen
        'dok_ktp_valid',
        'dok_kk_valid',
        'dok_slip_gaji_valid',
        'dok_rek_koran_valid',
        'dok_slik_valid',
        'dok_surat_kerja_valid',
        'dok_npwp_valid',
        // Keputusan
        'rekomendasi_marketing',
        'keputusan',
        'alasan_keputusan',
        'tgl_keputusan',
    ];

    protected $casts = [
        'dok_ktp_valid'         => 'boolean',
        'dok_kk_valid'          => 'boolean',
        'dok_slip_gaji_valid'   => 'boolean',
        'dok_rek_koran_valid'   => 'boolean',
        'dok_slik_valid'        => 'boolean',
        'dok_surat_kerja_valid' => 'boolean',
        'dok_npwp_valid'        => 'boolean',
        'tgl_keputusan'         => 'datetime',
    ];

    // ─── Daftar semua dokumen ────────────────────────────────────────────────

    /**
     * Mapping kolom DB → label tampilan.
     * Urutan ini dipakai di form dan laporan.
     */
    public static function getDokumenMap(): array
    {
        return [
            'dok_ktp_valid'         => 'KTP Debitur',
            'dok_kk_valid'          => 'Kartu Keluarga',
            'dok_slip_gaji_valid'   => 'Slip Gaji / Bukti Penghasilan',
            'dok_rek_koran_valid'   => 'Rekening Koran 3 Bulan Terakhir',
            'dok_slik_valid'        => 'Laporan SLIK OJK',
            'dok_surat_kerja_valid' => 'Surat Keterangan Kerja / SK',
            'dok_npwp_valid'        => 'NPWP',
        ];
    }

    // ─── Relasi ─────────────────────────────────────────────────────────────

    public function pengajuan()
    {
        return $this->belongsTo(Pengajuan::class);
    }

    public function marketing()
    {
        return $this->belongsTo(User::class, 'marketing_id');
    }

    // ─── Logika dokumen ─────────────────────────────────────────────────────

    /**
     * Jumlah dokumen yang sudah ditandai valid (nilai true).
     */
    public function getTotalValidDocuments(): int
    {
        $count = 0;
        foreach (array_keys(self::getDokumenMap()) as $kolom) {
            if ($this->{$kolom} === true) {
                $count++;
            }
        }
        return $count;
    }

    /**
     * Persentase dokumen valid dari total 7 dokumen.
     */
    public function getDocumentValidationPercentage(): float
    {
        return round(($this->getTotalValidDocuments() / 7) * 100, 1);
    }

    /**
     * Daftar dokumen yang belum valid (false atau null).
     */
    public function getMissingDocuments(): array
    {
        $missing = [];
        foreach (self::getDokumenMap() as $kolom => $label) {
            if (!$this->{$kolom}) {
                $missing[] = $label;
            }
        }
        return $missing;
    }

    /**
     * Cek apakah dokumen-dokumen wajib (kecuali NPWP kondisional) semua valid.
     */
    public function isDokumenWajibLengkap(bool $npwpWajib = false): bool
    {
        $wajib = [
            $this->dok_ktp_valid,
            $this->dok_kk_valid,
            $this->dok_slip_gaji_valid,
            $this->dok_rek_koran_valid,
            $this->dok_slik_valid,
            $this->dok_surat_kerja_valid,
        ];

        if ($npwpWajib) {
            $wajib[] = $this->dok_npwp_valid;
        }

        return !in_array(false, $wajib, true) && !in_array(null, $wajib, true);
    }

    // ─── Logika keputusan ───────────────────────────────────────────────────

    public function isSelesai(): bool
    {
        return !is_null($this->keputusan) && !is_null($this->tgl_keputusan);
    }

    public function setAjukanKeAdmin(?string $alasan = null): bool
    {
        return $this->update([
            'keputusan'        => self::KEPUTUSAN_AJUKAN_KE_ADMIN,
            'alasan_keputusan' => $alasan,
            'tgl_keputusan'    => now(),
        ]);
    }

    public function setMintaRevisi(string $alasan): bool
    {
        return $this->update([
            'keputusan'        => self::KEPUTUSAN_MINTA_REVISI,
            'alasan_keputusan' => $alasan,
            'tgl_keputusan'    => now(),
        ]);
    }

    public function setTolak(string $alasan): bool
    {
        return $this->update([
            'keputusan'        => self::KEPUTUSAN_TOLAK,
            'alasan_keputusan' => $alasan,
            'tgl_keputusan'    => now(),
        ]);
    }

    /**
     * Rekomendasi otomatis berdasarkan persentase dokumen valid.
     */
    public function hitungRekomendasiOtomatis(): string
    {
        $pct = $this->getDocumentValidationPercentage();

        if ($pct >= 85) {
            return self::REKOMENDASI_LAYAK;
        } elseif ($pct >= 60) {
            return self::REKOMENDASI_PERLU_PERTIMBANGAN;
        }

        return self::REKOMENDASI_TIDAK_LAYAK;
    }

    // ─── Accessor label ─────────────────────────────────────────────────────

    protected function rekomendasiLabel(): Attribute
    {
        return Attribute::make(get: fn() => match ($this->rekomendasi_marketing) {
            self::REKOMENDASI_LAYAK              => 'Layak',
            self::REKOMENDASI_PERLU_PERTIMBANGAN => 'Perlu Pertimbangan',
            self::REKOMENDASI_TIDAK_LAYAK        => 'Tidak Layak',
            default                              => '-',
        });
    }

    protected function keputusanLabel(): Attribute
    {
        return Attribute::make(get: fn() => match ($this->keputusan) {
            self::KEPUTUSAN_AJUKAN_KE_ADMIN => 'Diteruskan ke Admin',
            self::KEPUTUSAN_MINTA_REVISI    => 'Minta Revisi',
            self::KEPUTUSAN_TOLAK           => 'Ditolak',
            default                         => '-',
        });
    }

    // ─── Scopes ─────────────────────────────────────────────────────────────

    public function scopeByMarketing(Builder $q, int $id): Builder
    {
        return $q->where('marketing_id', $id);
    }

    public function scopeSudahDiputuskan(Builder $q): Builder
    {
        return $q->whereNotNull('tgl_keputusan');
    }

    public function scopeBelumDiputuskan(Builder $q): Builder
    {
        return $q->whereNull('tgl_keputusan');
    }

    public function scopeKeputusan(Builder $q, string $keputusan): Builder
    {
        return $q->where('keputusan', $keputusan);
    }
}

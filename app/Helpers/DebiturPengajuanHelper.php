<?php

namespace App\Helpers;

use App\Models\Pengajuan;

class DebiturPengajuanHelper
{
    // -----------------------------------------------------------------------
    // Kode pengajuan
    // -----------------------------------------------------------------------

    /**
     * Generate kode unik: KPR-YYYYMMDD-XXXXX
     * Contoh: KPR-20250512-A3F9B
     */
    public static function generateKode(): string
    {
        do {
            $kode = 'KPR-' . now()->format('Ymd') . '-' . strtoupper(substr(uniqid(), -5));
        } while (Pengajuan::where('kode_pengajuan', $kode)->exists());

        return $kode;
    }

    // -----------------------------------------------------------------------
    // Kalkulasi finansial KPR
    // -----------------------------------------------------------------------

    /**
     * Hitung estimasi angsuran bulanan menggunakan anuitas tetap.
     * Bunga default: 8,5% per tahun.
     */
    public static function hitungAngsuran(float $plafon, int $tenorTahun, float $bungaTahunan = 0.085): float
    {
        if ($plafon <= 0 || $tenorTahun <= 0) {
            return 0.0;
        }

        $r = $bungaTahunan / 12;
        $n = $tenorTahun * 12;

        return round($plafon * $r * pow(1 + $r, $n) / (pow(1 + $r, $n) - 1), 2);
    }

    /**
     * Hitung rasio angsuran terhadap penghasilan (%).
     * Menyertakan cicilan aktif yang sudah ada.
     */
    public static function hitungRasio(float $angsuranBaru, float $totalCicilanAktif, float $totalPenghasilan): float
    {
        if ($totalPenghasilan <= 0) {
            return 0.0;
        }

        return round(($angsuranBaru + $totalCicilanAktif) / $totalPenghasilan * 100, 2);
    }

    /**
     * Hitung total penghasilan dari array data form.
     */
    public static function totalPenghasilan(array $data): float
    {
        return (float) ($data['penghasilan_pokok'] ?? 0)
            + (float) ($data['tunjangan'] ?? 0)
            + (float) ($data['penghasilan_lain'] ?? 0);
    }

    /**
     * Apakah rasio angsuran termasuk berisiko tinggi (> 40%)?
     */
    public static function isRasioTinggi(float $rasio): bool
    {
        return $rasio > 40.0;
    }

    // -----------------------------------------------------------------------
    // Format rupiah (DIPERBAIKI - MENERIMA NULL)
    // -----------------------------------------------------------------------

    /**
     * Format angka ke format Rupiah dengan aman (menerima null)
     * 
     * @param float|int|string|null $nilai
     * @param bool $withPrefix
     * @return string
     */
    public static function rupiah($nilai, bool $withPrefix = true): string
    {
        // Konversi ke float, jika null atau tidak valid jadi 0
        $angka = is_numeric($nilai) ? floatval($nilai) : 0;
        
        $formatted = number_format($angka, 0, ',', '.');
        
        return $withPrefix ? 'Rp ' . $formatted : $formatted;
    }

    /**
     * Alias untuk rupiah() dengan prefix
     */
    public static function formatRupiah($nilai): string
    {
        return self::rupiah($nilai, true);
    }

    /**
     * Format rupiah tanpa prefix "Rp "
     */
    public static function formatAngka($nilai): string
    {
        return self::rupiah($nilai, false);
    }

    // -----------------------------------------------------------------------
    // Parsing nilai dari string ke float
    // -----------------------------------------------------------------------

    /**
     * Parse string rupiah ke float
     * Contoh: "Rp 1.500.000" → 1500000
     * 
     * @param string|null $rupiahString
     * @return float
     */
    public static function parseRupiah($rupiahString): float
    {
        if (empty($rupiahString)) {
            return 0.0;
        }
        
        // Hapus "Rp " dan titik, lalu konversi ke float
        $clean = str_replace(['Rp ', 'Rp', '.', ','], '', $rupiahString);
        return floatval($clean);
    }

    // -----------------------------------------------------------------------
    // Mapping nilai form → enum database
    // -----------------------------------------------------------------------

    public static function mapStatusPernikahan(string $value): string
    {
        return match ($value) {
            'sudah_menikah', 'Sudah Menikah' => 'menikah',
            'cerai', 'Cerai'                 => 'cerai',
            default                          => 'belum_menikah',
        };
    }

    public static function mapStatusTempatTinggal(string $value): ?string
    {
        return match ($value) {
            'Milik sendiri' => 'milik_sendiri',
            'Sewa'          => 'sewa',
            'Keluarga'      => 'keluarga',
            default         => null,
        };
    }

    public static function mapStatusPekerjaan(string $value): string
    {
        return match ($value) {
            'Karyawan'   => 'karyawan_swasta',
            'PNS'        => 'pns',
            'TNI-Polri'  => 'tni_polri',
            'Wiraswasta' => 'wiraswasta',
            'Profesional'=> 'profesional',
            default      => 'lainnya',
        };
    }

    public static function mapStatusKepegawaian(string $value): ?string
    {
        return match ($value) {
            'Tetap'    => 'tetap',
            'Kontrak'  => 'kontrak',
            'Percobaan'=> 'percobaan',
            default    => null,
        };
    }

    public static function mapStatusKredit(string $value): string
    {
        return match ($value) {
            'DPK'           => 'dpk',
            'Kurang lancar' => 'kurang_lancar',
            'Diragukan'     => 'diragukan',
            'Macet'         => 'macet',
            default         => 'lancar',
        };
    }

    public static function mapTujuanPembelian(string $value): string
    {
        return match ($value) {
            'investasi', 'Investasi' => 'investasi',
            default                   => 'hunian_sendiri',
        };
    }

    public static function mapSumberDp(string $value): ?string
    {
        return match ($value) {
            'Tabungan'  => 'tabungan',
            'Keluarga'  => 'keluarga',
            'Jual aset' => 'jual_aset',
            default     => 'lainnya',
        };
    }

    // -----------------------------------------------------------------------
    // Parsing nilai form
    // -----------------------------------------------------------------------

    /**
     * Parsing string lama bekerja ke [tahun, bulan].
     * Nilai yang mungkin: '<1 th', '1-2 th', '2-5 th', '>5 th'
     */
    public static function parseLamaBekerja(string $value): array
    {
        return match ($value) {
            '<1 th'  => [0, 11],
            '1-2 th' => [1, 0],
            '2-5 th' => [2, 0],
            '>5 th'  => [5, 0],
            default  => [0, 0],
        };
    }

    // -----------------------------------------------------------------------
    // Timeline status pengajuan
    // -----------------------------------------------------------------------

    /**
     * Bangun array timeline untuk ditampilkan di halaman detail pengajuan.
     *
     * @return array<int, array{label: string, status: string, waktu: string|null, aktif: bool}>
     */
    public static function buildTimeline(Pengajuan $pengajuan): array
    {
        $statuses = [
            'draft'                => ['label' => 'Draft dibuat',              'waktu' => $pengajuan->created_at],
            'submitted'            => ['label' => 'Pengajuan dikirim',         'waktu' => $pengajuan->tgl_submitted],
            'verifikasi_marketing' => ['label' => 'Verifikasi marketing',      'waktu' => $pengajuan->tgl_marketing_proses],
            'antrian_admin'        => ['label' => 'Antrian penilaian admin',   'waktu' => null],
            'penilaian_admin'      => ['label' => 'Penilaian admin',           'waktu' => $pengajuan->tgl_admin_proses],
            'selesai_dinilai'      => ['label' => 'Selesai dinilai',           'waktu' => $pengajuan->tgl_selesai],
        ];

        $urutan = [
            'draft', 'submitted', 'verifikasi_marketing',
            'revisi_debitur', 'antrian_admin', 'penilaian_admin', 'selesai_dinilai',
            'disetujui_sistem', 'ditolak_sistem',
        ];

        $currentIndex = array_search($pengajuan->status, $urutan, true) ?: 0;
        $timeline = [];

        foreach ($statuses as $key => $item) {
            $stepIndex = array_search($key, $urutan, true);
            $sudahLewat = $stepIndex !== false && $stepIndex <= $currentIndex;

            $timeline[] = [
                'label'  => $item['label'],
                'status' => $key,
                'waktu'  => $item['waktu'] ? \Carbon\Carbon::parse($item['waktu'])->format('d M Y H:i') : null,
                'aktif'  => $key === $pengajuan->status,
                'selesai'=> $sudahLewat && $key !== $pengajuan->status,
            ];
        }

        return $timeline;
    }

    // -----------------------------------------------------------------------
    // Ringkasan finansial untuk halaman detail
    // -----------------------------------------------------------------------

    /**
     * @return array<string, mixed>
     */
    public static function buildFinancialSummary(Pengajuan $pengajuan): array
    {
        return [
            'harga_properti'    => static::rupiah($pengajuan->harga_properti),
            'uang_muka'         => static::rupiah($pengajuan->uang_muka),
            'persen_dp'         => ($pengajuan->persen_dp ?? 0) . '%',
            'jumlah_pinjaman'   => static::rupiah($pengajuan->jumlah_pinjaman),
            'tenor'             => ($pengajuan->tenor_tahun ?? 0) . ' tahun (' . (($pengajuan->tenor_tahun ?? 0) * 12) . ' bulan)',
            'estimasi_angsuran' => static::rupiah($pengajuan->estimasi_angsuran),
            'rasio_angsuran'    => isset($pengajuan->rasio_angsuran) ? $pengajuan->rasio_angsuran . '%' : '-',
            'rasio_berisiko'    => static::isRasioTinggi((float) ($pengajuan->rasio_angsuran ?? 0)),
        ];
    }

    // -----------------------------------------------------------------------
    // Label & badge status
    // -----------------------------------------------------------------------

    /**
     * Kembalikan label bahasa Indonesia dan warna badge Bootstrap untuk setiap status.
     *
     * @return array{label: string, color: string}
     */
    public static function statusBadge(string $status): array
    {
        return match ($status) {
            'draft'                => ['label' => 'Draft',                  'color' => 'secondary'],
            'submitted'            => ['label' => 'Terkirim',               'color' => 'info'],
            'verifikasi_marketing' => ['label' => 'Verifikasi Marketing',   'color' => 'primary'],
            'revisi_debitur'       => ['label' => 'Perlu Revisi',           'color' => 'warning'],
            'ditolak_marketing'    => ['label' => 'Ditolak Marketing',      'color' => 'danger'],
            'antrian_admin'        => ['label' => 'Antrian Admin',          'color' => 'info'],
            'penilaian_admin'      => ['label' => 'Penilaian Admin',        'color' => 'primary'],
            'selesai_dinilai'      => ['label' => 'Selesai Dinilai',        'color' => 'success'],
            'ditolak_sistem'       => ['label' => 'Ditolak',                'color' => 'danger'],
            'disetujui_sistem'     => ['label' => 'Disetujui',              'color' => 'success'],
            default                => ['label' => ucfirst($status),         'color' => 'secondary'],
        };
    }
}
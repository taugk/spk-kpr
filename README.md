# SPK KPR Perumahan

Sistem Pendukung Keputusan (SPK) untuk proses pengajuan Kredit Pemilikan Rumah (KPR) berbasis metode **SMART (Simple Multi-Attribute Rating Technique)**. Dibangun dengan Laravel 13 dan template Deskapp – Free Bootstrap 4 HTML5 responsive admin dashboard template, sistem ini mengelola alur pengajuan KPR mulai dari pendaftaran debitur hingga keputusan akhir secara terstruktur dan transparan.

---

## Fitur Utama

- **Landing Page** — Informasi proyek properti, simulasi KPR, dan pencarian unit
- **Multi-Role Authentication** — Admin, Marketing, Manajer, dan Debitur
- **Alur Pengajuan KPR** — Dari submission debitur hingga keputusan akhir
- **Penilaian SMART** — Algoritma perhitungan skor multi-kriteria dengan bobot dan normalisasi
- **Verifikasi Marketing** — Pengecekan kelengkapan dan keputusan awal marketing
- **Dashboard Manajer** — Analisis kinerja, laporan, dan monitoring pengajuan
- **Manajemen Properti** — Data proyek, tipe unit, dan unit
- **Laporan & Ekspor** — Ekspor data pengajuan dan laporan statistik
- **Notifikasi** — Sistem notifikasi real-time per role

---

## Teknologi

| Komponen      | Teknologi                                    |
| ------------- | -------------------------------------------- |
| Backend       | PHP 8.3, Laravel 13                          |
| Frontend      | Blade, Tailwind CSS 4, Vite 8                |
| Database      | MySQL (SQLite untuk development)             |
| Arsitektur DB | Views & Stored Procedure (`sp_hitung_smart`) |
| Queue         | Database Queue                               |

---

## Role Pengguna

| Role          | Akses                                                           |
| ------------- | --------------------------------------------------------------- |
| **Debitur**   | Registrasi, pengajuan KPR, upload dokumen, pantau status        |
| **Marketing** | Verifikasi pengajuan, rekomendasi, laporan, riwayat             |
| **Admin**     | Penilaian SMART, manajemen properti, kriteria, debitur, laporan |
| **Manajer**   | Dashboard analisis, monitoring kinerja, laporan menyeluruh      |

---

## Alur Pengajuan

```
Debitur Mengajukan
        ↓
Verifikasi Marketing
        ↓
Penilaian SMART (Admin)
        ↓
Skor ≥ Threshold → Disetujui Sistem
Skor < Threshold → Ditolak Sistem
```

Status pengajuan: `submitted` → `verifikasi_marketing` → `revisi_debitur` → `penilaian_admin` → `disetujui_sistem` / `ditolak_sistem` / `ditolak_marketing`

---

## Instalasi

### Prasyarat

- PHP >= 8.3
- Composer
- Node.js & npm
- MySQL (atau SQLite untuk development)

### Langkah Instalasi

**1. Clone repository**

```bash
git clone <url-repo> spk-kpr-smart-persada
cd spk-kpr-smart-persada
```

**2. Setup otomatis (rekomendasi)**

```bash
composer run setup
```

Script ini akan menjalankan secara berurutan:

- `composer install`
- Menyalin `.env.example` ke `.env`
- Generate application key
- Menjalankan migrasi database
- `npm install`
- `npm run build`

**3. Setup manual (alternatif)**

```bash
composer install
cp .env.example .env
php artisan key:generate

# Konfigurasi database di .env terlebih dahulu, lalu:
php artisan migrate

npm install
npm run build
```

**4. Konfigurasi environment**

Edit file `.env` sesuai kebutuhan:

```env
APP_NAME="SPK KPR Smart Persada"
APP_URL=http://localhost

# Untuk production gunakan MySQL:
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=spk_kpr
DB_USERNAME=root
DB_PASSWORD=

# Storage untuk dokumen upload
FILESYSTEM_DISK=local
```

> **Catatan:** Migrasi akan otomatis membuat views (`v_pengajuan_lengkap`, `v_penilaian_detail`, `v_antrian_marketing`, `v_statistik_bulanan`) dan stored procedure `sp_hitung_smart` di MySQL.

---

## Menjalankan Aplikasi

### Development

```bash
composer run dev
```

Perintah ini menjalankan secara bersamaan:

- `php artisan serve` — Web server
- `php artisan queue:listen` — Queue worker
- `php artisan pail` — Log viewer
- `npm run dev` — Vite HMR

### Production

```bash
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan serve
```

Jalankan queue worker secara terpisah:

```bash
php artisan queue:work --daemon
```

---

## Struktur Direktori

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/          # Dashboard, Pengajuan, Penilaian, Properti, Kriteria, Laporan
│   │   ├── Auth/           # Login admin/staff
│   │   ├── Debitur/        # Login, Dashboard, Pengajuan debitur
│   │   ├── LandingPage/    # Landing page & simulasi KPR
│   │   ├── Manager/        # Dashboard, Analisis, Kinerja, Laporan manajer
│   │   └── Marketing/      # Dashboard, Verifikasi, Riwayat, Laporan marketing
│   ├── Middleware/
│   │   └── RoleMiddleware.php
│   └── Requests/
│       └── DebiturPengajuanRequest.php
├── Helpers/                # AdminHelper, DebiturPengajuanHelper, KriteriaHelper, MarketingHelper
├── Models/                 # Eloquent models
├── Providers/
└── Services/
    ├── Admin/              # SmartService, KriteriaService, ReportService, dll
    ├── Debitur/
    └── Marketing/

database/
├── migrations/             # 15+ migrasi termasuk views & stored procedure
└── seeders/

routes/
└── web.php                 # Semua route terproteksi middleware role
```

---

## Metode SMART

Sistem menggunakan algoritma **Simple Multi-Attribute Rating Technique** dengan tahapan:

1. **Definisi Kriteria** — Setiap kriteria memiliki kode, nama, tipe (benefit/cost), bobot, dan skala penilaian
2. **Input Nilai** — Admin menginput nilai untuk setiap kriteria berdasarkan data debitur
3. **Normalisasi** — Nilai dinormalisasi terhadap nilai minimum/maksimum pada skala kriteria
4. **Pembobotan** — Skor kontribusi = nilai normalisasi × bobot kriteria
5. **Agregasi** — Skor akhir = jumlah seluruh skor kontribusi
6. **Keputusan** — Skor akhir dibandingkan threshold (default: 65). Skor ≥ threshold → Disetujui

Perhitungan disimpan detail per kriteria di tabel `penilaian_detail` untuk keperluan audit dan transparansi.

---

## Database Views

| View                  | Deskripsi                                                   |
| --------------------- | ----------------------------------------------------------- |
| `v_pengajuan_lengkap` | Gabungan data pengajuan, debitur, properti, dan hasil SMART |
| `v_penilaian_detail`  | Detail skor SMART per kriteria per pengajuan                |
| `v_antrian_marketing` | Pengajuan aktif dalam antrean marketing                     |
| `v_statistik_bulanan` | Statistik pengajuan dan approval rate per bulan             |

---

## Lisensi

Proyek ini dikembangkan untuk keperluan internal. Dibangun di atas [Laravel Framework](https://laravel.com) yang dilisensikan di bawah [MIT License](https://opensource.org/licenses/MIT).

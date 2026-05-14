<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\{DashboardController, KriteriaController, KriteriaSkalaController, LaporanController, PengajuanController, PengaturanController, PenilaianController, PropertiController, UserController};
use App\Http\Controllers\Debitur\{DebiturDashboardController, DebiturLoginController, DebiturPengajuanController};
use App\Http\Controllers\Marketing\{MarketingDashboardController, MarketingLaporanController, MarketingNotifikasiController, MarketingPengajuanController, MarketingRiwayatController, MarketingVerifikasiController};
use App\Http\Controllers\LandingPage\LandingPageController;

/*
|--------------------------------------------------------------------------
| Landing Page
|--------------------------------------------------------------------------
*/

// Route landing page dengan data dari database
Route::get('/', [LandingPageController::class, 'index'])->name('home');

// Route detail untuk landing page
Route::get('/proyek/{id}', [LandingPageController::class, 'detailProyek'])->name('landing.proyek.detail');
Route::get('/unit/{id}', [LandingPageController::class, 'detailUnit'])->name('landing.unit.detail');

// Route AJAX untuk simulasi KPR
Route::post('/simulasi-kpr', [LandingPageController::class, 'simulasiKPR'])->name('simulasi.kpr');
Route::get('/cari-unit', [LandingPageController::class, 'cariUnit'])->name('landing.cari.unit');

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/

Route::prefix('auth')->group(function () {

    Route::get('/login', [DebiturLoginController::class, 'showLogin'])->name('debitur.login');
    Route::post('/login', [DebiturLoginController::class, 'login'])->name('debitur.login.process');

    Route::get('/register', [DebiturLoginController::class, 'showRegister'])->name('debitur.register');
    Route::post('/register', [DebiturLoginController::class, 'register'])->name('debitur.register.process');

    Route::get('/forgot-password', function () {
        return view('landing-page.auth.forgot-password');
    })->name('password.request');

    Route::get('/reset-password', function () {
        return view('landing-page.auth.reset-password');
    })->name('password.reset');

    Route::post('/logout', [DebiturLoginController::class, 'logout'])->name('debitur.logout');
});

Route::get('/login', [\App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [\App\Http\Controllers\Auth\LoginController::class, 'login'])->name('login.process');
Route::post('/logout', [\App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        /*
        |------------------------------------------------------------------
        | Dashboard
        |------------------------------------------------------------------
        */

        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        /*
        |------------------------------------------------------------------
        | Pengajuan
        |------------------------------------------------------------------
        */

        Route::get('/pengajuan/export', [PengajuanController::class, 'export'])
            ->name('pengajuan.export');

        Route::resource('pengajuan', PengajuanController::class)
            ->only(['index', 'show']);

        /*
        |------------------------------------------------------------------
        | Debitur
        |------------------------------------------------------------------
        */
        Route::resource('debitur', UserController::class)
            ->only(['index', 'show']);

        /*
        |------------------------------------------------------------------
        | Penilaian SMART
        |------------------------------------------------------------------
        */

        Route::resource('penilaian', PenilaianController::class)
            ->only(['index', 'create', 'store', 'show']);

            Route::post('/penilaian/auto-save', [PenilaianController::class, 'autoSave'])->name('penilaian.auto-save');
    Route::delete('/penilaian/clear-auto-save', [PenilaianController::class, 'clearAutoSave'])->name('penilaian.clear-auto-save');

        /*
        |------------------------------------------------------------------
        | Hasil Penilaian
        |------------------------------------------------------------------
        */

            Route::get('/hasil-penilaian', function () {
                return view('admin.hasil-penilaian');
            })->name('hasil-penilaian');

        /*
        |------------------------------------------------------------------
        | Kriteria
        |------------------------------------------------------------------
        */

        Route::resource('kriteria', KriteriaController::class)
            ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);

        /*
        |------------------------------------------------------------------
        | Subkriteria
        |------------------------------------------------------------------
        */

        Route::resource('subkriteria', KriteriaSkalaController::class)
            ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);

        Route::resource('kriteria-skala', KriteriaSkalaController::class)
            ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);

       /*
|------------------------------------------------------------------
| Properti
|------------------------------------------------------------------
*/

Route::prefix('properti')->name('properti.')->group(function () {
    // Halaman utama
    Route::get('/', [PropertiController::class, 'index'])->name('index');

    // Manajemen Proyek
    Route::get('/proyek', [PropertiController::class, 'proyek'])->name('proyek');
    Route::get('/proyek/create', [PropertiController::class, 'createProyek'])->name('proyek.create');
    Route::post('/proyek', [PropertiController::class, 'storeProyek'])->name('proyek.store');
    Route::get('/proyek/{id}/edit', [PropertiController::class, 'editProyek'])->name('proyek.edit');
    Route::put('/proyek/{id}', [PropertiController::class, 'updateProyek'])->name('proyek.update');
    Route::delete('/proyek/{id}', [PropertiController::class, 'destroyProyek'])->name('proyek.destroy');
    Route::post('/proyek/remove-gambar', [PropertiController::class, 'removeGambarProyek'])->name('proyek.remove-foto');

    // Manajemen Tipe Unit
    Route::get('/tipe-unit', [PropertiController::class, 'tipeUnit'])->name('tipe-unit');
    Route::get('/tipe-unit/create', [PropertiController::class, 'createTipeUnit'])->name('tipe-unit.create');
    Route::post('/tipe-unit', [PropertiController::class, 'storeTipeUnit'])->name('tipe-unit.store');
    Route::get('/tipe-unit/{id}/detail', [PropertiController::class, 'detailTipeUnit'])->name('tipe-unit.detail');
    Route::get('/tipe-unit/{id}/edit', [PropertiController::class, 'editTipeUnit'])->name('tipe-unit.edit');
    Route::put('/tipe-unit/{id}', [PropertiController::class, 'updateTipeUnit'])->name('tipe-unit.update');
    Route::delete('/tipe-unit/{id}', [PropertiController::class, 'destroyTipeUnit'])->name('tipe-unit.destroy');
    Route::post('/tipe-unit/remove-gambar', [PropertiController::class, 'removeGambarTipeUnit'])->name('tipe-unit.remove-gambar');

    // Manajemen Unit
    Route::get('/unit', [PropertiController::class, 'unit'])->name('unit');
    Route::get('/unit/create', [PropertiController::class, 'createUnit'])->name('unit.create');
    Route::post('/unit', [PropertiController::class, 'storeUnit'])->name('unit.store');
    Route::get('/unit/{id}/edit', [PropertiController::class, 'editUnit'])->name('unit.edit');
    Route::put('/unit/{id}', [PropertiController::class, 'updateUnit'])->name('unit.update');
    Route::delete('/unit/{id}', [PropertiController::class, 'destroyUnit'])->name('unit.destroy');
});
        /*
        |------------------------------------------------------------------
        | Users
        |------------------------------------------------------------------
        */

        Route::resource('users', UserController::class)
            ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);

        /*
        |------------------------------------------------------------------
        | Riwayat Penilaian
        |------------------------------------------------------------------
        */

            Route::resource('riwayat', PenilaianController::class)
                ->only(['index']);

        /*
        |------------------------------------------------------------------
        | Laporan
        |------------------------------------------------------------------
        */

        Route::resource('laporan', LaporanController::class)
            ->only(['index', 'create', 'store']);

        Route::get('/laporan/pengajuan', [LaporanController::class, 'laporanPengajuan'])
            ->name('laporan.pengajuan');

        Route::get('/laporan/penilaian', [LaporanController::class, 'laporanPenilaian'])
            ->name('laporan.penilaian');

        Route::get('/laporan/rekap', [LaporanController::class, 'laporanRekap'])
            ->name('laporan.rekap');

        Route::get('/laporan/penolakan', [LaporanController::class, 'laporanPenolakan'])
            ->name('laporan.penolakan');
        Route::get('/laporan/cetak-penolakan', [LaporanController::class, 'cetakLaporanPenolakan'])
            ->name('laporan.cetak-penolakan');

        Route::get('/laporan/pengajuan/export', [LaporanController::class, 'exportLaporanPengajuan'])
            ->name('laporan.pengajuan.export');

        Route::get('/laporan/penilaian/export', [LaporanController::class, 'exportLaporanPenilaian'])
            ->name('laporan.penilaian.export');

        Route::post('/laporan/cetak', [LaporanController::class, 'cetakLaporan'])
    ->name('laporan.cetak');

        Route::get('/laporan/export', [LaporanController::class, 'exportRekap'])
            ->name('laporan.export');

        /*
|------------------------------------------------------------------
| Pengaturan
|------------------------------------------------------------------
*/

Route::prefix('pengaturan')->name('pengaturan.')->controller(PengaturanController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::put('/', 'update')->name('update');
    Route::put('{kunci}/single', 'updateSingle')->name('update.single');
});

        /*
        |------------------------------------------------------------------
        | Profil Admin
        |------------------------------------------------------------------
        */

        Route::get('/profil', function () {
            return view('admin.profil');
        })->name('profil');
    });

/*
|--------------------------------------------------------------------------
| Debitur Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:debitur'])->prefix('debitur')->name('debitur.')->group(function () {

        /*
        |------------------------------------------------------------------
        | Dashboard
        |------------------------------------------------------------------
        */

        Route::get('/dashboard', function () {
            return view('debitur.dashboard');
        })->name('dashboard');

        /*
        |------------------------------------------------------------------
        | Profile
        |------------------------------------------------------------------
        */

        Route::get('/profile', function () {
            return view('debitur.profile');
        })->name('profile');

        /*
        |------------------------------------------------------------------
        | Applications
        |------------------------------------------------------------------
        */

        Route::get('/applications', function () {
            return view('debitur.applications');
        })->name('applications');

        Route::get('/applications/{id}', function ($id) {
            return view('debitur.application-detail', compact('id'));
        })->name('application.detail');

        /*
        |------------------------------------------------------------------
        | Riwayat Pengajuan
        |------------------------------------------------------------------
        */



        /*
        |------------------------------------------------------------------
        | Pengajuan KPR
        |------------------------------------------------------------------
        */

        Route::get('/pengajuan-kpr', [DebiturPengajuanController::class, 'create'])->name('pengajuan-kpr');

        Route::post('/pengajuan-kpr/store', [DebiturPengajuanController::class, 'store'])->name('pengajuan.store');

        Route::get('/pengajuan/show/{pengajuan}', [DebiturPengajuanController::class, 'show'])->name('pengajuan.show');

        Route::post('/pengajuan-kpr/draft', function () {
            //
        })->name('pengajuan.draft');

        Route::get('/pengajuan-kpr/edit/{pengajuan}', [DebiturPengajuanController::class, 'edit'])->name('pengajuan.edit');

        Route::post('/pengajuan-kpr/update/{pengajuan}', [DebiturPengajuanController::class, 'update'])->name('pengajuan.update');

        Route::get('/riwayat-pengajuan', [DebiturPengajuanController::class,'history'])->name('riwayat-pengajuan');

        // Route::get('/pengajuan-kpr/{id}', [DebiturPengajuanController::class, 'show'])->name('pengajuan.detail');

        /*
        |------------------------------------------------------------------
        | Simulasi KPR
        |------------------------------------------------------------------
        */

        Route::get('/simulasi-kpr', function () {
            return view('debitur.pages.simulasi-kpr');
        })->name('simulasi-kpr');

        /*
        |------------------------------------------------------------------
        | Properti
        |------------------------------------------------------------------
        */

        Route::get('/property', function () {
            return view('debitur.pages.property');
        })->name('properti');

        Route::get('/property/{id}', function ($id) {
            return view('debitur.pages.property-detail', compact('id'));
        })->name('properti.detail');

        /*
        |------------------------------------------------------------------
        | Dokumentasi
        |------------------------------------------------------------------
        */

        Route::get('/dokumentasi', function () {
            return view('debitur.pages.dokumentasi');
        })->name('dokumentasi');

        Route::get('/dokumentasi/{id}', function ($id) {
            return view('debitur.pages.dokumentasi-detail', compact('id'));
        })->name('dokumentasi.detail');

        /*
        |------------------------------------------------------------------
        | Profile
        |------------------------------------------------------------------
        */
        Route::get('/profile', function () {
            return view('debitur.pages.profile');
        })->name('profil');

        Route::get('/profile/edit', function () {
            return view('debitur.pages.edit-profile');
        })->name('profile.edit');
    });




// ============================================================
// ROUTE MARKETING
// Tugas: Verifikasi dokumen & lapangan, meneruskan ke admin
// ============================================================
Route::middleware(['auth', 'role:marketing'])
    ->prefix('marketing')
    ->name('marketing.')
    ->group(function () {

        // Dashboard Marketing
        Route::get('/dashboard', [MarketingDashboardController::class, 'index'])->name('dashboard');
        Route::get('/chart-data', [MarketingDashboardController::class, 'chartData'])->name('chart-data');

        // ── PENGAJUAN MASUK ─────────────────────────────────
        Route::prefix('pengajuan')->name('pengajuan.')->group(function () {
            Route::get('/masuk', [MarketingPengajuanController::class, 'masuk'])->name('masuk');
            Route::get('/masuk/export', [MarketingPengajuanController::class, 'export'])->name('masuk.export');
            
            Route::get('/antrian-admin', [MarketingPengajuanController::class, 'menungguAdmin'])->name('antrian.admin');
            Route::get('/revisi', [MarketingPengajuanController::class, 'revisi'])->name('data.revisi');
            Route::get('/ditolak', [MarketingPengajuanController::class, 'ditolak'])->name('ditolak');
            Route::get('/{pengajuan}', [MarketingPengajuanController::class, 'show'])->name('show');
            Route::post('/{pengajuan}/ambil', [MarketingPengajuanController::class, 'ambil'])->name('ambil');
            Route::get('/start-verifikasi/{pengajuan}', [MarketingPengajuanController::class, 'ambil'])->name('start-verifikasi');
        });

        // ── VERIFIKASI ──────────────────────────────────────
        Route::prefix('verifikasi')->name('verifikasi.')->group(function () {

            // VERIFIKASI DOKUMEN
            Route::get('/dokumen', [MarketingVerifikasiController::class, 'index'])->name('dokumen');
            Route::get('/dokumen/{pengajuan}', [MarketingVerifikasiController::class, 'show'])->name('dokumen.show');
            Route::post('/dokumen/{pengajuan}', [MarketingVerifikasiController::class, 'store'])->name('dokumen.store');
            Route::put('/dokumen/{pengajuan}', [MarketingVerifikasiController::class, 'update'])->name('dokumen.update');
            Route::get('/dokumen/export', [MarketingVerifikasiController::class, 'dokumenExport'])->name('dokumen.export');

            // API Routes untuk AJAX (PREVIEW & DOWNLOAD)
            Route::get('/pengajuan/{pengajuan}/dokumen', [MarketingVerifikasiController::class, 'getDokumenByPengajuan'])->name('get-dokumen');
            Route::get('/dokumen/download/{dokumen}', [MarketingVerifikasiController::class, 'downloadDokumen'])->name('dokumen.download');
            Route::get('/dokumen/preview/{dokumen}', [MarketingVerifikasiController::class, 'previewDokumen'])->name('dokumen.preview');

            // PROSES VERIFIKASI (VIA AJAX)
            Route::post('/proses/{pengajuan}', [MarketingVerifikasiController::class, 'prosesVerifikasi'])->name('proses');

            // DETAIL DAN REKAP
            Route::get('/detail/{pengajuan}', [MarketingVerifikasiController::class, 'detail'])->name('detail');
            Route::get('/rekap', [MarketingVerifikasiController::class, 'rekap'])->name('rekap');
            Route::get('/rekap/export', [MarketingVerifikasiController::class, 'rekapExport'])->name('rekap.export');
            Route::get('/cetak/{pengajuan}', [MarketingVerifikasiController::class, 'cetak'])->name('cetak');

            // HASIL VERIFIKASI (READ-ONLY)
            Route::get('/hasil/{pengajuan}', [MarketingVerifikasiController::class, 'hasil'])->name('hasil');
        });

        // ── RIWAYAT LENGKAP ─────────────────────────────────
        Route::prefix('riwayat')->name('riwayat.')->group(function () {
            Route::get('/semua', [MarketingRiwayatController::class, 'semua'])->name('semua');
            Route::get('/export', [MarketingRiwayatController::class, 'export'])->name('export');
            Route::get('/detail/{pengajuan}', [MarketingRiwayatController::class, 'detail'])->name('detail');
        });

        // ── LAPORAN MARKETING ───────────────────────────────
        Route::prefix('laporan')->name('laporan.')->group(function () {
            Route::get('/kinerja', [MarketingLaporanController::class, 'kinerja'])->name('kinerja');
            Route::get('/kinerja/export', [MarketingLaporanController::class, 'export'])->name('kinerja.export');
            Route::get('/target', [MarketingLaporanController::class, 'target'])->name('target');
        });

        // ── NOTIFIKASI ──────────────────────────────────────
        Route::prefix('notifikasi')->name('notifikasi.')->group(function () {
            Route::get('/', [MarketingNotifikasiController::class, 'index'])->name('index');
            Route::post('/{id}/baca', [MarketingNotifikasiController::class, 'tandaiBaca'])->name('baca');
            Route::post('/semua/baca', [MarketingNotifikasiController::class, 'tandaiSemuaBaca'])->name('baca-semua');
        });
    });



    // ============================================================



    // ROUTE MANAJER
// Tugas: Monitoring pengajuan, kinerja, laporan & analisis
// ============================================================
Route::middleware(['auth', 'role:manajer'])
    ->prefix('manajer')
    ->name('manajer.')
    ->group(function () {

        // Dashboard Monitoring
        Route::get('/dashboard', [ManajerDashboardController::class, 'index'])
            ->name('dashboard');

        // ── MONITORING PENGAJUAN ────────────────────────────
        Route::prefix('pengajuan')->name('pengajuan.')->group(function () {

            // Semua Pengajuan
            Route::get('/semua', [ManajerPengajuanController::class, 'semua'])
                ->name('semua');

            // Sedang Diproses
            Route::get('/proses', [ManajerPengajuanController::class, 'sedangDiproses'])
                ->name('proses');

            // Selesai Dinilai
            Route::get('/selesai', [ManajerPengajuanController::class, 'selesai'])
                ->name('selesai');

            // Detail pengajuan (read-only)
            Route::get('/{pengajuan}', [ManajerPengajuanController::class, 'show'])
                ->name('show');
        });

        // ── MONITORING KINERJA ──────────────────────────────
        Route::prefix('kinerja')->name('kinerja.')->group(function () {

            // Kinerja Marketing
            Route::get('/marketing', [ManajerKinerjaController::class, 'marketing'])
                ->name('marketing');

            // Kinerja Admin
            Route::get('/admin', [ManajerKinerjaController::class, 'admin'])
                ->name('admin');
        });

        // ── LAPORAN & STATISTIK ─────────────────────────────
        Route::prefix('laporan')->name('laporan.')->group(function () {

            // Laporan Bulanan
            Route::get('/bulanan', [ManajerLaporanController::class, 'bulanan'])
                ->name('bulanan');

            // Laporan Tahunan
            Route::get('/tahunan', [ManajerLaporanController::class, 'tahunan'])
                ->name('tahunan');

            // Export Data
            Route::get('/export', [ManajerLaporanController::class, 'exportIndex'])
                ->name('export');

            Route::post('/export', [ManajerLaporanController::class, 'exportProses'])
                ->name('export.proses');
        });

        // ── ANALISIS DATA ───────────────────────────────────
        Route::prefix('analisis')->name('analisis.')->group(function () {

            // Statistik Penilaian
            Route::get('/statistik', [ManajerAnalisisController::class, 'statistikPenilaian'])
                ->name('statistik');

            // Tren Pengajuan
            Route::get('/tren', [ManajerAnalisisController::class, 'trenPengajuan'])
                ->name('tren');
        });

        // ── REKAP PENILAIAN (opsional tambahan) ─────────────
        Route::prefix('penilaian')->name('penilaian.')->group(function () {

            // Rekap semua penilaian SMART
            Route::get('/', [ManajerPenilaianController::class, 'index'])
                ->name('index');

            // Detail hasil penilaian
            Route::get('/{penilaian}', [ManajerPenilaianController::class, 'show'])
                ->name('show');
        });
    });



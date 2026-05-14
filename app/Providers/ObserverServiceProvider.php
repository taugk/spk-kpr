<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Models\{DokumenPengajuan, Pengajuan, Penilaian, User};
use App\Observers\{DokumenPengajuanObserver, PengajuanObserver, PenilaianObserver, UserObserver};

class ObserverServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Pastikan model di bawah ini sudah ada. Jika belum, buat dengan artisan make:model.
        if (class_exists(Pengajuan::class)) {
            Pengajuan::observe(PengajuanObserver::class);
        }

        if (class_exists(Penilaian::class)) {
            Penilaian::observe(PenilaianObserver::class);
        }

        if (class_exists(DokumenPengajuan::class)) {
            DokumenPengajuan::observe(DokumenPengajuanObserver::class);
        }

        if (class_exists(User::class)) {
            User::observe(UserObserver::class);
        }
    }
}

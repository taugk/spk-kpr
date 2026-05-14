<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('debitur_pekerjaan', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->unique();
            $table->enum('status_pekerjaan', ['karyawan_swasta', 'karyawan_bumn', 'pns', 'tni_polri', 'wiraswasta', 'profesional', 'lainnya']);
            $table->string('nama_perusahaan', 150);
            $table->string('bidang_usaha', 100)->nullable();
            $table->string('jabatan', 100)->nullable();
            $table->enum('status_kepegawaian', ['tetap', 'kontrak', 'percobaan', 'pemilik'])->nullable();
            $table->unsignedTinyInteger('lama_bekerja_tahun')->default(0);
            $table->unsignedTinyInteger('lama_bekerja_bulan')->default(0);
            $table->text('alamat_perusahaan')->nullable();
            $table->string('kota_perusahaan', 100)->nullable();
            $table->string('telp_perusahaan', 20)->nullable();
            $table->string('npwp', 20)->nullable();
            $table->decimal('penghasilan_pokok', 15, 2)->default(0);
            $table->decimal('tunjangan_tetap', 15, 2)->default(0);
            $table->decimal('penghasilan_lain', 15, 2)->default(0);
            $table->decimal('total_penghasilan', 15, 2)->storedAs('penghasilan_pokok + tunjangan_tetap + penghasilan_lain');
            $table->timestamps();

            $table->foreign('user_id', 'fk_pekerjaan_user')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('debitur_pekerjaan');
    }
};

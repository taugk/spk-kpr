<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('pengajuan_id');
            $table->unsignedInteger('penilaian_id')->nullable();
            $table->enum('jenis_laporan', ['hasil_penilaian', 'surat_persetujuan', 'surat_penolakan', 'rekap_bulanan']);
            $table->string('nomor_laporan', 50)->unique();
            $table->unsignedInteger('dibuat_oleh');
            $table->string('path_file', 500)->nullable();
            $table->dateTime('tgl_cetak')->useCurrent();

            $table->foreign('pengajuan_id', 'fk_laporan_pengajuan')->references('id')->on('pengajuan');
            $table->foreign('penilaian_id', 'fk_laporan_penilaian')->references('id')->on('penilaian');
            $table->foreign('dibuat_oleh', 'fk_laporan_pembuat')->references('id')->on('users');
        });

        Schema::create('notifikasi', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('pengajuan_id')->nullable();
            $table->string('judul', 200);
            $table->text('pesan');
            $table->enum('tipe', ['info', 'sukses', 'peringatan', 'error'])->default('info');
            $table->boolean('dibaca')->default(false);
            $table->dateTime('tgl_dibaca')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('user_id', 'fk_notif_user')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('pengajuan_id', 'fk_notif_pengajuan')->references('id')->on('pengajuan')->nullOnDelete();
            $table->index(['user_id', 'dibaca'], 'idx_notif_user_dibaca');
        });

        Schema::create('pengaturan', function (Blueprint $table) {
            $table->increments('id');
            $table->string('kunci', 100)->unique();
            $table->text('nilai');
            $table->string('keterangan', 300)->nullable();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengaturan');
        Schema::dropIfExists('notifikasi');
        Schema::dropIfExists('laporan');
    }
};

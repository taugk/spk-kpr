<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('debitur_pribadi', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->unique();
            $table->char('nik', 16)->unique();
            $table->string('tempat_lahir', 100);
            $table->date('tanggal_lahir');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->enum('agama', ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu', 'Lainnya'])->nullable();
            $table->enum('status_pernikahan', ['belum_menikah', 'menikah', 'cerai']);
            $table->unsignedTinyInteger('jumlah_tanggungan')->default(0);
            $table->enum('pendidikan_terakhir', ['SD', 'SMP', 'SMA', 'D1', 'D2', 'D3', 'S1', 'S2', 'S3'])->nullable();
            $table->enum('kewarganegaraan', ['WNI', 'WNA'])->default('WNI');
            $table->string('nama_ibu_kandung', 100);
            $table->char('no_kk', 16)->nullable();
            $table->string('nama_pasangan', 100)->nullable();
            $table->char('nik_pasangan', 16)->nullable();
            $table->text('alamat_ktp');
            $table->string('rt_rw', 10)->nullable();
            $table->string('kelurahan', 100)->nullable();
            $table->string('kecamatan', 100)->nullable();
            $table->string('kota', 100);
            $table->string('provinsi', 100);
            $table->char('kode_pos', 5)->nullable();
            $table->enum('status_tempat_tinggal', ['milik_sendiri', 'sewa', 'keluarga', 'lainnya'])->nullable();
            $table->string('no_telepon', 20);
            $table->string('no_hp', 20);
            $table->string('email_aktif', 100);
            $table->string('pas_foto')->nullable();
            $table->timestamps();

            $table->foreign('user_id', 'fk_pribadi_user')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('debitur_pribadi');
    }
};

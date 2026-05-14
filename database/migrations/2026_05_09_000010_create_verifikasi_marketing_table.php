<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('verifikasi_marketing', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('pengajuan_id')->unique();
            $table->unsignedInteger('marketing_id');
            $table->boolean('dok_ktp_valid')->default(false);
            $table->boolean('dok_kk_valid')->default(false);
            $table->boolean('dok_slip_gaji_valid')->default(false);
            $table->boolean('dok_rek_koran_valid')->default(false);
            $table->boolean('dok_slik_valid')->default(false);
            $table->boolean('dok_surat_kerja_valid')->default(false);
            $table->boolean('dok_npwp_valid')->default(false);
            $table->date('tgl_kunjungan')->nullable();
            $table->text('alamat_dikunjungi')->nullable();
            $table->boolean('kondisi_sesuai_data')->nullable();
            $table->boolean('penghasilan_terverif')->nullable();
            $table->text('catatan_lapangan')->nullable();
            $table->enum('rekomendasi_marketing', ['layak', 'perlu_pertimbangan', 'tidak_layak'])->nullable();
            $table->enum('keputusan', ['ajukan_ke_admin', 'minta_revisi', 'tolak'])->nullable();
            $table->text('alasan_keputusan')->nullable();
            $table->dateTime('tgl_keputusan')->nullable();
            $table->timestamps();

            $table->foreign('pengajuan_id', 'fk_verif_pengajuan')->references('id')->on('pengajuan')->cascadeOnDelete();
            $table->foreign('marketing_id', 'fk_verif_marketing')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verifikasi_marketing');
    }
};

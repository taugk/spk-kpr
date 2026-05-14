<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{DB, Schema};

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penilaian', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('pengajuan_id')->unique();
            $table->unsignedInteger('admin_id');
            $table->dateTime('tgl_penilaian')->useCurrent();
            $table->decimal('skor_akhir', 8, 4)->nullable();
            $table->decimal('threshold', 8, 4)->default(60);
            $table->enum('hasil', ['layak', 'tidak_layak'])->nullable();
            $table->text('catatan_admin')->nullable();
            $table->timestamps();

            $table->foreign('pengajuan_id', 'fk_penilaian_pengajuan')->references('id')->on('pengajuan')->cascadeOnDelete();
            $table->foreign('admin_id', 'fk_penilaian_admin')->references('id')->on('users');
            $table->index('hasil', 'idx_penilaian_hasil');
        });

        Schema::create('penilaian_detail', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('penilaian_id');
            $table->unsignedInteger('kriteria_id');
            $table->decimal('nilai_input', 15, 4);
            $table->decimal('nilai_normalisasi', 8, 6);
            $table->decimal('bobot_snapshot', 5, 4);
            $table->decimal('skor_kontribusi', 8, 4);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('penilaian_id', 'fk_detail_penilaian')->references('id')->on('penilaian')->cascadeOnDelete();
            $table->foreign('kriteria_id', 'fk_detail_kriteria')->references('id')->on('kriteria');
            $table->unique(['penilaian_id', 'kriteria_id'], 'uq_penilaian_kriteria');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penilaian_detail');
        Schema::dropIfExists('penilaian');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{DB, Schema};

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kriteria', function (Blueprint $table) {
            $table->increments('id');
            $table->string('kode_kriteria', 10)->unique();
            $table->string('nama_kriteria', 100);
            $table->text('deskripsi')->nullable();
            $table->enum('tipe', ['benefit', 'cost']);
            // PERBAIKAN: ubah jadi DECIMAL(5,2) agar bisa simpan 25.00
            $table->decimal('bobot', 5, 2)->default(0);  // Dari 5,4 jadi 5,2
            $table->string('satuan', 50)->nullable();
            $table->decimal('nilai_min', 15, 4)->nullable();
            $table->decimal('nilai_max', 15, 4)->nullable();
            $table->unsignedTinyInteger('urutan')->default(1);
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });

        Schema::create('kriteria_skala', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('kriteria_id');
            $table->unsignedTinyInteger('skor');
            $table->string('keterangan', 200);
            $table->decimal('nilai_min', 15, 4)->nullable();
            $table->decimal('nilai_max', 15, 4)->nullable();
            $table->foreign('kriteria_id', 'fk_skala_kriteria')->references('id')->on('kriteria')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kriteria_skala');
        Schema::dropIfExists('kriteria');
    }
};
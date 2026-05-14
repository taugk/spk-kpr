<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{DB, Schema};

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proyek', function (Blueprint $table) {
            $table->increments('id');
            $table->string('kode_proyek', 20)->unique();
            $table->string('nama_proyek', 150);
            $table->text('lokasi');
            $table->string('kota', 100);
            $table->string('provinsi', 100);
            $table->text('deskripsi')->nullable();
            $table->json('foto_proyek')->nullable();
            $table->enum('status', ['aktif', 'tutup', 'habis'])->default('aktif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proyek');
    }
};

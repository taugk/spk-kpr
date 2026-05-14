<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipe_unit', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('proyek_id');
            $table->string('kode_tipe', 20);
            $table->string('nama_tipe', 100);
            $table->decimal('luas_tanah', 8, 2);
            $table->decimal('luas_bangunan', 8, 2);
            $table->unsignedTinyInteger('jumlah_kamar')->nullable();
            $table->unsignedTinyInteger('jumlah_wc')->nullable();
            $table->decimal('harga', 15, 2);
            $table->unsignedSmallInteger('stok_tersedia')->default(0);
            $table->json('gambar')->nullable();
            $table->timestamps();

            $table->foreign('proyek_id', 'fk_tipe_proyek')->references('id')->on('proyek');
            $table->index('proyek_id', 'idx_tipe_proyek');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tipe_unit');
    }
};

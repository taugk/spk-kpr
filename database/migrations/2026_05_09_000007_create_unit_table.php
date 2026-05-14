<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{DB, Schema};

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('unit', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('tipe_unit_id');
            $table->string('kode_unit', 20)->unique();
            $table->json('foto_unit')->nullable();
            $table->json('fasilitas')->nullable();
            $table->enum('status', ['tersedia', 'dipesan', 'terjual', 'dibatalkan'])->default('tersedia');
            $table->timestamps();

            $table->foreign('tipe_unit_id', 'fk_unit_tipe')->references('id')->on('tipe_unit');
            $table->index('status', 'idx_unit_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unit');
    }
};

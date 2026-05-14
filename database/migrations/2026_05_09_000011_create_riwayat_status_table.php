<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riwayat_status', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('pengajuan_id');
            $table->string('status_lama', 50)->nullable();
            $table->string('status_baru', 50);
            $table->unsignedInteger('diubah_oleh')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('pengajuan_id', 'fk_riwayat_pengajuan')->references('id')->on('pengajuan')->cascadeOnDelete();
            $table->foreign('diubah_oleh', 'fk_riwayat_user')->references('id')->on('users');
            $table->index('pengajuan_id', 'idx_riwayat_pengajuan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_status');
    }
};

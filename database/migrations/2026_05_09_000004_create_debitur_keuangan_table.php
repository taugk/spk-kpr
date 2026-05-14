<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('debitur_keuangan', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->unique();
            $table->string('nama_bank', 100);
            $table->string('nomor_rekening', 30);
            $table->string('nama_pemilik_rekening', 100);
            $table->enum('jenis_rekening', ['tabungan', 'giro', 'deposito'])->default('tabungan');
            $table->decimal('rata_saldo_3bln', 15, 2)->default(0);
            $table->decimal('rata_mutasi_kredit', 15, 2)->default(0);
            $table->decimal('total_cicilan_perbulan', 15, 2)->default(0);
            $table->unsignedTinyInteger('jumlah_kredit_aktif')->default(0);
            $table->decimal('limit_kartu_kredit', 15, 2)->default(0);
            $table->decimal('tagihan_kartu_kredit', 15, 2)->default(0);
            $table->boolean('memiliki_kpr_aktif')->default(false);
            $table->decimal('sisa_pokok_kpr_aktif', 15, 2)->default(0);
            $table->enum('status_kredit', ['lancar', 'dpk', 'kurang_lancar', 'diragukan', 'macet'])->default('lancar');
            $table->boolean('pernah_gagal_bayar')->default(false);
            $table->decimal('aset_properti_lain', 15, 2)->default(0);
            $table->decimal('aset_kendaraan', 15, 2)->default(0);
            $table->decimal('aset_tabungan_deposito', 15, 2)->default(0);
            $table->decimal('aset_investasi_lain', 15, 2)->default(0);
            $table->decimal('rasio_cicilan', 5, 2)->nullable();
            $table->timestamps();

            $table->foreign('user_id', 'fk_keuangan_user')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('debitur_keuangan');
    }
};

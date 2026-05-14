<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajuan', function (Blueprint $table) {
            $table->increments('id');
            $table->string('kode_pengajuan', 30)->unique();
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('unit_id');
            $table->decimal('harga_properti', 15, 2);
            $table->decimal('uang_muka', 15, 2);
            $table->decimal('persen_dp', 5, 2);
            $table->decimal('jumlah_pinjaman', 15, 2);
            $table->unsignedTinyInteger('tenor_tahun');
            $table->decimal('estimasi_angsuran', 15, 2)->nullable();
            $table->decimal('rasio_angsuran', 5, 2)->nullable();
            $table->enum('tujuan_pembelian', ['hunian_sendiri', 'investasi'])->default('hunian_sendiri');
            $table->enum('sumber_dp', ['tabungan', 'keluarga', 'jual_aset', 'lainnya'])->nullable();
            $table->enum('status', ['draft', 'submitted', 'verifikasi_marketing', 'revisi_debitur', 'ditolak_marketing', 'antrian_admin', 'penilaian_admin', 'selesai_dinilai', 'ditolak_sistem', 'disetujui_sistem'])->default('draft');
            $table->dateTime('tgl_submitted')->nullable();
            $table->dateTime('tgl_marketing_proses')->nullable();
            $table->dateTime('tgl_admin_proses')->nullable();
            $table->dateTime('tgl_selesai')->nullable();
            $table->unsignedInteger('marketing_id')->nullable();
            $table->unsignedInteger('admin_id')->nullable();
            $table->text('catatan_debitur')->nullable();
            $table->timestamps();

            $table->foreign('user_id', 'fk_pengajuan_user')->references('id')->on('users');
            $table->foreign('unit_id', 'fk_pengajuan_unit')->references('id')->on('unit');
            $table->foreign('marketing_id', 'fk_pengajuan_mkt')->references('id')->on('users');
            $table->foreign('admin_id', 'fk_pengajuan_admin')->references('id')->on('users');
            $table->index('status', 'idx_pengajuan_status');
            $table->index('user_id', 'idx_pengajuan_user');
            $table->index('marketing_id', 'idx_pengajuan_mkt');
            $table->index('admin_id', 'idx_pengajuan_admin');
            $table->index('tgl_submitted', 'idx_pengajuan_tgl');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan');
    }
};

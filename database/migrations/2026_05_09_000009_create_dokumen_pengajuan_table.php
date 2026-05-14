<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dokumen_pengajuan', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('pengajuan_id');
            $table->enum('jenis_dokumen', ['ktp','kk','npwp','buku_nikah','akta_cerai','ktp_pasangan','pas_foto','slip_gaji','surat_keterangan_kerja','sk_pengangkatan','spt_pph21','rekening_koran','slik_ojk','tagihan_kartu_kredit','bukti_cicilan_aktif','siup_nib','laporan_keuangan_usaha','rekening_koran_usaha','surat_izin_praktik','lainnya']);
            $table->string('nama_file');
            $table->string('path_file', 500);
            $table->unsignedInteger('ukuran_file')->nullable();
            $table->string('mime_type', 100)->nullable();
            $table->enum('status_verifikasi', ['belum_diperiksa', 'valid', 'tidak_valid', 'perlu_revisi'])->default('belum_diperiksa');
            $table->string('catatan_verifikasi', 500)->nullable();
            $table->unsignedInteger('diperiksa_oleh')->nullable();
            $table->dateTime('tgl_diperiksa')->nullable();
            $table->timestamps();

            $table->foreign('pengajuan_id', 'fk_dokumen_pengajuan')->references('id')->on('pengajuan')->cascadeOnDelete();
            $table->foreign('diperiksa_oleh', 'fk_dokumen_pemeriksa')->references('id')->on('users');
            $table->index('pengajuan_id', 'idx_dokumen_pengajuan');
            $table->index('jenis_dokumen', 'idx_dokumen_jenis');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dokumen_pengajuan');
    }
};

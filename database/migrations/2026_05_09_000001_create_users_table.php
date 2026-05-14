<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nama_lengkap', 100);
            $table->string('email', 100)->unique();
            $table->string('password');
            $table->enum('role', ['debitur', 'marketing', 'admin', 'manajer']);
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->string('foto_profil')->nullable();
            $table->dateTime('last_login')->nullable();
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index('role', 'idx_users_role');
            $table->index('status', 'idx_users_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};

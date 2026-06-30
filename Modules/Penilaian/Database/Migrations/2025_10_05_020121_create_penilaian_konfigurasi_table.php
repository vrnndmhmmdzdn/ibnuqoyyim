<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penilaian_konfigurasi', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tahun_ajaran_id')
                ->constrained('tahun_ajarans')
                ->cascadeOnDelete();

            $table->decimal('bobot_harian', 5, 2)->default(30);
            $table->decimal('bobot_tugas',  5, 2)->default(20);
            $table->decimal('bobot_pts',    5, 2)->default(20);
            $table->decimal('bobot_pas',    5, 2)->default(30);

            $table->timestamps();

            $table->unique('tahun_ajaran_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penilaian_konfigurasi');
    }
};
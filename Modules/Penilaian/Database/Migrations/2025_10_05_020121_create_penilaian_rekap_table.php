<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penilaian_rekap', function (Blueprint $table) {
            $table->id();

            $table->foreignId('kelas_id')
                ->constrained('kelas')
                ->cascadeOnDelete();

            $table->foreignId('siswa_id')
                ->constrained('siswas')
                ->cascadeOnDelete();

            $table->foreignId('mata_pelajaran_id')
                ->constrained('mata_pelajarans')
                ->cascadeOnDelete();

            $table->foreignId('tahun_ajaran_id')
                ->constrained('tahun_ajarans')
                ->cascadeOnDelete();

            $table->enum('semester', ['1', '2']);

            $table->decimal('rata_harian', 5, 2)->nullable();
            $table->decimal('rata_tugas',  5, 2)->nullable();
            $table->decimal('nilai_pts',   5, 2)->nullable();
            $table->decimal('nilai_pas',   5, 2)->nullable();
            $table->decimal('nilai_akhir', 5, 2)->nullable();

            $table->enum('predikat', ['A', 'B', 'C', 'D'])->nullable();

            $table->timestamps();

            $table->unique(
                ['siswa_id', 'mata_pelajaran_id', 'tahun_ajaran_id', 'semester'],
                'penilaian_rekap_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penilaian_rekap');
    }
};
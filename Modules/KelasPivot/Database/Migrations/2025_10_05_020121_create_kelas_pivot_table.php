<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kelas_pivot', function (Blueprint $table) {
            $table->id();

            $table->foreignId('kelas_id')
                ->constrained('kelas')
                ->cascadeOnDelete();

            $table->foreignId('siswa_id')
                ->constrained('siswas')
                ->cascadeOnDelete();

            $table->foreignId('tahun_ajaran_id')
                ->constrained('tahun_ajarans')
                ->cascadeOnDelete();

            $table->boolean('is_aktif')->default(true);
            $table->timestamps();

            $table->unique(
                ['kelas_id', 'siswa_id', 'tahun_ajaran_id'],
                'kelas_pivot_unique'
            );
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kelas_pivot');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penilaian_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('kelas_id')
                ->constrained('kelas')
                ->cascadeOnDelete();

            $table->foreignId('mata_pelajaran_id')
                ->constrained('mata_pelajarans')
                ->cascadeOnDelete();

            $table->foreignId('guru_id')
                ->nullable()
                ->constrained('gurus')
                ->nullOnDelete();

            $table->foreignId('tahun_ajaran_id')
                ->constrained('tahun_ajarans')
                ->cascadeOnDelete();

            $table->enum('semester', ['1', '2']);
            $table->enum('jenis', ['harian', 'tugas', 'pts', 'pas']);
            $table->string('judul');
            $table->date('tanggal');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penilaian_items');
    }
};
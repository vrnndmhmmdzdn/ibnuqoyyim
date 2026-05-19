<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('jadwal_pelajarans', function (Blueprint $table) {
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

            $table->enum('hari', [
                'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'
            ]);

            $table->time('jam_mulai');
            $table->time('jam_selesai');

            $table->timestamps();
            $table->softDeletes();

            // Satu kelas tidak bisa punya 2 mapel di hari + jam yang sama
            $table->unique(
                ['kelas_id', 'hari', 'jam_mulai', 'tahun_ajaran_id'],
                'jadwal_unik_per_kelas'
            );

            // Satu guru tidak bisa mengajar di 2 kelas di hari + jam yang sama
            $table->unique(
                ['guru_id', 'hari', 'jam_mulai', 'tahun_ajaran_id'],
                'jadwal_unik_per_guru'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_pelajarans');
    }
};
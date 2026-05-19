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
        Schema::create('kaldiks', function (Blueprint $table) {
            $table->id();
            $table->string('nama_acara');
            $table->string('kegiatan');
            $table->enum('kategori', ['Akademik', 'Non-Akademik', 'Ujian', 'Libur']);
            $table->enum('subject', ['Kelas 1', 'Kelas 2', 'Kelas 3', 'Kelas 4', 'Kelas 5', 'Kelas 6', 'Semua Kelas']);
            $table->string('tahun_ajaran');
            $table->datetime('jam_mulai');
            $table->datetime('jam_selesai');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kaldiks');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jurnal_gurus', function (Blueprint $table) {
            $table->id();

            $table->foreignId('guru_id')
                ->constrained('gurus')
                ->cascadeOnDelete();

            $table->foreignId('kelas_id')
                ->constrained('kelas')
                ->cascadeOnDelete();

            $table->foreignId('mata_pelajaran_id')
                ->constrained('mata_pelajarans')
                ->cascadeOnDelete();

            $table->foreignId('tahun_ajaran_id')
                ->constrained('tahun_ajarans')
                ->cascadeOnDelete();

            $table->date('tanggal');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->unsignedSmallInteger('pertemuan_ke')->nullable();

            $table->string('materi');
            $table->text('kompetensi_dasar');
            $table->text('deskripsi_kegiatan');

            $table->enum('metode_pembelajaran', [
                'ceramah', 'diskusi', 'praktik',
                'demonstrasi', 'tanya_jawab', 'penugasan', 'lainnya'
            ]);
            $table->string('media_pembelajaran')->nullable();

            $table->unsignedSmallInteger('jumlah_hadir')->default(0);
            $table->unsignedSmallInteger('jumlah_tidak_hadir')->default(0);

            $table->enum('capaian', ['tercapai', 'sebagian', 'belum'])
                ->default('tercapai');
            $table->text('tindak_lanjut')->nullable();
            $table->text('catatan')->nullable();

            $table->enum('status', ['draft', 'submitted'])->default('draft');
            $table->timestamp('submitted_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Satu guru tidak bisa isi 2 jurnal untuk kelas+mapel yang sama di hari yang sama
            $table->unique(
                ['guru_id', 'kelas_id', 'mata_pelajaran_id', 'tanggal'],
                'jurnal_unik_per_hari'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jurnal_gurus');
    }
};
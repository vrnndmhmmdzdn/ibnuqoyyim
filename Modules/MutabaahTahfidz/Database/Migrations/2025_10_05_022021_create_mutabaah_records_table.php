<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mutabaah_records', function (Blueprint $table) {
            $table->id();

            $table->foreignId('kelas_id')
                ->constrained('kelas')
                ->cascadeOnDelete();

            $table->foreignId('siswa_id')
                ->constrained('siswas')
                ->cascadeOnDelete();

            $table->foreignId('surah_id')
                ->nullable()
                ->constrained('mutabaah_surahs')
                ->nullOnDelete();

            $table->foreignId('guru_id')
                ->nullable()
                ->constrained('gurus')
                ->nullOnDelete();

            $table->date('tanggal');

            $table->enum('status', [
                'lanjut',
                'ulang',
                'membaca',
                'tasmi',
                'tidak_setoran',
                'tidak_masuk',
            ])->default('lanjut');

            $table->unsignedSmallInteger('ayat_awal')->nullable();
            $table->unsignedSmallInteger('ayat_akhir')->nullable();
            $table->unsignedSmallInteger('jumlah_ayat')->default(0);

            $table->enum('nilai', [
                'rasib',
                'jayyid',
                'jayyid_jiddan',
                'mumtaz',
            ])->nullable();

            $table->text('catatan')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Satu siswa hanya bisa punya 1 record per hari per kelas
            $table->unique(
                ['kelas_id', 'siswa_id', 'tanggal'],
                'mutabaah_unique_per_hari'
            );

            $table->index(['kelas_id', 'tanggal']);
            $table->index(['siswa_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mutabaah_records');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('siswas', function (Blueprint $table) {
            $table->id();
            
            // --- DATA AKADEMIK UTAMA ---
            $table->string('nisn')->unique()->comment('Nomor Induk Siswa Nasional');
            $table->string('nis')->unique()->comment('Nomor Induk Siswa Lokal');
            $table->foreignId('angkatan_id')->nullable()->constrained('angkatan')->nullOnDelete(); // Relasi ke tabel angkatan
            $table->string('status_siswa')->default('aktif'); // aktif, lulus, pindah, drop-out
            $table->date('tanggal_masuk');

            // --- DATA PRIBADI ---
            $table->string('nama_lengkap');
            $table->string('nama_panggilan')->nullable();
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->string('nik')->unique()->nullable(); // Nomor Induk Kependudukan
            
            // --- KONTAK & DOMISILI ---
            $table->string('email')->unique()->nullable();
            $table->string('nomor_hp')->nullable();
            $table->text('alamat')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            // --- DATA ORANG TUA / WALI ---
            $table->string('nama_ayah')->nullable();
            $table->string('pekerjaan_ayah')->nullable();
            $table->string('nama_ibu')->nullable();
            $table->string('pekerjaan_ibu')->nullable();
            $table->string('nama_wali')->nullable(); // Jika ikut kakek/paman
            $table->string('nomor_hp_orang_tua');

            // --- ATRIBUT TAMBAHAN ---
            $table->string('foto_siswa')->nullable(); // Untuk menyimpan path gambar
            $table->text('catatan_medis')->nullable(); // Alergi, penyakit bawaan, dll

            $table->timestamps();
            $table->softDeletes(); // Keamanan data agar tidak langsung terhapus permanen
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('siswa');
    }
};

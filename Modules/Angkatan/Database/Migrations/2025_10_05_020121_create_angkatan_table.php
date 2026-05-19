<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('angkatan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_angkatan')->unique();
            $table->integer('angkatan_ke')->unique();            // 15 Juli 2025
            $table->string('tahun_mulai')->unique();    
            $table->string('status')->default('aktif');   
            $table->timestamps();
            $table->softDeletes();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('angkatan');
    }
};



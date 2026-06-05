<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mutabaah_surahs', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('no_surah')->unique();
            $table->string('nama_surah');
            $table->unsignedSmallInteger('jumlah_ayat');
            $table->unsignedSmallInteger('juz');
            $table->timestamps();
        });
    }
 
    public function down(): void
    {
        Schema::dropIfExists('mutabaah_surahs');
    }
};
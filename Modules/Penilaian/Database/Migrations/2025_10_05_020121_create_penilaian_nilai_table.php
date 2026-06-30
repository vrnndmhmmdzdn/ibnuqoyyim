<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penilaian_nilai', function (Blueprint $table) {
            $table->id();

            $table->foreignId('item_id')
                ->constrained('penilaian_items')
                ->cascadeOnDelete();

            $table->foreignId('siswa_id')
                ->constrained('siswas')
                ->cascadeOnDelete();

            $table->decimal('nilai', 5, 2)->nullable();
            $table->text('catatan')->nullable();

            $table->timestamps();

            $table->unique(['item_id', 'siswa_id']);
            $table->index('item_id');
            $table->index('siswa_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penilaian_nilai');
    }
};
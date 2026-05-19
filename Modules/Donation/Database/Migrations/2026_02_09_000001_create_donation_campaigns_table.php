<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donation_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->longText('description');
            $table->string('cover_image_path')->nullable();
            $table->unsignedBigInteger('target_amount');
            $table->unsignedBigInteger('collected_amount')->default(0);
            $table->timestamp('deadline_at')->nullable();
            $table->enum('status', ['draft', 'active', 'closed'])->default('draft');
            $table->timestamps();

            $table->index('status');
            $table->index('deadline_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donation_campaigns');
    }
};

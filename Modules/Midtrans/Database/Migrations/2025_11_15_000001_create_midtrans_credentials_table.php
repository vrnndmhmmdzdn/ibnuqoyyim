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
        Schema::create('midtrans_credentials', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('Default');
            $table->enum('environment', ['sandbox', 'production'])->default('sandbox');
            $table->text('server_key')->nullable(); // Will be encrypted
            $table->text('client_key')->nullable(); // Will be encrypted
            $table->string('merchant_id')->nullable();
            $table->boolean('is_sanitized')->default(true);
            $table->boolean('is_3ds')->default(true);
            $table->string('notification_url')->nullable();
            $table->string('finish_url')->nullable();
            $table->string('unfinish_url')->nullable();
            $table->string('error_url')->nullable();
            $table->boolean('is_active')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Index
            $table->index('environment');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('midtrans_credentials');
    }
};

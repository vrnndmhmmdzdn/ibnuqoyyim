<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assistant_audit_logs', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('telegram_user_id')
                ->constrained('telegram_users')
                ->cascadeOnDelete();

            $table->string('channel')->default('telegram');
            $table->text('user_message')->nullable();
            $table->string('tool_name')->nullable();
            $table->json('arguments')->nullable();
            $table->json('result')->nullable();
            $table->enum('status', ['answered', 'tool_executed', 'denied', 'error'])->default('answered');
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assistant_audit_logs');
    }
};
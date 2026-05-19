<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dynamic_form_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_id')->constrained('dynamic_forms')->cascadeOnDelete();
            $table->string('submission_token')->unique();
            $table->json('data'); // Jawaban dalam format JSON (key-value dari SurveyJS)
            $table->string('responder_email')->nullable();
            $table->string('responder_name')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('submitted_at');
            $table->timestamps();
            
            $table->index('form_id');
            $table->index('submitted_at');
            $table->index('submission_token');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dynamic_form_submissions');
    }
};

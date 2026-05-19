<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dynamic_forms', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('slug')->unique();
            $table->string('public_link')->unique();
            $table->json('schema'); // SurveyJS JSON schema
            $table->json('theme')->nullable(); // SurveyJS theme configuration
            $table->boolean('is_active')->default(true);
            $table->boolean('require_login')->default(false);
            $table->boolean('allow_multiple_submissions')->default(true);
            $table->boolean('collect_email')->default(false);
            $table->json('settings')->nullable(); // Settings tambahan (redirect URL, notifications, dll)
            $table->timestamp('expires_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('slug');
            $table->index('public_link');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dynamic_forms');
    }
};

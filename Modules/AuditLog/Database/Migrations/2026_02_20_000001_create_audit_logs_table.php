<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('event', 32);

            $table->string('auditable_type');
            $table->string('auditable_id');

            $table->nullableMorphs('actor');

            $table->ipAddress('ip_address')->nullable();
            $table->string('method', 16)->nullable();
            $table->text('url')->nullable();
            $table->text('user_agent')->nullable();

            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();

            $table->timestamp('created_at')->useCurrent();

            $table->index(['auditable_type', 'auditable_id']);
            $table->index('event');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};

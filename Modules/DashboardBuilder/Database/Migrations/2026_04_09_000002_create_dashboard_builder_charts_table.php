<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dashboard_builder_charts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('dashboard_id')
                ->constrained('dashboard_builder_dashboards')
                ->cascadeOnDelete();
            $table->string('title');
            $table->unsignedInteger('sort_order')->default(1);
            $table->json('config_json');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dashboard_builder_charts');
    }
};

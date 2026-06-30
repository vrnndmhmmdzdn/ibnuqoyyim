<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dashboard_builder_dashboards', function (Blueprint $table): void {
            $table->uuid('public_uuid')->nullable()->unique()->after('slug');
            $table->boolean('is_published')->default(false)->after('is_active');
            $table->boolean('published_requires_login')->default(false)->after('is_published');
            $table->timestamp('published_at')->nullable()->after('published_requires_login');
        });
    }

    public function down(): void
    {
        Schema::table('dashboard_builder_dashboards', function (Blueprint $table): void {
            $table->dropColumn([
                'public_uuid',
                'is_published',
                'published_requires_login',
                'published_at',
            ]);
        });
    }
};

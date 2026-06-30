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
        Schema::table('midtrans_transactions', function (Blueprint $table) {
            $table->string('updated_via')->nullable()->after('raw_response');
            $table->timestamp('status_updated_at')->nullable()->after('updated_via');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('midtrans_transactions', function (Blueprint $table) {
            $table->dropColumn(['updated_via', 'status_updated_at']);
        });
    }
};

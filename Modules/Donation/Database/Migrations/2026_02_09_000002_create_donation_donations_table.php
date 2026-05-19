<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donation_donations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('donation_campaigns')->cascadeOnDelete();
            $table->string('donor_name')->nullable();
            $table->string('donor_email')->nullable();
            $table->unsignedBigInteger('amount');
            $table->string('message', 200)->nullable();
            $table->boolean('is_anonymous')->default(false);
            $table->enum('status', ['draft', 'pending', 'paid', 'failed', 'expired', 'refunded'])->default('draft');
            $table->string('payment_provider')->default('midtrans');
            $table->string('provider_order_id')->unique();
            $table->string('provider_transaction_id')->nullable();
            $table->string('provider_payment_type')->nullable();
            $table->json('provider_raw_response')->nullable();
            $table->text('snap_token')->nullable();
            $table->text('snap_redirect_url')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('campaign_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donation_donations');
    }
};

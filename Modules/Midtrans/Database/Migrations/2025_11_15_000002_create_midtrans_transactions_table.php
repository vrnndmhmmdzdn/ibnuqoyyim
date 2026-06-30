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
        Schema::create('midtrans_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('midtrans_credential_id')->constrained('midtrans_credentials')->onDelete('cascade');
            $table->string('order_id')->unique();
            $table->string('transaction_id')->nullable();
            $table->decimal('gross_amount', 15, 2);
            $table->string('payment_type')->nullable();
            $table->string('transaction_status')->nullable();
            $table->string('fraud_status')->nullable();
            $table->timestamp('transaction_time')->nullable();
            $table->text('snap_token')->nullable();
            $table->text('snap_url')->nullable();
            $table->json('customer_details')->nullable();
            $table->json('item_details')->nullable();
            $table->json('metadata')->nullable();
            $table->text('raw_response')->nullable();
            $table->timestamps();

            // Index
            $table->index('order_id');
            $table->index('transaction_id');
            $table->index('transaction_status');
            $table->index('midtrans_credential_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('midtrans_transactions');
    }
};

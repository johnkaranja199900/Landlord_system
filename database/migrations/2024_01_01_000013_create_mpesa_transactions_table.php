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
        Schema::create('mpesa_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->nullable()->constrained()->nullOnDelete();
            $table->string('transaction_reference')->unique(); // M-Pesa transaction ID (e.g., QFH2D8A2B3)
            $table->string('merchant_request_id')->nullable();
            $table->string('checkout_request_id')->nullable();
            $table->string('account_reference'); // Tenant account/payment reference
            $table->string('phone_number');
            $table->decimal('amount', 12, 2);
            $table->timestamp('transaction_date');
            $table->string('result_code'); // Daraja result code
            $table->string('result_description')->nullable();
            $table->json('raw_payload')->nullable(); // Store full callback for audit
            $table->enum('processing_status', ['pending', 'processed', 'failed', 'reconciled'])->default('pending');
            $table->enum('reconciliation_status', ['unreconciled', 'reconciled', 'failed'])->default('unreconciled');
            $table->timestamp('reconciled_at')->nullable();
            $table->timestamps();
            
            // Unique constraint on transaction_reference for idempotency
            $table->unique('transaction_reference');
            
            // Indexes for efficient lookups
            $table->index('transaction_reference');
            $table->index('account_reference');
            $table->index('phone_number');
            $table->index('processing_status');
            $table->index('reconciliation_status');
            $table->index('transaction_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mpesa_transactions');
    }
};

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
        Schema::create('deposit_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deposit_id')->constrained('tenant_deposits')->onDelete('cascade');
            $table->enum('transaction_type', ['payment', 'refund', 'deduction', 'adjustment']);
            $table->decimal('amount', 12, 2);
            $table->foreignId('payment_id')->nullable()->constrained()->nullOnDelete();
            $table->string('reference')->nullable(); // Payment reference or receipt number
            $table->text('description')->nullable();
            $table->timestamps();
            
            $table->index('deposit_id');
            $table->index('transaction_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deposit_transactions');
    }
};

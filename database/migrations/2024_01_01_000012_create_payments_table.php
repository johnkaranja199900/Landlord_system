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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('landlord_id')->constrained()->onDelete('cascade');
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('tenancy_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('payment_method', ['mpesa', 'cash', 'bank_transfer', 'cheque', 'other']);
            $table->decimal('amount', 12, 2);
            $table->date('payment_date');
            $table->string('reference')->nullable(); // External reference (e.g., M-Pesa transaction ID)
            $table->enum('status', ['pending', 'confirmed', 'reversed', 'failed'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Indexes for frequent queries
            $table->index('landlord_id');
            $table->index('tenant_id');
            $table->index('payment_method');
            $table->index('status');
            $table->index('payment_date');
            $table->index('reference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};

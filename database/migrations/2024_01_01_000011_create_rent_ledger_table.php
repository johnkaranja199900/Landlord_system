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
        Schema::create('rent_ledger', function (Blueprint $table) {
            $table->id();
            $table->foreignId('landlord_id')->constrained()->onDelete('cascade');
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('tenancy_id')->constrained()->onDelete('cascade');
            $table->foreignId('unit_id')->constrained()->onDelete('cascade');
            $table->foreignId('invoice_id')->nullable()->constrained('rent_invoices')->nullOnDelete();
            $table->foreignId('payment_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('transaction_type', ['rent_charge', 'payment', 'adjustment', 'credit', 'reversal', 'waiver', 'transfer', 'refund']);
            $table->decimal('debit', 12, 2)->default(0); // Amount owed (charges)
            $table->decimal('credit', 12, 2)->default(0); // Amount paid/credited
            $table->string('reference')->nullable(); // Receipt number, payment reference, etc.
            $table->text('description')->nullable();
            $table->date('transaction_date');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete(); // Actor who created this entry
            $table->timestamps();
            
            // Indexes for efficient ledger queries
            $table->index('landlord_id');
            $table->index('tenant_id');
            $table->index('tenancy_id');
            $table->index('transaction_type');
            $table->index('transaction_date');
            $table->index(['tenant_id', 'transaction_date']);
            $table->index(['landlord_id', 'transaction_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rent_ledger');
    }
};

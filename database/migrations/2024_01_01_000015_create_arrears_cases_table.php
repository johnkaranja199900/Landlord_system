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
        Schema::create('arrears_cases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('tenancy_id')->constrained()->onDelete('cascade');
            $table->foreignId('unit_id')->constrained()->onDelete('cascade');
            $table->enum('risk_level', ['current', 'low', 'medium', 'high', 'critical']);
            $table->integer('months_overdue');
            $table->decimal('amount_overdue', 12, 2);
            $table->date('oldest_due_date');
            $table->enum('status', ['active', 'resolved', 'escalated'])->default('active');
            $table->timestamps();
            
            // Indexes for efficient queries
            $table->index('tenant_id');
            $table->index('risk_level');
            $table->index('status');
            $table->index(['risk_level', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('arrears_cases');
    }
};

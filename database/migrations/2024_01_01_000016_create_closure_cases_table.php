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
        Schema::create('closure_cases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('tenancy_id')->constrained()->onDelete('cascade');
            $table->foreignId('unit_id')->constrained()->onDelete('cascade');
            $table->text('reason');
            $table->decimal('arrears_amount', 12, 2);
            $table->integer('arrears_months');
            $table->date('notice_date')->nullable();
            $table->date('vacating_date')->nullable();
            $table->enum('status', ['pending_review', 'notice_issued', 'final_notice', 'vacating_scheduled', 'vacated', 'cancelled', 'resolved'])->default('pending_review');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            // Indexes for efficient queries
            $table->index('tenant_id');
            $table->index('status');
            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('closure_cases');
    }
};

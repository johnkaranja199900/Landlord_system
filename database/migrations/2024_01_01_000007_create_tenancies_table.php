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
        Schema::create('tenancies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('unit_id')->constrained()->onDelete('cascade');
            $table->date('start_date');
            $table->date('expected_end_date')->nullable();
            $table->date('actual_end_date')->nullable();
            $table->decimal('monthly_rent', 12, 2); // Rent at time of tenancy (snapshot)
            $table->decimal('deposit_required', 12, 2);
            $table->integer('due_day')->default(5); // Day of month rent is due (1-31)
            $table->enum('status', ['active', 'terminated', 'expired', 'evicted'])->default('active');
            $table->timestamps();
            
            // Prevent duplicate active tenancy for same unit
            // This is enforced at application level as well
            $table->index(['unit_id', 'status']);
            $table->index(['tenant_id', 'status']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenancies');
    }
};

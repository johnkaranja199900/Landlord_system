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
        Schema::create('tenant_deposits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenancy_id')->constrained()->onDelete('cascade');
            $table->decimal('required_amount', 12, 2);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->decimal('balance', 12, 2)->generatedAs('required_amount - paid_amount')->stored();
            $table->enum('status', ['pending', 'paid', 'refunded', 'partially_refunded', 'deducted'])->default('pending');
            $table->timestamps();
            
            $table->index('tenancy_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_deposits');
    }
};

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
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('block_id')->constrained()->onDelete('cascade');
            $table->string('unit_number');
            $table->string('floor')->nullable();
            $table->string('unit_type')->nullable(); // e.g., studio, 1br, 2br
            $table->enum('status', ['vacant', 'reserved', 'occupied', 'under_maintenance', 'blocked', 'closed'])->default('vacant');
            $table->decimal('current_rent', 12, 2)->nullable(); // Current rent amount
            $table->decimal('deposit_requirement', 12, 2)->nullable();
            $table->text('description')->nullable();
            $table->json('meter_info')->nullable(); // For utility meter information
            $table->timestamps();
            $table->softDeletes(); // Soft delete to preserve history
            
            // Unique constraint: unit_number per block
            $table->unique(['block_id', 'unit_number']);
            
            // Indexes for frequent queries
            $table->index('block_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};

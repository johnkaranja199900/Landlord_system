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
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('landlord_id')->constrained()->onDelete('cascade');
            $table->string('full_name');
            $table->string('phone');
            $table->string('alternative_phone')->nullable();
            $table->string('email')->nullable();
            $table->string('national_id_reference')->nullable(); // ID number or similar
            $table->string('address')->nullable();
            $table->string('emergency_contact')->nullable();
            $table->string('next_of_kin')->nullable();
            $table->string('account_number')->unique(); // Unique payment account/reference
            $table->enum('status', ['active', 'inactive', 'blacklisted'])->default('active');
            $table->date('registered_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Indexes for frequent queries and search
            $table->index('landlord_id');
            $table->index('status');
            $table->index('phone');
            $table->index('account_number');
            $table->index('full_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};

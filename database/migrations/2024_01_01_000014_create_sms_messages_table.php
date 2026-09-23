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
        Schema::create('sms_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('landlord_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete();
            $table->string('phone');
            $table->text('message');
            $table->enum('message_type', ['single', 'bulk', 'scheduled']);
            $table->string('provider')->default('textsms'); // SMS provider identifier
            $table->string('provider_message_id')->nullable(); // Message ID from provider
            $table->string('provider_status')->nullable(); // Provider response code
            $table->json('provider_response')->nullable(); // Full provider response
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->string('failure_reason')->nullable();
            $table->integer('retry_count')->default(0);
            $table->timestamps();
            
            // Indexes for efficient queries
            $table->index('landlord_id');
            $table->index('tenant_id');
            $table->index('message_type');
            $table->index('provider_status');
            $table->index('scheduled_at');
            $table->index('sent_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sms_messages');
    }
};

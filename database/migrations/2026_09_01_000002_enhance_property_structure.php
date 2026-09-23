<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Extend the existing Landlord > Property > Block > Unit > Tenant > Tenancy
     * hierarchy with utility configuration, tenancy allocation fields and
     * shared-occupancy support.
     */
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->boolean('shared_occupancy_enabled')->default(false)->after('status');
        });

        Schema::table('units', function (Blueprint $table) {
            $table->json('utility_config')->nullable()->after('meter_info');
            $table->boolean('shared_occupancy_allowed')->default(false)->after('utility_config');
        });

        Schema::table('tenancies', function (Blueprint $table) {
            $table->date('allocation_date')->nullable()->after('start_date');
            $table->date('expected_move_in_date')->nullable()->after('allocation_date');
            $table->decimal('deposit_paid', 12, 2)->default(0)->after('deposit_required');
            $table->foreignId('created_by')->nullable()->after('status')
                ->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('tenancies', function (Blueprint $table) {
            $table->dropConstrainedForeignId('created_by');
            $table->dropColumn(['allocation_date', 'expected_move_in_date', 'deposit_paid']);
        });
        Schema::table('units', function (Blueprint $table) {
            $table->dropColumn(['utility_config', 'shared_occupancy_allowed']);
        });
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn('shared_occupancy_enabled');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Configurable Role-Based Access Control tables:
     * roles, permissions, role_permission pivot, assigned_roles and permission_grants.
     *
     * Staff (and any user) may receive explicit per-user permission grants so their
     * access is restricted to exactly what was assigned to them.
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');            // display name
            $table->string('slug')->unique();  // machine name, e.g. super_admin, landlord
            $table->text('description')->nullable();
            $table->boolean('is_system')->default(false); // system roles cannot be deleted
            $table->timestamps();
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();  // e.g. units.update
            $table->string('module')->index(); // e.g. units, tenancies, reports
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('role_permission', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
            $table->unique(['role_id', 'permission_id']);
        });

        Schema::create('assigned_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->foreignId('landlord_id')->nullable()
                ->constrained('landlords')->nullOnDelete(); // scope of the assignment
            $table->unique(['user_id', 'role_id', 'landlord_id']);
            $table->index('user_id');
        });

        Schema::create('permission_grants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
            $table->foreignId('landlord_id')->nullable()
                ->constrained('landlords')->nullOnDelete();
            $table->unique(['user_id', 'permission_id', 'landlord_id']);
        });

        // Users gain an optional landlord link (staff / accountants belong to a landlord).
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('landlord_id')->nullable()->after('id')
                ->constrained('landlords')->nullOnDelete();
            $table->boolean('is_super_admin')->default(false)->after('landlord_id');
            $table->index('landlord_id');
        });

        // Seed the default configurable roles.
        $now = now();
        foreach ([
            ['Super Administrator', 'super-admin', 'Manages global configuration and all tenants of the platform.', true],
            ['Landlord', 'landlord', 'Owns properties and manages their own financial records.', true],
            ['Property Manager', 'property-manager', 'Manages property operations according to assigned permissions.', true],
            ['Accountant', 'accountant', 'Manages payments, invoices, receipts, rent ledgers and financial reports.', true],
            ['Property Staff', 'property-staff', 'Access restricted to explicitly assigned permissions only.', true],
        ] as [$name, $slug, $desc, $system]) {
            DB::table('roles')->insert([
                'name' => $name, 'slug' => $slug, 'description' => $desc,
                'is_system' => $system, 'created_at' => $now, 'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('landlord_id');
            $table->dropColumn('is_super_admin');
        });
        Schema::dropIfExists('permission_grants');
        Schema::dropIfExists('assigned_roles');
        Schema::dropIfExists('role_permission');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }
};

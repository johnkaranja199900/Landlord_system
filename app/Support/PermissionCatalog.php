<?php

namespace App\Support;

/**
 * Central, configurable permission catalog. Roles are mapped to these
 * permission slugs in the RolePermissionSeeder and can be adjusted at
 * runtime through the Super Administrator role management screens.
 */
class PermissionCatalog
{
    /**
     * @return array<string, array<int, string>> module => permission slugs
     */
    public static function all(): array
    {
        return [
            'global' => [
                'global.manage',           // super admin: system-wide configuration
                'roles.manage',            // create/edit roles & permission assignments
                'users.manage',            // manage platform users
                'landlords.manage',        // manage landlord accounts
            ],
            'properties' => [
                'properties.view',
                'properties.create',
                'properties.update',
                'properties.delete',
            ],
            'blocks' => [
                'blocks.view',
                'blocks.create',
                'blocks.update',
                'blocks.delete',
            ],
            'units' => [
                'units.view',
                'units.create',
                'units.update',
                'units.delete',          // soft delete only
                'units.update-rent',     // configure monthly rent (writes rent history)
            ],
            'tenants' => [
                'tenants.view',
                'tenants.create',
                'tenants.update',
                'tenants.delete',
            ],
            'tenancies' => [
                'tenancies.view',
                'tenancies.allocate',    // allocate tenant to unit
                'tenancies.terminate',   // move-out / vacating process
                'tenancies.update-status',
            ],
            'finance' => [
                'invoices.view',
                'invoices.generate',
                'payments.view',
                'payments.record',
                'payments.reverse',
                'ledger.view',
                'ledger.adjust',
                'reports.financial',
            ],
        ];
    }

    /**
     * Default role slug => permission slugs mapping (configurable afterwards).
     *
     * @return array<string, array<int, string>>
     */
    public static function defaultRolePermissions(): array
    {
        $flat = array_merge(...array_values(self::all()));

        return [
            // Super Administrator: everything (also hard-bypassed in code).
            'super-admin' => $flat,

            // Landlord: full control over their own properties & financial records.
            'landlord' => array_values(array_filter(
                $flat,
                fn ($p) => !str_starts_with($p, 'global.')
                    && !in_array($p, ['roles.manage', 'users.manage', 'landlords.manage'], true)
            )),

            // Property Manager: property operations (no finance mutation, no structure deletion).
            'property-manager' => [
                'properties.view',
                'blocks.view', 'blocks.create', 'blocks.update',
                'units.view', 'units.create', 'units.update', 'units.update-rent',
                'tenants.view', 'tenants.create', 'tenants.update',
                'tenancies.view', 'tenancies.allocate', 'tenancies.terminate', 'tenancies.update-status',
                'invoices.view', 'payments.view', 'ledger.view', 'reports.financial',
            ],

            // Accountant: payments, invoices, receipts, rent ledgers, financial reports.
            'accountant' => [
                'properties.view', 'units.view', 'tenants.view', 'tenancies.view',
                'invoices.view', 'invoices.generate',
                'payments.view', 'payments.record', 'payments.reverse',
                'ledger.view', 'ledger.adjust',
                'reports.financial',
            ],

            // Property Staff: NO default permissions — access must be explicitly granted.
            'property-staff' => [],
        ];
    }
}

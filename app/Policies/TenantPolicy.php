<?php

namespace App\Policies;

use App\Models\Tenant;
use App\Models\User;

/**
 * Tenant personal data is protected: a tenant record is only visible to
 * its own landlord's team (or a super admin). Other landlords are denied.
 */
class TenantPolicy
{
    private function owns(User $user, Tenant $tenant): bool
    {
        return $user->is_super_admin
            || (int) $user->landlord_id === (int) $tenant->landlord_id;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasPermission('tenants.view');
    }

    public function view(User $user, Tenant $tenant): bool
    {
        return $user->hasPermission('tenants.view') && $this->owns($user, $tenant);
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('tenants.create');
    }

    public function update(User $user, Tenant $tenant): bool
    {
        return $user->hasPermission('tenants.update') && $this->owns($user, $tenant);
    }

    /**
     * Tenants are never deleted once they have tenancy history —
     * close the tenancy instead. Soft delete allowed only for pristine records.
     */
    public function delete(User $user, Tenant $tenant): bool
    {
        if (! $user->hasPermission('tenants.delete') || ! $this->owns($user, $tenant)) {
            return false;
        }

        return $tenant->tenancies()->doesntExist();
    }
}

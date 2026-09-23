<?php

namespace App\Policies;

use App\Models\Tenancy;
use App\Models\User;

class TenancyPolicy
{
    private function owns(User $user, Tenancy $tenancy): bool
    {
        return $user->is_super_admin
            || (int) $user->landlord_id === (int) $tenancy->unit->block->property->landlord_id;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasPermission('tenancies.view');
    }

    public function view(User $user, Tenancy $tenancy): bool
    {
        return $user->hasPermission('tenancies.view') && $this->owns($user, $tenancy);
    }

    public function allocate(User $user): bool
    {
        return $user->hasPermission('tenancies.allocate');
    }

    public function terminate(User $user, Tenancy $tenancy): bool
    {
        return $user->hasPermission('tenancies.terminate') && $this->owns($user, $tenancy);
    }

    public function updateStatus(User $user, Tenancy $tenancy): bool
    {
        return $user->hasPermission('tenancies.update-status') && $this->owns($user, $tenancy);
    }
}

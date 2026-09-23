<?php

namespace App\Policies;

use App\Models\Landlord;
use App\Models\User;

/**
 * Landlords may only touch their own record. Super admins manage globally.
 */
class LandlordPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('landlords.manage');
    }

    public function view(User $user, Landlord $landlord): bool
    {
        return $user->is_super_admin
            || $user->hasPermission('landlords.manage')
            || $user->landlord_id === $landlord->id;
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('landlords.manage');
    }

    public function update(User $user, Landlord $landlord): bool
    {
        return $user->hasPermission('landlords.manage');
    }

    public function delete(User $user, Landlord $landlord): bool
    {
        return $user->hasPermission('landlords.manage');
    }
}

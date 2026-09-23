<?php

namespace App\Policies;

use App\Models\Unit;
use App\Models\User;

class UnitPolicy
{
    private function owns(User $user, Unit $unit): bool
    {
        return $user->is_super_admin
            || (int) $user->landlord_id === (int) $unit->block->property->landlord_id;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasPermission('units.view');
    }

    public function view(User $user, Unit $unit): bool
    {
        return $user->hasPermission('units.view') && $this->owns($user, $unit);
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('units.create');
    }

    public function update(User $user, Unit $unit): bool
    {
        return $user->hasPermission('units.update') && $this->owns($user, $unit);
    }

    /**
     * Configure monthly rent — writes an immutable rent-history segment.
     */
    public function updateRent(User $user, Unit $unit): bool
    {
        return $user->hasPermission('units.update-rent') && $this->owns($user, $unit);
    }

    /**
     * Soft delete only. Units with financial history remain traceable.
     */
    public function delete(User $user, Unit $unit): bool
    {
        if (! $user->hasPermission('units.delete') || ! $this->owns($user, $unit)) {
            return false;
        }

        return $unit->tenancies()->doesntExist();
    }
}

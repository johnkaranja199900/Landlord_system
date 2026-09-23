<?php

namespace App\Policies;

use App\Models\Property;
use App\Models\User;

class PropertyPolicy
{
    private function owns(User $user, Property $property): bool
    {
        return $user->is_super_admin
            || (int) $user->landlord_id === (int) $property->landlord_id;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasPermission('properties.view');
    }

    public function view(User $user, Property $property): bool
    {
        return $user->hasPermission('properties.view') && $this->owns($user, $property);
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('properties.create');
    }

    public function update(User $user, Property $property): bool
    {
        return $user->hasPermission('properties.update') && $this->owns($user, $property);
    }

    public function delete(User $user, Property $property): bool
    {
        return $user->hasPermission('properties.delete') && $this->owns($user, $property);
    }
}

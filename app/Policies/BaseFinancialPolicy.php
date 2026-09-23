<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Base policy enforcing landlord-scoped ownership plus permission checks.
 * Models are expected to expose a landlord scope via resolveLandlordId().
 */
abstract class BaseFinancialPolicy
{
    protected function landlordIdOf(Model $record): ?int
    {
        return isset($record->landlord_id) ? (int) $record->landlord_id : null;
    }

    protected function owns(User $user, Model $record): bool
    {
        return $user->is_super_admin
            || ((int) $user->landlord_id !== 0 && $this->landlordIdOf($record) === (int) $user->landlord_id);
    }

    protected function allowed(User $user, Model $record, string $permission): bool
    {
        return $user->hasPermission($permission) && $this->owns($user, $record);
    }
}

<?php

namespace App\Policies;

use App\Models\Block;
use App\Models\User;

class BlockPolicy
{
    private function owns(User $user, Block $block): bool
    {
        return $user->is_super_admin
            || (int) $user->landlord_id === (int) $block->property->landlord_id;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasPermission('blocks.view');
    }

    public function view(User $user, Block $block): bool
    {
        return $user->hasPermission('blocks.view') && $this->owns($user, $block);
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('blocks.create');
    }

    public function update(User $user, Block $block): bool
    {
        return $user->hasPermission('blocks.update') && $this->owns($user, $block);
    }

    public function delete(User $user, Block $block): bool
    {
        return $user->hasPermission('blocks.delete') && $this->owns($user, $block);
    }
}

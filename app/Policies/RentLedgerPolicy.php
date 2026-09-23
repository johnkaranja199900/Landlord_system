<?php

namespace App\Policies;

use App\Models\RentLedger;
use App\Models\User;

class RentLedgerPolicy extends BaseFinancialPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('ledger.view');
    }

    public function view(User $user, RentLedger $entry): bool
    {
        return $this->allowed($user, $entry, 'ledger.view');
    }

    public function adjust(User $user): bool
    {
        return $user->hasPermission('ledger.adjust');
    }
}

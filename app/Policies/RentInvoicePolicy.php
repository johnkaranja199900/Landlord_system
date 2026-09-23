<?php

namespace App\Policies;

use App\Models\RentInvoice;
use App\Models\User;

class RentInvoicePolicy extends BaseFinancialPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('invoices.view');
    }

    public function view(User $user, RentInvoice $invoice): bool
    {
        return $this->allowed($user, $invoice, 'invoices.view');
    }

    public function generate(User $user): bool
    {
        return $user->hasPermission('invoices.generate');
    }

    public function cancel(User $user, RentInvoice $invoice): bool
    {
        return $this->allowed($user, $invoice, 'invoices.generate');
    }
}

<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy extends BaseFinancialPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('payments.view');
    }

    public function view(User $user, Payment $payment): bool
    {
        return $this->allowed($user, $payment, 'payments.view');
    }

    public function record(User $user): bool
    {
        return $user->hasPermission('payments.record');
    }

    public function reverse(User $user, Payment $payment): bool
    {
        return $this->allowed($user, $payment, 'payments.reverse');
    }
}

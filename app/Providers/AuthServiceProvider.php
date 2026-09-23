<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Super Administrator bypasses all gates/policies (global management).
        Gate::before(fn (User $user, string $ability) => $user->is_super_admin ? true : null);
    }
}

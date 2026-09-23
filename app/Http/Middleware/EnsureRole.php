<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restricts a route group to users holding one of the given roles
 * (Super Administrators always pass).
 */
class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        abort_unless($user, 401, 'Unauthenticated.');

        abort_unless(
            $user->is_super_admin || $user->hasRole(...$roles),
            403,
            'Your role is not authorized for this area.'
        );

        return $next($request);
    }
}

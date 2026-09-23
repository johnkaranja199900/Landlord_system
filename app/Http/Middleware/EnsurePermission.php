<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Server-side authorization middleware. Rejects requests whose route requires
 * permissions the authenticated user does not hold. This is applied in
 * addition to policies — hiding buttons in Blade is never the defense.
 */
class EnsurePermission
{
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        $user = $request->user();

        abort_unless($user, 401, 'Unauthenticated.');

        foreach ($permissions as $permission) {
            abort_unless(
                $user->hasPermission($permission),
                403,
                "You do not have permission to perform this action ({$permission})."
            );
        }

        return $next($request);
    }
}

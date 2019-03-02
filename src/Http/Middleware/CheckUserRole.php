<?php

namespace Specialtactics\L5Api\Http\Middleware;

use Closure;
use Specialtactics\L5Api\Exceptions\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
class CheckUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param array|string $roles Roles to check for
     * @return mixed
     */
    public function handle($request, Closure $next, ...$roles)
    {
        $loggedInUser = $request->user();

        // Check user is logged in
        if (empty($loggedInUser)) {
            throw new UnauthorizedHttpException('Unauthorized - not logged in!');
        }

        if (!is_array($roles)) {
            $roles = [$roles];
        }

        // Match user roles to requested roles
        $matchedRoles = array_intersect($roles, $loggedInUser->getRoles());

        if (empty($matchedRoles)) {
            throw new AccessDeniedHttpException('You do not have the permission to use this resource');
        }

        return $next($request);
    }
}

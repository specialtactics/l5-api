<?php

namespace Specialtactics\L5Api\Http\Middleware;

use Closure;

class SnakeCase
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {

        return $next($request);
    }
}

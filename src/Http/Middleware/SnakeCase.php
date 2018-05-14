<?php

namespace Specialtactics\L5Api\Http\Middleware;

use Closure;

class SnakeCase
{
    protected const RELEVANT_METHODS = ['POST', 'PATCH', 'PUT', 'DELETE', 'GET'];

    /**
     * Handle an incoming request.
     *
     * Replace all request keys with snake_cased equivilents
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (in_array($request->method(), static::RELEVANT_METHODS)) {

            $parameters = $request->request->all();

            if (!empty($parameters) && count($parameters) > 0) {
                $parameters = snake_case_array_keys($parameters);

                $request->request->replace($parameters);
            }
        }

        return $next($request);
    }
}
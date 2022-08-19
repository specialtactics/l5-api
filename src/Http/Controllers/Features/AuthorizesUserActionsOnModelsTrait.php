<?php

namespace Specialtactics\L5Api\Http\Controllers\Features;

use Illuminate\Contracts\Auth\Access\Gate;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Trait AuthorizesUsersActionsAgainstModelsTrait
 *
 * These are wrappers for Illuminate\Foundation\Auth\Access\Authorizable from the perspective of a RESTful controller
 * authorizing the access of authenticated users on a given resource model
 */
trait AuthorizesUserActionsOnModelsTrait
{
    /**
     * Shorthand function which checks the currently logged in user against an action for the controller's model,
     * and throws a 403 if unauthorized
     *
     * Only checks if a policy exists for that model.
     *
     * @param  string  $ability
     * @param  array|mixed  $arguments
     *
     * @throws AccessDeniedHttpException
     */
    public function authorizeUserAction($ability, $arguments = [])
    {
        // Ability could be discarded for child controller parent resource checks
        if (is_null($ability)) {
            return true;
        }

        if (! $this->userCan($ability, $arguments)) {
            throw new AccessDeniedHttpException('Unauthorized action');
        }
    }

    /**
     * This function can be used to add conditions to the query builder,
     * which will specify the currently logged in user's ownership of the model
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder|null
     */
    public function qualifyCollectionQuery($query)
    {
        $user = auth()->user();

        $modelPolicy = app(Gate::class)->getPolicyFor(static::$model);

        // If no policy exists for this model, then there's nothing to check
        if (is_null($modelPolicy)) {
            return $query;
        }

        // Add conditions to the query, if they are defined in the model's policy
        if (method_exists($modelPolicy, 'qualifyCollectionQueryWithUser')) {
            $query = $modelPolicy->qualifyCollectionQueryWithUser($user, $query);
        }

        return $query;
    }

    /**
     * Determine if the currently logged in user can perform the specified ability on the model of the controller
     * When relevant, a specific instance of a model is used - otherwise, the model name.
     *
     * Only checks if a policy exists for that model.
     *
     * @param  string  $ability
     * @param  array|mixed  $arguments
     * @return bool
     */
    public function userCan($ability, $arguments = [])
    {
        $user = auth()->user();

        // If no arguments are specified, set it to the controller's model (default)
        if (empty($arguments)) {
            $arguments = static::$model;
        }

        // Get policy for model
        if (is_array($arguments)) {
            $model = reset($arguments);
        } else {
            $model = $arguments;
        }

        $modelPolicy = app(Gate::class)->getPolicyFor($model);

        // If no policy exists for this model, then there's nothing to check
        if (is_null($modelPolicy)) {
            return true;
        }

        // Check if the authenticated user has the required ability for the model
        if ($user->can($ability, $arguments)) {
            return true;
        }

        // If not, check if we have a custom Response object, and if so, utilise it
        // @var $response \Illuminate\Auth\Access\Response
        $response = app(Gate::class)->forUser($user)->inspect($ability, $arguments);
        if (! empty($response->message())) {
            throw new HttpException($response->code(), $response->message());
        }

        return false;
    }

    /**
     * Determine if the user does not have a given ability for the model
     *
     * @param  string  $ability
     * @param  array|mixed  $arguments
     * @return bool
     */
    public function userCant($ability, $arguments = [])
    {
        return ! $this->userCan($ability, $arguments);
    }

    /**
     * Determine if the user does not have a given ability for the model
     *
     * @param  string  $ability
     * @param  array|mixed  $arguments
     * @return bool
     */
    public function userCannot($ability, $arguments = [])
    {
        return $this->userCant($ability, $arguments);
    }
}

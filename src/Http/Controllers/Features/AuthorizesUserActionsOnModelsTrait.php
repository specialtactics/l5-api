<?php

namespace Specialtactics\L5Api\Http\Controllers\Features;

use Gate;
use Illuminate\Http\Request;
use Specialtactics\L5Api\Exceptions\UnauthorizedHttpException;
use Specialtactics\L5Api\Models\RestfulModel;

/**
 * Trait AuthorizesUsersActionsAgainstModelsTrait
 *
 * These are wrappers for Illuminate\Foundation\Auth\Access\Authorizable from the perspective of a RESTful controller
 * authorizing the access of authenticated users on a given resource model
 *
 * @package Specialtactics\L5Api\Http\Controllers\Features
 */
trait AuthorizesUserActionsOnModelsTrait
{
    /**
     * Shorthand function which checks the currently logged in user against an action for the controller's model,
     * and throws a 401 if unauthorized
     *
     * Only checks if a policy exists for that model.
     *
     * @param string $ability
     * @param array|mixed $arguments
     * @throws UnauthorizedHttpException
     */
    public function authorizeUserAction($ability, $arguments = []) {
        if (! $this->userCan($ability, $arguments)) {
            throw new UnauthorizedHttpException('Unauthorized action');
        }
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

        $modelPolicy = Gate::getPolicyFor($model);

        // If no policy exists for this model, then there's nothing to check
        if (is_null($modelPolicy)) {
            return true;
        }

        // Check if the authenticated user has the required ability for the model
        if ($user->can($ability, $arguments)) {
            return true;
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
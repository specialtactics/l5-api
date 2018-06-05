<?php

namespace Specialtactics\L5Api\Http\Controllers\Features;

use Gate;
use Illuminate\Http\Request;

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
     * Determine if the entity has a given ability.
     *
     * @param  string  $ability
     * @param  array|mixed  $arguments
     * @return bool
     */
    public function userCan($ability, $arguments = [])
    {
        $user = auth()->user();

        $modelPolicy = Gate::getPolicyFor(static::$model);

        // If no policy exists for this model, then there's nothing to check
        if (is_null($modelPolicy)) {
            return true;
        }

        // If no arguments are specified, set it to the controller's model (default)
        if (empty($arguments)) {
            $arguments[] = static::$model;
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

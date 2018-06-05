<?php

namespace Specialtactics\L5Api\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

class RestfulModelPolicy
{
    use HandlesAuthorization;

    /**
     * Process 'global' authorisation rules
     *
     * @param $user
     * @param $ability
     * @return bool
     */
    public function before($user, $ability)
    {
        if ($user->isAdmin()) {
            return true;
        }
    }
}

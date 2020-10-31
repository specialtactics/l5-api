<?php

namespace App\Policies;

use Specialtactics\L5Api\Policies\RestfulModelPolicy;
use App\Models\User;

class BasePolicy extends RestfulModelPolicy
{
    /**
     * Process 'global' authorisation rules
     *
     * @param $user
     * @param $ability
     * @return bool
     */
    public function before(User $user, $ability)
    {
        if ($user->isAdmin()) {
            return true;
        }
    }
}

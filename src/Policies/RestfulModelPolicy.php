<?php

namespace Specialtactics\L5Api\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Specialtactics\L5Api\Models\RestfulModel;
use App\Models\User;

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
    public function before(User $user, $ability)
    {
        if ($user->isAdmin()) {
            return true;
        }
    }
}

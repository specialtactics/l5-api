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
    public function before($user, $ability)
    {
        if ($user->isAdmin()) {
            return true;
        }
    }

    public function create(User $user)
    {
        // @todo
    }

    public function view(User $user, RestfulModel $model)
    {
        // @todo
    }

    public function update(User $user, RestfulModel $model) {
        // @todo
    }

    public function delete(User $user, RestfulModel $model) {
        return $this->update($user, $model);
    }

    public function own(User $user, RestfulModel $model) {
        // @todo
    }
}

<?php

namespace Specialtactics\L5Api\Tests\App\Policies;

use Specialtactics\L5Api\Tests\App\Models\User;
use Specialtactics\L5Api\Tests\App\Models\Topic;

class TopicPolicy extends BasePolicy
{
    /**
     * Determine whether the user can create Topic.
     *
     * @param  \Specialtactics\L5Api\Tests\App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the Topic.
     *
     * @param  \Specialtactics\L5Api\Tests\App\Models\User  $user
     * @param  \Specialtactics\L5Api\Tests\App\Models\Topic  $topic
     * @return mixed
     */
    public function view(User $user, Topic $topic)
    {
        return $this->own($user, $topic);
    }

    /**
     * Determine whether the user can update the Topic.
     *
     * @param  \Specialtactics\L5Api\Tests\App\Models\User  $user
     * @param  \Specialtactics\L5Api\Tests\App\Models\Topic  $topic
     * @return mixed
     */
    public function update(User $user, Topic $topic)
    {
        return $this->own($user, $topic);
    }

    /**
     * Determine whether the user can delete the Topic.
     *
     * @param  \Specialtactics\L5Api\Tests\App\Models\User  $user
     * @param  \Specialtactics\L5Api\Tests\App\Models\Topic  $topic
     * @return mixed
     */
    public function delete(User $user, Topic $topic)
    {
        return $this->own($user, $topic);
    }

    /**
     * Determine whether the user owns the Topic.
     *
     * @param  \Specialtactics\L5Api\Tests\App\Models\User  $user
     * @param  \Specialtactics\L5Api\Tests\App\Models\Topic  $topic
     * @return mixed
     */
    public function own(User $user, Topic $topic) {
        return true;
    }

    /**
     * This function can be used to add conditions to the query builder,
     * which will specify the user's ownership of the model for the get collection query of this model
     *
     * @param \Specialtactics\L5Api\Tests\App\Models\User $user A user object against which to construct the query. By default, the currently logged in user is used.
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder|null
     */
    public function qualifyCollectionQueryWithUser(User $user, $query)
    {
        return $query;
    }
}

<?php

namespace App\Models\Policies;

use App\Models\User;
use App\Models\Forum;

class ForumPolicy extends BasePolicy
{
    /**
     * Determine whether the user can create Forum.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can create Forum.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAll(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the Forum.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Forum  $forum
     * @return mixed
     */
    public function view(User $user, Forum $forum)
    {
        return $this->own($user, $forum);
    }

    /**
     * Determine whether the user can update the Forum.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Forum  $forum
     * @return mixed
     */
    public function update(User $user, Forum $forum)
    {
        return $this->own($user, $forum);
    }

    /**
     * Determine whether the user can delete the Forum.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Forum  $forum
     * @return mixed
     */
    public function delete(User $user, Forum $forum)
    {
        return $this->own($user, $forum);
    }

    /**
     * Determine whether the user owns the Forum.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Forum  $forum
     * @return mixed
     */
    public function own(User $user, Forum $forum) {
        return true;
    }

    /**
     * This function can be used to add conditions to the query builder,
     * which will specify the user's ownership of the model for the get collection query of this model
     *
     * @param \App\Models\User $user A user object against which to construct the query. By default, the currently logged in user is used.
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder|null
     */
    public function qualifyCollectionQueryWithUser(User $user, $query)
    {
        return $query;
    }
}

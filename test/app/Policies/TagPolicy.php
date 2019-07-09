<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Tag;

class TagPolicy extends BasePolicy
{
    /**
     * Determine whether the user can create tag.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the tag.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Tag  $tag
     * @return mixed
     */
    public function view(User $user, Tag $tag)
    {
        return $this->own($user, $tag);
    }

    /**
     * Determine whether the user can update the tag.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Tag  $tag
     * @return mixed
     */
    public function update(User $user, Tag $tag)
    {
        return $this->own($user, $tag);
    }

    /**
     * Determine whether the user can delete the tag.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Tag  $tag
     * @return mixed
     */
    public function delete(User $user, Tag $tag)
    {
        return $this->own($user, $tag);
    }

    /**
     * Determine whether the user owns the tag.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Tag  $tag
     * @return mixed
     */
    public function own(User $user, Tag $tag) {
        //
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

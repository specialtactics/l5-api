<?php

namespace App\Providers;

use App\Models\Forum;
use App\Models\Post;
use App\Models\Topic;
use App\Policies\ForumPolicy;
use App\Policies\PostPolicy;
use App\Policies\TopicPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Forum::class => ForumPolicy::class,
        //Topic::class => TopicPolicy::class,
        Post::class => PostPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}

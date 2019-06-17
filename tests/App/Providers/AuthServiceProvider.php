<?php

namespace Specialtactics\L5Api\Tests\App\Providers;

use Specialtactics\L5Api\Tests\App\Models\Forum;
use Specialtactics\L5Api\Tests\App\Models\Post;
use Specialtactics\L5Api\Tests\App\Models\Topic;
use Specialtactics\L5Api\Tests\App\Policies\ForumPolicy;
use Specialtactics\L5Api\Tests\App\Policies\PostPolicy;
use Specialtactics\L5Api\Tests\App\Policies\TopicPolicy;
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

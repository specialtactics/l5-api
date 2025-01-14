<?php

namespace Specialtactics\L5Api;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use Illuminate\Foundation\AliasLoader;

class L5ApiServiceProvider extends LaravelServiceProvider
{
    /**
     * Register the application services.
     */
    public function register()
    {
        // Set API Transformer Adapter & properties
        $this->app['Dingo\Api\Transformer\Factory']->setAdapter(function ($app) {
            return new \Dingo\Api\Transformer\Adapter\Fractal(new \PHPOpenSourceSaver\Fractal\Manager, 'include', ',');
        });

        // Register Fascades
        $loader = AliasLoader::getInstance();
        $loader->alias('API', \Dingo\Api\Facade\API::class);
        $loader->alias('APIRoute', \Dingo\Api\Facade\Route::class);

        if ($this->app->runningInConsole()) {
            $this->setupGenerators();
        }
    }

    /**
     * Bootstrap the application services.
     *
     * @param  \Illuminate\Routing\Router  $router
     */
    public function boot(Router $router, Dispatcher $event)
    {
    }

    public function setupGenerators()
    {
        $this->commands(Console\Commands\MakeApiResource::class);

        //
        // Override relevant artisan stubs
        // Controller-Model-Policy
        $this->commands(Console\Commands\Laravel\ControllerMakeCommand::class);
        $this->commands(Console\Commands\Laravel\ModelMakeCommand::class);
        $this->commands(Console\Commands\Laravel\PolicyMakeCommand::class);

        // Database - Migration and Seeder
        //$this->commands(Console\Commands\ControllerMakeCommand::class);
        $this->commands(Console\Commands\Laravel\SeederMakeCommand::class);
    }
}

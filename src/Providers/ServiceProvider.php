<?php

namespace Specialtactics\L5Api\Providers;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use Illuminate\Foundation\AliasLoader;

class ServiceProvider extends LaravelServiceProvider
{
    /**
     * Register the application services.
     */
    public function register()
    {
        // Set API Transformer Adapter & properties
        $this->app['Dingo\Api\Transformer\Factory']->setAdapter(function ($app) {
            return new \Dingo\Api\Transformer\Adapter\Fractal(new \League\Fractal\Manager, 'include', ',');
        });

        // Register Fascades
        $loader = AliasLoader::getInstance();
        $loader->alias('API', \Dingo\Api\Facade\API::class);
        $loader->alias('APIRoute', \Dingo\Api\Facade\Route::class);
    }

    /**
     * Bootstrap the application services.
     *
     * @param \Illuminate\Routing\Router $router
     */
    public function boot(Router $router, Dispatcher $event)
    {

    }
}

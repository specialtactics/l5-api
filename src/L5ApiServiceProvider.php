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
            return new \Dingo\Api\Transformer\Adapter\Fractal(new \League\Fractal\Manager, 'include', ',');
        });

        // Register Fascades
        $loader = AliasLoader::getInstance();
        $loader->alias('API', \Dingo\Api\Facade\API::class);
        $loader->alias('APIRoute', \Dingo\Api\Facade\Route::class);

        if ($this->app->runningInConsole()) {
            $this->commands(Console\Commands\MakeApiResource::class);
        }

        // Set the logger instance to the laravel's default to begin with
        APIBoilerplate::setLogger(\Log::getLogger());
    }

    /**
     * Bootstrap the application services.
     *
     * @param  \Illuminate\Routing\Router  $router
     */
    public function boot(Router $router, Dispatcher $event)
    {
    }
}

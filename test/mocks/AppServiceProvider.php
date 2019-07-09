<?php

namespace Specialtactics\L5Api\Test\Mocks;

use Illuminate\Support\ServiceProvider;
use App\Exceptions\ApiExceptionHandler;
use Config;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerExceptionHandler();
    }

    /**
     * Register the exception handler - extends the Dingo one
     *
     * @return void
     */
    protected function registerExceptionHandler()
    {
        $this->app->singleton('api.exception', function ($app) {
            return new ApiExceptionHandler($app['Illuminate\Contracts\Debug\ExceptionHandler'], Config('api.errorFormat', [
                'message' => ':message',
                'errors' => ':errors',
                'code' => ':code',
                'statusCode' => ':status_code',
                'debug' => ':debug',
            ]), Config('api.debug', true));
        });
    }
}

<?php

namespace Specialtactics\L5Api\Tests;

use Illuminate\Database\Eloquent\Factory as EloquentFactory;
use Orchestra\Testbench\TestCase as BaseTestCase;
use JWTAuth;
use Mockery;

/**
 * This class sets up the necessary testing infrastructure
 *
 * Class TestingSetup
 * @package Specialtactics\L5Api\Tests
 */
class TestingSetup extends BaseTestCase
{
    /**
     * Set up the testing environment
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Do migrations for tests
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        $factory = app(EloquentFactory::class);
        $factory->load(__DIR__ . '/../database/factories');

        $this->artisan('migrate', ['--database' => 'testing', '--seed' => true]);
    }

    /**
     * Tear down the testing environment
     */
    public function tearDown(): void
    {
        Mockery::close();
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        // Set the testing config files to be used by the test app
        $app['config']->set('api', include __DIR__ . '/../config/api.php');
        $app['config']->set('auth', include __DIR__ . '/../config/auth.php');
        $app['config']->set('jwt', include __DIR__ . '/../config/jwt.php');
    }

    /**
     * Specify which service providers to use for the testing app
     *
     * @param \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            \Tymon\JWTAuth\Providers\LaravelServiceProvider::class,
            \Dingo\Api\Provider\LaravelServiceProvider::class,
            \Specialtactics\L5Api\L5ApiServiceProvider::class,
            \Specialtactics\L5Api\Test\Mocks\AppServiceProvider::class,
            \Specialtactics\L5Api\Test\Mocks\RouteServiceProvider::class,
        ];
    }

    /**
     * Specify aliases/facades for the testing app
     *
     * @param \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageAliases($app) {
        return [
            'API' => \Dingo\Api\Facade\API::class,
            'JWTAuth' => \Tymon\JWTAuth\Facades\JWTAuth::class,
        ];
    }

    /**
     * Resolve application HTTP Kernel implementation.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function resolveApplicationHttpKernel($app)
    {
        // Use our testing app http kernel
        $app->singleton(\Illuminate\Contracts\Http\Kernel::class, \App\Http\Kernel::class);
    }
}

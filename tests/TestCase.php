<?php

namespace Specialtactics\L5Api\Tests;

use Mockery as m;
use Dingo\Api\Http;
use Dingo\Api\Auth\Auth;
use Dingo\Api\Dispatcher;
use Illuminate\Http\Request;
use Dingo\Api\Routing\Router;
use Dingo\Api\Tests\Stubs\UserStub;
use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;
use Dingo\Api\Tests\Stubs\MiddlewareStub;
use Dingo\Api\Tests\Stubs\TransformerStub;
use Dingo\Api\Tests\Stubs\RoutingAdapterStub;
use Dingo\Api\Exception\InternalHttpException;
use Dingo\Api\Tests\Stubs\UserTransformerStub;
use Dingo\Api\Exception\ValidationHttpException;
use Dingo\Api\Transformer\Factory as TransformerFactory;
use Illuminate\Support\Facades\Request as RequestFacade;
use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Do migrations for tests
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');

        $this->artisan('migrate', ['--database' => 'testing']);

        $this->setUpDingo();

        $this->setupRoutes();
    }

    public function setUpDingo()
    {
        $this->container = new Container;
        $this->container['request'] = Request::create('/', 'GET');
        $this->container['api.auth'] = new MiddlewareStub;
        $this->container['api.limiting'] = new MiddlewareStub;

        Http\Request::setAcceptParser(new Http\Parser\Accept('vnd', 'api', 'v1', 'json'));

        $this->transformerFactory = new TransformerFactory($this->container, new TransformerStub);

        $this->adapter = new RoutingAdapterStub;
        $this->exception = m::mock(\Dingo\Api\Exception\Handler::class);
        $this->router = new Router($this->adapter, $this->exception, $this->container, null, null);

        $this->auth = new Auth($this->router, $this->container, []);
        $this->dispatcher = new Dispatcher($this->container, new Filesystem, $this->router, $this->auth);

        app()->instance(\Illuminate\Routing\Router::class, $this->adapter, true);

        $this->dispatcher->setSubtype('api');
        $this->dispatcher->setStandardsTree('vnd');
        $this->dispatcher->setDefaultVersion('v1');
        $this->dispatcher->setDefaultFormat('json');

        Http\Response::setFormatters(['json' => new Http\Response\Format\Json]);
        Http\Response::setTransformer($this->transformerFactory);
    }

    public function tearDown(): void
    {
        m::close();
    }

    public function setupRoutes()
    {
        require __DIR__ . '/routes.php';
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
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }

    /**
     * Set the service providers of this package
     *
     * @param \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            'Dingo\Api\Provider\LaravelServiceProvider',
            'Specialtactics\L5Api\L5ApiServiceProvider',
        ];
    }

    /**
     * Set the service providers of this package
     *
     * @param \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageAliases($app) {
        // Will be useful later
        return [
            ';'
        ];
    }
}

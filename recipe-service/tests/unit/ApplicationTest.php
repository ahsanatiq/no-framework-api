<?php
namespace Tests;

class ApplicationTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    protected $app;

    // include the bootstrap file that is needed for initializing application
    public static function setUpBeforeClass()
    {
        // require codecept_root_dir('bootstrap/app.php');
    }

    public function _before()
    {
        $this->container = \Illuminate\Container\Container::getInstance();
        $this->app = $this->container->make('app');
    }

    // tests
    public function testApplicationInstance()
    {
        $this->assertInstanceOf(
            \App\Application::class,
            $this->app
        );
    }


    public function testContainerInstanceInsideApplication()
    {
        $this->assertInstanceOf(
            \Illuminate\Container\Container::class,
            $this->app->container
        );

        $this->assertEquals(
            \Illuminate\Container\Container::getInstance(),
            $this->app->container
        );
    }

    public function testRouterInstanceInsideApplication()
    {
        $router = $this->app->router;
        $this->assertInstanceOf(
            \Illuminate\Routing\Router::class,
            $router
        );
    }



    public function testRequestInstanceInsideApplication()
    {
        $request = $this->app->request;
        $this->assertInstanceOf(
            \Illuminate\Http\Request::class,
            $request
        );
    }

    public function testEventsInstanceInsideApplication()
    {
        $events = $this->app->events;
        $this->assertInstanceOf(
            \Illuminate\Events\Dispatcher::class,
            $events
        );
    }

    public function testConfigInstanceInsideApplication()
    {
        $config = $this->app->config;
        $this->assertInstanceOf(
            \Illuminate\Config\Repository::class,
            $config
        );
    }

    public function testSymfonyFinderInstanceInsideApplication()
    {
        $finder = $this->app->finder;
        $this->assertInstanceOf(
            \Symfony\Component\Finder\Finder::class,
            $finder
        );
    }

    public function testDBInstanceInsideApplication()
    {
        $db = $this->app->db;
        $this->assertInstanceOf(
            \Illuminate\Database\Capsule\Manager::class,
            $db
        );
    }

    public function testApplicationSingletonInstances()
    {
        $app1 = $this->container->make('app');
        $app2 = $this->container->make('app');
        $this->assertSame(
            $app1,
            $app2
        );
    }

    public function testApplicationSingletonInstancesUsingHelpers()
    {
        $app1 = app();
        $app2 = app();
        $this->assertSame(
            $app1,
            $app2
        );

        $app1 = app();
        $app2 = $this->app;
        $this->assertSame(
            $app1,
            $app2
        );

        $app1 = app();
        $app2 = $this->container->make('app');
        $this->assertSame(
            $app1,
            $app2
        );
    }
}

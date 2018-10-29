<?php
class ApplicationTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    public function _before()
    {
        app()->run();
    }

    // tests
    public function testApplicationInstance()
    {
        $app = new App\Application();
        $this->assertInstanceOf(
            \App\Application::class,
            $app
        );
    }

    public function testContainerInstanceInsideApplication()
    {
        $app = new App\Application();
        $this->assertInstanceOf(
            Illuminate\Container\Container::class,
            $app->getContainer()
        );
    }

    public function testRouterInstanceInsideContainer()
    {
        $router = container()->make('router');
        $this->assertInstanceOf(
            Illuminate\Routing\Router::class,
            $router
        );
    }

    public function testRequestInstanceInsideContainer()
    {
        $request = container()->make('request');
        $this->assertInstanceOf(
            Illuminate\Http\Request::class,
            $request
        );
    }

    public function testApplicationSingletonInstances()
    {
        $app1 = new App\Application();
        $app2 = new App\Application();
        $this->assertNotSame(
            $app1,
            $app2
        );

        $app1 = App\Application::getInstance();
        $app2 = App\Application::getInstance();
        $this->assertSame(
            $app1,
            $app2
        );

        $app1 = App\Application::getInstance();
        $app2 = new App\Application();
        $this->assertNotSame(
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
        $app2 = App\Application::getInstance();
        $this->assertSame(
            $app1,
            $app2
        );
    }

    public function testHelperFunctions()
    {
        $this->assertInstanceOf(
            \App\Application::class,
            app()
        );

        $this->assertInstanceOf(
            Illuminate\Container\Container::class,
            container()
        );

        $this->assertInstanceOf(
            Illuminate\Config\Repository::class,
            config()
        );

    }
}

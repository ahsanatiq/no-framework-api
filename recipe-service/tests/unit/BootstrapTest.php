<?php
namespace Tests;

class BootstrapTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
        $this->container = \Illuminate\Container\Container::getInstance();
    }

    // tests
    public function testContainerInstance()
    {
        $this->assertInstanceOf(
            \Illuminate\Container\Container::class,
            $this->container
        );
    }

    public function testRouterInstanceInsideContainer()
    {
        $router = $this->container->make('Illuminate\Routing\Router');
        $this->assertInstanceOf(
            \Illuminate\Routing\Router::class,
            $router
        );
        $router = $this->container->make('router');
        $this->assertInstanceOf(
            \Illuminate\Routing\Router::class,
            $router
        );
    }

    public function testRequestInstanceInsideContainer()
    {
        $request = $this->container->make('Illuminate\Http\Request');
        $this->assertInstanceOf(
            \Illuminate\Http\Request::class,
            $request
        );
        $request = $this->container->make('request');
        $this->assertInstanceOf(
            \Illuminate\Http\Request::class,
            $request
        );
    }

    public function testEventsInstanceInsideContainer()
    {
        $events = $this->container->make('Illuminate\Events\Dispatcher');
        $this->assertInstanceOf(
            \Illuminate\Events\Dispatcher::class,
            $events
        );
        $events = $this->container->make('dispatcher');
        $this->assertInstanceOf(
            \Illuminate\Events\Dispatcher::class,
            $events
        );
    }

    public function testConfigInstanceInsideContainer()
    {
        $config = $this->container->make('Illuminate\Config\Repository');
        $this->assertInstanceOf(
            \Illuminate\Config\Repository::class,
            $config
        );
        $config = $this->container->make('config');
        $this->assertInstanceOf(
            \Illuminate\Config\Repository::class,
            $config
        );
    }

    public function testSymfonyFinderInstanceInsideContainer()
    {
        $finder = $this->container->make('Symfony\Component\Finder\Finder');
        $this->assertInstanceOf(
            \Symfony\Component\Finder\Finder::class,
            $finder
        );
        $finder = $this->container->make('finder');
        $this->assertInstanceOf(
            \Symfony\Component\Finder\Finder::class,
            $finder
        );
    }

    public function testDBInstanceInsideContainer()
    {
        $db = $this->container->make('Illuminate\Database\Capsule\Manager');
        $this->assertInstanceOf(
            \Illuminate\Database\Capsule\Manager::class,
            $db
        );
        $db = $this->container->make('database');
        $this->assertInstanceOf(
            \Illuminate\Database\Capsule\Manager::class,
            $db
        );
    }

    public function testApplicationInstanceInsideContainer()
    {
        $app = $this->container->make('app');
        $this->assertInstanceOf(
            \App\Application::class,
            $app
        );
    }
}

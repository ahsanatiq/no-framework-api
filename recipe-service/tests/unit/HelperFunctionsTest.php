<?php
namespace Tests;

class HelperFunctionsTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    // tests
    public function testApplication()
    {
        $this->assertInstanceOf(
            \App\Application::class,
            app()
        );
    }

    public function testContainer()
    {
        $this->assertInstanceOf(
            \Illuminate\Container\Container::class,
            container()
        );
    }

    public function testConfiguration()
    {
        $this->assertInstanceOf(
            \Illuminate\Config\Repository::class,
            config()
        );
    }

    public function testDispatcher()
    {
        $this->assertInstanceOf(
            \Illuminate\Events\Dispatcher::class,
            dispatcher()
        );
    }
}

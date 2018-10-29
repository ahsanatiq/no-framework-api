<?php
namespace App;

use App\Exceptions\ExceptionHandler;
use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Router;
use Symfony\Component\Finder\Finder;

class Application
{
    private $container;
    private $config;
    private $request;
    private $router;
    private static $instance = null;

    public function __construct()
    {
        $this->container = new Container;
    }

    public function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new Application();
        }

        return self::$instance;
    }

    public function getContainer()
    {
        return $this->container;
    }

    public function run()
    {
        try {
            $this->config = $this->bootstrapConfig();
            $this->request = $this->bootstrapRequest();
            $this->router = $this->bootstrapRouter();
            $response = $this->router->dispatch($this->request);
            // Send the response back to the browser
            $response->send();
        } catch (\Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    private function bootstrapRouter()
    {
        // Initialize Router
        $events = new Dispatcher($this->container);
        $router = new Router($events, $this->container);
        $this->container->instance('router', $router);
        $finder = new Finder();
        $finder->files()->in(__DIR__.'/../routes/');
        foreach ($finder as $file) {
            require($file->getRealPath());
        }
        return $router;
    }

    private function bootstrapRequest()
    {
        $request = Request::capture();
        $this->container->instance('Illuminate\Http\Request', $request);
        $this->container->alias('Illuminate\Http\Request', 'request');
        return $request;
    }

    private function bootstrapConfig()
    {
        $configItems = [];
        $finder = new Finder();
        $finder->files()->in(__DIR__.'/../config/');
        foreach ($finder as $file) {
            $configItems = array_merge($configItems, [
                $file->getBasename('.php') => require($file->getRealPath())
            ]);
        }
        $config = new Repository($configItems);
        $this->container->instance('config', $config);
        return $config;
    }
}

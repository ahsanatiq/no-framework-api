<?php
namespace App;

use App\Exceptions\ExceptionHandler;
use Illuminate\Config\Repository as ConfigRepository;
use Illuminate\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Router;
use Symfony\Component\Finder\Finder;

class Application
{
    public $container;
    public $config;
    public $db;
    public $events;
    public $finder;
    public $request;
    public $response;
    public $router;

    public function __construct(
        ConfigRepository $config,
        Finder $finder,
        Request $request,
        Response $response,
        Router $router
    ) {
        $this->container = Container::getInstance();
        $this->config    = $config;
        $this->finder    = $finder;
        $this->request   = $request;
        $this->response  = $response;
        $this->router    = $router;

        $this->registerConfig();
        $this->registerRoutes();
    }

    public function run()
    {
        try {
            $response = $this->router->dispatch($this->request);
            // Send the response back to the browser
            $response->send();
        } catch (\Exception $e) {
            ExceptionHandler::handle($e, $this->request);
        }
    }

    private function registerConfig()
    {
        $this->checkHeadersForEnv();
        $configItems = [];
        $this->finder->files()->in(__DIR__.'/../config/');
        foreach ($this->finder as $file) {
            $configItems = array_merge($configItems, [
                $file->getBasename('.php') => require($file->getRealPath())
            ]);
        }
        $this->config->set($configItems);
    }

    private function registerRoutes()
    {
        $this->finder->files()->in(__DIR__.'/../routes/');
        foreach ($this->finder as $file) {
            $router = $this->router;
            require($file->getRealPath());
        }
    }

    private function checkHeadersForEnv()
    {
        if (isset($_SERVER['HTTP_APP_ENV']) && !empty($_SERVER['HTTP_APP_ENV'])) {
            $envFile = __DIR__.'/../.env.'.$_SERVER['HTTP_APP_ENV'];
            if (file_exists($envFile)) {
                loadEnvironmentFromFile($envFile);
            }
        }
    }
}

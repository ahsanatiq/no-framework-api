<?php
namespace App;

use App\Exceptions\ExceptionHandler;
use Illuminate\Config\Repository as ConfigRepository;
use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher as EventsDispatcher;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Router;
use Symfony\Component\Finder\Finder;
use Illuminate\Database\Capsule\Manager as DbCapsule;

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
        DbCapsule $db,
        EventsDispatcher $events,
        Finder $finder,
        Request $request,
        Response $response,
        Router $router
    )
    {
        $this->container = Container::getInstance();
        $this->config    = $config;
        $this->db        = $db;
        $this->events    = $events;
        $this->finder    = $finder;
        $this->request   = $request;
        $this->response  = $response;
        $this->router    = $router;

        $this->registerConfig();
        $this->registerRoutes();
        $this->registerDbConnection();
        $this->registerRepositories();
        $this->registerEvents();
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

    private function registerDbConnection()
    {
        $this->db->addConnection($this->config['db'][$this->config['db.default']]);
    }

    private function registerRepositories()
    {
        $this->container->bind(App\Repositories\Contracts\RecipeRepositoryInterface::class,
                               App\Repositories\Eloquent\RecipeRepository::class);
    }

    private function registerEvents()
    {
        $this->events->listen([\App\Events\NewRatingCreatedEvent::class],
                               \App\Listeners\UpdateRecipeRating::class);
    }
}

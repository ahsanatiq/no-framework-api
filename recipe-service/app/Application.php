<?php
namespace App;

use App\Exceptions\ExceptionHandler;
use Illuminate\Config\Repository as ConfigRepository;
use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager as DbCapsule;
use Illuminate\Events\Dispatcher as EventsDispatcher;
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
        DbCapsule $db,
        EventsDispatcher $events,
        Finder $finder,
        Request $request,
        Response $response,
        Router $router
    ) {
        $this->container = Container::getInstance();
        $this->config    = $config;
        $this->db        = $db;
        $this->events    = $events;
        $this->finder    = $finder;
        $this->request   = $request;
        $this->response  = $response;
        $this->router    = $router;

        $this->registerConfig();
        $this->registerMiddlewares();
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

    private function registerMiddlewares()
    {
        $this->router->aliasMiddleware('auth', \App\Middlewares\AuthenticateMiddleware::class);
        $this->router->aliasMiddleware('json', \App\Middlewares\JsonHeaderMiddleware::class);
    }

    private function registerDbConnection()
    {
        $this->db->addConnection($this->config['db'][$this->config['db.default']]);
    }

    private function registerRepositories()
    {
        $this->container->bind(
            \App\Repositories\Contracts\RecipeRepositoryInterface::class,
            \App\Repositories\Eloquent\RecipeRepository::class
        );
    }

    private function registerEvents()
    {
        $this->events->listen(
            [\App\Events\NewRatingCreatedEvent::class],
            \App\Listeners\UpdateRecipeRating::class
        );
        $this->events->listen(
            [
                \App\Events\RecipeCreatedEvent::class,
                \App\Events\RecipeUpdatedEvent::class,
                \App\Events\RecipeDeletedEvent::class,
            ],
            \App\Listeners\QueueRecipeEvents::class
        );
    }

    private function checkHeadersForEnv()
    {
        $commandArgs = parseFileArguments();
        if (isset($_SERVER['HTTP_APP_ENV']) && !empty($_SERVER['HTTP_APP_ENV'])) {
            $envFile = __DIR__.'/../.env.'.$_SERVER['HTTP_APP_ENV'];
        }
        else if (isset($commandArgs['env']) && !empty($commandArgs['env'])) {
            $envFile = __DIR__.'/../.env.'.$commandArgs['env'];
        }
        if (!empty($envFile) && file_exists($envFile)) {
            loadEnvironmentFromFile($envFile);
        }
    }
}

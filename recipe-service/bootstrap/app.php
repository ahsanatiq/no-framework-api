<?php

use Illuminate\Config\Repository as ConfigRepository;
use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager as DbCapsule;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Router;
use Illuminate\Translation\FileLoader;
use Illuminate\Translation\Translator;
use Illuminate\Validation\Factory as ValidationFactory;
use Symfony\Component\Finder\Finder;
use Dotenv\Dotenv;


// Codeception code-coverage sciprt
include __DIR__.'/../c3.php';

require_once __DIR__ . '/../vendor/autoload.php';

$container = Container::getInstance();

$container->singleton('Illuminate\Config\Repository', 'Illuminate\Config\Repository');
$container->alias('Illuminate\Config\Repository', 'config');

$dispatcher = new Dispatcher($container);
$container->instance('Illuminate\Events\Dispatcher', $dispatcher);
$container->alias('Illuminate\Events\Dispatcher', 'dispatcher');

$router = new Router($dispatcher, $container);
$container->instance('Illuminate\Routing\Router', $router);
$container->alias('Illuminate\Routing\Router', 'router');

$container->singleton('Symfony\Component\Finder\Finder', 'Symfony\Component\Finder\Finder');
$container->alias('Symfony\Component\Finder\Finder', 'finder');

$request = Request::capture();
$container->instance('Illuminate\Http\Request', $request);
$container->alias('Illuminate\Http\Request', 'request');

$container->singleton('Monolog\Logger', function($container){
    $config = $container->make('config');
    $logger = new \Monolog\Logger($config['app.name']);
    $formatter = new Monolog\Formatter\LineFormatter(null, null, false, true);
    $handler = new \Monolog\Handler\RotatingFileHandler(
        $config['app.log_file'],
        $config['app.log_days'],
        constant('\Monolog\Logger::'.strtoupper($config['app.log_level']))
    );
    $handler->setFormatter($formatter);
    $logger->pushHandler($handler);
    return $logger;
});
$container->alias('Monolog\Logger', 'logger');

$capsule = new DbCapsule($container);
$capsule->setEventDispatcher($dispatcher);
$capsule->setAsGlobal();
$capsule->bootEloquent();
$container->instance('Illuminate\Database\Capsule\Manager', $capsule);
$container->alias('Illuminate\Database\Capsule\Manager', 'database');

$loader = new FileLoader(new Filesystem, __DIR__.'/../resources/lang');
$translator = new Translator($loader, 'en');
$validation = new ValidationFactory($translator, $container);
$container->instance('Illuminate\Validation\Factory', $validation);
$container->alias('Illuminate\Validation\Factory', 'validation');

$container->singleton('app', 'App\Application');

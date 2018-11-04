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

require_once __DIR__ . '/../vendor/autoload.php';

$container = Container::getInstance();

$config = new ConfigRepository;
$container->instance('Illuminate\Config\Repository', $config);
$container->alias('Illuminate\Config\Repository', 'config');

$events = new Dispatcher($container);
$container->instance('Illuminate\Events\Dispatcher', $events);
$container->alias('Illuminate\Events\Dispatcher', 'dispatcher');

$router = new Router($events, $container);
$container->instance('Illuminate\Routing\Router', $router);
$container->alias('Illuminate\Routing\Router', 'router');

$finder = new Finder();
$container->instance('Symfony\Component\Finder\Finder', $finder);
$container->alias('Symfony\Component\Finder\Finder', 'finder');

$request = Request::capture();
$container->instance('Illuminate\Http\Request', $request);
$container->alias('Illuminate\Http\Request', 'request');

$capsule = new DbCapsule($container);
$capsule->setEventDispatcher($events);
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
return $container->make('app');

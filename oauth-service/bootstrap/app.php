<?php

use Illuminate\Config\Repository as ConfigRepository;
use Illuminate\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Router;
use Symfony\Component\Finder\Finder;
use Dotenv\Dotenv;

require_once __DIR__ . '/../vendor/autoload.php';

$container = Container::getInstance();

$config = new ConfigRepository;
$container->instance('Illuminate\Config\Repository', $config);
$container->alias('Illuminate\Config\Repository', 'config');

$router = new Router($dispatcher, $container);
$container->instance('Illuminate\Routing\Router', $router);
$container->alias('Illuminate\Routing\Router', 'router');

$finder = new Finder();
$container->instance('Symfony\Component\Finder\Finder', $finder);
$container->alias('Symfony\Component\Finder\Finder', 'finder');

$request = Request::capture();
$container->instance('Illuminate\Http\Request', $request);
$container->alias('Illuminate\Http\Request', 'request');

$container->singleton('app', 'App\Application');
return $container->make('app');

<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;

// Create a service container
$container = new Container;

// Create a request from server variables, and bind it to the container; optional
$request = Request::capture();
$container->instance('Illuminate\Http\Request', $request);

$events = new Dispatcher($container);
$router = new Router($events, $container);

// Load the routes
require_once __DIR__.'/../routes/api.php';

// Dispatch the request through the router
$response = $router->dispatch($request);

// Send the response back to the browser
$response->send();

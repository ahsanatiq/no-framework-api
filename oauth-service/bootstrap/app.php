<?php

use Dotenv\Dotenv;
use Illuminate\Config\Repository as ConfigRepository;
use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Router;
use Symfony\Component\Finder\Finder;

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

$container->singleton('OAuth2\Server', function($container) {
    $config = $container->make('config');

    $default = $config['db.default'];
    $pdoStorage = new OAuth2\Storage\Pdo([
        'dsn' => $config['db.default'].':dbname='.$config['db'][$default]['database'].';host='.$config['db'][$default]['host'],
        'username' => $config['db'][$default]['username'],
        'password' => $config['db'][$default]['password']
    ]);

    $publicKey  = file_get_contents($config['app.public_key']);
    $privateKey = file_get_contents($config['app.private_key']);
    $keyStorage = new OAuth2\Storage\Memory(['keys' => [
        'public_key'  => $publicKey,
        'private_key' => $privateKey,
    ]]);

    $server = new OAuth2\Server([
        'public_key'=>$keyStorage,
        'access_token'=>$pdoStorage,
        'client_credentials'=>$pdoStorage,
        'user_credentials'=>$pdoStorage,
        'authorization_code'=>$pdoStorage,
        'client'=>$pdoStorage,
        'scope'=>$pdoStorage,
    ], [
        'use_jwt_access_tokens' => true,
    ]);

    $server->addGrantType(new OAuth2\GrantType\ClientCredentials($pdoStorage));
    $server->addGrantType(new OAuth2\GrantType\UserCredentials($pdoStorage));
    $server->addGrantType(new OAuth2\GrantType\AuthorizationCode($pdoStorage));

    return $server;
});

$container->singleton('app', 'App\Application');
return $container->make('app');

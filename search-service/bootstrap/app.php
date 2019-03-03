<?php

use Elasticsearch\ClientBuilder;
use Illuminate\Config\Repository as ConfigRepository;
use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Illuminate\Events\EventServiceProvider;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Queue\Capsule\Manager as Queue;
use Illuminate\Redis\RedisManager;
use Illuminate\Routing\Router;
use Illuminate\Translation\FileLoader;
use Illuminate\Translation\Translator;
use Illuminate\Validation\Factory as ValidationFactory;
use Symfony\Component\Finder\Finder;

require_once __DIR__ . '/../vendor/autoload.php';

$container = container_instance();

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

$loader = new FileLoader(new Filesystem, __DIR__.'/../resources/lang');
$translator = new Translator($loader, 'en');
$validation = new ValidationFactory($translator, $container);
$container->instance('Illuminate\Validation\Factory', $validation);
$container->alias('Illuminate\Validation\Factory', 'validation');

(new EventServiceProvider($container))->register();
$container->singleton('redis', function ($container) {
    $config = $container->make('config');
    return new RedisManager($container, $config['db']['redis']['driver'], [
        'default' => [
            'host' => $config['db']['redis']['host'],
            'password' => $config['db']['redis']['password'],
            'port' => $config['db']['redis']['port'],
            'database' => $config['db']['redis']['database'],
        ],
    ]);
});

$container->singleton('Illuminate\Queue\QueueManager', function($container) {
    $config = $container->make('config');
    $queue = new Queue($container);
    $queue->addConnection([
        'driver' => 'redis',
        'connection' => 'default',
        'queue' => $config['app.queue'],
    ], 'redis');
    return $queue->getQueueManager();
});
$container->alias('Illuminate\Queue\QueueManager', 'queue');

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

$container->bind('Elasticsearch\Client', function($container) {
    $config = $container->make('config');
    $builder = ClientBuilder::create()
        ->setHosts($config['db.elasticsearch.hosts'])
        ->setRetries(2);

    if($config['app.env']!='production') {
        $logger = $container->make('logger');
        $builder->setLogger($logger);
    }

    return $builder->build();
});
$container->alias('Elasticsearch\Client', 'elasticsearch');

$container->singleton('app', 'App\Application');
$container->make('app');

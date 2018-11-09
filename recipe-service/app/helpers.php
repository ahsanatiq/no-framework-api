<?php

if (!function_exists('container')) {
    function container()
    {
        return Illuminate\Container\Container::getInstance();
    }
}

if (!function_exists('app')) {
    function app()
    {
        return container()->make('app');
    }
}


if (!function_exists('config')) {
    function config()
    {
        return container()->make('config');
    }
}

if (!function_exists('request')) {
    function request()
    {
        return container()->make('request');
    }
}

if (!function_exists('dispatcher')) {
    function dispatcher()
    {
        return container()->make('dispatcher');
    }
}

if (!function_exists('currentUrl')) {
    function currentUrl()
    {
        return \Purl\Url::fromCurrent();
    }
}

if (!function_exists('loadEnvironmentFromFile')) {
    function loadEnvironmentFromFile($file)
    {
        return (new Dotenv\Dotenv(dirname($file), basename($file)))->overload();
    }
}

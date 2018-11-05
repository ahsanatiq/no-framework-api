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

if (!function_exists('dispatcher')) {
    function dispatcher()
    {
        return container()->make('dispatcher');
    }
}

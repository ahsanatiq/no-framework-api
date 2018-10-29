<?php

if (!function_exists('app')) {
    function app()
    {
        return \App\Application::getInstance();
    }
}

if (!function_exists('container')) {
    function container()
    {
        return app()->getContainer();
    }
}

if (!function_exists('config')) {
    function config()
    {
        return container()->make('config');
    }
}

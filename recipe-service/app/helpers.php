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


if (!function_exists('logger')) {
    function logger()
    {
        return container()->make('logger');
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

if (!function_exists('parseFileArguments')) {
    function parseFileArguments($noopt = array()) {
        $result = [];
        if(!isset($GLOBALS['argv']))
            return [];
        $params = $GLOBALS['argv'];
        reset($params);
        while (list($tmp, $p) = each($params))
        {
            if ($p{0} == '-') {
                $pname = substr($p, 1);
                $value = true;
                if ($pname{0} == '-') {
                    // long-opt (--<param>)
                    $pname = substr($pname, 1);
                    if (strpos($p, '=') !== false) {
                        // value specified inline (--<param>=<value>)
                        list($pname, $value) = explode('=', substr($p, 2), 2);
                    }
                }
                // check if next parameter is a descriptor or a value
                $nextparm = current($params);
                if (!in_array($pname, $noopt) && $value === true && $nextparm !== false && $nextparm{0} != '-') {
                    list($tmp, $value) = each($params);
                }
                $result[$pname] = $value;
            } else {
                // param doesn't belong to any option
                $result[] = $p;
            }
        }
        return $result;
    }
}

class AppContainer extends Illuminate\Container\Container
{
    public function isDownForMaintenance()
    {
        return false;
    }
}

if (!function_exists('container_instance')) {
    function container_instance()
    {
        $container = new AppContainer;
        Illuminate\Container\Container::setInstance($container);
        return $container;
    }
}

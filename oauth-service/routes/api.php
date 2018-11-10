<?php

$router->get('protected', 'App\Controllers\OauthController@getProtected');
$router->prefix('/oauth/')->namespace('App\Controllers')->group(
    function ($router) {
        $router->get('/', function() {
            return 'hello';
        });
        $router->post('token', 'OauthController@getToken');

    }
);

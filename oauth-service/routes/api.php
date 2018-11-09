<?php

$router->prefix('/api/v1/')->namespace('App\Controllers')->group(
    function ($router) {
        $router->get('token', 'OauthController@getToken');
        $router->get('authorize', 'OauthController@getAuthorize');
    }
);

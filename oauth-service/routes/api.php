<?php

$router->prefix('/oauth/')->namespace('App\Controllers')->group(
    function ($router) {
        $router->post('token', 'OauthController@getToken');
        $router->post('authorize', 'OauthController@getAuthorize');
    }
);

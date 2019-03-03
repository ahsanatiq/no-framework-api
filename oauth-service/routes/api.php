<?php

$router->prefix('api/v1/oauth/')->namespace('App\Controllers')->group(
    function ($router) {
        $router->post('token', 'OauthController@getToken');
        $router->post('authorize', 'OauthController@getAuthorize');
    }
);

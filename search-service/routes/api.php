<?php

$router->prefix('/api/v1/')->namespace('App\Controllers')->middleware('json')->group(
    function ($router) {
        $router->get('search', 'RecipeController@search');
    }
);

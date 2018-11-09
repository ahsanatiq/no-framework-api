<?php

$router->prefix('/api/v1/')->namespace('App\Controllers')->group(
    function ($router) {
        $router->get('recipes', 'RecipeController@getList');
        $router->post('recipes', 'RecipeController@create');
        $router->get('recipes/{id}', 'RecipeController@get')->where('id', '[0-9]+');
        $router->match(['put','patch'], 'recipes/{id}', 'RecipeController@update')->where('id', '[0-9]+');
        $router->delete('recipes/{id}', 'RecipeController@delete')->where('id', '[0-9]+');
        $router->post('recipes/{id}/rating', 'RatingController@create')->where('id', '[0-9]+');
    }
);

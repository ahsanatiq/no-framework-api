<?php

$router->prefix('/api/v1/')->namespace('App\Controllers')->group(function ($router) {

    $router->get('recipes', 'RecipeController@getList');
    $router->post('recipes', 'RecipeController@create');
    $router->get('recipes/{id}', 'RecipeController@get');
    $router->put('recipes/{id}', 'RecipeController@update');
    $router->patch('recipes/{id}', 'RecipeController@update');
    $router->delete('recipes/{id}', 'RecipeController@delete');
    $router->post('recipes/{id}/rating', 'RatingController@create');

});

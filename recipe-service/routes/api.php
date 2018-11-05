<?php

$router->prefix('/api/v1/')->namespace('App\Controllers')->group(function ($router) {

    $router->get('recipes', 'RecipeController@getList');
    // $router->post('recipes', 'RecipeController@create', [$auth]);
    $router->get('recipes/{id}', 'RecipeController@get');
    // $router->put('recipes/{id}', 'RecipeController@update', [$auth]);
    // $router->patch('recipes/{id}', 'RecipeController@update', [$auth]);
    // $router->delete('recipes/{id}', 'RecipeController@delete', [$auth]);
    $router->post('recipes/{id}/rating', 'RecipeController@createRating');

});

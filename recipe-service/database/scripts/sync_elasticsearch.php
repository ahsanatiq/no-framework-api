<?php

use App\Models\Recipe;

require __DIR__.'/../../bootstrap/app.php';

$queue = container()->make('queue');
$queue->connection('redis')->push('App\Jobs\UpdateElasticsearchJob@handle', [
    'recipe' => null,
    'event_type' => 'deleteAll',
    'app_env' => config()->get('app.env')
]);

$recipes =  Recipe::all();
foreach ($recipes as $recipe) {
    $queue->connection('redis')->push('App\Jobs\UpdateElasticsearchJob@handle', [
        'recipe' => $recipe->toArray(),
        'event_type' => 'create',
        'app_env' => config()->get('app.env')
    ]);
}

<?php
namespace App\Jobs;

use App\Services\RecipeService;
use Symfony\Component\Dotenv\Dotenv;

class UpdateElasticsearchJob
{
    private $dotEnv;

    public function __construct(Dotenv $dotEnv)
    {
        $this->dotEnv = $dotEnv;
    }

    public function handle($job, $data)
    {
        logger()->info('job handle: '.__CLASS__, ['recipe_id' => $data['recipe']['id']]);

        $envPath = __DIR__.'/../../.env.'.$data['app_env'];
        if (file_exists($envPath = file_exists($envPath) ? $envPath : __DIR__.'/../../.env')) {
            $envVars = $this->dotEnv->parse(file_get_contents($envPath), $envPath);
            config()->set('db.elasticsearch.recipes_index', $envVars['ELASTICSEARCH_RECIPES_INDEX']);
        }

        // need to instantiate a new instance of recipe service after elastic_index is updated above
        $recipeService = container()->make('App\Services\RecipeService');

        $recipe = $data['recipe'];

        switch ($data['event_type']) {
            case 'create':
                logger()->info('Recipe create event.');
                $recipeService->create($recipe);
                break;
            case 'update':
                logger()->info('Recipe update event.');
                $recipeService->update($recipe, $recipe['id']);
                break;
            case 'delete':
                logger()->info('Recipe delete event.');
                $recipeService->delete($recipe['id']);
                break;
            case 'deleteAll':
                logger()->info('Recipe delete all event.');
                $recipeService->deleteAll();
                break;
            default:
                logger()->info('job handle: '.__CLASS__.', no event_type found');
                break;
        }

        $job->delete();
    }
}

<?php
namespace App\Listeners;

use App\Events\Contracts\RecipeEventInterface;
use App\Jobs\RecipeJob;
use App\Services\RecipeService;
use Illuminate\Queue\QueueManager;

class QueueRecipeEvents
{
    protected $queue;

    public function __construct(QueueManager $queue)
    {
        $this->queue = $queue;
    }

    public function handle(RecipeEventInterface $event)
    {
        $recipe = $event->getRecipe();
        $this->queue->connection('redis')->push('App\Jobs\UpdateElasticsearchJob@handle', [
            'recipe' => $recipe,
            'event_type' => $event->getEventType(),
            'app_env' => config()->get('app.env')
        ]);
        logger()->info('queued the job to redis', ['recipe_id'=>$recipe['id']]);
    }
}

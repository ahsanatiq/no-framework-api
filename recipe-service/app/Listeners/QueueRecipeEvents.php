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
        logger()->info('queued the job to redis', ['recipe_id'=>$event->recipe['id']]);
        $this->queue->connection('redis')->push('App\Jobs\RecipeJobDoesnotExists@handle', ['recipe' => $event->getRecipe()]);
    }
}

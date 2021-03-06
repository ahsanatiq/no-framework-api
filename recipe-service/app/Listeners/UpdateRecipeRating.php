<?php
namespace App\Listeners;

use App\Events\NewRatingCreatedEvent;
use App\Services\RecipeService;

class UpdateRecipeRating
{
    protected $recipeService;

    public function __construct(RecipeService $recipeService)
    {
        $this->recipeService = $recipeService;
    }

    public function handle(NewRatingCreatedEvent $event)
    {
        $recipe = $this->recipeService->updateRating($event->recipe['id']);
        logger()->info('New rating event created. updating recipe average rating.', [
            'recipe_id'=> $event->recipe['id'],
            'average_rating'=>$recipe['rating']
        ]);
    }
}

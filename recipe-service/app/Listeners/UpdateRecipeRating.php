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
        $recipe = $this->recipeService->updateRating($event->recipeId);
    }
}

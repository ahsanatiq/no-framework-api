<?php
namespace App\Events;

use App\Events\Contracts\RecipeEventInterface;

class RecipeUpdatedEvent implements RecipeEventInterface
{
    public $recipe;

    public function __construct($recipe)
    {
        $this->recipe = $recipe;
    }

    public function getRecipe()
    {
        return $this->recipe;
    }
}

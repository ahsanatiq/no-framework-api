<?php
namespace App\Events;

use App\Events\Contracts\RecipeEventInterface;

class RecipeUpdatedEvent implements RecipeEventInterface
{
    private $recipe;
    const EVENT_TYPE = 'update';

    public function __construct($recipe)
    {
        $this->recipe = $recipe;
    }

    public function getRecipe()
    {
        return $this->recipe;
    }

    public function getEventType()
    {
        return self::EVENT_TYPE;
    }
}

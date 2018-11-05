<?php
namespace App\Events;

class NewRatingCreatedEvent
{
    public $recipeId;
    public $rating;

    public function __construct($recipeId, $rating)
    {
        $this->recipeId = $recipeId;
        $this->rating = $rating;
    }
}

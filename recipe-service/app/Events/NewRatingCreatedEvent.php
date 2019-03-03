<?php
namespace App\Events;

class NewRatingCreatedEvent
{
    public $recipe;
    public $rating;

    public function __construct($recipe, $rating)
    {
        $this->recipe = $recipe;
        $this->rating = $rating;
    }
}

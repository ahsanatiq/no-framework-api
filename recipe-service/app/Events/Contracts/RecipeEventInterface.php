<?php
namespace App\Events\Contracts;

interface RecipeEventInterface
{
    public function getRecipe();
    public function getEventType();
}

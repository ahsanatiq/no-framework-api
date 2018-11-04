<?php
namespace App\Repositories\Arrays;

use App\Repositories\Contracts\RecipeRepositoryInterface;

class RecipeRepository implements RecipeRepositoryInterface
{
    private $recipe = [];

    function __construct()
    {
    }

    public function getAll()
    {
        return $this->recipe;
    }

}

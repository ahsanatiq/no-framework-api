<?php

namespace App\Repositories\Eloquent;

use App\Exceptions\RecipeNotFoundException;
use App\Exceptions\UnexpectedException;
use App\Models\Recipe;
use App\Repositories\Contracts\RecipeRepositoryInterface;

class RecipeRepository implements RecipeRepositoryInterface
{
    private $recipeModel;

    function __construct(Recipe $recipeModel)
    {
        $this->recipeModel = $recipeModel;
    }

    public function getAll()
    {
        return $this->recipeModel->all();
    }

    public function getPaginated($perPage, $pageNum)
    {
        return $this->recipeModel->paginate(
            $perPage,
            ['*'],
            config()->get('app.page_param'),
            $pageNum
        );
    }

    public function getById($recipeId)
    {
        $recipe = $this->recipeModel->find($recipeId);
        if(!$recipe)
        {
            throw new RecipeNotFoundException;
        }
        return $recipe;
    }

    public function create($data)
    {
        return $this->recipeModel->create($data);
    }

    public function update($data, $recipeId)
    {
        $recipe = $this->getById($recipeId);
        if($recipe->update($data)) {
            return $recipe;
        }
        throw new UnexpectedException;
    }

    public function delete($recipeId)
    {
        $recipe = $this->getById($recipeId);
        if($recipe->delete()) {
            return true;
        }
        throw new UnexpectedException;
    }

    public function createRating($data, $recipeId)
    {
        $recipe = $this->getById($recipeId);
        $rating = $recipe->ratings()->create($data);
        if($rating)
        {
            return rating;
        }
        throw new UnexpectedException;
    }

    public function updateRating($recipeId)
    {
        $recipe = $this->getById($recipeId);
        $rating = $recipe->ratings()->average('rating')->get();
        $recipe->rating = $rating;
        if($recipe->save())
        {
            return $recipe;
        }
        throw new UnexpectedException;
    }
}

<?php

namespace App\Services;

use App\Exceptions\UnexpectedException;
use App\Repositories\Contracts\RecipeRepositoryInterface;
use App\Services\Validators\RecipeValidator;

class RecipeService extends BaseService
{
    protected $recipeRepository;
    protected $recipeValidator;

    public function __construct(RecipeRepositoryInterface $recipeRepository, RecipeValidator $validator)
    {
        $this->recipeValidator = $validator;
        $this->recipeRepository = $recipeRepository;
    }

    public function getSearchPaginated($query, $perPage, $pageNum)
    {
        return $this->recipeRepository->getSearchPaginated($query, $perPage, $pageNum);
    }

    public function getById($id)
    {
        return $this->recipeRepository->getById($id);
    }

    public function create($data)
    {
        $this->recipeValidator->validate($data);
        $recipe = $this->recipeRepository->create($data);
        logger()->info('Recipe created.', ['recipe_id'=> $recipe['id']]);
        return $recipe;
    }

    public function update($data, $recipeId)
    {
        $this->recipeValidator->setMode('update')->validate($data);
        $recipe = $this->getById($recipeId);
        $updatedRecipe = $this->recipeRepository->update($data, $recipe['id']);
        logger()->info('Recipe updated.', ['recipe_id'=> $updatedRecipe['id']]);
        return $updatedRecipe;
    }

    public function delete($recipeId)
    {
       $recipe = $this->getById($recipeId);
       $result = $this->recipeRepository->delete($recipe['id']);
       logger()->info('Recipe deleted.', ['recipe_id'=> $recipe['id']]);
       return $result;
    }

    public function deleteAll()
    {
       $result = $this->recipeRepository->deleteAll();
       logger()->info('Recipes all deleted.');
       return $result;
    }

}

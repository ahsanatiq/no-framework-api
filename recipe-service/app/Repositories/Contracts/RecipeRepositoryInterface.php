<?php
namespace App\Repositories\Contracts;

interface RecipeRepositoryInterface
{
    public function getPaginated($perPage, $pageNum);
    public function getById($recipeId);
    public function create($data);
    public function update($data, $recipeId);
    public function delete($recipeId);
    public function createRating($data, $recipeId);
    public function updateRating($recipeId);
}

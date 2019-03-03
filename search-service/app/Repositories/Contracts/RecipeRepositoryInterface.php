<?php
namespace App\Repositories\Contracts;

interface RecipeRepositoryInterface
{
    public function getSearchPaginated($query, $perPage, $pageNum);
    public function getById($recipeId);
    public function create($data);
    public function update($data, $recipeId);
    public function delete($recipeId);
}

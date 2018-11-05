<?php
namespace App\Repositories\Contracts;

interface RecipeRepositoryInterface
{
    public function getPaginated($perPage, $pageNum);
    public function getById($id);
    public function create($data);
    public function update($data,$id);
    public function delete($id);
    public function createRating($data, $id);
}

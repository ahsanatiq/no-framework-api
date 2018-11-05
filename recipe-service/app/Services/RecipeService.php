<?php
namespace App\Services;

use App\Repositories\Contracts\RecipeRepositoryInterface;
use App\Services\Validators\RecipeValidator;

class RecipeService extends BaseService
{
    private $recipeRepository;
    private $validator;
    // private $rating;

    public function __construct(RecipeRepositoryInterface $recipeRepository, RecipeValidator $validator)
    {
        $this->recipeRepository = $recipeRepository;
        $this->validator = $validator;
    }

    public function getAll()
    {
        return $this->recipeRepository->getAll();
    }

    public function getPaginated($perPage, $pageNum)
    {
        return $this->recipeRepository->getPaginated($perPage, $pageNum);
    }

    public function getById($id)
    {
        return $this->recipeRepository->getById($id);
    }

    public function create($data)
    {
        $this->validator->validate($data);
        return $this->recipeRepository->create($data);
    }

    public function update($data, $id)
    {
        $this->validator->setMode('update');
        $this->validator->validate($data);

        $recipe = $this->getById($id);
        return $this->recipeRepository->update($data, $recipe['id']);
    }

    public function delete($id)
    {
        $recipe = $this->getById($id);
        return $this->recipeRepository->delete($recipe['id']);
    }

}

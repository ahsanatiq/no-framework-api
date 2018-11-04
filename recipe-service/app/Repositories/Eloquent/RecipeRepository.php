<?php

namespace App\Repositories\Eloquent;

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

    public function getById($id)
    {
        return $this->recipeModel->findorFail($id);
    }

    public function create($data)
    {
        return $this->recipeModel->create($data);
    }

    public function update($data, $id)
    {
        $recipe = $this->getById($id);
        if($recipe->update($data)) {
            return $recipe;
        }
        throw new UnexpectedException;
    }

    public function delete($id)
    {
        $recipe = $this->getById($id);
        if($recipe->delete()) {
            return true;
        }
        throw new UnexpectedException;
    }
}

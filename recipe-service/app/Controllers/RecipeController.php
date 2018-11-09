<?php
namespace App\Controllers;

use App\Services\RecipeService;
use App\Transformers\RecipeTransformer;
use Illuminate\Http\Request;

class RecipeController extends BaseController
{
    private $recipeService;
    private $recipeTransformer;

    public function __construct(RecipeService $recipeService, RecipeTransformer $recipeTransformer)
    {
        $this->recipeService = $recipeService;
        $this->recipeTransformer = $recipeTransformer;
    }

    public function getList(Request $request)
    {
        $recipesPaginated = $this->recipeService->getPaginated(
            $this->getPerPage($request),
            $this->getPageNum($request)
        );
        return $this->toFractalResponse(
            $recipesPaginated->getCollection(),
            $this->recipeTransformer,
            $recipesPaginated
        );
    }

    public function get($recipeId)
    {
        $recipe = $this->recipeService->getById($recipeId);
        return $this->toFractalResponse(
            $recipe,
            $this->recipeTransformer
        );
    }

    public function create(Request $request)
    {
        $newRecipe = $this->recipeService->create($request->all());
        return $this->toFractalResponse(
            $newRecipe,
            $this->recipeTransformer
        );
    }

    public function update($recipeId, Request $request)
    {
        $newRecipe = $this->recipeService->update($request->all(), $recipeId);
        return $this->toFractalResponse(
            $newRecipe,
            $this->recipeTransformer
        );
    }

    public function delete($recipeId)
    {
        $result = $this->recipeService->delete($recipeId);
        return ['success'=>$result];
    }
}

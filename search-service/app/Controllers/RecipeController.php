<?php
namespace App\Controllers;

use App\Services\RecipeService;
use App\Transformers\RecipeTransformer;
use Illuminate\Http\Request;

class RecipeController extends BaseController
{
    protected $recipeService;
    private $recipeTransformer;

    public function __construct(RecipeService $recipeService, RecipeTransformer $recipeTransformer)
    {
        $this->recipeService = $recipeService;
        $this->recipeTransformer = $recipeTransformer;
    }

    public function search(Request $request)
    {
        $recipesPaginated = $this->recipeService->getSearchPaginated(
            $request->input('query'),
            $this->getPerPage($request),
            $this->getPageNum($request)
        );
        return $this->toFractalResponse(
            $recipesPaginated->getCollection(),
            $this->recipeTransformer,
            $recipesPaginated
        );
    }
}

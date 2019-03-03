<?php
namespace App\Controllers;

use App\Services\RecipeService;
use App\Transformers\RecipeTransformer;
use Illuminate\Http\Request;

class RatingController extends BaseController
{
    private $recipeService;
    private $recipeTransformer;

    public function __construct(RecipeService $recipeService, RecipeTransformer $recipeTransformer)
    {
        $this->recipeService = $recipeService;
        $this->recipeTransformer = $recipeTransformer;
    }
    public function create(Request $request, $recipeId)
    {
        $recipe = $this->recipeService->createRating($request->all(), $recipeId);
        return $this->toFractalResponse(
            $recipe,
            $this->recipeTransformer
        );
    }
}

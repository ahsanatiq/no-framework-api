<?php
namespace App\Controllers;

use Illuminate\Http\Request;

class RatingController extends RecipeController
{
    public function create($recipeId, Request $request)
    {
        $recipe = $this->recipeService->createRating($request->all(), $recipeId);
        return $this->toFractalResponse(
            $recipe,
            $this->recipeTransformer
        );
    }
}

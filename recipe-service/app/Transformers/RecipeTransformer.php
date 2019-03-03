<?php

namespace App\Transformers;

use App\Models\Recipe;
use League\Fractal\TransformerAbstract;

class RecipeTransformer extends TransformerAbstract
{
    public function transform(Recipe $recipe)
    {
        return [
            'id'              => (int) $recipe->id,
            'name'            => $recipe->name,
            'description'     => $recipe->description,
            'difficulty'      => (int) $recipe->difficulty,
            'prep_time'       => (int) $recipe->prep_time,
            'vegetarian'      => (bool) $recipe->vegetarian,
            'rating'          => $recipe->rating != 0 ? (float) round($recipe->rating, 2) : null,
            'created_at'      => $recipe->created_at->format(\DateTime::ATOM),
            'updated_at'      => $recipe->updated_at->format(\DateTime::ATOM),
        ];
    }
}

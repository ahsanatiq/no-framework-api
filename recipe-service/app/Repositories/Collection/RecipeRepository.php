<?php
namespace App\Repositories\Collection;

use App\Exceptions\RecipeNotFoundException;
use App\Repositories\Contracts\RecipeRepositoryInterface;
use Illuminate\Support\Collection;

class RecipeRepository implements RecipeRepositoryInterface
{
    private $recipes;
    private $ratings;

    public function __construct()
    {
        $this->recipes = new Collection();
        $this->ratings = new Collection();
    }

    public function getAll()
    {
        return $this->recipes->all();
    }

    public function getPaginated($perPage, $pageNum)
    {
        return $this->recipes->sortByDesc('id')->forPage($pageNum, $perPage)->toArray();
    }

    public function getById($id)
    {
        $recipe = $this->recipes->where('id', $id)->first();
        if (!$recipe) {
            throw new RecipeNotFoundException;
        }
        return $recipe;
    }

    public function create($data)
    {
        $maxid = $this->recipes->max('id');
        $data['id'] = $maxid ? ++$maxid : 1;
        $data['rating'] = '0';
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->recipes->push($data);
        return $data;
    }

    public function update($data, $id)
    {
        $data = new Collection($data);
        $recipe = $this->getById($id);
        $updatedRecipe = array_merge($recipe, $data->only(
            'name',
            'description',
            'prep_time',
            'difficulty',
            'vegetarian',
            'rating'
        )->all());
        $this->recipes = $this->recipes->keyBy('id');
        $this->recipes->put($id, $updatedRecipe);
        return $updatedRecipe;
    }

    public function delete($id)
    {
        $recipe = $this->getById($id);
        $this->recipes = $this->recipes->keyBy('id');
        $this->recipes->forget($id);
        return true;
    }

    public function createRating($data, $id)
    {
        $recipe = $this->getById($id);
        $data['recipe_id'] = $recipe['id'];
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->ratings->push($data);
        return $recipe;
    }

    public function updateRating($id)
    {
        $recipe = $this->getById($id);
        $rating = $this->ratings->where('recipe_id', $id)->average('rating');
        $recipe = $this->update(['rating' => round($rating, 2)], $id);
        return $recipe;
    }
}

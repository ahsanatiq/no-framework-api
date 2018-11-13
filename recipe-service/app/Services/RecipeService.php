<?php
namespace App\Services;

use App\Events\NewRatingCreatedEvent;
use App\Events\RecipeCreatedEvent;
use App\Events\RecipeDeletedEvent;
use App\Events\RecipeUpdatedEvent;
use App\Repositories\Contracts\RecipeRepositoryInterface;
use App\Services\Validators\RatingValidator;
use App\Services\Validators\RecipeValidator;
use Illuminate\Support\Collection;

class RecipeService extends BaseService
{
    private $recipeRepository;
    private $recipeValidator;
    private $ratingValidator;

    public function __construct(
        RecipeRepositoryInterface $recipeRepository,
        RecipeValidator $recipeValidator,
        RatingValidator $ratingValidator
    ) {
        $this->recipeRepository = $recipeRepository;
        $this->recipeValidator = $recipeValidator;
        $this->ratingValidator = $ratingValidator;
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
        $this->recipeValidator->validate($data);
        $recipe = $this->recipeRepository->create($data);
        dispatcher()->dispatch(new RecipeCreatedEvent($recipe));
        logger()->info('Recipe created.', ['recipe_id'=> $recipe['id']]);
        return $recipe;
    }

    public function update($data, $id)
    {
        $this->recipeValidator->setMode('update');
        $this->recipeValidator->validate($data);

        $recipe = $this->getById($id);
        $updatedRecipe = $this->recipeRepository->update($data, $recipe['id']);
        dispatcher()->dispatch(new RecipeUpdatedEvent($updatedRecipe));
        logger()->info('Recipe updated.', ['recipe_id'=> $updatedRecipe['id']]);
        return $updatedRecipe;
    }

    public function delete($id)
    {
        $recipe = $this->getById($id);
        $result = $this->recipeRepository->delete($recipe['id']);
        dispatcher()->dispatch(new RecipeDeletedEvent($recipe));
        logger()->info('Recipe deleted.', ['recipe_id'=> $recipe['id']]);
        return $result;
    }

    public function createRating($data, $recipeId)
    {
        $this->getById($recipeId);
        $this->ratingValidator->validate($data);
        $rating = $this->recipeRepository->createRating($data, $recipeId);
        dispatcher()->dispatch(new NewRatingCreatedEvent($recipeId, $rating));
        $recipe = $this->getById($recipeId);
        logger()->info('Recipe has been rated.', ['recipe_id' => $recipe['id'], 'rating' => $data['rating']]);
        return $recipe;
    }

    public function updateRating($recipeId)
    {
        return $this->recipeRepository->updateRating($recipeId);
    }
}

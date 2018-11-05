<?php
namespace App\Services;

use App\Events\NewRatingCreatedEvent;
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
    )
    {
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
        return $this->recipeRepository->create($data);
    }

    public function update($data, $id)
    {
        $this->recipeValidator->setMode('update');
        $this->recipeValidator->validate($data);

        $recipe = $this->getById($id);
        return $this->recipeRepository->update($data, $recipe['id']);
    }

    public function delete($id)
    {
        $recipe = $this->getById($id);
        return $this->recipeRepository->delete($recipe['id']);
    }

    public function createRating($data, $recipeId)
    {
        $this->ratingValidator->validate($data);
        $rating = $this->recipeRepository->createRating($data, $recipeId);
        dispatcher()->dispatch(new NewRatingCreatedEvent($recipeId, $rating));
        return $this->getById($recipeId);
    }

    public function updateRating($recipeId)
    {
        return $this->recipeRepository->updateRating($recipeId);
    }

}

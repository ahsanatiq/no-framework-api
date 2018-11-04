<?php

use App\Exceptions\ValidationException;
use App\Models\Recipe;
use App\Repositories\Arrays\RecipeRepository;
use App\Services\RecipeService;
use App\Services\Validators\RecipeValidator;

class RecipeServiceTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    public $recipeService;

    protected function _before()
    {
        // $this->recipeService = new RecipeService(new RecipeRepository());
        container()->bind(
            'App\Repositories\Contracts\RecipeRepositoryInterface',
            'App\Repositories\Arrays\RecipeRepository'
        );
        container()->bind('recipeService', 'App\Services\RecipeService');
        $this->recipeService = container()->make('recipeService');
    }

    // tests
    public function testCanInitialize()
    {
        $this->assertInstanceOf(
            App\Services\RecipeService::class,
            $this->recipeService
        );
    }

    public function testGetRecipesEmptyAfterInitialization()
    {
        $recipes = $this->recipeService->getAll();
        $this->assertCount(0, $recipes);
    }

    public function testCreateRecipe()
    {
        $faker = Faker\Factory::create();
        $recipeData = [
            'name' => '',
            'description' => $faker->paragraphs(3, true),
            'prep_time'   => $faker->numberBetween(10, 60),
            'difficulty'  => $faker->numberBetween(1, 3),
            'vegetarian'  => $faker->randomElement([true, false])
        ];
        $this->expectException(ValidationException::class);
        // $this->expectExceptionMessage('name is required');
        $recipeId = $this->recipeService->create($recipeData);
        // $this->assertNotEmpty($recipeId);
    }

}

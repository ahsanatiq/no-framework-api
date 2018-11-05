<?php

use App\Exceptions\RecipeNotFoundException;
use App\Exceptions\ValidationException;
use App\Services\RecipeService;
use App\Services\Validators\RecipeValidator;

class RecipeServiceTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    public $recipeService;
    public $faker;

    protected function _before()
    {
        $this->faker = Faker\Factory::create();
        container()->bind(
            'App\Repositories\Contracts\RecipeRepositoryInterface',
            'App\Repositories\Collection\RecipeRepository'
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

    public function testGetZeroRecipesAfterInitialization()
    {
        $recipes = $this->recipeService->getAll();
        $this->assertCount(0, $recipes);
    }

    public function testValidationRequiredWithNameMissingWhenCreatingRecipe()
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('The name field is required.');
        $this->expectExceptionCode(422);
        $data = $this->generateRecipeData();
        $data['name'] = '';
        $recipeId = $this->recipeService->create($data);
    }

    public function testValidationRequiredWithDescriptionMissingWhenCreatingRecipe()
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('The description field is required.');
        $this->expectExceptionCode(422);
        $data = $this->generateRecipeData();
        $data['description'] = '';
        $recipeId = $this->recipeService->create($data);
    }

    public function testValidationRequiredWithPrepTimeMissingWhenCreatingRecipe()
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('The prep time field is required.');
        $this->expectExceptionCode(422);
        $data = $this->generateRecipeData();
        $data['prep_time'] = '';
        $recipeId = $this->recipeService->create($data);
    }

    public function testValidationRequiredWithDifficultyMissingWhenCreatingRecipe()
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('The difficulty field is required.');
        $this->expectExceptionCode(422);
        $data = $this->generateRecipeData();
        $data['difficulty'] = '';
        $recipeId = $this->recipeService->create($data);
    }

    public function testValidationRequiredWithVegetarianMissingWhenCreatingRecipe()
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('The vegetarian field is required.');
        $this->expectExceptionCode(422);
        $data = $this->generateRecipeData();
        $data['vegetarian'] = '';
        $recipeId = $this->recipeService->create($data);
    }

    public function testValidationMaxLengthWithNameWhenCreatingRecipe()
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('The name may not be greater than 100 characters.');
        $this->expectExceptionCode(422);
        $data = $this->generateRecipeData();
        $data['name'] = $this->faker->words(150);
        $recipeId = $this->recipeService->create($data);
    }

    public function testValidationNumericWithPrepTimeWhenCreatingRecipe()
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('The prep time must be a number.');
        $this->expectExceptionCode(422);
        $data = $this->generateRecipeData();
        $data['prep_time'] = $this->faker->text(10);
        $recipeId = $this->recipeService->create($data);
    }

    public function testValidationNumericWithDifficultyWhenCreatingRecipe()
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('The difficulty must be a number.');
        $this->expectExceptionCode(422);
        $data = $this->generateRecipeData();
        $data['difficulty'] = $this->faker->text(10);
        $recipeId = $this->recipeService->create($data);
    }

    public function testValidationRangeWithPrepTimeWhenCreatingRecipe()
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('The prep time must be between 1 and 9999.');
        $this->expectExceptionCode(422);
        $data = $this->generateRecipeData();
        $data['prep_time'] = $this->faker->numberBetween(9999,1000000);
        $recipeId = $this->recipeService->create($data);
    }

    public function testValidationRangeWithDifficultyWhenCreatingRecipe()
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('The difficulty must be between 1 and 3.');
        $this->expectExceptionCode(422);
        $data = $this->generateRecipeData();
        $data['difficulty'] = $this->faker->numberBetween(3,1000000);
        $recipeId = $this->recipeService->create($data);
    }

    public function testValidationBooleanWithVegetarianWhenCreatingRecipe()
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('The vegetarian field must be true or false.');
        $this->expectExceptionCode(422);
        $data = $this->generateRecipeData();
        $data['vegetarian'] = $this->faker->randomElement(['a','abc','1234',122356]);
        $recipeId = $this->recipeService->create($data);
    }

    public function testCreatingRecipe()
    {
        $data = $this->createRecipe();
        $this->assertArraySubset($data['original'], $data['result']);
    }

    public function testGettingRecipe()
    {
        $data = $this->createRecipe();
        $recipe = $this->recipeService->getById($data['result']['id']);
        $this->assertEquals($recipe, $data['result']);
    }

    public function testGettingRecipeByIdThatDoesNotExists()
    {
        $this->expectException(App\Exceptions\RecipeNotFoundException::class);
        $this->expectExceptionMessage('Recipe not found.');
        $this->expectExceptionCode(404);
        $this->recipeService->getById($this->faker->numberBetween(1,1000));
    }

    public function testGettingRecipeWithPagination()
    {
        $data1 = $this->createRecipe();
        $data2 = $this->createRecipe();
        $data3 = $this->createRecipe();
        $data4 = $this->createRecipe();
        $data5 = $this->createRecipe();
        $recipes = $this->recipeService->getPaginated('2','1');
        $this->assertCount(2, $recipes);
        $recipe1 = array_shift($recipes);
        $recipe2 = array_shift($recipes);
        $this->assertEquals($data5['result'], $recipe1);
        $this->assertEquals($data4['result'], $recipe2);
        $recipes = $this->recipeService->getPaginated('2','2');
        $this->assertCount(2, $recipes);
        $recipe1 = array_shift($recipes);
        $recipe2 = array_shift($recipes);
        $this->assertEquals($data3['result'], $recipe1);
        $this->assertEquals($data2['result'], $recipe2);
        $recipes = $this->recipeService->getPaginated('2','3');
        $this->assertCount(1, $recipes);
        $recipe1 = array_shift($recipes);
        $this->assertEquals($data1['result'], $recipe1);
    }

    public function testUpdateRecipe()
    {
        $recipe = $this->createRecipe();
        $data = $this->generateRecipeData();
        $updatedRecipe = $this->recipeService->update($data,$recipe['result']['id']);
        $this->assertArraySubset($data, $updatedRecipe);
        $recipeRow = $this->recipeService->getById($recipe['result']['id']);
        $this->assertEquals($updatedRecipe, $recipeRow);
    }

    public function testPartialUpdateRecipeWithName()
    {
        $recipe = $this->createRecipe();
        $generatedData = $this->generateRecipeData();
        $data['name'] = $generatedData['name'];
        $originalRecipe = $this->recipeService->getById($recipe['result']['id']);
        $updatedRecipe = $this->recipeService->update($data,$recipe['result']['id']);
        $updatedrecipeNew = $this->recipeService->getById($recipe['result']['id']);
        $this->assertEquals($data['name'], $updatedRecipe['name']);
        $this->assertEquals($updatedRecipe, $updatedrecipeNew);
        $this->assertEquals(array_merge($originalRecipe,$data), $updatedrecipeNew);
    }

    public function testPartialUpdateRecipeWithDescription()
    {
        $recipe = $this->createRecipe();
        $generatedData = $this->generateRecipeData();
        $data['description'] = $generatedData['description'];
        $originalRecipe = $this->recipeService->getById($recipe['result']['id']);
        $updatedRecipe = $this->recipeService->update($data,$recipe['result']['id']);
        $updatedrecipeNew = $this->recipeService->getById($recipe['result']['id']);
        $this->assertEquals($data['description'], $updatedRecipe['description']);
        $this->assertEquals($updatedRecipe, $updatedrecipeNew);
        $this->assertEquals(array_merge($originalRecipe,$data), $updatedrecipeNew);
    }

    public function testPartialUpdateRecipeWithPrepTime()
    {
        $recipe = $this->createRecipe();
        $generatedData = $this->generateRecipeData();
        $data['prep_time'] = $generatedData['prep_time'];
        $originalRecipe = $this->recipeService->getById($recipe['result']['id']);
        $updatedRecipe = $this->recipeService->update($data,$recipe['result']['id']);
        $updatedrecipeNew = $this->recipeService->getById($recipe['result']['id']);
        $this->assertEquals($data['prep_time'], $updatedRecipe['prep_time']);
        $this->assertEquals($updatedRecipe, $updatedrecipeNew);
        $this->assertEquals(array_merge($originalRecipe,$data), $updatedrecipeNew);
    }

    public function testPartialUpdateRecipeWithDifficulty()
    {
        $recipe = $this->createRecipe();
        $generatedData = $this->generateRecipeData();
        $data['difficulty'] = $generatedData['difficulty'];
        $originalRecipe = $this->recipeService->getById($recipe['result']['id']);
        $updatedRecipe = $this->recipeService->update($data,$recipe['result']['id']);
        $updatedrecipeNew = $this->recipeService->getById($recipe['result']['id']);
        $this->assertEquals($data['difficulty'], $updatedRecipe['difficulty']);
        $this->assertEquals($updatedRecipe, $updatedrecipeNew);
        $this->assertEquals(array_merge($originalRecipe,$data), $updatedrecipeNew);
    }

    public function testPartialUpdateRecipeWithVegetarian()
    {
        $recipe = $this->createRecipe();
        $generatedData = $this->generateRecipeData();
        $data['vegetarian'] = $generatedData['vegetarian'];
        $originalRecipe = $this->recipeService->getById($recipe['result']['id']);
        $updatedRecipe = $this->recipeService->update($data,$recipe['result']['id']);
        $updatedrecipeNew = $this->recipeService->getById($recipe['result']['id']);
        $this->assertEquals($data['vegetarian'], $updatedRecipe['vegetarian']);
        $this->assertEquals($updatedRecipe, $updatedrecipeNew);
        $this->assertEquals(array_merge($originalRecipe,$data), $updatedrecipeNew);
    }

    public function testValidationNameMaxLengthWhenUpdateRecipe()
    {
        $recipe = $this->createRecipe();
        $generatedData = $this->generateRecipeData();
        $data['name'] = $this->faker->words(150);
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('The name may not be greater than 100 characters.');
        $this->expectExceptionCode(422);
        $updatedRecipe = $this->recipeService->update($data,$recipe['result']['id']);
    }

    public function testValidationPrepTimeNumericWhenUpdateRecipe()
    {
        $recipe = $this->createRecipe();
        $generatedData = $this->generateRecipeData();
        $data['prep_time'] = $this->faker->words(150);
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('The prep time must be a number.');
        $this->expectExceptionCode(422);
        $updatedRecipe = $this->recipeService->update($data,$recipe['result']['id']);
    }

    public function testValidationPrepTimeRangeWhenUpdateRecipe()
    {
        $recipe = $this->createRecipe();
        $generatedData = $this->generateRecipeData();
        $data['prep_time'] = $this->faker->numberBetween(999999,10000000000);
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('The prep time must be between 1 and 9999.');
        $this->expectExceptionCode(422);
        $updatedRecipe = $this->recipeService->update($data,$recipe['result']['id']);
    }

    public function testValidationDifficultyNumericWhenUpdateRecipe()
    {
        $recipe = $this->createRecipe();
        $generatedData = $this->generateRecipeData();
        $data['difficulty'] = $this->faker->words(150);
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('The difficulty must be a number.');
        $this->expectExceptionCode(422);
        $updatedRecipe = $this->recipeService->update($data,$recipe['result']['id']);
    }

    public function testValidationDifficultyRangeWhenUpdateRecipe()
    {
        $recipe = $this->createRecipe();
        $generatedData = $this->generateRecipeData();
        $data['difficulty'] = $this->faker->numberBetween(3,10000);
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('The difficulty must be between 1 and 3.');
        $this->expectExceptionCode(422);
        $updatedRecipe = $this->recipeService->update($data,$recipe['result']['id']);
    }

    public function testValidationVegetarianBooleanWhenUpdateRecipe()
    {
        $recipe = $this->createRecipe();
        $generatedData = $this->generateRecipeData();
        $data['vegetarian'] = $this->faker->randomElement([3,10000,'a','asdf']);
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('The vegetarian field must be true or false.');
        $this->expectExceptionCode(422);
        $updatedRecipe = $this->recipeService->update($data,$recipe['result']['id']);
    }

    public function testDeleteRecipeWithIdThatDoesNotExists()
    {
        $this->expectException(RecipeNotFoundException::class);
        $this->expectExceptionMessage('Recipe not found.');
        $this->expectExceptionCode(404);
        $this->recipeService->delete($this->faker->numberBetween(10,1000));
    }

    public function testDeleteRecipe()
    {
        $recipe = $this->createRecipe();
        $result = $this->recipeService->delete($recipe['result']['id']);
        $this->assertTrue($result);
        $this->expectException(RecipeNotFoundException::class);
        $this->expectExceptionMessage('Recipe not found.');
        $this->expectExceptionCode(404);
        $this->recipeService->getById($recipe['result']['id']);
    }

    public function testValidationWithWrongRecipeIdWhenCreatingRating()
    {
        $this->expectException(RecipeNotFoundException::class);
        $this->expectExceptionMessage('Recipe not found.');
        $this->expectExceptionCode(404);
        $ratingData = $this->generateRatingData();
        $ratedRecipe = $this->recipeService->createRating($ratingData, $this->faker->numberBetween(10,10000));
    }

    public function testValidationWithRatingRequiredWhenCreatingRating()
    {
        $recipe = $this->createRecipe();
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('The rating field is required.');
        $this->expectExceptionCode(422);
        $ratingData = $this->generateRatingData();
        $ratingData['rating'] = '';
        $ratedRecipe = $this->recipeService->createRating($ratingData, $recipe['result']['id']);
    }

    public function testValidationWithRatingNumericWhenCreatingRating()
    {
        $recipe = $this->createRecipe();
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('The rating must be a number.');
        $this->expectExceptionCode(422);
        $ratingData = $this->generateRatingData();
        $ratingData['rating'] = $this->faker->randomElement(['a','abc',true]);
        $ratedRecipe = $this->recipeService->createRating($ratingData, $recipe['result']['id']);
    }

    public function testValidationWithRatingZeroWhenCreatingRating()
    {
        $recipe = $this->createRecipe();
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('The rating must be between 1 and 5.');
        $this->expectExceptionCode(422);
        $ratingData = $this->generateRatingData();
        $ratingData['rating'] = 0;
        $ratedRecipe = $this->recipeService->createRating($ratingData, $recipe['result']['id']);
    }

    public function testValidationWithRatingRangeWhenCreatingRating()
    {
        $recipe = $this->createRecipe();
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('The rating must be between 1 and 5.');
        $this->expectExceptionCode(422);
        $ratingData = $this->generateRatingData();
        $ratingData['rating'] = $this->faker->numberBetween(6,1000);
        $ratedRecipe = $this->recipeService->createRating($ratingData, $recipe['result']['id']);
    }

    public function testRatingRecipeWithSuccess()
    {
        $recipe = $this->createRecipe();
        $ratingRecipe = $this->createRating($recipe['result']['id']);
        $this->assertEquals($recipe['result'], $ratingRecipe['result']);
    }

    public function createRecipe()
    {
        $data = $this->generateRecipeData();
        $recipe = $this->recipeService->create($data);
        return ['original'=>$data, 'result'=>$recipe];
    }

    public function generateRecipeData()
    {
        return [
            'name' => $this->faker->word(),
            'description' => $this->faker->paragraphs(3, true),
            'prep_time'   => $this->faker->numberBetween(1, 9999),
            'difficulty'  => $this->faker->numberBetween(1, 3),
            'vegetarian'  => $this->faker->randomElement([true, false])
        ];
    }

    public function createRating($recipeId)
    {
        $data = $this->generateRatingData();
        $recipe = $this->recipeService->createRating($data, $recipeId);
        return ['original'=>$data, 'result'=>$recipe];
    }

    public function generateRatingData()
    {
        return [
            'rating'  => $this->faker->numberBetween(1, 5)
        ];
    }

}

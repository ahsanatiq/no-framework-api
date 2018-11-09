<?php
namespace Tests;

use Codeception\Util\HttpCode;
use Codeception\Util\JsonArray;
use Faker\Factory as FakerFactory;
use AcceptanceTester;

class RecipesAPICest
{
    protected $faker;
    public function _before(AcceptanceTester $I)
    {
        $this->faker = FakerFactory::create();
        $I->haveHttpHeader('APP_ENV', 'testing');
    }

    // tests
    public function getStatusCode404WhenWrongRoute(AcceptanceTester $I)
    {
        $resourceNotFoundJson = [
            'type'    => 'NotFoundHttpException',
            'message' => 'Resource not found.',
            'code'    => HttpCode::NOT_FOUND,
        ];
        $recipeNotFoundJson = [
            'type'    => 'RecipeNotFoundException',
            'message' => 'Recipe not found.',
            'code'    => HttpCode::NOT_FOUND,
        ];
        $combinations[] = ['route' => '/'];
        $combinations[] = ['route' => '/' . $this->faker->word];
        $combinations[] = ['route' => '/recipes/' . $this->faker->word];
        $combinations[] = ['route' => '/recipes/rating' . $this->faker->word];
        $combinations[] = [
            'route'         => '/recipes/' . $this->faker->numberBetween(0, 1000),
            'expected_on'   => ['GET', 'PUT', 'PATCH', 'DELETE'],
            'expected_json' => 'recipeNotFoundJson',
        ];
        $combinations[] = [
            'route'         => '/recipes/' . $this->faker->numberBetween(0, 1000) . '/rating' ,
            'expected_on'   => ['POST'],
            'expected_on'   => ['POST'],
            'expected_json' => 'recipeNotFoundJson',
        ];

        foreach ($combinations as $combination) {
            foreach (['GET', 'POST', 'PUT', 'PATCH', 'DELETE'] as $verb) {
                $I->{'send' . $verb}($combination['route']);
                $I->seeResponseCodeIs(HttpCode::NOT_FOUND);
                $I->seeResponseIsJson();
                $expected_json = (
                    !empty($combination['expected_json'])
                    && !empty($combination['expected_on'])
                    && in_array($verb, $combination['expected_on'])
                )
                ? ${$combination['expected_json']}
                : $resourceNotFoundJson;
                $I->seeResponseContainsJson($expected_json);
            }
        }
    }

    public function getEmptyListWhenNoRecipe(AcceptanceTester $I)
    {
        $I->sendGET('/recipes');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseJsonMatchesXpath('//data');
        $response = (new JsonArray($I->grabResponse()))->toArray();
        $I->assertCount(0, $response['data']);
    }

    public function getListWhenEmpty(AcceptanceTester $I)
    {
        $I->sendGET('/recipes');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseJsonMatchesXpath('//data');
        $response = (new JsonArray($I->grabResponse()))->toArray();
        $I->assertCount(0, $response['data']);
    }

    public function createRecipeWithValidationsErrors(AcceptanceTester $I)
    {
        $recipeData  = $this->generateRecipeData();
        $validations = $this->getValidations();
        foreach ($validations as $validation) {
            $I->sendPOST('/recipes', array_merge($recipeData, [$validation['title'] => $validation['try']]));
            $I->seeResponseCodeIs(HttpCode::UNPROCESSABLE_ENTITY);
            $I->seeResponseIsJson();
            $I->seeResponseContainsJson([
                'type'    => 'ValidationException',
                'message' => [$validation['message']],
                'code'    => 422,
            ]);
            $this->getListWhenEmpty($I);
        }
    }

    public function createAndGetRecipesWithoutPagination(AcceptanceTester $I)
    {
        $itemsPerPage = config()->get('app.items_per_page');
        $recipes      = $this->createRecipes($I, $itemsPerPage);
        $numOfRecipes = count($recipes);
        $I->sendGET('/recipes');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseJsonMatchesXpath('//data');
        $I->dontSeeResponseJsonMatchesXpath('//data/meta');
        $response = (new JsonArray($I->grabResponse()))->toArray();
        $I->assertCount($numOfRecipes, $response['data']);
        for ($j = ($numOfRecipes - 1); $j >= 0; $j--) {
            // Testing the recipe order, recipes got back in the descending order
            $I->assertArraySubset($recipes[$j], $response['data'][($numOfRecipes - 1) - $j]);
            $this->getRecipeById($I, $recipes[$j]);
        }
    }

    public function createAndGetRecipesWithPagination(AcceptanceTester $I, \Codeception\Module\REST $restModule)
    {
        $apiUrl              = $restModule->_getConfig('url');
        $numOfRecipes        = '10';
        $itemsPerPage        = config()->get('app.items_per_page');
        $numOfPages          = floor(($numOfRecipes + $itemsPerPage - 1) / $itemsPerPage);
        $recipes             = $this->createRecipes($I, $numOfRecipes);
        $numOfRecipesCreated = count($recipes);
        $I->assertEquals($numOfRecipes, $numOfRecipesCreated);

        for ($i = 1; $i <= $numOfPages; $i++) {
            $params = ['page' => $i];
            $I->sendGET('/recipes', $params);
            $I->seeResponseCodeIs(HttpCode::OK);
            $I->seeResponseIsJson();
            $I->seeResponseJsonMatchesXpath('//data');
            $I->seeResponseJsonMatchesXpath('//meta/pagination');
            $response = (new JsonArray($I->grabResponse()))->toArray();

            $startIndex = $i * $itemsPerPage - ($itemsPerPage - 1);
            $endIndex   = min($startIndex + ($itemsPerPage - 1), $numOfRecipes);
            $countIndex = $endIndex - $startIndex + 1;

            $nextLink     = $apiUrl . '/recipes?page=' . ($i + 1);
            $previousLink = $apiUrl . '/recipes?page=' . ($i - 1);
            $links        = [];
            if ($i != 1) {
                $links['previous'] = $previousLink;
            }
            if ($i != $numOfPages) {
                $links['next'] = $nextLink;
            }
            $I->assertArraySubset(['pagination' => [
                'total'        => (int) $numOfRecipes,
                'count'        => (int) $countIndex,
                'per_page'     => (int) $itemsPerPage,
                'current_page' => $i,
                'total_pages'  => (int) $numOfPages,
                'links'        => $links,
            ]], $response['meta']);

            $I->assertCount($countIndex, $response['data']);
            for ($j = $startIndex; $j <= $endIndex; $j++) {
                // Testing the recipe order, recipes got back in the descending order
                $I->assertArraySubset($recipes[$numOfRecipes - $j], $response['data'][($j - 1) % $itemsPerPage]);
                $this->getRecipeById($I, $recipes[$numOfRecipes - $j]);
            }
        }
    }

    public function updateWithPutAndGetRecipe(AcceptanceTester $I)
    {
        $this->updateAndGetRecipe($I, 'PUT');
    }

    public function updateWithPatchAndGetRecipe(AcceptanceTester $I)
    {
        $this->updateAndGetRecipe($I, 'PATCH');
    }

    public function updateRecipeWithValidationsErrors(AcceptanceTester $I)
    {
        $recipe      = $this->createRecipe($I);
        $validations = $this->getValidations();
        foreach ($validations as $validation) {
            $I->sendPUT('/recipes/' . $recipe['id'], array_merge($recipe, [
                $validation['title'] => $validation['try']
            ]));
            $I->seeResponseCodeIs(HttpCode::UNPROCESSABLE_ENTITY);
            $I->seeResponseIsJson();
            $I->seeResponseContainsJson([
                'type'    => 'ValidationException',
                'message' => [$validation['message']],
                'code'    => 422,
            ]);
            $this->getRecipeById($I, $recipe);
        }
    }

    public function deleteRecipe(AcceptanceTester $I)
    {
        $recipe = $this->createRecipe($I);
        $I->sendDELETE('/recipes/' . $recipe['id']);
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['success' => true]);
        $this->getRecipeByIdThatDoesNotExists($I, $recipe);
        $this->getListWhenEmpty($I);
    }

    public function testRatingWhenCreatingAndUpdatingRecipe(AcceptanceTester $I)
    {
        $recipeData = $this->generateRecipeData();
        $recipeData['rating'] = '5';
        $recipe = $this->createRecipe($I, $recipeData);
        $I->assertEquals(null, $recipe['rating']);
        $this->getRecipeById($I, $recipe);
        $recipe['rating'] = '5';
        $recipeUpdated = $this->updateAndGetRecipe($I, 'put', $recipeData);
        $I->assertEquals(null, $recipeUpdated['rating']);
        $this->getRecipeById($I, $recipeUpdated);
    }

    public function ratingRecipesWithValidationErrors(AcceptanceTester $I)
    {
        $recipe = $this->createRecipe($I);
        $validations = [
            ['try'=>'', 'message'=>'The rating field is required.'],
            ['try'=>$this->faker->word, 'message'=>'The rating must be a number.'],
            ['try'=>'0', 'message'=>'The rating must be between 1 and 5.'], // zero rating is not allowed
            ['try'=>$this->faker->numberBetween(6, 1000), 'message'=>'The rating must be between 1 and 5.']
        ];
        foreach ($validations as $validation) {
            $I->sendPOST('/recipes/'.$recipe['id'].'/rating', ['rating'=>$validation['try']]);
            $I->seeResponseCodeIs(HttpCode::UNPROCESSABLE_ENTITY);
            $I->seeResponseIsJson();
            $I->seeResponseContainsJson([
                'type'    => 'ValidationException',
                'message' => [$validation['message']],
                'code'    => 422,
            ]);
        }
    }

    public function testRatingOnRecipes(AcceptanceTester $I)
    {
        $recipe1 = $this->createRecipe($I);
        $recipe2 = $this->createRecipe($I);
        $ratedRecipe1 = $this->createRating($I, $recipe1, 5);
        $I->assertEquals(5, $ratedRecipe1['rating']);
        $ratedRecipe2 = $this->createRating($I, $recipe2, 1);
        $I->assertEquals(1, $ratedRecipe2['rating']);
        $ratedRecipe1 = $this->createRating($I, $recipe1, 3);
        $I->assertEquals(4, $ratedRecipe1['rating']);
        $ratedRecipe1 = $this->createRating($I, $recipe1, 2);
        $I->assertEquals(3.33, $ratedRecipe1['rating']);
        $ratedRecipe1 = $this->createRating($I, $recipe1, 5);
        $I->assertEquals(3.75, $ratedRecipe1['rating']);
        $ratedRecipe2 = $this->createRating($I, $recipe2, 1);
        $I->assertEquals(1, $ratedRecipe2['rating']);
        $ratedRecipe2 = $this->createRating($I, $recipe2, 2);
        $I->assertEquals(1.33, $ratedRecipe2['rating']);
    }

    protected function createRating(AcceptanceTester $I, $recipe, $rating)
    {
        $I->sendPOST('/recipes/'.$recipe['id'].'/rating', ['rating'=>$rating]);
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        unset($recipe['rating']);
        unset($recipe['updated_at']);
        $I->seeResponseContainsJson(['data'=>$recipe]);
        $response = (new JsonArray($I->grabResponse()))->toArray();
        $recipe = $this->getRecipeById($I, $recipe);
        $I->assertEquals($recipe, $response['data']);
        return $recipe;
    }

    protected function updateAndGetRecipe(AcceptanceTester $I, $httpVerb = 'put', $data = null)
    {
        $recipe = $this->createRecipe($I);
        if ($data) {
            $newDataForRequest  = $data;
            $newDataForResponse = array_filter($data, function ($key) {
                return ($key != 'rating' ? true : false);
            }, ARRAY_FILTER_USE_KEY);
        } else {
            $newDataForResponse = $newDataForRequest = $this->generateRecipeData();
        }
        if (strtolower($httpVerb) == 'put') {
            $I->sendPUT('/recipes/' . $recipe['id'], $newDataForRequest);
        } else {
            $I->sendPATCH('/recipes/' . $recipe['id'], $newDataForRequest);
        }
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $I->dontSeeResponseJsonMatchesXpath('//data/meta');
        $I->seeResponseContainsJson(['data' => $newDataForResponse]);
        $response = (new JsonArray($I->grabResponse()))->toArray();
        $this->getRecipeById($I, $response['data']);
        return $response['data'];
    }

    protected function getRecipeById(AcceptanceTester $I, $recipe)
    {
        $I->sendGET('/recipes/' . $recipe['id']);
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $I->dontSeeResponseJsonMatchesXpath('//data/meta');
        $I->seeResponseContainsJson(['data' => $recipe]);
        $response = (new JsonArray($I->grabResponse()))->toArray();
        return $response['data'];
    }

    protected function getRecipeByIdThatDoesNotExists(AcceptanceTester $I, $recipe)
    {
        $I->sendGET('/recipes/' . $recipe['id']);
        $I->seeResponseCodeIs(HttpCode::NOT_FOUND);
        $I->seeResponseIsJson();
        $I->dontSeeResponseJsonMatchesXpath('//data/meta');
        $I->seeResponseContainsJson([
            'type'    => 'RecipeNotFoundException',
            'message' => 'Recipe not found.',
            'code'    => '404',
        ]);
    }

    protected function createRecipes(AcceptanceTester $I, $numOfRecipes)
    {
        for ($i = 0; $i <= ($numOfRecipes - 1); $i++) {
            $recipes[$i] = $this->createRecipe($I);
        }
        return $recipes;
    }

    protected function createRecipe(AcceptanceTester $I, $data = null)
    {
        if ($data) {
            $recipeRequestData  = $data;
            $recipeResponseData = array_filter($data, function ($key) {
                return ($key != 'rating' ? true : false);
            }, ARRAY_FILTER_USE_KEY);
        } else {
            $recipeRequestData  = $this->generateRecipeData();
            $recipeResponseData = $recipeRequestData;
        }
        $I->sendPOST('/recipes', $recipeRequestData);
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseJsonMatchesXpath('//data');
        $response = (new JsonArray($I->grabResponse()))->toArray();
        $I->assertArraySubset($recipeResponseData, $response['data']);
        return $response['data'];
    }

    protected function generateRecipeData()
    {
        return [
            'name'        => $this->faker->word(),
            'description' => $this->faker->paragraphs(3, true),
            'prep_time'   => $this->faker->numberBetween(1, 9999),
            'difficulty'  => $this->faker->numberBetween(1, 3),
            'vegetarian'  => $this->faker->randomElement([true, false]),
        ];
    }

    protected function getValidations()
    {
        return [
            ['title' => 'name',
            'try' => '',
            'message' => 'The name field is required.'],
            ['title' => 'description',
            'try' => '',
            'message' => 'The description field is required.'],
            ['title' => 'prep_time',
            'try' => '',
            'message' => 'The prep time field is required.'],
            ['title' => 'difficulty',
            'try' => '',
            'message' => 'The difficulty field is required.'],
            ['title' => 'vegetarian',
            'try' => '',
            'message' => 'The vegetarian field is required.'],
            ['title' => 'name',
            'try' => $this->faker->text(100) . $this->faker->text(100),
            'message' => 'The name may not be greater than 100 characters.'],
            ['title' => 'prep_time',
            'try' => $this->faker->text(10),
            'message' => 'The prep time must be a number.'],
            ['title' => 'prep_time',
            'try' => $this->faker->numberBetween(10000, 99999),
            'message' => 'The prep time must be between 1 and 9999.'],
            ['title' => 'difficulty',
            'try' => $this->faker->text(10),
            'message' => 'The difficulty must be a number.'],
            ['title' => 'difficulty',
            'try' => $this->faker->numberBetween(-10, 0),
            'message' => 'The difficulty must be between 1 and 3.'],
            ['title' => 'difficulty',
            'try' => $this->faker->numberBetween(4, 100),
            'message' => 'The difficulty must be between 1 and 3.'],
            ['title' => 'vegetarian',
            'try' => $this->faker->randomElement(['a', 'abc', '1234', 122356]),
            'message' => 'The vegetarian field must be true or false.'],
        ];
    }
}

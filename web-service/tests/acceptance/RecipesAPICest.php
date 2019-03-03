<?php
namespace Tests;

use Codeception\Util\HttpCode;
use Codeception\Util\JsonArray;
use Faker\Factory as FakerFactory;
use AcceptanceTester;

class RecipesAPICest
{
    protected $faker;
    protected $restModule;

    public function _before(AcceptanceTester $I)
    {
        $this->faker = FakerFactory::create('en_US');
        $I->haveHttpHeader('APP_ENV', 'testing');
    }

    public function _inject(\Codeception\Module\REST $restModule)
    {
        $this->restModule = $restModule;
    }

    // tests
    public function getStatusCode404WhenWrongRoute(AcceptanceTester $I)
    {
        $this->getAuthenticated($I);
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
        $this->getAuthenticated($I);
        $recipeData  = $this->generateRecipeData();
        $validations = $this->getValidations();
        foreach ($validations as $validation) {
            $I->sendPOST('/recipes', array_merge($recipeData, [$validation['title'] => $validation['try']]));
            $I->seeResponseCodeIs(HttpCode::UNPROCESSABLE_ENTITY);
            $I->seeResponseIsJson();
            $I->seeResponseContainsJson([
                'type'    => 'ValidationException',
                'message' => [$validation['message']],
                'code'    => HttpCode::UNPROCESSABLE_ENTITY,
            ]);
            $this->getListWhenEmpty($I);
        }
    }

    public function createAndGetRecipesWithoutPagination(AcceptanceTester $I)
    {
        $this->getAuthenticated($I);
        $itemsPerPage = $this->restModule->_getConfig('app_items_per_page');
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

    public function createAndGetRecipesWithPagination(AcceptanceTester $I)
    {
        $this->getAuthenticated($I);
        $apiUrl              = $this->restModule->_getConfig('app_url');
        $numOfRecipes        = '10';
        $itemsPerPage        = $this->restModule->_getConfig('app_items_per_page');
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

            $nextLink     = '/api/v1/recipes?page=' . ($i + 1);
            $previousLink = '/api/v1/recipes?page=' . ($i - 1);
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

    public function createAndSearchRecipesWithoutPagination(AcceptanceTester $I)
    {
        $this->getAuthenticated($I);
        $itemsPerPage = $this->restModule->_getConfig('app_items_per_page');

        $recipes = (new JsonArray(file_get_contents(codecept_root_dir('tests/_data/recipes.json'))))->toArray();
        foreach ($recipes as $key => $value) {
            $recipes[$key] = array_merge($this->generateRecipeData(), $value);
            $this->createRecipe($I, $recipes[$key]);
        }
        $numOfRecipes = count($recipes);

        // 3 second wait for sync of data between recipe-service to search-service
        usleep(3 * 1000000);

        foreach([
            ['query' => 'garlic', 'expectedCount' => 1, 'expectedRecipe'=>0],
            ['query' => 'chicken', 'expectedCount' => 1, 'expectedRecipe'=>1],
            ['query' => 'delicious', 'expectedCount' => 2, 'expectedRecipe'=>[1,2]],
            ['query' => 'tomato', 'expectedCount' => 2, 'expectedRecipe'=>[1,2]],
            ['query' => 'Potato', 'expectedCount' => 2, 'expectedRecipe'=>[0,3]],
            ['query' => 'beef and chicken', 'expectedCount' => 2, 'expectedRecipe'=>[0,1]],
            ['query' => 'potato + -avocado', 'expectedCount' => 1, 'expectedRecipe'=>0],
        ] as $search)
        {
            $I->sendGET($this->restModule->_getConfig('search_url'), ['query'=>urlencode($search['query'])]);
            $I->seeResponseCodeIs(HttpCode::OK);
            $I->seeResponseIsJson();
            $I->seeResponseJsonMatchesXpath('//data');
            $I->seeResponseJsonMatchesXpath('//meta/pagination');
            if (!is_array($search['expectedRecipe'])) {
                $search['expectedRecipe'] = [$search['expectedRecipe']];
            }
            $response = (new JsonArray($I->grabResponse()))->toArray();
            $I->assertCount($search['expectedCount'], $response['data']);
            foreach ($search['expectedRecipe'] as $expectedRecipe)
            {
                $I->seeResponseContainsJson(['data'=>[$recipes[$expectedRecipe]]]);
            }


        }
    }

    public function createAndSearchRecipesWithPagination(AcceptanceTester $I)
    {
        $this->getAuthenticated($I);
        $itemsPerPage = $this->restModule->_getConfig('app_items_per_page');

        $recipes = (new JsonArray(file_get_contents(codecept_root_dir('tests/_data/recipes.json'))))->toArray();
        $recipes[0] = str_ireplace('chicken', '', $recipes[0]);
        $recipes[1] = str_ireplace('garlic', '', $recipes[1]);
        for ($i=0; $i<($itemsPerPage*2); $i++) {
            $recipesGarlic[$i] = $this->createRecipe($I, array_merge($this->generateRecipeData(), $recipes[0]));
        }
        for ($i=0; $i<($itemsPerPage); $i++) {
            $recipesChicken[$i] = $this->createRecipe($I, array_merge($this->generateRecipeData(), $recipes[1]));
        }

         // 3 second wait for sync of data between recipe-service to search-service
        usleep(3 * 1000000);

        foreach([
            ['recipe' => $recipesGarlic, 'query'=> 'Garlic', 'original'=>$recipes[0]],
            ['recipe' => $recipesChicken, 'query'=> 'Chicken', 'original'=>$recipes[1]],
        ] as $recipeRow)
        {
            $numOfRecipes = count($recipeRow['recipe']);
            $numOfPages = floor(($numOfRecipes + $itemsPerPage - 1) / $itemsPerPage);

            for ($i = 1; $i <= $numOfPages; $i++) {
                $I->sendGET($this->restModule->_getConfig('search_url'), ['query'=>urlencode($recipeRow['query']), 'page' => $i]);
                $I->seeResponseCodeIs(HttpCode::OK);
                $I->seeResponseIsJson();
                $I->seeResponseJsonMatchesXpath('//data');
                $I->seeResponseJsonMatchesXpath('//meta/pagination');
                $response = (new JsonArray($I->grabResponse()))->toArray();

                $startIndex = $i * $itemsPerPage - ($itemsPerPage - 1);
                $endIndex   = min($startIndex + ($itemsPerPage - 1), $numOfRecipes);
                $countIndex = $endIndex - $startIndex + 1;

                $nextLink     = '/api/v1/search?query='.urlencode($recipeRow['query']).'&page=' . ($i + 1);
                $previousLink = '/api/v1/search?query='.urlencode($recipeRow['query']).'&page=' . ($i - 1);
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
                    $I->assertArraySubset($recipeRow['original'], $response['data'][($j - 1) % $itemsPerPage]);
                }
            }
        }
    }

    public function updateAndSearchRecipe(AcceptanceTester $I)
    {
        $this->getAuthenticated($I);
        $recipes = (new JsonArray(file_get_contents(codecept_root_dir('tests/_data/recipes.json'))))->toArray();
        $recipesGarlic = $this->updateAndGetRecipe($I, 'PUT', array_merge($this->generateRecipeData(), $recipes[0]));

        usleep(3 * 1000000);

        $I->sendGET($this->restModule->_getConfig('search_url'), ['query'=>urlencode('Garlic')]);
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseJsonMatchesXpath('//data');
        $response = (new JsonArray($I->grabResponse()))->toArray();
        $I->seeResponseContainsJson(['data'=>[$recipes[0]]]);
    }

    public function deleteAndSearchRecipe(AcceptanceTester $I)
    {
        $this->getAuthenticated($I);
        $recipes = (new JsonArray(file_get_contents(codecept_root_dir('tests/_data/recipes.json'))))->toArray();
        $recipe = $this->createRecipe($I, array_merge($this->generateRecipeData(), $recipes[0]));
        $I->sendDELETE('/recipes/' . $recipe['id']);
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['success' => true]);

        usleep(3 * 1000000);

        $I->sendGET($this->restModule->_getConfig('search_url'), ['query'=>urlencode('Garlic')]);
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseJsonMatchesXpath('//data');
        $response = (new JsonArray($I->grabResponse()))->toArray();
        $I->dontSeeResponseContainsJson(['data'=>[$recipes[0]]]);
    }

    public function updateWithPutAndGetRecipe(AcceptanceTester $I)
    {
        $this->getAuthenticated($I);
        $this->updateAndGetRecipe($I, 'PUT');
    }

    public function updateWithPatchAndGetRecipe(AcceptanceTester $I)
    {
        $this->getAuthenticated($I);
        $this->updateAndGetRecipe($I, 'PATCH');
    }

    public function updateRecipeWithValidationsErrors(AcceptanceTester $I)
    {
        $this->getAuthenticated($I);
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
                'code'    => HttpCode::UNPROCESSABLE_ENTITY,
            ]);
            $this->getRecipeById($I, $recipe);
        }
    }

    public function deleteRecipe(AcceptanceTester $I)
    {
        $this->getAuthenticated($I);
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
        $this->getAuthenticated($I);
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
        $this->getAuthenticated($I);
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
                'code'    => HttpCode::UNPROCESSABLE_ENTITY,
            ]);
        }
    }

    public function testRatingOnRecipes(AcceptanceTester $I)
    {
        $this->getAuthenticated($I);
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

    public function testOauth2AuthenticationValidationWhenWrongClientCredentials(AcceptanceTester $I)
    {
        $tokenUrl = $this->restModule->_getConfig('oauth_token_url');
        $clientId = $this->restModule->_getConfig('oauth_client_id');
        $clientSecret = $this->restModule->_getConfig('oauth_client_secret');
        $grantType = $this->restModule->_getConfig('oauth_grant_type');
        $I->setHeader('Authorization', 'Basic '.base64_encode($this->faker->word . ':' .$this->faker->word));
        $I->sendPOST($tokenUrl, ['grant_type'=>$grantType]);
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['error' => 'invalid_client', 'error_description' => 'The client credentials are invalid']);
    }

    public function testOauth2AuthenticationValidationWhenWrongGrantType(AcceptanceTester $I)
    {
        $tokenUrl = $this->restModule->_getConfig('oauth_token_url');
        $clientId = $this->restModule->_getConfig('oauth_client_id');
        $clientSecret = $this->restModule->_getConfig('oauth_client_secret');
        $grantType = $this->restModule->_getConfig('oauth_grant_type');
        $I->setHeader('Authorization', 'Basic '.base64_encode($this->faker->word . ':' .$this->faker->word));
        $fakeGrantType = $this->faker->word;
        $I->sendPOST($tokenUrl, ['grant_type'=>$fakeGrantType]);
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['error' => 'unsupported_grant_type', 'error_description' => 'Grant type "'.$fakeGrantType.'" not supported']);
    }

    public function testProtectedEndpointsWithoutAuthentication(AcceptanceTester $I)
    {
        $this->getAuthenticated($I);
        $recipe = $this->createRecipe($I);
        $I->deleteHeader('Authorization');
        $protectedEndpoints = [
            ['verb' => 'POST', 'route' => '/recipes'],
            ['verb' => 'PUT', 'route' => '/recipes/'.$recipe['id']],
            ['verb' => 'PATCH', 'route' => '/recipes/'.$recipe['id']],
            ['verb' => 'DELETE', 'route' => '/recipes/'.$recipe['id']],
        ];
        foreach($protectedEndpoints as $endpoint) {
            $I->{'send'.$endpoint['verb']}($endpoint['route'], $this->generateRecipeData());
            $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
            $I->seeResponseIsJson();
            $I->seeResponseContainsJson([
                'type'    => 'UnauthorizedException',
                'message' => 'Token not present',
                'code'    => HttpCode::UNAUTHORIZED
            ]);
        }
    }

    public function testProtectedEndpointsWithInvalidToken(AcceptanceTester $I)
    {
        $accessToken = $this->getAuthenticated($I);
        $recipe = $this->createRecipe($I);

        $accessTokenParts = explode('.', $accessToken);
        $payload = $accessTokenParts[1];
        $accessTokenParts[1] = substr_replace($payload, '', rand(0,strlen($payload)), '1');
        $accessToken = implode('.', $accessTokenParts);
        $I->amBearerAuthenticated($accessToken);

        $protectedEndpoints = [
            ['verb' => 'POST', 'route' => '/recipes'],
            ['verb' => 'PUT', 'route' => '/recipes/'.$recipe['id']],
            ['verb' => 'PATCH', 'route' => '/recipes/'.$recipe['id']],
            ['verb' => 'DELETE', 'route' => '/recipes/'.$recipe['id']],
        ];
        foreach($protectedEndpoints as $endpoint) {
            $I->{'send'.$endpoint['verb']}($endpoint['route'], $this->generateRecipeData());
            $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
            $I->seeResponseIsJson();
            $I->seeResponseContainsJson([
                'type'    => 'UnauthorizedException',
                'message' => 'Invalid token',
                'code'    => HttpCode::UNAUTHORIZED
            ]);
        }
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
        $recipes = [];
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
        // $response = (new JsonArray($I->grabResponse()))->toArray();
        $response = (new JsonArray($I->grabResponse()))->toArray();
        // codecept_debug('1:'.var_export($recipeResponseData,true));
        // codecept_debug('2:'.var_export($response['data'],true));
        $I->assertArraySubset($recipeResponseData, $response['data']);
        return $response['data'];
    }

    protected function getAuthenticated(AcceptanceTester $I)
    {
        $tokenUrl = $this->restModule->_getConfig('oauth_token_url');
        $clientId = $this->restModule->_getConfig('oauth_client_id');
        $clientSecret = $this->restModule->_getConfig('oauth_client_secret');
        $grantType = $this->restModule->_getConfig('oauth_grant_type');
        # this does not work! instead use Authentication Basic Header
        // $I->amHttpAuthenticated('testclient', 'testpass');
        $I->setHeader('Authorization', 'Basic '.base64_encode($clientId . ':' .$clientSecret));
        $I->sendPOST($tokenUrl, ['grant_type'=>$grantType]);
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $response = (new JsonArray($I->grabResponse()))->toArray();
        $I->assertArrayHasKey('access_token', $response);
        $I->assertNotEmpty($response['access_token']);
        $I->deleteHeader('Authorization');
        $I->amBearerAuthenticated($response['access_token']);
        return $response['access_token'];
    }

    protected function generateRecipeData()
    {
        return [
            'name'        => $this->faker->word(),
            'description' => $this->faker->realText(),
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

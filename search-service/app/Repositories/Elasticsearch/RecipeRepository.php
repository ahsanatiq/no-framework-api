<?php

namespace App\Repositories\Elasticsearch;

use App\Exceptions\RecipeNotFoundException;
use App\Exceptions\UnexpectedException;
use App\Repositories\Contracts\RecipeRepositoryInterface;
use Elasticsearch\Client as ElasticsearchClient;
use Illuminate\Pagination\LengthAwarePaginator;

class RecipeRepository implements RecipeRepositoryInterface
{
    private $elasticsearch;

    public function __construct(ElasticsearchClient $elasticsearch)
    {
        $this->elasticsearch = $elasticsearch;

        if(!$this->indexExists())
        {
            $this->indexCreate();
        }
    }

    public function getSearchPaginated($query, $perPage, $pageNum)
    {
        try {
            $response = $this->elasticsearch->search([
                'index' => config()->get('db.elasticsearch.recipes_index'),
                'type' => 'all',
                'body' => [
                    'query' => [
                        'simple_query_string'=> [
                            'query' => $query,
                            'fields' => ['name^3', 'description']
                        ]
                    ]
                ],
                'from' => ($pageNum - 1) * $perPage,
                'size' => $perPage
            ]);
        } catch (\Exceptions $e) {
            throw new UnexpectedException;
        }
        $items = [];
        $total = 0;
        if (!empty($response['hits']['hits'])) {
            $total = $response['hits']['total'];
            foreach($response['hits']['hits'] as $row)
            {
                $items[] = $row['_source'];
            }
        }
        $options = [
            'path' => currentUrl(),
            'pageName' => config()->get('app.page_param'),
        ];
        return container()->makeWith(LengthAwarePaginator::class, compact(
            'items', 'total', 'perPage', 'pageNum', 'options'
        ));
    }

    public function getById($recipeId)
    {
        try {
            $response = $this->elasticsearch->get([
                'index' => config()->get('db.elasticsearch.recipes_index'),
                'type' => 'all',
                'id' => $recipeId,
            ]);
            if ($response['found']) {
                return $response['_source'];
            }
            throw new RecipeNotFoundException;
        } catch (\Exceptions $e) {
            throw new UnexpectedException;
        }
    }

    public function create($data)
    {
        try {
            $response = $this->elasticsearch->index([
                'index' => config()->get('db.elasticsearch.recipes_index'),
                'type' => 'all',
                'id' => $data['id'],
                'body' => $data
            ]);
            logger()->info('recipe created:', [$response]);
            if($response) {
                return $data;
            }
            throw new UnexpectedException;
        } catch (\Exceptions $e) {
            throw new UnexpectedException;
        }
    }

    public function update($data, $recipeId)
    {
        $recipe = $this->getById($recipeId);
        try {
            $response = $this->elasticsearch->update([
                'index' => config()->get('db.elasticsearch.recipes_index'),
                'type' => 'all',
                'id' => $recipeId,
                'body' => ['doc' => $data]
            ]);
            logger()->info('recipe updated:', [$response]);
            if($response) {
                return array_merge($recipe, $data);
            }
            throw new UnexpectedException;
        } catch (\Exceptions $e) {
            throw new UnexpectedException;
        }
    }

    public function delete($recipeId)
    {
        $recipe = $this->getById($recipeId);
        try {
            $response = $this->elasticsearch->delete([
                'index' => config()->get('db.elasticsearch.recipes_index'),
                'type' => 'all',
                'id' => $recipeId,
            ]);
            logger()->info('recipe deleted:', [$response]);
            if($response) {
                return true;
            }
            throw new UnexpectedException;
        } catch (\Exceptions $e) {
            throw new UnexpectedException;
        }
    }

    public function deleteAll()
    {
        try {

            $response = $this->elasticsearch->deleteByQuery([
                'index' => config()->get('db.elasticsearch.recipes_index'),
                'type' => 'all',
                'body' => [
                    'query' => [
                        'match_all' => (object)[]
                    ],
                    'conflicts' => 'proceed'
                ],
            ]);
            logger()->info('recipes all deleted:', [$response]);
            if($response) {
                return true;
            }
            throw new UnexpectedException;
        } catch (\Exceptions $e) {
            throw new UnexpectedException;
        }
    }

    public function indexExists()
    {
        try {
            $response = $this->elasticsearch->indices()->exists([
                'index' => config()->get('db.elasticsearch.recipes_index'),
            ]);
            logger()->info('recipe index exists.', [$response]);
            return $response;
        } catch (\Exceptions $e) {
            throw new UnexpectedException;
        }
    }

    public function indexCreate()
    {
        try {
            $response = $this->elasticsearch->indices()->create([
                'index' => config()->get('db.elasticsearch.recipes_index'),
                'body' => [
                    'mappings' => [
                        'all' => [
                            'properties' => [
                                'id' => [
                                    'type' => 'integer'
                                ],
                                'name' => [
                                    'type' => 'text',
                                    'analyzer' => 'english'
                                ],
                                'description' => [
                                    'type' => 'text',
                                    'analyzer' => 'english'
                                ],
                                'prep_time' => [
                                    'type' => 'integer'
                                ],
                                'difficulty' => [
                                    'type' => 'integer'
                                ],
                                'vegetarian' => [
                                    'type' => 'keyword'
                                ],
                                'rating' => [
                                    'type' => 'float'
                                ],
                                'created_at' => [
                                    'type'=>  'date',
                                    'format'=> 'yyyy-MM-dd HH:mm:ss.SSS||yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis'
                                ],
                                'updated_at' => [
                                    'type'=>  'date',
                                    'format'=> 'yyyy-MM-dd HH:mm:ss.SSS||yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis'
                                ]
                            ]
                        ]
                    ]
                ]
            ]);
            logger()->info('recipe index created.', [$response]);
            return $response;
        } catch (\Exceptions $e) {
            throw new UnexpectedException;
        }
    }
}

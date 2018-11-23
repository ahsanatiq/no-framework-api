<?php

return [
    'default' => getenv('DB_CONNECTION'),
    'redis' => [
        'driver'    => 'predis',
        'host'      => getenv('REDIS_HOST') ?: 'redis',
        'password'  => getenv('REDIS_PASSWORD') ?: null,
        'port'      => getenv('REDIS_PORT') ?: '6379',
        'database'  => getenv('REDIS_DATABASE') ?: 0,
    ],
    'elasticsearch' => [
        'hosts'         => getenv('ELASTICSEARCH_HOSTS')
                            ? explode(',', getenv('ELASTICSEARCH_HOSTS'))
                            : ['elasticsearch'],
        'password'      => getenv('ELASTICSEARCH_PASSWORD') ?: null,
        'recipes_index' => getenv('ELASTICSEARCH_RECIPES_INDEX') ?: 'recipes',
    ],
];

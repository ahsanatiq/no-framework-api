<?php

return [
    'paths' => [
        'migrations' => __DIR__ . '/database/migrations',
        'seeds'      => __DIR__ . '/database/seeds',
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_database' => 'default',
        'default' => [
            'adapter' => getenv('DB_CONNECTION'),
            'host'    => getenv('DB_HOST'),
            'name'    => getenv('DB_NAME'),
            'user'    => getenv('DB_USER'),
            'pass'    => getenv('DB_PASSWORD'),
            'port'    => getenv('DB_PORT'),
        ],
        'testing' => [
            'adapter' => 'sqlite',
            'name'    => getenv('DB_NAME'),
        ]
    ]
];

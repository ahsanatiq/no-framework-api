<?php
$envTesting = require '.env.testing.php';

return [
    'paths' => [
        'migrations' => ['database' => __DIR__.'/database/migrations'],
        'seeds'      => ['database' => __DIR__.'/database/seeds'],
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
            'adapter' => $envTesting['DB_CONNECTION'],
            'host'    => $envTesting['DB_HOST'],
            'name'    => $envTesting['DB_NAME'],
            'user'    => $envTesting['DB_USER'],
            'pass'    => $envTesting['DB_PASSWORD'],
            'port'    => $envTesting['DB_PORT'],
        ]
    ]
];

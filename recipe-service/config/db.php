<?php

return [
    'default'       => getenv('DB_CONNECTION'),
    'pgsql'         => [
        'driver'    => 'pgsql',
        'host'      => getenv('DB_HOST'),
        'database'  => getenv('DB_NAME'),
        'username'  => getenv('DB_USER'),
        'password'  => getenv('DB_PASSWORD'),
        'port'      => getenv('DB_PORT'),
        'charset'   => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ],
];

<?php
 return [
    'env'            => getenv('APP_ENV') ?: 'dev', // dev, testing, production
    'name'           => getenv('APP_NAME') ?: 'recipe-service',
    'url'            => getenv('APP_URL') ?: 'http://localhost',
    'items_per_page' => getenv('APP_ITEMS_PER_PAGE') ?: 15,
    'public_key'     => getenv('APP_PUBLIC_KEY') ?: '/server/keys/id_rsa.pub',
    'page_param'     => 'page',
    'per_page_param' => 'per_page',
    'log_file'       => getenv('APP_LOG_FILE') ?: '/server/logs/app.log',
    'log_days'       => getenv('APP_LOG_DAYS') ?: '7',
    'log_level'      => getenv('APP_LOG_LEVEL') ?: 'DEBUG',
];

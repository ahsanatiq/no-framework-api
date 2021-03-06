<?php
return [
    'env'         => getenv('APP_ENV') ?: 'dev', // dev, testing, production
    'name'        => getenv('APP_NAME') ?: 'oauth-service',
    'url'         => getenv('APP_URL') ?: 'http://localhost',
    'public_key'  => getenv('APP_PUBLIC_KEY') ?: '/server/keys/id_rsa.pub',
    'private_key' => getenv('APP_PRIVATE_KEY') ?: '/server/keys/id_rsa',
    'log_file'    => getenv('APP_LOG_FILE') ?: '/server/logs/app.log',
    'log_days'    => getenv('APP_LOG_DAYS') ?: '7',
    'log_level'   => getenv('APP_LOG_LEVEL') ?: 'DEBUG',
];

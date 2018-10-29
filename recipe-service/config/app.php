<?php
return [
    'env'      => getenv('APP_ENV') ?: 'production',
    'name'     => getenv('APP_NAME') ?: 'recipe-service',
    'url'      => getenv('APP_URL') ?: 'http://localhost',
];

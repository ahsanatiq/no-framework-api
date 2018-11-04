<?php
return [
    'env'            => getenv('APP_ENV') ?: 'production',
    'name'           => getenv('APP_NAME') ?: 'recipe-service',
    'url'            => getenv('APP_URL') ?: 'http://localhost',
    'items_per_page' => 15,
    'page_param'     => 'page',
    'per_page_param' => 'per_page',
];

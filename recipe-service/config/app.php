<?php
return [
    'env'            => getenv('APP_ENV') ?: 'dev', // dev, testing, production
    'name'           => getenv('APP_NAME') ?: 'recipe-service',
    'url'            => getenv('APP_URL') ?: 'http://localhost',
    'items_per_page' => getenv('APP_ITEMS_PER_PAGE') ?: 15,
    'page_param'     => 'page',
    'per_page_param' => 'per_page',
];

<?php
return [
    'env'            => getenv('APP_ENV') ?: 'dev', // dev, testing, production
    'name'           => getenv('APP_NAME') ?: 'oauth-service',
    'url'            => getenv('APP_URL') ?: 'http://localhost',
];

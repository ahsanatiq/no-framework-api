<?php
require 'vendor/autoload.php';

$path = __DIR__.'/.env.testing';
return $envTesting = (new Symfony\Component\Dotenv\Dotenv())->parse(file_get_contents($path), $path);

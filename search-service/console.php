#!/usr/bin/env php

<?php

use App\Commands\ConsumeRecipesCommand;
use Symfony\Component\Console\Application;

require_once __DIR__.'/bootstrap/app.php';

$app = new Application('Search Service Console', 'v1.0.0');
$app->add(new ConsumeRecipesCommand);
$app->run();

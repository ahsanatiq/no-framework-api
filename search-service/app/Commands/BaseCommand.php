<?php

namespace App\Commands;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BaseCommand extends SymfonyCommand
{

    public function beforeExecute(InputInterface $input) // , OutputInterface $output
    {
        if ($input->getOption('env') != config()->get('app.env')) {
            $envFile = __DIR__.'/../../.env.'.$input->getOption('env');
        }
        if (!empty($envFile) && file_exists($envFile)) {
            loadEnvironmentFromFile($envFile);
        }
    }
}

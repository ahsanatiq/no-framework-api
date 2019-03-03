<?php

namespace App\Commands;

use App\Exceptions\WorkerExceptionHandler;
use Console\Command;
use Illuminate\Queue\Worker;
use Illuminate\Queue\WorkerOptions;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ConsumeRecipesCommand extends BaseCommand
{

    public function configure()
    {
        $this
        ->setName('consume:recipes')
        ->setDescription('consume the events that are comming into the redis message queue')
        ->setHelp('This command allows you to keep consuming the messages/events in the redis server...')
        ->addOption(
            'env',
            null,
            InputOption::VALUE_OPTIONAL,
            'The environment in which this consumer will run',
            config()->get('app.env')
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        parent::beforeExecute($input, $output);
        $queue = container()->make('queue');
        $events = dispatcher();
        $worker = new Worker($queue, $events, new WorkerExceptionHandler);
        $options = new WorkerOptions(
            $delay = 3,
            $memory = 128,
            $timeout = 60,
            $sleep = 3,
            $maxTries = 1,
            $force = false,
            $stopWhenEmpty = false
        );
        $worker->daemon('redis', config()->get('app.queue'), $options);
    }
}

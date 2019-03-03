<?php
namespace App\Exceptions;

use Illuminate\Contracts\Debug\ExceptionHandler;
use \Exception;

class WorkerExceptionHandler implements ExceptionHandler
{
    public function report(Exception $e)
    {
        $this->output($e);
    }

    public function render($request, Exception $e)
    {
        $this->output($e, $request);
    }

    public function renderForConsole($output, Exception $e)
    {
        $this->output($e, $output);
    }

    public function output($e, $extra = null)
    {
        echo PHP_EOL.json_encode(['message'=>$e->getMessage(),'trace'=>$e->getTrace(), 'extra'=>$extra]).PHP_EOL;
    }
}

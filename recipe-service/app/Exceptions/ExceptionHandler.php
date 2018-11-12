<?php

namespace App\Exceptions;

use App\Exceptions\NotFoundHttpException as AppNotFoundHttpException;
use App\Exceptions\RecipeNotFoundException;
use App\Exceptions\UnexpectedException;
use App\Exceptions\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ExceptionHandler
{

    public static function handle($e)
    {
        $loggerLevel = 'debug';
        switch ($e) {
            case $e instanceof NotFoundHttpException:
                $data = (new AppNotFoundHttpException)->getData();
                break;
            case $e instanceof MethodNotAllowedHttpException:
                $data = (new AppNotFoundHttpException)->getData();
                break;
            case $e instanceof ValidationException:
                $data = $e->getData();
                break;
            case $e instanceof RecipeNotFoundException:
                $data = $e->getData();
                break;
            case $e instanceof UnauthorizedException:
                $data = $e->getData();
                $loggerLevel = 'info';
                break;
            default:
                $data = (new UnexpectedException)->getData();
                $loggerLevel = 'error';
                break;
        }

        $extraInfo = [
            'exception_type' => get_class($e),
            'exception_message' => $e->getMessage(),
            'trace' => $e->getTrace()
        ];

        logger()->$loggerLevel('Exception occured.', array_merge($data,$extraInfo));

        if (config()->get('app.env') != 'production') {
            $data = array_merge($data, $extraInfo);
        }

        return (new Response($data, $data['code']))->send();
    }
}

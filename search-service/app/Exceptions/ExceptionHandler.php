<?php

namespace App\Exceptions;

use App\Exceptions\BadRequest400Exception;
use App\Exceptions\NotFoundHttpException as AppNotFoundHttpException;
use App\Exceptions\RecipeNotFoundException;
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
            case $e instanceof \Elasticsearch\Common\Exceptions\Missing404Exception:
                $data = (new RecipeNotFoundException)->getData();
                break;
            case $e instanceof \Elasticsearch\Common\Exceptions\BadRequest400Exception:
                $data = (new BadRequest400Exception($e))->getData();
                break;
            case $e instanceof ValidationException:
                $data = $e->getData();
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

        logger()->$loggerLevel('Exception occured.', array_merge($data, $extraInfo));

        if (config()->get('app.env') != 'production') {
            $data = array_merge($data, $extraInfo);
        }

        return (new Response($data, $data['code']))->send();
    }
}

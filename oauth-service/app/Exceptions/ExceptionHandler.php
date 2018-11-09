<?php

namespace App\Exceptions;

use App\Exceptions\NotFoundHttpException as AppNotFoundHttpException;
use App\Exceptions\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ExceptionHandler
{

    public static function handle($e)
    {
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
            default:
                $data = (new UnexpectedException)->getData();
                break;
        }
        if (config()->get('app.env') != 'production') {
            $data['exception_type'] = get_class($e);
            $data['exception_message'] = $e->getMessage();
            $data['trace'] = $e->getTrace();
        }

        return (new Response($data, $data['code']))->send();
    }
}

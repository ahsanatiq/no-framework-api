<?php

namespace App\Exceptions;

use App\Exceptions\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Exceptions\NotFoundHttpException as AppNotFoundHttpException;

class ExceptionHandler
{

    public static function handle($e, Request $request) {
        switch ($e) {
            case $e instanceof NotFoundHttpException:
                $data = (new AppNotFoundHttpException($request))->getData();
                break;
            case $e instanceof ValidationException:
                $data = $e->getData();
                break;
            default:
                $data = (new UnexpectedException)->getData();
                if (config()->get('app.env') != 'production') {
                    $data['message'] = $e->getMessage();
                }
                break;
        }
        if (config()->get('app.env') != 'production') {
            $data['trace'] = $e->getTrace();
        }

        return (new Response($data, $data['code']))->send();
    }

}

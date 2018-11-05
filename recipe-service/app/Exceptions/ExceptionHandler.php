<?php

namespace App\Exceptions;

use App\Exceptions\NotFoundHttpException as AppNotFoundHttpException;
use App\Exceptions\RecipeNotFoundException;
use App\Exceptions\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
            case $e instanceof RecipeNotFoundException:
                $data = $e->getData();
                break;
            default:
                $data = (new UnexpectedException)->getData();
                if (config()->get('app.env') != 'production') {
                    $data['type'] = get_class($e);
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

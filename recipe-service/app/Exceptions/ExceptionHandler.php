<?php

namespace App\Exceptions;

use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ExceptionHandler
{

    public static function handle($e) {
        switch ($e) {
            case $e instanceof NotFoundHttpException:
                $request = container()->make('request');
                $method  = $request->getMethod();
                $path    = $request->getPathInfo();
                $message = "Method {$method} was not found in path {$path}";
                $data = [
                    'type'      => get_class($e),
                    'message'   => $message,
                    'code'      => 404,
                ];
                break;
            default:
                $data = [
                    'type'      => get_class($e),
                    'message'   => 'Whoops, something went wrong',
                    'code'      => 500,
                ];
                break;
        }

        if (config()->get('app.env') != 'production' && $data['code'] != 422) {
            $data['trace'] = $e->getTrace();
        }

        return (new Response($data, $data['code']))->send();
    }

}

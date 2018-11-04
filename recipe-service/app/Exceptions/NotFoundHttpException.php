<?php
namespace App\Exceptions;

use Illuminate\Http\Request;

class NotFoundHttpException extends BaseException
{
    public function __construct(Request $request)
    {
        $method = $request->getMethod();
        $path = $request->getPathInfo();
        $message = "Method {$method} is not found in path {$path}";
        parent::__construct($message, 404);
    }
}

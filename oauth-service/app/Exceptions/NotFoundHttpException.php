<?php
namespace App\Exceptions;

use Illuminate\Http\Request;

class NotFoundHttpException extends BaseException
{
    public function __construct()
    {
        $message = "Resource not found.";
        parent::__construct($message, 404);
    }
}

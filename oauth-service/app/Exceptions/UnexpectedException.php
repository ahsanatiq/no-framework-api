<?php
namespace App\Exceptions;

class UnexpectedException extends BaseException
{
    public function __construct()
    {
        $message = 'Whoops, something went wrong';
        parent::__construct($message, 500);
    }
}

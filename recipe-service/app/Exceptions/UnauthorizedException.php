<?php
namespace App\Exceptions;

class UnauthorizedException extends BaseException
{
    public function __construct($message = 'Unauthorized')
    {
        parent::__construct($message, 401);
    }
}

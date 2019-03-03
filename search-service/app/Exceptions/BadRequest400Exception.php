<?php
namespace App\Exceptions;

class BadRequest400Exception extends BaseException
{
    public function __construct($originalException)
    {
        $msg = json_decode($originalException->getMessage(), true);
        parent::__construct($msg['error']['reason'] ?: 'Bad Request', 400);
    }
}

<?php
namespace App\Exceptions;

abstract class BaseException extends \Exception
{

    public function getData()
    {
        return [
            'type' => $this->getExceptionType(),
            'message' => $this->getExceptionMessage(),
            'code' => $this->getExceptionCode()
        ];
    }

    public function getExceptionType()
    {
        return basename(str_replace('\\', '/', get_class($this)));
    }

    public function getExceptionMessage()
    {
        return $this->getMessage();
    }

    public function getExceptionCode()
    {
        return $this->getCode();
    }
}

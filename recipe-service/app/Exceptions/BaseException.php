<?php
namespace App\Exceptions;

use Illuminate\Support\MessageBag;

abstract class BaseException extends \Exception
{

    public function __construct( $message = null, $code = 0, Exception $previous = null ) {
        parent::__construct( $message, $code, $previous);
    }

    public function getData()
    {
        return [
            'type' => $this->get_type(),
            'message' => $this->get_message(),
            'code' => $this->get_code()
        ];
    }

    public function get_type()
    {
        return basename(str_replace('\\', '/', get_class($this)));
    }

    public function get_message()
    {
        return $this->getMessage();
    }

    public function get_code()
    {
        return $this->getCode();
    }
}

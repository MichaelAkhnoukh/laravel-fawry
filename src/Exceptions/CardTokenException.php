<?php

namespace Caishni\Fawry\Exceptions;

use Exception;

class CardTokenException extends Exception
{
    public static function cardAlreadyExists()
    {
        return new static('card already exists');
    }

    public static function error()
    {
        return new static('error generating card token');
    }
}
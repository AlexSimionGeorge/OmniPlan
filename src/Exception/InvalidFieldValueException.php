<?php

namespace App\Exception;

use Exception;

class InvalidFieldValueException extends Exception
{
    public function __construct(string $field, string $value)
    {
        parent::__construct("Invalid field value: " . $field . " with value " . $value);
    }
}
<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class MissingFieldsException extends HttpException
{
    public function __construct(string $message = 'Required fields are missing.', int $statusCode = 400)
    {
        parent::__construct($statusCode, $message);
    }
}

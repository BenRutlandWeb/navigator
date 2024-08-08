<?php

namespace Navigator\Auth\Exceptions;

use Navigator\Http\Exceptions\HttpException;

class AuthenticationException extends HttpException
{
    public function __construct(string $message = '')
    {
        parent::__construct(403, $message ?: 'Unauthorized.');
    }
}

<?php

namespace Navigator\Auth\Exceptions;

use Navigator\Http\Exceptions\HttpException;

class NotFoundException extends HttpException
{
    public function __construct(string $message = '')
    {
        parent::__construct(404, $message ?: 'Page not found.');
    }
}

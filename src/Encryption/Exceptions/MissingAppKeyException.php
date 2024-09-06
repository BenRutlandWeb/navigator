<?php

namespace Navigator\Encryption\Exceptions;

use RuntimeException;

class MissingAppKeyException extends RuntimeException
{
    public function __construct(string $message = 'No application encryption key has been specified.')
    {
        parent::__construct($message);
    }
}

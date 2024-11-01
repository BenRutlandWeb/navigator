<?php

namespace Navigator\Http\Exceptions;

use Exception;
use Throwable;

class HttpException extends Exception
{
    public function __construct(public readonly int $statusCode, string $message = '', public readonly array $headers = [], int $code = 0, ?Throwable $e = null)
    {
        parent::__construct($message ?: get_status_header_desc($statusCode), $code, $e);
    }
}

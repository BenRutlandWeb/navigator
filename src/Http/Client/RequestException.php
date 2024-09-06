<?php

namespace Navigator\Http\Client;

use Exception;

class RequestException extends Exception
{
    public function __construct(public Response $response)
    {
        parent::__construct("HTTP request returned status code {$response->status()}.", $response->status());
    }
}

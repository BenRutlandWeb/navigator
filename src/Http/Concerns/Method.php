<?php

namespace Navigator\Http\Concerns;

use Navigator\Collections\Arr;
use Navigator\Foundation\Concerns\Arrayable;

enum Method: string implements Arrayable
{
    case GET = 'GET';
    case POST = 'POST';
    case PUT = 'PUT';
    case PATCH = 'PATCH';
    case DELETE = 'DELETE';

    public function toArray(): array
    {
        return Arr::map(static::cases(), fn ($method) => $method->value);
    }
}

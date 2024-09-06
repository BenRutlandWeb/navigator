<?php

namespace Navigator\Http\Client;

/** @mixin Http */
class Pool
{
    /** @var array<array-key, Http> */
    protected array $pool = [];

    public function as(string $key): Http
    {
        return $this->pool[$key] = $this->asyncRequest();
    }

    public function asyncRequest(): Http
    {
        return (new Http())->async();
    }

    /** @return array<array-key, Http> */
    public function getRequests(): array
    {
        return $this->pool;
    }

    public function __call(string $method, array $parameters = []): Http
    {
        return $this->pool[] = $this->asyncRequest()->$method(...$parameters);
    }
}

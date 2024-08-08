<?php

namespace Navigator\Http\Concerns;

trait HasServer
{
    /** @var array<string, mixed> */
    protected array $server = [];

    public function set_server_params(array $server = []): void
    {
        $this->server = $server;
    }

    /** @return array<string, mixed> */
    public function get_server_params(): array
    {
        return $this->server;
    }

    public function server(string $key, mixed $default = null): mixed
    {
        return $this->server[$key] ?? $default;
    }
}

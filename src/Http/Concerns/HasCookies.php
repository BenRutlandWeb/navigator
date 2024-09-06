<?php

namespace Navigator\Http\Concerns;

trait HasCookies
{
    /** @var array<string, mixed> */
    protected array $cookies = [];

    public function set_cookie_params(array $cookies = []): void
    {
        $this->cookies = $cookies;
    }

    /** @return array<string, mixed> */
    public function get_cookie_params(): array
    {
        return $this->cookies;
    }

    public function hasCookie(string $key): bool
    {
        return isset($this->cookies[$key]);
    }

    public function cookie(string $key, mixed $default = null): mixed
    {
        return $this->cookies[$key] ?? $default;
    }
}

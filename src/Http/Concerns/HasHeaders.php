<?php

namespace Navigator\Http\Concerns;

use Navigator\Collections\Arr;

trait HasHeaders
{
    /** @return array<string, string> */
    public function headers(): array
    {
        return Arr::map($this->get_headers(), fn ($header) => $header[0]);
    }

    public function hasHeader(string $key): bool
    {
        return !is_null($this->header($key));
    }

    public function header(string $key, mixed $default = null): mixed
    {
        return $this->get_header($key) ?? $default;
    }
}

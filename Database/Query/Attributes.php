<?php

namespace Navigator\Database\Query;

use Navigator\Collections\Arr;

class Attributes
{
    public function __construct(protected array $attributes = [])
    {
        //
    }

    public function merge(array $attributes = []): static
    {
        $this->attributes = Arr::merge($this->attributes, $attributes);

        return $this;
    }

    public function set(string $key, mixed $value): static
    {
        $this->attributes[$key] = $value;

        return $this;
    }

    public function toArray(): array
    {
        return $this->attributes;
    }

    public function forQuery(): array
    {
        return $this->attributes;
    }
}

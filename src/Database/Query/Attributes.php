<?php

namespace Navigator\Database\Query;

use JsonSerializable;
use Navigator\Collections\Arr;
use Navigator\Foundation\Concerns\Arrayable;

class Attributes implements Arrayable, JsonSerializable
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

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}

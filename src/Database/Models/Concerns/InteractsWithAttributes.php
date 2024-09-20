<?php

namespace Navigator\Database\Models\Concerns;

trait InteractsWithAttributes
{
    public function __isset(string $key): bool
    {
        return isset($this->object->$key);
    }

    public function __get(string $key): mixed
    {
        return $this->object->$key;
    }

    public function __set(string $key, mixed $value): void
    {
        $this->object->$key = $value;
    }

    public function __unset(string $key): void
    {
        unset($this->object->$key);
    }

    public function offsetExists($key): bool
    {
        return isset($this->object->$key);
    }

    public function offsetGet($key): mixed
    {
        return $this->object->$key;
    }

    public function offsetSet($key, mixed $value): void
    {
        $this->object->$key = $value;
    }
    public function offsetUnset($key): void
    {
        unset($this->object->$key);
    }

    public function toArray(): array
    {
        return $this->object->to_array();
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function fill(array $attributes): static
    {
        foreach ($attributes as $key => $value) {
            $this->object->$key = $value;
        }

        return $this;
    }
}

<?php

namespace Navigator\Database\Query\Concerns;

trait HasAttributes
{
    public function where(string $key, mixed $value): static
    {
        $this->attributes->set($key, $value);

        return $this;
    }

    public function whereIn(string $key, array $values): static
    {
        if (count($values)) {
            return $this->where($key, $values);
        }

        return $this;
    }

    /** @param array<string, mixed> $wheres */
    public function whereMany(array $wheres): static
    {
        foreach ($wheres as $key => $value) {
            $this->where($key, $value);
        }

        return $this;
    }

    public function toArray(): array
    {
        return $this->attributes->toArray();
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}

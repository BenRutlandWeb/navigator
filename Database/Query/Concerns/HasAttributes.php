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
        return $this->where($key, $values);
    }
}

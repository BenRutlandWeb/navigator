<?php

namespace Navigator\Database\Query\Concerns;

trait AggregatesQueries
{
    public function avg(string $column): mixed
    {
        return $this->get()->pluck($column)->avg();
    }

    public function sum(string $column): mixed
    {
        return $this->get()->pluck($column)->sum();
    }

    public function max(string $column): mixed
    {
        return $this->get()->pluck($column)->max();
    }

    public function min(string $column): mixed
    {
        return $this->get()->pluck($column)->min();
    }

    public function exists(): bool
    {
        return $this->count() > 0;
    }

    public function doesntExist(): bool
    {
        return !$this->exists();
    }
}

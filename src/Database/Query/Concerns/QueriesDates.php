<?php

namespace Navigator\Database\Query\Concerns;

use DateTimeInterface;
use Navigator\Database\Query\DateQuery;

trait QueriesDates
{
    /** @param (callable(DateQuery): void) $callback */
    public function whereDate(callable $callback): static
    {
        $callback($query = DateQuery::make());

        return $this->where('date_query', $query->toArray());
    }

    public function whereDateBefore(string $column, DateTimeInterface|string $date): static
    {
        return $this->where('date_query', [[
            'column' => $column,
            'before' => DateQuery::resolveDateTime($date),
        ]]);
    }

    public function whereDateAfter(string $column, DateTimeInterface|string $date): static
    {
        return $this->where('date_query', [[
            'column' => $column,
            'after'  => DateQuery::resolveDateTime($date),
        ]]);
    }
}

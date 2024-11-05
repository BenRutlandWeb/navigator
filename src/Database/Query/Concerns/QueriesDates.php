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
        return $this->whereDate(function (DateQuery $query) use ($column, $date) {
            $query->where($column, null, ['before' => DateQuery::resolveDateTime($date)]);
        });
    }

    public function whereDateAfter(string $column, DateTimeInterface|string $date): static
    {
        return $this->whereDate(function (DateQuery $query) use ($column, $date) {
            $query->where($column, null, ['after' => DateQuery::resolveDateTime($date)]);
        });
    }

    public function whereDateBetween(string $column, DateTimeInterface|string $start, DateTimeInterface|string $end): static
    {
        return $this->whereDate(function (DateQuery $query) use ($column, $start, $end) {
            $query->where($column, null, [
                'before' => DateQuery::resolveDateTime($end),
                'after'  => DateQuery::resolveDateTime($start),
            ]);
        });
    }

    public function whereDateNotBetween(string $column, DateTimeInterface|string $start, DateTimeInterface|string $end): static
    {
        return $this->whereDate(function (DateQuery $query) use ($column, $start, $end) {
            $query->where($column, null, ['before' => DateQuery::resolveDateTime($start)])
                ->orWhere($column, null, ['after'  => DateQuery::resolveDateTime($end)]);
        });
    }
}

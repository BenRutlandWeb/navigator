<?php

namespace Navigator\Database\Query\Concerns;

use Navigator\Database\Query\DateQuery;

trait QueriesDates
{
    /** @param (callable(DateQuery): void) $callback */
    public function whereDate(callable $callback): static
    {
        $callback($query = DateQuery::make());

        return $this->where('date_query', $query->toArray());
    }
}

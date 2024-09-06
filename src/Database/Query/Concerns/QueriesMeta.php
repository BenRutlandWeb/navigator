<?php

namespace Navigator\Database\Query\Concerns;

use Navigator\Database\Query\MetaQuery;

trait QueriesMeta
{
    /** @param (callable(MetaQuery): void) $callback */
    public function whereMeta(callable $callback): static
    {
        $callback($query = MetaQuery::make());

        return $this->where('meta_query', $query->toArray());
    }
}

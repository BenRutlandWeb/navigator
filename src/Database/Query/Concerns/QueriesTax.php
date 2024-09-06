<?php

namespace Navigator\Database\Query\Concerns;

use Navigator\Database\Query\TaxQuery;

trait QueriesTax
{
    /** @param (callable(TaxQuery): void) $callback */
    public function whereTax(callable $callback): static
    {
        $callback($query = TaxQuery::make());

        return $this->where('tax_query', $query->toArray());
    }
}

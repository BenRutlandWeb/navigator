<?php

namespace Navigator\Database\Query\Concerns;

use Generator;
use Navigator\Pagination\Paginator;

trait PaginatesQueries
{
    public function forPage(int $page, int $perPage = 15): static
    {
        return $this->offset(($page - 1) * $perPage)->limit($perPage);
    }

    /** @return Paginator<T> */
    public function paginate(int $perPage = 15, string $pageName = 'page', ?int $page = null, ?int $total = null): Paginator
    {
        $page = $page ?: Paginator::resolveCurrentPage($pageName);
        $query = clone $this;
        $results = $this->forPage($page, $perPage)->get();

        return new Paginator($results, $total ?? $query->count(), $perPage, $page, $pageName);
    }

    /** @param (callable(T, int): mixed) $callback */
    public function chunk(int $count, callable $callback): bool
    {
        $page = 1;

        do {
            $results = $this->forPage($page, $count)->get();

            $countResults = $results->count();

            if (!$countResults) {
                break;
            }

            if ($callback($results, $page) === false) {
                return false;
            }

            unset($results);

            $page++;
        } while ($countResults == $count);

        return true;
    }

    public function lazy(int $chunk = 1000): Generator
    {
        $page = 1;

        while (true) {
            $results = $this->forPage($page++, $chunk)->get();

            foreach ($results as $result) {
                yield $result;
            }

            if ($results->count() < $chunk) {
                return;
            }
        }
    }
}

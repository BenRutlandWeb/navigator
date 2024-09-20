<?php

namespace Navigator\Database\Query;

use JsonSerializable;
use Navigator\Collections\Collection;
use Navigator\Database\BuilderInterface;
use Navigator\Database\Exceptions\ModelNotFoundException;
use Navigator\Database\ModelInterface;
use Navigator\Database\Models\Term;
use Navigator\Database\Query\Concerns\HasAttributes;
use Navigator\Database\Query\Concerns\Order;
use Navigator\Database\Query\Concerns\QueriesMeta;
use Navigator\Database\Relation;
use Navigator\Foundation\Concerns\Arrayable;
use Navigator\Pagination\Paginator;
use WP_Term;
use WP_Term_Query;

/** @template T of ModelInterface */
class TermBuilder implements Arrayable, BuilderInterface, JsonSerializable
{
    use HasAttributes;
    use QueriesMeta;

    /** @param class-string<T> $model */
    public function __construct(readonly public string $model, protected Attributes $attributes = new TermAttributes())
    {
        //
    }

    public function include(array $ids): static
    {
        return $this->where('include', $ids);
    }

    public function exclude(array $ids): static
    {
        return $this->where('exclude', $ids);
    }

    public function search(string $query): static
    {
        return $this->where('search', $query);
    }

    public function limit(int $limit): static
    {
        return $this->where('number', $limit);
    }

    public function offset(int $offset): static
    {
        return $this->where('offset', $offset);
    }

    public function orderBy(string $column, Order $direction = Order::ASC): static
    {
        return $this->where('orderby', $column)->where('order', $direction->value);
    }

    public function latest(): static
    {
        return $this->orderBy('term_id', Order::DESC);
    }

    public function oldest(): static
    {
        return $this->orderBy('term_id');
    }

    /** @return Collection<int, T> */
    public function get(): Collection
    {
        return Collection::make($this->runQuery()->get_terms())
            ->map(function (WP_Term $term) {
                $model = Relation::getMorphedModel($term->taxonomy) ?? $this->model;
                return new $model($term);
            });
    }

    /** @return ?T */
    public function first(): ?Term
    {
        return $this->limit(1)->get()->first();
    }

    /** @return ?T */
    public function firstOrFail(): ?Term
    {
        return $this->first() ?? throw new ModelNotFoundException($this->model);
    }

    public function count(): int
    {
        return $this->where('fields', 'count')->runQuery()->get_terms();
    }

    public function toSql(): ?string
    {
        return $this->runQuery()->request;
    }

    public function runQuery(): WP_Term_Query
    {
        return new WP_Term_Query($this->attributes->forQuery());
    }

    /** @return Paginator<T> */
    public function paginate(int $perPage = 5, string $pageName = 'page', ?int $page = null, ?int $total = null): Paginator
    {
        $page = $page ?: Paginator::resolveCurrentPage($pageName);
        $query = clone $this;
        $results = $this->limit($perPage)->offset(($page - 1) * $perPage)->get();

        return new Paginator($results, $total ?? $query->count(), $perPage, $page, $pageName);
    }

    /** @return ?T */
    public function create(array $attributes): ?ModelInterface
    {
        return $this->model::create(
            $this->attributes->merge($attributes)->toArray()
        );
    }

    public function update(array $attributes): int
    {
        $affectedRows = 0;

        foreach ($this->get() as $term) {
            if ($term->update($attributes)) {
                $affectedRows++;
            }
        }

        return $affectedRows;
    }

    public function delete(): int
    {
        $affectedRows = 0;

        foreach ($this->get() as $term) {
            if ($term->delete()) {
                $affectedRows++;
            }
        }

        return $affectedRows;
    }
}

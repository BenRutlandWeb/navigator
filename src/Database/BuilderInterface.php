<?php

namespace Navigator\Database;

use Generator;
use JsonSerializable;
use Navigator\Collections\Collection;
use Navigator\Database\Query\Concerns\Order;
use Navigator\Foundation\Concerns\Arrayable;
use Navigator\Pagination\Paginator;

/** @template T of ModelInterface */
interface BuilderInterface extends Arrayable, JsonSerializable
{
    /**
     * @param class-string<T> $model
     */
    public function __construct(string $model);

    /** @return Collection<int, T> */
    public function get(): Collection;

    /** @return ?T */
    public function first(): ?ModelInterface;

    public function count(): int;

    public function toSql(): ?string;

    /** @return Paginator<T> */
    public function paginate(int $perPage = 15, string $pageName = 'page', ?int $page = null, ?int $total = null): Paginator;

    /** @param (callable(T, int): mixed) $callback */
    public function chunk(int $count, callable $callback): bool;

    public function lazy(int $chunk = 1000): Generator;

    public function where(string $key, mixed $value): static;

    public function whereIn(string $key, array $values): static;

    /** @param int[] $ids */
    public function include(array $ids): static;

    /** @param int[] $ids */
    public function exclude(array $ids): static;

    public function search(string $query): static;

    public function limit(int $limit): static;

    public function offset(int $offset): static;

    public function forPage(int $page, int $perPage = 15): static;

    public function orderBy(string $column, Order $direction = Order::ASC): static;

    public function latest(): static;

    public function oldest(): static;

    /** @return ?T */
    public function create(array $attributes): ?ModelInterface;

    public function update(array $attributes): int;

    public function delete(): int;
}
